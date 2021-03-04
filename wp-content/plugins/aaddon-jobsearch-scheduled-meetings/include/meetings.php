<?php
// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// functions class
class JobSearch_Sched_Meetings_Functions {

    public function __construct() {
        
        add_action('init', array($this, 'meeting_post_type'));
        add_action('wp_ajax_jobsearch_create_new_job_meeting_sched', array($this, 'create_new_job_meeting'));
        
        add_action('wp_ajax_jobsearch_resched_new_job_meeting_sched', array($this, 'resched_new_job_meeting'));
        
        //add_action('jobsearch_meetin_avail_time_slots', array($this, 'meetin_avail_time_slots'), 10, 2);
        
        add_action('jobsearch_candash_notific_settins_btns_after', array($this, 'candash_notific_settins_btn'));
        add_filter('jobsearch_user_notifics_list_item_html', array($this, 'user_notifics_list_item_html'), 10, 2);
        
        add_action('wp_ajax_jobsearch_set_scheduled_meeting_time_slots', array($this, 'set_scheduled_meeting_time_slots'));
        add_action('wp_ajax_jobsearch_generate_scheduled_meeting_time_slots', array($this, 'create_scheduled_meeting_time_slots'));
        
        add_action('wp_ajax_jobsearch_cancel_scheduled_meeting_bycand', array($this, 'cancel_scheduled_meeting_bycand'));
        
        add_action('wp_ajax_jobsearch_delete_scheduled_meeting_byemp', array($this, 'delete_scheduled_meeting_byemp'));
        
        add_action('wp_head', array($this, 'meeting_calendar_days_off_employer'), 50);
    }

    public function meeting_post_type() {
        $labels = array(
            'name' => _x('Meetings', 'meetings post type general name', 'jobsearch-shmeets'),
            'singular_name' => _x('Meeting', 'post type singular name', 'jobsearch-shmeets'),
            'menu_name' => _x('Meetings', 'admin menu', 'jobsearch-shmeets'),
            'name_admin_bar' => _x('Meeting', 'add new on admin bar', 'jobsearch-shmeets'),
            'add_new' => _x('Add New', 'meeting', 'jobsearch-shmeets'),
            'add_new_item' => __('Add New Meeting', 'jobsearch-shmeets'),
            'new_item' => __('New Meeting', 'jobsearch-shmeets'),
            'edit_item' => __('Edit Meeting', 'jobsearch-shmeets'),
            'view_item' => __('View Meeting', 'jobsearch-shmeets'),
            'all_items' => __('All Meetings ', 'jobsearch-shmeets'),
            'search_items' => __('Search Meetings', 'jobsearch-shmeets'),
            'parent_item_colon' => __('Parent Meetings:', 'jobsearch-shmeets'),
            'not_found' => __('No meetings found.', 'jobsearch-shmeets'),
            'not_found_in_trash' => __('No meetings found in Trash.', 'jobsearch-shmeets')
        );

        $args = array(
            'labels' => $labels,
            'description' => __('Description.', 'jobsearch-shmeets'),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => false,
            'show_in_menu' => false,
            'query_var' => false,
            'capability_type' => 'post',
            'has_archive' => false,
            'exclude_from_search' => true,
            'hierarchical' => false,
            'supports' => array('title')
        );
        register_post_type('jobsearch_meets', $args);
    }

    public function create_new_job_meeting() {
        $meet_date = $_POST['meeting_date'];
        $meet_duration = $_POST['meeting_duration'];
        $meet_time = $_POST['meeting_time'];
        $meet_note = $_POST['meeting_note'];
        $meet_cand_id = $_POST['meeting_with_cand'];
        $meet_job_id = $_POST['meeting_obj_id'];
        $zoom_meet = $_POST['zoom_meet'];
        
        $cand_user_id = jobsearch_get_candidate_user_id($meet_cand_id);
        $cand_user_obj = get_user_by('ID', $cand_user_id);
        
        $current_time = current_time('timestamp');
        
        $user_id = get_current_user_id();
        $meetin_emp_id = jobsearch_get_user_employer_id($user_id);
        
        if ($meet_date != '') {
            $meet_date = strtotime($meet_date);
        } else {
            $json_data = array('error' => '1', 'msg' => '<div class="alert alert-danger">' . esc_html__('Please select the meeting date.', 'jobsearch-shmeets') . '</div>');
            wp_send_json($json_data);
        }
        if ($meet_duration <= 0) {
            $json_data = array('error' => '1', 'msg' => '<div class="alert alert-danger">' . esc_html__('Please enter the correct duration of meeting.', 'jobsearch-shmeets') . '</div>');
            wp_send_json($json_data);
        }
        if ($meet_time == '') {
            $json_data = array('error' => '1', 'msg' => '<div class="alert alert-danger">' . esc_html__('Please select the time of meeting.', 'jobsearch-shmeets') . '</div>');
            wp_send_json($json_data);
        }
        
        $meetin_platform = 'onboard';
        
        $meetime_exlod = explode('-', $meet_time);
        $meet_start_time = isset($meetime_exlod[0]) ? strtotime($meetime_exlod[0]) : '';
        $meet_end_time = isset($meetime_exlod[1]) ? strtotime($meetime_exlod[1]) : '';
        
        $meet_exct_stime = 0;
        $meet_exct_entime = 0;
        if ($meet_start_time > 0) {
            $meet_exct_stime = strtotime(date('d-m-Y', $meet_date) . ' ' . date('H:i', $meet_start_time));
            $meet_exct_entime = strtotime(date('d-m-Y', $meet_date) . ' ' . date('H:i', $meet_end_time));
        }
        
        $zoom_meetins_switch = get_post_meta($meetin_emp_id, 'jobsearch_zoom_meetins_switch', true);
        $zoom_refresh_token = get_post_meta($meetin_emp_id, 'jobsearch_zoom_refresh_token', true);
        if ($zoom_meet == '1' && $zoom_meetins_switch == 'on' && $zoom_refresh_token != '') {
            global $JobSearch_Sched_ZoomMeets;
            $emp_zoom_email = get_post_meta($meetin_emp_id, 'jobsearch_zoom_user_email_address', true);
            $access_token = $JobSearch_Sched_ZoomMeets->user_zoom_access_token($user_id);
            $data = array(
                'schedule_for' => $emp_zoom_email,
                'topic' => sprintf(esc_html__('Interview meeting for job - %s', 'jobsearch-shmeets'), get_the_title($meet_job_id)),
                'start_time' => date('Y-m-d', $meet_exct_stime) . 'T' . date('H:i:s', $meet_exct_stime),
                'timezone' => wp_timezone_string(),
                'duration' => $meet_duration,
                'agenda' => $meet_note,
            );
            $data_str = json_encode($data);

            $url = 'https://api.zoom.us/v2/users/' . $emp_zoom_email . '/meetings';
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
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token,
            ));

            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result, true);
            if (isset($result['id'])) {
                $zoom_meeting_id = $result['id'];
                $meetin_platform = 'zoom';
                
                $zoom_meet_url = isset($result['join_url']) ? $result['join_url'] : '';
            }
        }
        
        $post_data = array(
            'post_type' => 'jobsearch_meets',
            'post_title' => 'Meeting ' . $current_time,
            'post_status' => 'publish',
        );
        $meet_post_id = wp_insert_post($post_data);
        
        update_post_meta($meet_post_id, 'meeting_date', $meet_date);
        update_post_meta($meet_post_id, 'meeting_duration', $meet_duration);
        update_post_meta($meet_post_id, 'meeting_time', $meet_time);
        update_post_meta($meet_post_id, 'meet_start_time', $meet_exct_stime);
        update_post_meta($meet_post_id, 'meet_end_time', $meet_exct_entime);
        update_post_meta($meet_post_id, 'meeting_note', $meet_note);
        update_post_meta($meet_post_id, 'meeting_platform', $meetin_platform);
        update_post_meta($meet_post_id, 'meeting_candidate', $meet_cand_id);
        update_post_meta($meet_post_id, 'meeting_employer', $meetin_emp_id);
        update_post_meta($meet_post_id, 'meeting_job', $meet_job_id);
        
        if ($meetin_platform == 'zoom') {
            update_post_meta($meet_post_id, 'zoom_meeting_id', $zoom_meeting_id);
            update_post_meta($meet_post_id, 'zoom_meeting_url', $zoom_meet_url);
        }
        
        do_action('jobsearch_meeting_email_to_candidate', $cand_user_obj, $meet_post_id, $meet_job_id);
        self::notify_cand_for_meeting($meet_post_id, $meet_job_id, $meet_cand_id);
        
        $json_data = array('error' => '0', 'msg' => '<div class="alert alert-success">' . esc_html__('Meeting created successfully.', 'jobsearch-shmeets') . '</div>');
        wp_send_json($json_data);
    }
    
    public function resched_new_job_meeting() {
        $meet_date = $_POST['meeting_date'];
        $meet_duration = $_POST['meeting_duration'];
        $meet_time = $_POST['meeting_time'];
        $meet_note = $_POST['meeting_note'];
        $meet_post_id = $_POST['meeting_id'];
        $meet_job_id = $_POST['meeting_obj_id'];
        
        $user_id = get_current_user_id();
        
        $is_employer = jobsearch_user_is_employer($user_id);
        $is_candidate = jobsearch_user_is_candidate($user_id);
        
        if ($is_employer) {
            $meet_cand_id = $_POST['meeting_with_cand'];

            $cand_user_id = jobsearch_get_candidate_user_id($meet_cand_id);
            $cand_user_obj = get_user_by('ID', $cand_user_id);
            $meetin_emp_id = jobsearch_get_user_employer_id($user_id);
        } else {
            $meetin_emp_id = $_POST['meeting_with_emp'];
            
            $meet_cand_id = jobsearch_get_user_candidate_id($user_id);
            $cand_user_id = jobsearch_get_candidate_user_id($meet_cand_id);
            $cand_user_obj = get_user_by('ID', $cand_user_id);
        }
        
        $current_time = current_time('timestamp');
        
        if ($meet_date != '') {
            $meet_date = strtotime($meet_date);
        } else {
            $json_data = array('error' => '1', 'msg' => '<div class="alert alert-danger">' . esc_html__('Please select the meeting date.', 'jobsearch-shmeets') . '</div>');
            wp_send_json($json_data);
        }
        if ($meet_duration <= 0) {
            $json_data = array('error' => '1', 'msg' => '<div class="alert alert-danger">' . esc_html__('Please enter the correct duration of meeting.', 'jobsearch-shmeets') . '</div>');
            wp_send_json($json_data);
        }
        if ($meet_time == '') {
            $json_data = array('error' => '1', 'msg' => '<div class="alert alert-danger">' . esc_html__('Please select the time of meeting.', 'jobsearch-shmeets') . '</div>');
            wp_send_json($json_data);
        }
        
        $meetime_exlod = explode('-', $meet_time);
        $meet_start_time = isset($meetime_exlod[0]) ? strtotime($meetime_exlod[0]) : '';
        $meet_end_time = isset($meetime_exlod[1]) ? strtotime($meetime_exlod[1]) : '';
        
        $meet_exct_stime = 0;
        $meet_exct_entime = 0;
        if ($meet_start_time > 0) {
            $meet_exct_stime = strtotime(date('d-m-Y', $meet_date) . ' ' . date('H:i', $meet_start_time));
            $meet_exct_entime = strtotime(date('d-m-Y', $meet_date) . ' ' . date('H:i', $meet_end_time));
        }
        
        $meet_zoom_id = get_post_meta($meet_post_id, 'zoom_meeting_id', true);
        $zoom_meetins_switch = get_post_meta($meetin_emp_id, 'jobsearch_zoom_meetins_switch', true);
        $zoom_refresh_token = get_post_meta($meetin_emp_id, 'jobsearch_zoom_refresh_token', true);
        if ($zoom_meetins_switch == 'on' && $zoom_refresh_token != '' && $meet_zoom_id != '') {
            global $JobSearch_Sched_ZoomMeets;
            $emp_zoom_email = get_post_meta($meetin_emp_id, 'jobsearch_zoom_user_email_address', true);
            $emp_user_id = jobsearch_get_employer_user_id($meetin_emp_id);
            $access_token = $JobSearch_Sched_ZoomMeets->user_zoom_access_token($emp_user_id);
            
            $data = array(
                'schedule_for' => $emp_zoom_email,
                'topic' => sprintf(esc_html__('Reschedule Interview meeting for job - %s', 'jobsearch-shmeets'), get_the_title($meet_job_id)),
                'start_time' => date('Y-m-d', $meet_exct_stime) . 'T' . date('H:i:s', $meet_exct_stime),
                'timezone' => wp_timezone_string(),
                'agenda' => $meet_note,
            );
            $data_str = json_encode($data);

            $url = 'https://api.zoom.us/v2/meetings/' . $meet_zoom_id;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            // make sure we are POSTing
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
            // allow us to use the returned data from the request
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //we are sending json
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token,
            ));

            $result = curl_exec($ch);
            curl_close($ch);
        }
        
        $meeting_resched_by = get_post_meta($meet_post_id, 'meeting_resched_by', true);
        $meeting_resched_by = !empty($meeting_resched_by) ? $meeting_resched_by : array();
        if ($is_employer) {
            $meeting_resched_by[] = array(
                'emp_id' => $meetin_emp_id,
                'time' => $current_time,
                'meet_note' => $meet_note,
            );
        } else {
            $meeting_resched_by[] = array(
                'cand_id' => $meet_cand_id,
                'time' => $current_time,
                'meet_note' => $meet_note,
            );
        }
        update_post_meta($meet_post_id, 'meeting_resched_by', $meeting_resched_by);
        
        update_post_meta($meet_post_id, 'meeting_date', $meet_date);
        update_post_meta($meet_post_id, 'meeting_duration', $meet_duration);
        update_post_meta($meet_post_id, 'meeting_time', $meet_time);
        update_post_meta($meet_post_id, 'meet_start_time', $meet_exct_stime);
        update_post_meta($meet_post_id, 'meet_end_time', $meet_exct_entime);
        update_post_meta($meet_post_id, 'meeting_candidate', $meet_cand_id);
        update_post_meta($meet_post_id, 'meeting_employer', $meetin_emp_id);
        update_post_meta($meet_post_id, 'meeting_job', $meet_job_id);
        
        // remove cancel if canceled by candidate
        update_post_meta($meet_post_id, 'meeting_canceled', '');
        
        if ($is_employer) {
            do_action('jobsearch_meeting_email_to_candidate', $cand_user_obj, $meet_post_id, $meet_job_id);
            self::notify_cand_for_meeting($meet_post_id, $meet_job_id, $meet_cand_id);
        } else {
            do_action('jobsearch_meeting_reschedule_email_to_employer', $cand_user_obj, $meet_post_id, $meet_job_id);
        }
        
        $json_data = array('error' => '0', 'msg' => '<div class="alert alert-success">' . esc_html__('Meeting Re-Scheduled successfully.', 'jobsearch-shmeets') . '</div>');
        wp_send_json($json_data);
    }
    
    public function cancel_scheduled_meeting_bycand() {
        $meeting_id = $_POST['meet_id'];
        
        if ($meeting_id > 0) {
            
            update_post_meta($meeting_id, 'meeting_canceled', 'yes');
            
            $meet_cand_id = get_post_meta($meeting_id, 'meeting_candidate', true);
            $meet_job_id = get_post_meta($meeting_id, 'meeting_job', true);
            
            $cand_user_id = jobsearch_get_candidate_user_id($meet_cand_id);
            $cand_user_obj = get_user_by('ID', $cand_user_id);
            
            do_action('jobsearch_meeting_cancel_email_to_employer', $cand_user_obj, $meeting_id, $meet_job_id);
        }
        $json_data = array('text' => esc_html__('Canceled', 'jobsearch-shmeets'));
        wp_send_json($json_data);
    }
    
    public function delete_scheduled_meeting_byemp() {
        $meeting_id = $_POST['meet_id'];
        
        if ($meeting_id > 0) {
            
            wp_delete_post($meeting_id, true);
        }
        $json_data = array('text' => esc_html__('Deleted', 'jobsearch-shmeets'));
        wp_send_json($json_data);
    }
    
    public function candash_notific_settins_btn($candidate_id) {
        $notifics_me_meetforjob = get_post_meta($candidate_id, 'jobsearch_field_notific_meetforjob', true);
        ?>
        <li class="jobsearch-column-6">
            <?php
            Jobsearch_Dashboard_Notifications::seting_onoff_btn('opt-notific-meetforjob', 'notific_meetforjob', $notifics_me_meetforjob, esc_html__('Notify me for Job Interview Meeting', 'jobsearch-shmeets'));
            ?>
        </li>
        <?php
    }
    
    public static function notify_cand_for_meeting($meet_post_id, $job_id, $candidate_id) {
        global $jobsearch_plugin_options;

        $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';

        $add_notifics_for_cands = isset($jobsearch_plugin_options['add_notifics_for_cands']) ? $jobsearch_plugin_options['add_notifics_for_cands'] : '';

        $employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        if ($dash_notifics_switch == 'on' && $add_notifics_for_cands == 'on') {
            
            $notifics_me_meetforjob = get_post_meta($candidate_id, 'jobsearch_field_notific_meetforjob', true);
            if ($notifics_me_meetforjob == 'yes') {
                $notifics_list = get_post_meta($candidate_id, 'jobsearch_cand_notifics_list', true);;
                $notifics_list = !empty($notifics_list) ? $notifics_list : array();
                //
                $notific_data = array(
                    'unique_id' => uniqid(),
                    'type' => 'meeting_for_intrview',
                    'time' => current_time('timestamp'),
                    'job_id' => $job_id,
                    'employer_id' => $employer_id,
                    'viewed' => 0,
                );
                $notifics_list[] = $notific_data;
                update_post_meta($candidate_id, 'jobsearch_cand_notifics_list', $notifics_list);
            }
        }
        //
    }
    
    public function user_notifics_list_item_html($html, $notific_item) {
        
        $notific_unique_id = isset($notific_item['unique_id']) ? $notific_item['unique_id'] : '';
        $notific_type = isset($notific_item['type']) ? $notific_item['type'] : '';
        $notific_time = isset($notific_item['time']) ? $notific_item['time'] : '';
        $notific_job_id = isset($notific_item['job_id']) ? $notific_item['job_id'] : '';
        $notific_employer_id = isset($notific_item['employer_id']) ? $notific_item['employer_id'] : '';
        $notific_cand_id = isset($notific_item['candidate_id']) ? $notific_item['candidate_id'] : '';
        $notific_viewed = isset($notific_item['viewed']) ? $notific_item['viewed'] : '';

        if ($notific_type == 'meeting_for_intrview') {
            $anchor_allowed_tags = array(
                'a' => array(
                    'href' => array(),
                    'title' => array(),
                )
            );
            $html = sprintf(wp_kses(__('You are selected for meeting for the job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\' you applied.', 'jobsearch-shmeets'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
        }
        
        return $html;
    }
    
    public function meetin_avail_time_slots($meet_date, $time_duration, $emp_id = '') {
        global $sitepress;
        if ($emp_id > 0) {
            $employer_id = $emp_id;
        } else {
            $user_id = get_current_user_id();
            $employer_id = jobsearch_get_user_employer_id($user_id);
        }
        $current_date = strtotime(current_time('d-m-Y'));
        $current_time = current_time('timestamp');
        
        $is_current_date = true;
        if ($meet_date != '' && strtotime($meet_date) > $current_date) {
            $is_current_date = false;
        }
        
        if ($time_duration > 0 && $time_duration <= 120) {
            $time_duration = $time_duration;
        } else {
            $time_duration = 30;
        }
        
        $meet_day = strtolower(date('l', strtotime($meet_date)));
        
        $avail_days = get_post_meta($employer_id, 'employer_meetin_availble_days', true);
        $time_from = get_post_meta($employer_id, $meet_day . 'meetin_time_from', true);
        $time_to = get_post_meta($employer_id, $meet_day . 'meetin_time_to', true);
        
        $time_from_timstmp = strtotime('00:00');
        $time_to_timstmp = strtotime('24:00');
        
        if ($time_from != '' && $time_to != '' && absint(strtotime($time_to)) > absint(strtotime($time_from))) {
        
            $time_from_timstmp = strtotime($time_from);
            $time_to_timstmp = strtotime($time_to);
        }
        
        $duration_in_secs = $time_duration * 60;
        
        ob_start();
        for ($xtime = $time_from_timstmp; $xtime < $time_to_timstmp; $xtime+=$duration_in_secs) {
            $toxtime = $xtime + $duration_in_secs;
            if ($toxtime > $time_to_timstmp) {
                $toxtime = $time_to_timstmp;
            }
            $avail_time_slot = true;
            if ($is_current_date && $current_time > $xtime) {
                $avail_time_slot = false;
            }
            
            // For already meetings same time slots check
            $slot_start_time = strtotime($meet_date . ' ' . date('H:i', $xtime));
            $slot_end_time = strtotime($meet_date . ' ' . date('H:i', $toxtime));
            
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $sitepress_def_lang = $sitepress->get_default_language();
                $sitepress_curr_lang = $sitepress->get_current_language();
                $sitepress->switch_lang($sitepress_def_lang, true);
            }
            $meets_args = array(
                'posts_per_page' => '1',
                'post_type' => 'jobsearch_meets',
                'post_status' => 'publish',
                'fields' => 'ids', // only load ids
                'meta_query' => array(
                    array(
                        'key' => 'meeting_employer',
                        'value' => $employer_id,
                        'compare' => '=',
                    ),
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'meet_start_time',
                            'value' => array($slot_start_time, $slot_end_time),
                            'type' => 'numeric',
                            'compare' => 'BETWEEN',
                        ),
                        array(
                            'key' => 'meet_end_time',
                            'value' => array($slot_start_time, $slot_end_time),
                            'type' => 'numeric',
                            'compare' => 'BETWEEN',
                        ),
                    ),
                ),
            );
            $meets_query = new WP_Query($meets_args);
            $found_meets = $meets_query->found_posts;
            
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $sitepress->switch_lang($sitepress_curr_lang, true);
            }
            
            if ($found_meets > 0) {
                $avail_time_slot = false;
            }
            //
            if ($avail_time_slot) {
                ?>
                <option value="<?php echo date('H:i', $xtime) . '-' . date('H:i', $toxtime) ?>"><?php echo date_i18n(get_option('time_format'), $xtime) . ' - ' . date_i18n(get_option('time_format'), $toxtime) ?></option>
                <?php
            }
        }
        
        $html = ob_get_clean();
        
        return $html;
    }
    
    public function set_scheduled_meeting_time_slots() {
        global $JobSearch_Sched_ZoomMeets;
        $current_date = strtotime(current_time('d-m-Y'));
        $emp_id = $_POST['emp_id'];
        $meet_date = $_POST['meet_date'];
        $meet_duration = $_POST['meet_duration'];
        
        $emp_user_id = jobsearch_get_employer_user_id($emp_id);
        $JobSearch_Sched_ZoomMeets->reset_zoom_access_token_byid($emp_user_id);
        
        $slots_html = $this->meetin_avail_time_slots($meet_date, $meet_duration, $emp_id);
        ob_start();
        if ($slots_html != '') {
            ?>
            <select name="meeting_time" class="meetslots-selectize">
                <option value=""><?php esc_html_e('Timing', 'jobsearch-shmeets') ?></option>
                <?php echo ($slots_html) ?>
            </select>
            <script>
                jQuery('.meetslots-selectize').selectize();
            </script>
            <?php
        }
        $slots_select_html = ob_get_clean();
        
        $msg = '';
        if ($slots_select_html == '') {
            $msg = esc_html__('No timing is available for this day. Please choose another day.', 'jobsearch-shmeets');
        }
        
        wp_send_json(array('slots' => $slots_select_html, 'msg' => $msg));
    }
    
    public function create_scheduled_meeting_time_slots() {
        
        $emp_id = $_POST['emp'];
        
        $current_date = strtotime(current_time('d-m-Y'));
        
        $emp_user_id = jobsearch_get_employer_user_id($emp_id);
        $JobSearch_Sched_ZoomMeets->reset_zoom_access_token_byid($emp_user_id);
        
        $slots_html = $this->meetin_avail_time_slots($current_date, 30, $emp_id);
        ob_start();
        if ($slots_html != '') {
            ?>
            <select name="meeting_time" class="meetslots-selectize">
                <option value=""><?php esc_html_e('Timing', 'jobsearch-shmeets') ?></option>
                <?php echo ($slots_html) ?>
            </select>
            <script>
                jQuery('.meetslots-selectize').selectize();
            </script>
            <?php
        }
        $slots_select_html = ob_get_clean();
        
        $msg = '';
        if ($slots_select_html == '') {
            $msg = esc_html__('No timing is available for this day. Please choose another day.', 'jobsearch-shmeets');
        }
        
        wp_send_json(array('slots' => $slots_select_html, 'msg' => $msg));
    }
    
    public function meeting_calendar_days_off_employer() {
        global $jobsearch_plugin_options;
        $my_meetings_switch = isset($jobsearch_plugin_options['dashmenu_meetings_switch']) ? $jobsearch_plugin_options['dashmenu_meetings_switch'] : '';
        
        $user_id = get_current_user_id();
        $is_employer = jobsearch_user_is_employer($user_id);
        $get_tab = isset($_GET['tab']) ? $_GET['tab'] : '';
        if ($my_meetings_switch == 'on' && $is_employer && ($get_tab == 'manage-jobs' || $get_tab == 'all-applicants' || $get_tab == 'meetings')) {
            $style_str = '';
            $employer_id = jobsearch_get_user_employer_id($user_id);
            $week_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
            $avail_days = get_post_meta($employer_id, 'employer_meetin_availble_days', true);
            
            if (is_array($avail_days)) {
                $off_days = array_diff($week_days, $avail_days);
                if (!empty($off_days)) {
                    foreach ($off_days as $off_day) {
                        $style_str .= '.picker-day.' . $off_day . '{color:#ababab;cursor:not-allowed !important;pointer-events:none;}';
                    }
                }
            }
            if ($style_str != '') {
                ?>
                <style><?php echo ($style_str) ?></style>
                <?php
            }
        }
    }

}

return new JobSearch_Sched_Meetings_Functions();
