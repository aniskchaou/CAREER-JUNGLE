<?php
// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// functions class
class JobSearch_Sched_Meets_Dashtab_Content {

    public function __construct() {

        add_action('jobsearch_shmeets_cand_dash_tab_lists', array($this, 'cand_dash_tab_lists'));
        add_action('jobsearch_shmeets_emp_dash_tab_lists', array($this, 'emp_dash_tab_lists'));
        
        add_action('wp_ajax_jobsearch_dash_meetin_settins_call', array($this, 'dash_meetin_settins_save'));
    }

    public static function week_days() {
        $week_days = array(
            'monday' => esc_html__('Monday', 'jobsearch-shmeets'),
            'tuesday' => esc_html__('Tuesday', 'jobsearch-shmeets'),
            'wednesday' => esc_html__('Wednesday', 'jobsearch-shmeets'),
            'thursday' => esc_html__('Thursday', 'jobsearch-shmeets'),
            'friday' => esc_html__('Friday', 'jobsearch-shmeets'),
            'saturday' => esc_html__('Saturday', 'jobsearch-shmeets'),
            'sunday' => esc_html__('Sunday', 'jobsearch-shmeets'),
        );
        return $week_days;
    }

    public function cand_dash_tab_lists() {
        global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings, $sitepress;
        
        $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
        $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');
        
        wp_enqueue_script('jobsearch-shmeets-scripts');
        
        $week_days = self::week_days();
        
        $current_date = strtotime(current_time('d-m-Y'));
        
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $sitepress_def_lang = $sitepress->get_default_language();
            $sitepress_curr_lang = $sitepress->get_current_language();
            $sitepress->switch_lang($sitepress_def_lang, true);
        }
        
        $user_id = get_current_user_id();
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
        
        $saved_meets_view = get_post_meta($candidate_id, 'jobsearch_user_meetings_view', true);
        
        $meetings_view = 'list';
        if (isset($_GET['meetings_view']) && $_GET['meetings_view'] != '') {
            $meetings_view = $_GET['meetings_view'];
        } else if ($saved_meets_view != '') {
            $meetings_view = $saved_meets_view;
        }
        
        $reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;
        if ($meetings_view == 'calendar') {
            $reults_per_page = '-1';
        }
        
        $page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
        $args = array(
            'post_type' => 'jobsearch_meets',
            'posts_per_page' => $reults_per_page,
            'paged' => $page_num,
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_key' => 'meet_start_time',
            'order' => 'ASC',
            'orderby' => 'meta_value_num',
            'meta_query' => array(
                array(
                    'key' => 'meeting_candidate',
                    'value' => $candidate_id,
                    'compare' => '=',
                ),
            ),
        );
        $meetins_query = new WP_Query($args);
        $meetins_posts = $meetins_query->posts;
        $total_meets = $meetins_query->found_posts;
        $total_page_meets = $meetins_query->post_count;
        
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $sitepress->switch_lang($sitepress_curr_lang, true);
        }

        if (!empty($meetins_posts)) {
//            foreach ($meetins_posts as $meetinn_id) {
//                $meet_date_tochk = get_post_meta($meetinn_id, 'meeting_date', true);
//                $meet_exct_stime = get_post_meta($meetinn_id, 'meet_start_time', true);
//                echo date('d M, Y', $meet_date_tochk) . '<br>';
//                echo date('d M, Y H:i', $meet_exct_stime) . '<br>';
//            }
        ?>
        <div class="meetings-list-con">
            <?php
            if ($meetings_view == 'calendar') {
                    wp_enqueue_script('shmeets-fullcalendar');
                    wp_enqueue_script('shmeets-addevent');
                    ?>
                    <div id="meetings-fulcalendar"></div>
                    <?php
                    $meets_footr_args = array(
                        'current_date' => $current_date,
                    );
                    add_action('wp_footer', function() use ($meetins_posts, $meets_footr_args) {
                        $current_date = isset($meets_footr_args['current_date']) ? $meets_footr_args['current_date'] : '';
                        foreach ($meetins_posts as $meet_post_id) {
                            $meet_date = get_post_meta($meet_post_id, 'meeting_date', true);
                            $meet_duration = get_post_meta($meet_post_id, 'meeting_duration', true);
                            $meet_time = get_post_meta($meet_post_id, 'meeting_time', true);
                            $meet_exct_stime = get_post_meta($meet_post_id, 'meet_start_time', true);
                            $meet_exct_entime = get_post_meta($meet_post_id, 'meet_end_time', true);
                            $meet_note = get_post_meta($meet_post_id, 'meeting_note', true);
                            $meeting_resched_by = get_post_meta($meet_post_id, 'meeting_resched_by', true);
                            $meetin_platform = get_post_meta($meet_post_id, 'meeting_platform', true);
                            $meet_cand_id = get_post_meta($meet_post_id, 'meeting_candidate', true);
                            $meetin_emp_id = get_post_meta($meet_post_id, 'meeting_employer', true);
                            $meet_job_id = get_post_meta($meet_post_id, 'meeting_job', true);
                            
                            $meeting_is_cancel = get_post_meta($meet_post_id, 'meeting_canceled', true);

                            $emp_user_id = jobsearch_get_employer_user_id($meetin_emp_id);
                            $emp_user_obj = get_user_by('id', $emp_user_id);

                            $meet_month = date_i18n('M', $meet_date);
                            $meet_ddate = date_i18n('d', $meet_date);
                            $meet_day = $meet_date > $current_date ? date_i18n('l', $meet_date) : esc_html__('Today', 'jobsearch-shmeets');

                            $post_thumbnail_id = jobsearch_job_get_profile_image($meet_job_id);
                            $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, apply_filters('jobsearch_jobs_actlist_thmb_size', 'thumbnail'));
                            $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                            $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;

                            $company_name = jobsearch_job_get_company_name($meet_job_id, '@ ');
                            $job_city_title = jobsearch_post_city_contry_txtstr($meet_job_id, true, false, true);
                            $job_type_str = jobsearch_job_get_all_jobtypes($meet_job_id, 'jobsearch-option-btn');

                            $meet_pltform_str = esc_html__('On-Board', 'jobsearch-shmeets');
                            $meet_pltform_icon = 'fa fa-file-text-o';
                            $meet_pltform_link = 'javascript:void(0);';
                            if ($meetin_platform == 'zoom') {
                                $meet_pltform_icon = 'fa fa-video-camera';
                                $meet_pltform_str = esc_html__('On Zoom', 'jobsearch-shmeets');
                                $meet_pltform_link = get_post_meta($meet_post_id, 'zoom_meeting_url', true);
                            }
                            $time_duration_inmin = 1;
                            if ($meet_exct_entime > 0 && $meet_exct_entime > $meet_exct_stime) {
                                $meet_times_diff = $meet_exct_entime - $meet_exct_stime;
                                if ($meet_times_diff  > 60) {
                                    $time_duration_inmin = $meet_times_diff/60;
                                }
                            }
                            $time_duration_str = sprintf(esc_html__('%s min', 'jobsearch-shmeets'), $time_duration_inmin);
                            ?>
                            <div id="calendarPopover<?php echo ($meet_post_id) ?>" class="calendar-eventpopvr-mainbox" style="display: none;">
                                <div class="calendar-eventpopvr-inner">
                                    <a href="javascript:void(0);" class="close-calnder-evntpop"><i class="fa fa-times"></i></a>
                                    <div class="meet-txtimg-con">
                                        <div class="meet-job-imgcon">
                                            <a href="<?php echo esc_url(get_permalink($meet_job_id)) ?>" target="_blank"><img src="<?php echo esc_url($post_thumbnail_src) ?>" alt=""></a>
                                        </div>
                                        <div class="meet-job-detinfcon">
                                            <h2><a href="<?php echo esc_url(get_permalink($meet_job_id)) ?>" target="_blank"><?php echo wp_trim_words(get_the_title($meet_job_id), 4) ?></a><?php echo ($meeting_is_cancel == 'yes' ? ' <small style="color: #ff0000;">(Canceled)</small>' : '') ?></h2>
                                            <?php
                                            if ($company_name != '') {
                                                echo '<span class="job-company">' . ($company_name) . '</span>';
                                            }
                                            if ($job_city_title != '') {
                                                echo '<span class="job-location"><i class="jobsearch-icon jobsearch-maps-and-flags"></i> ' . ($job_city_title) . '</span>';
                                            }
                                            if ($job_type_str != '') {
                                                echo ($job_type_str);
                                            }
                                            ?>
                                            <div class="meting-typdur-con">
                                                <a href="<?php echo ($meet_pltform_link) ?>" class="interview-schedule-zoom"><i class="<?php echo ($meet_pltform_icon) ?>"></i> <?php echo ($meet_pltform_str) ?></a>
                                                <span class="meetin-duration"><?php echo ($time_duration_str) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="interview-schedule-atcon">
                                        <i class="fa fa-clock-o"></i> <?php esc_html_e('Interview schedule @', 'jobsearch-shmeets') ?> <span><?php echo date_i18n(get_option('time_format'), $meet_exct_stime) ?> - <?php echo date_i18n(get_option('time_format'), $meet_exct_entime) ?></span>
                                    </div>
                                    <?php
                                    $meet_note_txt = '';
                                    if ($meet_note != '') {
                                        $meet_note_txt = $meet_note;
                                    } else if (!empty($meeting_resched_by)) {
                                        foreach ($meeting_resched_by as $meet_resched_itm) {
                                            if (isset($meet_resched_itm['meet_note']) && $meet_resched_itm['meet_note'] != '') {
                                                $meet_note_txt = $meet_resched_itm['meet_note'];
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="meetin-belowsec-con">
                                        <?php
                                        if ($meet_note_txt != '') {
                                            ?>
                                            <div class="meting-note-con">
                                                <div class="meetnote-icon-con"><i class="fa fa-file-text-o"></i> <?php esc_html_e('Note', 'jobsearch-shmeets') ?></div>
                                                <div class="meetnote-text-con"><?php echo ($meet_note_txt) ?> <a href="javascript:void(0);" class="meetin-notespop-btn" data-id="<?php echo ($meet_post_id) ?>"><?php esc_html_e('More detail', 'jobsearch-shmeets') ?></a></div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <div class="meetin-belowbtns-con">
                                            <div class="meetin-reschedbtn-con">
                                                <div class="meetin-belowbtn-iner">
                                                    <a href="javascript:void(0);" class="jobsearch-meet-reschedulepop" data-id="<?php echo ($meet_job_id) ?>" data-cd="<?php echo ($meet_cand_id) ?>" data-md="<?php echo ($meet_post_id) ?>"><i class="fa fa-calendar"></i> <?php esc_html_e('Re-schedule Meeting', 'jobsearch-shmeets') ?></a>
                                                </div>
                                            </div>
                                            <div class="meetin-addclendrbtn-con">
                                                <div class="meetin-belowbtn-iner">
                                                    <a href="javascript:void(0);" class="jobsearch-meet-addclendrbtn addeventatc">
                                                        <i class="fa fa-calendar"></i> <?php esc_html_e('Add to Calendar', 'jobsearch-shmeets') ?>
                                                        <span class="start"><?php echo date('m/d/Y h:i A', $meet_exct_stime) ?></span>
                                                        <span class="end"><?php echo date('m/d/Y h:i A', $meet_exct_entime) ?></span>
                                                        <span class="timezone"><?php echo wp_timezone_string() ?></span>
                                                        <span class="title"><?php printf(esc_html__('Interview for job "%s"', 'jobsearch-shmeets'), get_the_title($meet_job_id)) ?></span>
                                                        <span class="description"><?php echo ($meet_note_txt) ?></span>
                                                        <span class="location"><?php echo ($job_city_title) ?></span>
                                                        <span class="organizer"><?php echo get_the_title($meetin_emp_id) ?></span>
                                                        <span class="organizer_email"><?php echo ($emp_user_obj->user_email) ?></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $meet_notes_arr = array();
                            if ($meet_note != '') {
                                $meet_note_txt = $meet_note;
                                $meet_notes_arr[] = array(
                                    'meet_note' => $meet_note,
                                    'emp_id' => $meetin_emp_id,
                                    'meet_post_id' => $meet_post_id,
                                );
                            }
                            if (!empty($meeting_resched_by)) {
                                foreach ($meeting_resched_by as $meet_resched_itm) {
                                    if (isset($meet_resched_itm['meet_note']) && $meet_resched_itm['meet_note'] != '') {
                                        $meet_notes_arr[] = $meet_resched_itm;
                                    }
                                }
                            }
                            if (!empty($meet_notes_arr)) {
                                $this->meetin_notes_html($meet_post_id, $meet_notes_arr);
                            }
                        }
                        ?>
                        <script type="text/javascript">
                            window.addeventasync = function() {
                                addeventatc.settings({
                                    css: false,
                                    appleical  : {show:true, text:"<?php _e('Apple Calendar', 'jobsearch-shmeets') ?>"},
                                    google     : {show:true, text:"<?php _e('Google <em>(online)</em>', 'jobsearch-shmeets') ?>"},
                                    office365  : {show:true, text:"<?php _e('Office 365 <em>(online)</em>', 'jobsearch-shmeets') ?>"},
                                    outlook    : {show:true, text:"<?php _e('Outlook', 'jobsearch-shmeets') ?>"},
                                    outlookcom : {show:true, text:"<?php _e('Outlook.com <em>(online)</em>', 'jobsearch-shmeets') ?>"},
                                    yahoo      : {show:true, text:"<?php _e('Yahoo <em>(online)</em>', 'jobsearch-shmeets') ?>"}
                                });
                            };
                            document.addEventListener('DOMContentLoaded', function() {
                                var meetings_calendar = document.getElementById('meetings-fulcalendar');

                                var calendar = new FullCalendar.Calendar(meetings_calendar, {
                                    headerToolbar: {
                                        left: 'title',
                                        center: '',
                                        right: 'prev,next'
                                    },
                                    initialDate: '<?php echo date('Y-m-d') ?>',
                                    navLinks: true, // can click day/week names to navigate views
                                    businessHours: true, // display business hours
                                    editable: false,
                                    selectable: true,
                                    events: [
                                        <?php
                                        foreach ($meetins_posts as $meet_post_id) {
                                            $meet_date = get_post_meta($meet_post_id, 'meeting_date', true);
                                            $meet_duration = get_post_meta($meet_post_id, 'meeting_duration', true);
                                            $meet_time = get_post_meta($meet_post_id, 'meeting_time', true);
                                            $meet_exct_stime = get_post_meta($meet_post_id, 'meet_start_time', true);
                                            $meet_exct_entime = get_post_meta($meet_post_id, 'meet_end_time', true);
                                            ?>
                                            {
                                                type: <?php echo ($meet_post_id) ?>,
                                                title: '<?php echo wp_trim_words(get_the_title($meet_job_id), 4) ?>',
                                                start: '<?php echo date('Y-m-d', $meet_exct_stime) ?>T<?php echo date('H:i:s', $meet_exct_stime) ?>',
                                                end: '<?php echo date('Y-m-d', $meet_exct_entime) ?>T<?php echo date('H:i:s', $meet_exct_entime) ?>',
                                            },
                                            <?php
                                        }
                                        ?>
                                    ],
                                    eventClick: function (event) {
                                        var meeting_id = event.event.extendedProps.type;
                                        var jsEvent = event.jsEvent;
                                        jQuery('.calendar-eventpopvr-mainbox').hide();
                                        var target_popver = jQuery('#calendarPopover' + meeting_id);
                                        var target_popver_height = target_popver.height();
                                        target_popver.removeAttr('style');

                                        var actual_y_cords = jsEvent.pageY - 5;
                                        if (actual_y_cords > target_popver_height) {
                                            actual_y_cords = actual_y_cords - target_popver_height;
                                        }

                                        var top_pos = actual_y_cords;
                                        var left_pos = jsEvent.pageX - 50;
                                        target_popver.css({left: left_pos,top: top_pos});
                                    },
                                });
                                calendar.render();
                            });
                            jQuery(document).on('click', '.close-calnder-evntpop', function() {
                                jQuery(this).parents('.calendar-eventpopvr-mainbox').hide();
                            });
                        </script>
                        <?php
                    }, 30, 2);
                } else {
                    ?>
                    <div class="jobsearch-interview-list-container">
                        <?php
                        $very_first_meet = true;
                        $meets_countr = 1;
                        foreach ($meetins_posts as $meet_post_id) {
                            $meet_date = get_post_meta($meet_post_id, 'meeting_date', true);
                            $meet_duration = get_post_meta($meet_post_id, 'meeting_duration', true);
                            $meet_time = get_post_meta($meet_post_id, 'meeting_time', true);
                            $meet_exct_stime = get_post_meta($meet_post_id, 'meet_start_time', true);
                            $meet_exct_entime = get_post_meta($meet_post_id, 'meet_end_time', true);
                            $meeting_resched_by = get_post_meta($meet_post_id, 'meeting_resched_by', true);
                            $meet_note = get_post_meta($meet_post_id, 'meeting_note', true);
                            $meetin_platform = get_post_meta($meet_post_id, 'meeting_platform', true);
                            $meet_cand_id = get_post_meta($meet_post_id, 'meeting_candidate', true);
                            $meetin_emp_id = get_post_meta($meet_post_id, 'meeting_employer', true);
                            $meet_job_id = get_post_meta($meet_post_id, 'meeting_job', true);
                            
                            $meeting_is_cancel = get_post_meta($meet_post_id, 'meeting_canceled', true);

                            $meet_month = date_i18n('M', $meet_date);
                            $meet_ddate = date_i18n('d', $meet_date);
                            $meet_day = $meet_date > $current_date ? date_i18n('l', $meet_date) : esc_html__('Today', 'jobsearch-shmeets');
                            if ($very_first_meet) {
                                $left_meet_date = $meet_date;
                                ob_start();
                                ?>
                                <div class="jobsearch-interview-list-container-left">
                                    <div class="jobsearch-interview-list-container-left-inner"><div class="jobsearch-interview-list-container-left-text"><span><?php echo ($meet_month) ?></span> <strong><?php echo ($meet_ddate) ?></strong> <span><?php echo ($meet_day) ?></span></div></div>
                                </div>
                                <?php
                                echo '<div class="jobsearch-interview-list-container-right">' . "\n";
                                $left_date_html = ob_get_clean();
                                $right_content_html = '';
                            }
                            if ($meet_date > $left_meet_date) {
                                $left_meet_date = $meet_date;
                                $end_tags = '</div>' . "\n";
                                $end_tags .= '</div>' . "\n";
                                //
                                echo ($left_date_html);
                                echo ($right_content_html);
                                echo ($end_tags);
                                //
                                ob_start();
                                echo '<div class="jobsearch-interview-list-container">' . "\n";
                                ?>
                                <div class="jobsearch-interview-list-container-left">
                                    <div class="jobsearch-interview-list-container-left-inner"><div class="jobsearch-interview-list-container-left-text"><span><?php echo ($meet_month) ?></span> <strong><?php echo ($meet_ddate) ?></strong> <span><?php echo ($meet_day) ?></span></div></div>
                                </div>
                                <?php
                                echo '<div class="jobsearch-interview-list-container-right">' . "\n";
                                $left_date_html = ob_get_clean();
                                $right_content_html = '';
                            }
                            ob_start();
                            $start_time_str = date_i18n(get_option('time_format'), $meet_exct_stime);
                            $end_time_str = date_i18n(get_option('time_format'), $meet_exct_entime);

                            $time_duration_inmin = 1;
                            if ($meet_exct_entime > 0 && $meet_exct_entime > $meet_exct_stime) {
                                $meet_times_diff = $meet_exct_entime - $meet_exct_stime;
                                if ($meet_times_diff  > 60) {
                                    $time_duration_inmin = $meet_times_diff/60;
                                }
                            }
                            $time_duration_str = sprintf(esc_html__('%s min', 'jobsearch-shmeets'), $time_duration_inmin);

                            $post_thumbnail_id = jobsearch_employer_get_profile_image($meetin_emp_id);
                            $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, apply_filters('jobsearch_emps_actlist_thmb_size', 'thumbnail'));
                            $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                            $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_employer_image_placeholder() : $post_thumbnail_src;
                            $meetin_with_str = sprintf(__('Meeting with&nbsp;<a href="%s">%s</a>', 'jobsearch-shmeets'), get_permalink($meetin_emp_id), get_the_title($meetin_emp_id));

                            $meet_pltform_str = esc_html__('On-Board', 'jobsearch-shmeets');
                            $meet_pltform_icon = 'fa fa-file-text-o';
                            $meet_pltform_link = 'javascript:void(0);';
                            if ($meetin_platform == 'zoom') {
                                $meet_pltform_icon = 'fa fa-video-camera';
                                $meet_pltform_str = esc_html__('On Zoom', 'jobsearch-shmeets');
                                $meet_pltform_link = get_post_meta($meet_post_id, 'zoom_meeting_url', true);;
                            }

                            $meet_notes_arr = array();
                            if ($meet_note != '') {
                                $meet_note_txt = $meet_note;
                                $meet_notes_arr[] = array(
                                    'meet_note' => $meet_note,
                                    'emp_id' => $meetin_emp_id,
                                    'meet_post_id' => $meet_post_id,
                                );
                            } else if (!empty($meeting_resched_by)) {
                                foreach ($meeting_resched_by as $meet_resched_itm) {
                                    if (isset($meet_resched_itm['meet_note']) && $meet_resched_itm['meet_note'] != '') {
                                        $meet_notes_arr[] = $meet_resched_itm;
                                    }
                                }
                            }
                            
                            $meet_notes_arr = array();
                            if ($meet_note != '') {
                                $meet_note_txt = $meet_note;
                                $meet_notes_arr[] = array(
                                    'meet_note' => $meet_note,
                                    'emp_id' => $meetin_emp_id,
                                    'meet_post_id' => $meet_post_id,
                                );
                            }
                            if (!empty($meeting_resched_by)) {
                                foreach ($meeting_resched_by as $meet_resched_itm) {
                                    if (isset($meet_resched_itm['meet_note']) && $meet_resched_itm['meet_note'] != '') {
                                        $meet_notes_arr[] = $meet_resched_itm;
                                    }
                                }
                            }
                            
                            if (!empty($meet_notes_arr)) {
                                add_action('wp_footer', function() use ($meet_notes_arr, $meet_post_id) {
                                    //
                                    $this->meetin_notes_html($meet_post_id, $meet_notes_arr);
                                }, 30, 2);
                            }
                            ?>
                            <div class="jobsearch-interview-list-container-right-inner<?php echo ($very_first_meet ? ' first' : '') ?>">
                                <div class="jobsearch-interview-schedule-columnk jobsearch-interview-first"> 
                                    <div class="schedule-maincol-inner-wrap">
                                        <h2><a href="<?php echo get_permalink($meet_job_id) ?>"><?php echo get_the_title($meet_job_id) ?></a><?php echo ($meeting_is_cancel == 'yes' ? ' <small style="color: #ff0000;">(Canceled)</small>' : '') ?>  <span> <img src="<?php echo ($post_thumbnail_src) ?>" alt=""> <?php echo ($meetin_with_str) ?></span></h2>
                                    </div> 
                                </div>
                                <div class="jobsearch-interview-schedule-columnk"> 
                                    <div class="schedule-maincol-inner-wrap metting-wrap">
                                        <a href="<?php echo ($meet_pltform_link) ?>" class="jobsearch-interview-schedule-zoom"><i class="<?php echo ($meet_pltform_icon); ?>"></i> <?php echo ($meet_pltform_str) ?></a>
                                    </div> 
                                </div>
                                <div class="jobsearch-interview-schedule-columnk jobsearch-interview-three"> <div class="schedule-maincol-inner-wrap"><div class="jobsearch-interview-schedule-date"><i class="fa fa-clock-o"></i> <?php echo ($start_time_str) ?> - <?php echo ($end_time_str) ?></div></div> </div>
                                <div class="jobsearch-interview-schedule-columnk jobsearch-interview-four"> <div class="schedule-maincol-inner-wrap"><div class="jobsearch-interview-schedule-time"><i class="fa fa-hourglass-2"></i> <?php echo ($time_duration_str) ?></div></div> </div>
                                <div class="jobsearch-interview-schedule-columnk jobsearch-interview-last"> 
                                    <div class="schedule-maincol-inner-wrap"> 
                                        <div class="jobsearch-interview-schedule-action">
                                            <a href="javascript:void(0);" class="meetin-notespop-btn" data-id="<?php echo ($meet_post_id) ?>"><span><i class="fa fa-file-text-o"></i> <?php echo (!empty($meet_notes_arr) ? count($meet_notes_arr) : 0) ?></span></a> 
                                            <div class="candidate-more-acts-con">
                                                <a href="javascript:void(0);" class="more-actions"><?php esc_html_e('Actions', 'jobsearch-shmeets') ?> <i class="fa fa-angle-down"></i></a>
                                                <ul style="display: none;">
                                                    <li><a href="javascript:void(0);" class="jobsearch-meet-reschedulepop" data-id="<?php echo ($meet_job_id) ?>" data-cd="<?php echo ($meetin_emp_id) ?>" data-md="<?php echo ($meet_post_id) ?>"><?php esc_html_e('Re-schedule Meeting', 'jobsearch-shmeets') ?></a></li>
                                                    <?php
                                                    if ($meeting_is_cancel == 'yes') {
                                                        ?>
                                                        <li><a href="javascript:void(0);" class="jobsearch-meet-cancelbtn"><?php esc_html_e('Canceled', 'jobsearch-shmeets') ?> <span class="cancel-loder"></span></a></li>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <li><a href="javascript:void(0);" class="jobsearch-meet-cancelbtn" data-id="<?php echo ($meet_job_id) ?>" data-md="<?php echo ($meet_post_id) ?>"><?php esc_html_e('Cancel Meeting', 'jobsearch-shmeets') ?> <span class="cancel-loder"></span></a></li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div> 
                                    </div> 
                                </div>
                            </div>
                            <?php
                            $right_content_html .= ob_get_clean();
                            if ($meets_countr == $total_page_meets) {
                                $end_tags = '</div>' . "\n";
                                //
                                echo ($left_date_html);
                                echo ($right_content_html);
                                echo ($end_tags);
                                //
                            }
                            $very_first_meet = false;
                            $meets_countr++;
                        }
                        ?>
                    </div>
                    <?php
                    wp_reset_postdata();
                    $total_pages = 1;
                    if ($total_meets > 0 && $reults_per_page > 0 && $total_meets > $reults_per_page) {
                        $total_pages = ceil($total_meets / $reults_per_page);
                        ?>
                        <div class="jobsearch-pagination-blog">
                            <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                        </div>
                        <?php
                    }
                }
            } else {
                echo '<p>' . esc_html__('No meeting found.', 'jobsearch-shmeets') . '</p>';
            }
            ?>
        </div>
        <?php
    }

    public function emp_dash_tab_lists() {
        global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings, $sitepress;
        
        $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
        $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');
        
        wp_enqueue_script('jobsearch-shmeets-scripts');
        
        $week_days = self::week_days();
        
        $current_date = strtotime(current_time('d-m-Y'));
        
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $sitepress_def_lang = $sitepress->get_default_language();
            $sitepress_curr_lang = $sitepress->get_current_language();
            $sitepress->switch_lang($sitepress_def_lang, true);
        }
        
        $user_id = get_current_user_id();
        $user_obj = wp_get_current_user();
        $employer_id = jobsearch_get_user_employer_id($user_id);
        
        $saved_meets_view = get_post_meta($employer_id, 'jobsearch_user_meetings_view', true);
        
        $meetings_view = 'list';
        if (isset($_GET['meetings_view']) && $_GET['meetings_view'] != '') {
            $meetings_view = $_GET['meetings_view'];
        } else if ($saved_meets_view != '') {
            $meetings_view = $saved_meets_view;
        }
        
        $reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;
        
        if ($meetings_view == 'calendar') {
            $reults_per_page = '-1';
        }

        $page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
        $args = array(
            'post_type' => 'jobsearch_meets',
            'posts_per_page' => $reults_per_page,
            'paged' => $page_num,
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_key' => 'meet_start_time',
            'order' => 'ASC',
            'orderby' => 'meta_value_num',
            'meta_query' => array(
                array(
                    'key' => 'meeting_employer',
                    'value' => $employer_id,
                    'compare' => '=',
                ),
            ),
        );
        $meetins_query = new WP_Query($args);
        $meetins_posts = $meetins_query->posts;
        $total_meets = $meetins_query->found_posts;
        $total_page_meets = $meetins_query->post_count;
        
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $sitepress->switch_lang($sitepress_curr_lang, true);
        }
        ?>
        <div class="meetings-list-con">
            <?php
            if (!empty($meetins_posts)) {
//                foreach ($meetins_posts as $meetinn_id) {
//                    $meet_date_tochk = get_post_meta($meetinn_id, 'meeting_date', true);
//                    $meet_exct_stime = get_post_meta($meetinn_id, 'meet_start_time', true);
//                    echo date('d M, Y', $meet_date_tochk) . '<br>';
//                    echo date('d M, Y H:i', $meet_exct_stime) . '<br>';
//                }
                
                if ($meetings_view == 'calendar') {
                    wp_enqueue_script('shmeets-fullcalendar');
                    wp_enqueue_script('shmeets-addevent');
                    ?>
                    <div id="meetings-fulcalendar"></div>
                    <?php
                    $meets_footr_args = array(
                        'current_date' => $current_date,
                        'employer_id' => $employer_id,
                        'user_obj' => $user_obj,
                    );
                    add_action('wp_footer', function() use ($meetins_posts, $meets_footr_args) {
                        $current_date = isset($meets_footr_args['current_date']) ? $meets_footr_args['current_date'] : '';
                        $employer_id = isset($meets_footr_args['employer_id']) ? $meets_footr_args['employer_id'] : '';
                        $user_obj = isset($meets_footr_args['user_obj']) ? $meets_footr_args['user_obj'] : '';
                        foreach ($meetins_posts as $meet_post_id) {
                            $meet_date = get_post_meta($meet_post_id, 'meeting_date', true);
                            $meet_duration = get_post_meta($meet_post_id, 'meeting_duration', true);
                            $meet_time = get_post_meta($meet_post_id, 'meeting_time', true);
                            $meet_exct_stime = get_post_meta($meet_post_id, 'meet_start_time', true);
                            $meet_exct_entime = get_post_meta($meet_post_id, 'meet_end_time', true);
                            $meet_note = get_post_meta($meet_post_id, 'meeting_note', true);
                            $meeting_resched_by = get_post_meta($meet_post_id, 'meeting_resched_by', true);
                            $meetin_platform = get_post_meta($meet_post_id, 'meeting_platform', true);
                            $meet_cand_id = get_post_meta($meet_post_id, 'meeting_candidate', true);
                            $meetin_emp_id = get_post_meta($meet_post_id, 'meeting_employer', true);
                            $meet_job_id = get_post_meta($meet_post_id, 'meeting_job', true);
                            
                            $meeting_is_cancel = get_post_meta($meet_post_id, 'meeting_canceled', true);

                            $meet_month = date_i18n('M', $meet_date);
                            $meet_ddate = date_i18n('d', $meet_date);
                            $meet_day = $meet_date > $current_date ? date_i18n('l', $meet_date) : esc_html__('Today', 'jobsearch-shmeets');
                            
                            $post_thumbnail_id = jobsearch_job_get_profile_image($meet_job_id);
                            $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, apply_filters('jobsearch_jobs_actlist_thmb_size', 'thumbnail'));
                            $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                            $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
                            
                            $company_name = jobsearch_job_get_company_name($meet_job_id, '@ ');
                            $job_city_title = jobsearch_post_city_contry_txtstr($meet_job_id, true, false, true);
                            $job_type_str = jobsearch_job_get_all_jobtypes($meet_job_id, 'jobsearch-option-btn');
                            
                            $meet_pltform_str = esc_html__('On-Board', 'jobsearch-shmeets');
                            $meet_pltform_icon = 'fa fa-file-text-o';
                            $meet_pltform_link = 'javascript:void(0);';
                            if ($meetin_platform == 'zoom') {
                                $meet_pltform_icon = 'fa fa-video-camera';
                                $meet_pltform_str = esc_html__('On Zoom', 'jobsearch-shmeets');
                                $meet_pltform_link = get_post_meta($meet_post_id, 'zoom_meeting_url', true);;
                            }
                            $time_duration_inmin = 1;
                            if ($meet_exct_entime > 0 && $meet_exct_entime > $meet_exct_stime) {
                                $meet_times_diff = $meet_exct_entime - $meet_exct_stime;
                                if ($meet_times_diff  > 60) {
                                    $time_duration_inmin = $meet_times_diff/60;
                                }
                            }
                            $time_duration_str = sprintf(esc_html__('%s min', 'jobsearch-shmeets'), $time_duration_inmin);
                            ?>
                            <div id="calendarPopover<?php echo ($meet_post_id) ?>" class="calendar-eventpopvr-mainbox" style="display: none;">
                                <div class="calendar-eventpopvr-inner">
                                    <a href="javascript:void(0);" class="close-calnder-evntpop"><i class="fa fa-times"></i></a>
                                    <div class="meet-txtimg-con">
                                        <div class="meet-job-imgcon">
                                            <a href="<?php echo esc_url(get_permalink($meet_job_id)) ?>" target="_blank"><img src="<?php echo esc_url($post_thumbnail_src) ?>" alt=""></a>
                                        </div>
                                        <div class="meet-job-detinfcon">
                                            <h2><a href="<?php echo esc_url(get_permalink($meet_job_id)) ?>" target="_blank"><?php echo wp_trim_words(get_the_title($meet_job_id), 4) ?></a><?php echo ($meeting_is_cancel == 'yes' ? ' <small style="color: #ff0000;">(Canceled)</small>' : '') ?></h2>
                                            <?php
                                            if ($company_name != '') {
                                                echo '<span class="job-company">' . ($company_name) . '</span>';
                                            }
                                            if ($job_city_title != '') {
                                                echo '<span class="job-location"><i class="jobsearch-icon jobsearch-maps-and-flags"></i> ' . ($job_city_title) . '</span>';
                                            }
                                            if ($job_type_str != '') {
                                                echo ($job_type_str);
                                            }
                                            ?>
                                            <div class="meting-typdur-con">
                                                <a href="<?php echo ($meet_pltform_link) ?>" class="interview-schedule-zoom"><i class="<?php echo ($meet_pltform_icon) ?>"></i> <?php echo ($meet_pltform_str) ?></a>
                                                <span class="meetin-duration"><?php echo ($time_duration_str) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="interview-schedule-atcon">
                                        <i class="fa fa-clock-o"></i> <?php esc_html_e('Interview schedule @', 'jobsearch-shmeets') ?> <span><?php echo date_i18n(get_option('time_format'), $meet_exct_stime) ?> - <?php echo date_i18n(get_option('time_format'), $meet_exct_entime) ?></span>
                                    </div>
                                    <?php
                                    $meet_note_txt = '';
                                    if ($meet_note != '') {
                                        $meet_note_txt = $meet_note;
                                    } else if (!empty($meeting_resched_by)) {
                                        foreach ($meeting_resched_by as $meet_resched_itm) {
                                            if (isset($meet_resched_itm['meet_note']) && $meet_resched_itm['meet_note'] != '') {
                                                $meet_note_txt = $meet_resched_itm['meet_note'];
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="meetin-belowsec-con">
                                        <?php
                                        if ($meet_note_txt != '') {
                                            ?>
                                            <div class="meting-note-con">
                                                <div class="meetnote-icon-con"><i class="fa fa-file-text-o"></i> <?php esc_html_e('Note', 'jobsearch-shmeets') ?></div>
                                                <div class="meetnote-text-con"><?php echo ($meet_note_txt) ?> <a href="javascript:void(0);" class="meetin-notespop-btn" data-id="<?php echo ($meet_post_id) ?>"><?php esc_html_e('More detail', 'jobsearch-shmeets') ?></a></div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <div class="meetin-belowbtns-con">
                                            <div class="meetin-reschedbtn-con">
                                                <div class="meetin-belowbtn-iner">
                                                    <a href="javascript:void(0);" class="jobsearch-meet-reschedulepop" data-id="<?php echo ($meet_job_id) ?>" data-cd="<?php echo ($meet_cand_id) ?>" data-md="<?php echo ($meet_post_id) ?>"><i class="fa fa-calendar"></i> <?php esc_html_e('Re-schedule Meeting', 'jobsearch-shmeets') ?></a>
                                                </div>
                                            </div>
                                            <div class="meetin-addclendrbtn-con">
                                                <div class="meetin-belowbtn-iner">
                                                    <a href="javascript:void(0);" class="jobsearch-meet-addclendrbtn addeventatc">
                                                        <i class="fa fa-calendar"></i> <?php esc_html_e('Add to Calendar', 'jobsearch-shmeets') ?>
                                                        <span class="start"><?php echo date('m/d/Y h:i A', $meet_exct_stime) ?></span>
                                                        <span class="end"><?php echo date('m/d/Y h:i A', $meet_exct_entime) ?></span>
                                                        <span class="timezone"><?php echo wp_timezone_string() ?></span>
                                                        <span class="title"><?php printf(esc_html__('Interview for job "%s"', 'jobsearch-shmeets'), get_the_title($meet_job_id)) ?></span>
                                                        <span class="description"><?php echo ($meet_note_txt) ?></span>
                                                        <span class="location"><?php echo ($job_city_title) ?></span>
                                                        <span class="organizer"><?php echo get_the_title($employer_id) ?></span>
                                                        <span class="organizer_email"><?php echo ($user_obj->user_email) ?></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $meet_notes_arr = array();
                            if ($meet_note != '') {
                                $meet_note_txt = $meet_note;
                                $meet_notes_arr[] = array(
                                    'meet_note' => $meet_note,
                                    'emp_id' => $meetin_emp_id,
                                    'meet_post_id' => $meet_post_id,
                                );
                            }
                            if (!empty($meeting_resched_by)) {
                                foreach ($meeting_resched_by as $meet_resched_itm) {
                                    if (isset($meet_resched_itm['meet_note']) && $meet_resched_itm['meet_note'] != '') {
                                        $meet_notes_arr[] = $meet_resched_itm;
                                    }
                                }
                            }
                            if (!empty($meet_notes_arr)) {
                                $this->meetin_notes_html($meet_post_id, $meet_notes_arr);
                            }
                        }
                        ?>
                        <script type="text/javascript">
                            window.addeventasync = function() {
                                addeventatc.settings({
                                    css: false,
                                    appleical  : {show:true, text:"<?php _e('Apple Calendar', 'jobsearch-shmeets') ?>"},
                                    google     : {show:true, text:"<?php _e('Google <em>(online)</em>', 'jobsearch-shmeets') ?>"},
                                    office365  : {show:true, text:"<?php _e('Office 365 <em>(online)</em>', 'jobsearch-shmeets') ?>"},
                                    outlook    : {show:true, text:"<?php _e('Outlook', 'jobsearch-shmeets') ?>"},
                                    outlookcom : {show:true, text:"<?php _e('Outlook.com <em>(online)</em>', 'jobsearch-shmeets') ?>"},
                                    yahoo      : {show:true, text:"<?php _e('Yahoo <em>(online)</em>', 'jobsearch-shmeets') ?>"}
                                });
                            };
                            document.addEventListener('DOMContentLoaded', function() {
                                var meetings_calendar = document.getElementById('meetings-fulcalendar');

                                var calendar = new FullCalendar.Calendar(meetings_calendar, {
                                    headerToolbar: {
                                        left: 'title',
                                        center: '',
                                        right: 'prev,next'
                                    },
                                    initialDate: '<?php echo date('Y-m-d') ?>',
                                    navLinks: true, // can click day/week names to navigate views
                                    businessHours: true, // display business hours
                                    editable: false,
                                    selectable: true,
                                    events: [
                                        <?php
                                        foreach ($meetins_posts as $meet_post_id) {
                                            $meet_date = get_post_meta($meet_post_id, 'meeting_date', true);
                                            $meet_duration = get_post_meta($meet_post_id, 'meeting_duration', true);
                                            $meet_time = get_post_meta($meet_post_id, 'meeting_time', true);
                                            $meet_exct_stime = get_post_meta($meet_post_id, 'meet_start_time', true);
                                            $meet_exct_entime = get_post_meta($meet_post_id, 'meet_end_time', true);
                                            ?>
                                            {
                                                type: <?php echo ($meet_post_id) ?>,
                                                title: '<?php echo wp_trim_words(get_the_title($meet_job_id), 4) ?>',
                                                start: '<?php echo date('Y-m-d', $meet_exct_stime) ?>T<?php echo date('H:i:s', $meet_exct_stime) ?>',
                                                end: '<?php echo date('Y-m-d', $meet_exct_entime) ?>T<?php echo date('H:i:s', $meet_exct_entime) ?>',
                                            },
                                            <?php
                                        }
                                        ?>
                                    ],
                                    eventClick: function (event) {
                                        var meeting_id = event.event.extendedProps.type;
                                        var jsEvent = event.jsEvent;
                                        jQuery('.calendar-eventpopvr-mainbox').hide();
                                        var target_popver = jQuery('#calendarPopover' + meeting_id);
                                        var target_popver_height = target_popver.height();
                                        target_popver.removeAttr('style');
                                        
                                        var actual_y_cords = jsEvent.pageY - 5;
                                        if (actual_y_cords > target_popver_height) {
                                            actual_y_cords = actual_y_cords - target_popver_height;
                                        }
                                        
                                        var top_pos = actual_y_cords;
                                        var left_pos = jsEvent.pageX - 50;
                                        target_popver.css({left: left_pos,top: top_pos});
                                    },
                                });
                                calendar.render();
                            });
                            jQuery(document).on('click', '.close-calnder-evntpop', function() {
                                jQuery(this).parents('.calendar-eventpopvr-mainbox').hide();
                            });
                        </script>
                        <?php
                    }, 30, 2);
                } else {
                    ?>
                    <div class="jobsearch-interview-list-container">
                        <?php
                        $very_first_meet = true;
                        $meets_countr = 1;
                        foreach ($meetins_posts as $meet_post_id) {
                            $meet_date = get_post_meta($meet_post_id, 'meeting_date', true);
                            $meet_duration = get_post_meta($meet_post_id, 'meeting_duration', true);
                            $meet_time = get_post_meta($meet_post_id, 'meeting_time', true);
                            $meet_exct_stime = get_post_meta($meet_post_id, 'meet_start_time', true);
                            $meet_exct_entime = get_post_meta($meet_post_id, 'meet_end_time', true);
                            $meeting_resched_by = get_post_meta($meet_post_id, 'meeting_resched_by', true);
                            $meet_note = get_post_meta($meet_post_id, 'meeting_note', true);
                            $meetin_platform = get_post_meta($meet_post_id, 'meeting_platform', true);
                            $meet_cand_id = get_post_meta($meet_post_id, 'meeting_candidate', true);
                            $meetin_emp_id = get_post_meta($meet_post_id, 'meeting_employer', true);
                            $meet_job_id = get_post_meta($meet_post_id, 'meeting_job', true);
                            
                            $meeting_is_cancel = get_post_meta($meet_post_id, 'meeting_canceled', true);

                            $meet_month = date_i18n('M', $meet_date);
                            $meet_ddate = date_i18n('d', $meet_date);
                            $meet_day = $meet_date == $current_date ? esc_html__('Today', 'jobsearch-shmeets') : date_i18n('l', $meet_date);
                            if ($very_first_meet) {
                                $left_meet_date = $meet_date;
                                ob_start();
                                ?>
                                <div class="jobsearch-interview-list-container-left">
                                    <div class="jobsearch-interview-list-container-left-inner"><div class="jobsearch-interview-list-container-left-text"><span><?php echo ($meet_month) ?></span> <strong><?php echo ($meet_ddate) ?></strong> <span><?php echo ($meet_day) ?></span></div></div>
                                </div>
                                <?php
                                echo '<div class="jobsearch-interview-list-container-right">' . "\n";
                                $left_date_html = ob_get_clean();
                                $right_content_html = '';
                            }
                            if ($meet_date > $left_meet_date) {
                                $left_meet_date = $meet_date;
                                $end_tags = '</div>' . "\n";
                                $end_tags .= '</div>' . "\n";
                                //
                                echo ($left_date_html);
                                echo ($right_content_html);
                                echo ($end_tags);
                                //
                                ob_start();
                                echo '<div class="jobsearch-interview-list-container">' . "\n";
                                ?>
                                <div class="jobsearch-interview-list-container-left">
                                    <div class="jobsearch-interview-list-container-left-inner"><div class="jobsearch-interview-list-container-left-text"><span><?php echo ($meet_month) ?></span> <strong><?php echo ($meet_ddate) ?></strong> <span><?php echo ($meet_day) ?></span></div></div>
                                </div>
                                <?php
                                echo '<div class="jobsearch-interview-list-container-right">' . "\n";
                                $left_date_html = ob_get_clean();
                                $right_content_html = '';
                            }
                            ob_start();
                            $start_time_str = date_i18n(get_option('time_format'), $meet_exct_stime);
                            $end_time_str = date_i18n(get_option('time_format'), $meet_exct_entime);

                            $time_duration_inmin = 1;
                            if ($meet_exct_entime > 0 && $meet_exct_entime > $meet_exct_stime) {
                                $meet_times_diff = $meet_exct_entime - $meet_exct_stime;
                                if ($meet_times_diff  > 60) {
                                    $time_duration_inmin = $meet_times_diff/60;
                                }
                            }
                            $time_duration_str = sprintf(esc_html__('%s min', 'jobsearch-shmeets'), $time_duration_inmin);

                            $cand_img_src = jobsearch_candidate_img_url_comn($meet_cand_id);
                            $meetin_with_str = sprintf(__('Meeting with&nbsp;<a href="%s">%s</a>', 'jobsearch-shmeets'), get_permalink($meet_cand_id), get_the_title($meet_cand_id));

                            $meet_pltform_str = esc_html__('On-Board', 'jobsearch-shmeets');
                            $meet_pltform_icon = 'fa fa-file-text-o';
                            $meet_pltform_link = 'javascript:void(0);';
                            if ($meetin_platform == 'zoom') {
                                $meet_pltform_icon = 'fa fa-video-camera';
                                $meet_pltform_str = esc_html__('On Zoom', 'jobsearch-shmeets');
                                $meet_pltform_link = get_post_meta($meet_post_id, 'zoom_meeting_url', true);;
                            }
                            
                            $meet_notes_arr = array();
                            if ($meet_note != '') {
                                $meet_notes_arr[] = array(
                                    'meet_note' => $meet_note,
                                    'emp_id' => $meetin_emp_id,
                                    'meet_post_id' => $meet_post_id,
                                );
                            }
                            if (!empty($meeting_resched_by)) {
                                foreach ($meeting_resched_by as $meet_resched_itm) {
                                    if (isset($meet_resched_itm['meet_note']) && $meet_resched_itm['meet_note'] != '') {
                                        $meet_notes_arr[] = $meet_resched_itm;
                                    }
                                }
                            }
                            if (!empty($meet_notes_arr)) {
                                add_action('wp_footer', function() use ($meet_notes_arr, $meet_post_id) {
                                    $this->meetin_notes_html($meet_post_id, $meet_notes_arr);
                                }, 30, 2);
                            }
                            ?>
                            <div class="jobsearch-interview-list-container-right-inner<?php echo ($very_first_meet ? ' first' : '') ?>">
                                <div class="jobsearch-interview-schedule-columnk jobsearch-interview-first"> 
                                    <div class="schedule-maincol-inner-wrap">
                                        <h2><a href="<?php echo get_permalink($meet_job_id) ?>"><?php echo get_the_title($meet_job_id) ?></a><?php echo ($meeting_is_cancel == 'yes' ? ' <small style="color: #ff0000;">(Canceled)</small>' : '') ?> <span> <img src="<?php echo ($cand_img_src) ?>" alt=""> <?php echo ($meetin_with_str) ?></span></h2>
                                    </div> 
                                </div>
                                <div class="jobsearch-interview-schedule-columnk"> 
                                    <div class="schedule-maincol-inner-wrap metting-wrap">
                                        <a href="<?php echo ($meet_pltform_link) ?>" class="jobsearch-interview-schedule-zoom"><i class="<?php echo ($meet_pltform_icon) ?>"></i> <?php echo ($meet_pltform_str) ?></a>
                                    </div>
                                </div>
                                <div class="jobsearch-interview-schedule-columnk jobsearch-interview-three"> <div class="schedule-maincol-inner-wrap"><div class="jobsearch-interview-schedule-date"><i class="fa fa-clock-o"></i> <?php echo ($start_time_str) ?> - <?php echo ($end_time_str) ?></div></div> </div>
                                <div class="jobsearch-interview-schedule-columnk jobsearch-interview-four"> <div class="schedule-maincol-inner-wrap"><div class="jobsearch-interview-schedule-time"><i class="fa fa-hourglass-2"></i> <?php echo ($time_duration_str) ?></div></div> </div>
                                <div class="jobsearch-interview-schedule-columnk jobsearch-interview-last"> 
                                    <div class="schedule-maincol-inner-wrap"> 
                                        <div class="jobsearch-interview-schedule-action">
                                            <a href="javascript:void(0);" class="meetin-notespop-btn" data-id="<?php echo ($meet_post_id) ?>"><span><i class="fa fa-file-text-o"></i> <?php echo (!empty($meet_notes_arr) ? count($meet_notes_arr) : 0) ?></span></a> 
                                            <div class="candidate-more-acts-con">
                                                <a href="javascript:void(0);" class="more-actions"><?php esc_html_e('Actions', 'jobsearch-shmeets') ?> <i class="fa fa-angle-down"></i></a>
                                                <ul style="display: none;">
                                                    <li><a href="javascript:void(0);" class="jobsearch-meet-reschedulepop" data-id="<?php echo ($meet_job_id) ?>" data-cd="<?php echo ($meet_cand_id) ?>" data-md="<?php echo ($meet_post_id) ?>"><?php esc_html_e('Re-schedule Meeting', 'jobsearch-shmeets') ?></a></li>
                                                    <li><a href="javascript:void(0);" class="jobsearch-meet-deletebtn" data-id="<?php echo ($meet_job_id) ?>" data-md="<?php echo ($meet_post_id) ?>"><?php esc_html_e('Delete Meeting', 'jobsearch-shmeets') ?> <span class="delete-loder"></span></a></li>
                                                </ul>
                                            </div>
                                        </div> 
                                    </div> 
                                </div>
                            </div>
                            <?php
                            $right_content_html .= ob_get_clean();
                            if ($meets_countr == $total_page_meets) {
                                $end_tags = '</div>' . "\n";
                                //
                                echo ($left_date_html);
                                echo ($right_content_html);
                                echo ($end_tags);
                                //
                            }
                            $very_first_meet = false;
                            $meets_countr++;
                        }
                        ?>
                    </div>
                    <?php
                    wp_reset_postdata();
                    $total_pages = 1;
                    if ($total_meets > 0 && $reults_per_page > 0 && $total_meets > $reults_per_page) {
                        $total_pages = ceil($total_meets / $reults_per_page);
                        ?>
                        <div class="jobsearch-pagination-blog">
                            <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                        </div>
                        <?php
                    }
                }
            } else {
                echo '<p>' . esc_html__('No meeting found.', 'jobsearch-shmeets') . '</p>';
            }
            ?>
        </div>
        <div class="meetings-settins-con" style="display: none;">
            <form method="post" id="jobsearch-meetin-settinsform">
                <div class="meetsetins-hding"><h2><?php esc_html_e('Available Days', 'jobsearch-shmeets') ?></h2></div>
                <div class="jobsearch-timmings-thead">
                    <div><?php esc_html_e('Days', 'jobsearch-shmeets') ?></div>
                    <div><?php esc_html_e('From', 'jobsearch-shmeets') ?></div>
                    <div><?php esc_html_e('To', 'jobsearch-shmeets') ?></div>
                </div>
                <?php
                $avail_days = get_post_meta($employer_id, 'employer_meetin_availble_days', true);
                $avail_days = empty($avail_days) ? array() : $avail_days;
                foreach($week_days as $day_key => $day_val) {
                    $time_from = get_post_meta($employer_id, $day_key . 'meetin_time_from', true);
                    $time_to = get_post_meta($employer_id, $day_key . 'meetin_time_to', true);
                    ?>
                    <div class="jobsearch-timmings-setting-flex">
                        <div><?php echo ($day_val) ?></div>
                        <div><input type="text" class="meetin-time-insetins" name="<?php echo ($day_key) ?>_time_from" value="<?php echo ($time_from) ?>" autocomplete="off"></div>
                        <div><input type="text" class="meetin-time-insetins" name="<?php echo ($day_key) ?>_time_to" value="<?php echo ($time_to) ?>" autocomplete="off"></div>
                        <div><input type="checkbox" name="meetin_avail_days[]" value="<?php echo ($day_key) ?>"<?php echo (in_array($day_key, $avail_days) ? ' checked' : '') ?>></div>
                    </div>
                    <?php
                }
                ?>
                <div class="jobsearch-timmings-setting-btn">
                    <input type="hidden" name="action" value="jobsearch_dash_meetin_settins_call">
                    <input type="submit" value="<?php esc_html_e('Save Settings', 'jobsearch-shmeets') ?>">
                    <span class="meet-settings-loder"></span>
                    <div class="meet-settins-fmsg" style="display: none;"></div>
                </div>
            </form>
            <?php
            $zoom_meetings_switch = isset($jobsearch_plugin_options['zoom_meetings_switch']) ? $jobsearch_plugin_options['zoom_meetings_switch'] : '';
            if ($zoom_meetings_switch == 'on') {
                ?>
                <div class="jobsearch-apisetting-wrapper">

                    <?php
                    $zoom_meetins_switch = get_post_meta($employer_id, 'jobsearch_zoom_meetins_switch', true);
                    ?>
                    <div class="jobsearch-apisetting-head">
                        <div class="jobsearch-apisetting-coll"><span><?php esc_html_e('Zoom API Setting', 'jobsearch-shmeets') ?></span></div>
                        <div class="jobsearch-apisetting-coll">
                            <a class="jobsearch-apisetting-zoombtn"><i class="fa fa-video-camera"></i> <?php esc_html_e('Zoom Meetings', 'jobsearch-shmeets') ?></a>
                            <div class="chekunchk-opt-box">
                                <div class="chekunchk-opt-boxiner">
                                    <input type="hidden" name="zoom_meetins_switch" value="<?php echo ($zoom_meetins_switch) ?>">
                                    <input id="opt-zoomeet-switch" type="checkbox" class="corect-opt-chkunchk"<?php echo ($zoom_meetins_switch == 'on' ? ' checked=""' : '') ?>>
                                    <label for="opt-zoomeet-switch">
                                        <span class="chkunchk-onoffswitch-inner"></span>
                                        <span class="chkunchk-onoffswitch-switch"></span>
                                    </label>
                                </div>
                                <strong class="opt-notific-lodr"></strong>
                            </div>
                        </div>
                    </div>

                    <?php
                    $user_client_id = get_post_meta($employer_id, 'jobsearch_zoom_auth_client_id', true);
                    $user_client_secret = get_post_meta($employer_id, 'jobsearch_zoom_auth_client_secret', true);
                    $user_zoom_email = get_post_meta($employer_id, 'jobsearch_zoom_user_email_address', true);
                    $user_refresh_token = get_post_meta($employer_id, 'jobsearch_zoom_refresh_token', true);

                    $alredy_auth = false;
                    if ($user_refresh_token != '') {
                        $alredy_auth = true;
                        ?>
                        <p><?php esc_html_e('You are Authorized with zoom.', 'jobsearch-shmeets') ?> <a href="javascript:void(0);" class="zoom-userreauth-btn"><?php esc_html_e('Re-Authorize', 'jobsearch-shmeets') ?></a></p>
                        <?php
                    }
                    ?>
                    <form method="post" class="getauth-withzoom-form"<?php echo ($alredy_auth ? ' style="display: none;"' : '') ?>>
                        <div class="jobsearch-apisetting-content">
                            <div>
                                <span><?php esc_html_e('Zoom Email', 'jobsearch-shmeets') ?></span>
                                <input type="text" name="zoom_email" value="<?php echo ($user_zoom_email) ?>">
                            </div>
                            <div>
                                <span><?php esc_html_e('Zoom Client ID', 'jobsearch-shmeets') ?></span>
                                <input type="text" name="zoom_client_id" value="<?php echo ($user_client_id) ?>">
                            </div>
                            <div>
                                <span><?php esc_html_e('Client Secret', 'jobsearch-shmeets') ?></span>
                                <input type="text" name="client_secret" value="<?php echo ($user_client_secret) ?>">
                            </div>
                            <div>
                                <input type="submit" class="getauth-withzoom-btn" value="<?php esc_html_e('Get Authorize with zoom', 'jobsearch-shmeets') ?>">
                            </div>
                        </div>
                    </form>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }
    
    public function meetin_notes_html($meet_post_id, $meet_notes_arr) {
        krsort($meet_notes_arr);
        ?>
        <div class="jobsearch-modal schedd-meetnotes-popup fade" id="JobSearchSchedMeetinNotes<?php echo ($meet_post_id) ?>">
            <div class="modal-inner-area">&nbsp;</div>
            <div class="modal-content-area">
                <div class="modal-box-area">
                    <div class="jobsearch-modal-title-box">
                        <h2><?php esc_html_e('Meeting History', 'jobsearch-shmeets') ?></h2>
                        <span class="modal-close"><i class="fa fa-times"></i></span>
                    </div>
                    <?php
                    $notes_counter = 1;
                    foreach ($meet_notes_arr as $meet_note_obj) {
                        if (isset($meet_note_obj['emp_id'])) {
                            $written_by = get_the_title($meet_note_obj['emp_id']);
                        } else if (isset($meet_note_obj['cand_id'])) {
                            $written_by = get_the_title($meet_note_obj['cand_id']);
                        }
                        ?>
                        <div class="meet-noteobj-item<?php echo ($notes_counter == 1 ? ' new-note' : '') ?>">
                            <div class="meet-note-hder">
                                <strong class="note-written-by"><?php echo (isset($meet_note_obj['time']) ? __('Rescheduled By', 'jobsearch-shmeets') : __('Created By', 'jobsearch-shmeets')) ?>: <?php echo ($written_by) ?></strong> 
                                <?php
                                if (isset($meet_note_obj['time']) && $meet_note_obj['time'] > 0) {
                                    ?>
                                    <span class="note-written-time"><?php echo date_i18n(get_option('date_format'), $meet_note_obj['time']) ?></span>
                                    <?php
                                } else {
                                    $meet_obj = get_post($meet_post_id);
                                    if (isset($meet_obj->post_date)) {
                                        $meetin_create_date = $meet_obj->post_date;
                                        ?>
                                        <span class="note-written-time"><?php echo date_i18n(get_option('date_format'), strtotime($meetin_create_date)) ?></span>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <div class="meet-note-det">
                                <p><?php echo ($meet_note_obj['meet_note']) ?></p>
                            </div>
                        </div>
                        <?php
                        $notes_counter++;
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function dash_meetin_settins_save() {
        $week_days = self::week_days();
        
        $user_id = get_current_user_id();
        $employer_id = jobsearch_get_user_employer_id($user_id);
        
        foreach($week_days as $day_key => $day_val) {
            if (isset($_POST[$day_key . '_time_from'])) {
                $time_from = $_POST[$day_key . '_time_from'];
                update_post_meta($employer_id, $day_key . 'meetin_time_from', $time_from);
            }
            if (isset($_POST[$day_key . '_time_to'])) {
                $time_to = $_POST[$day_key . '_time_to'];
                update_post_meta($employer_id, $day_key . 'meetin_time_to', $time_to);
            }
        }
        
        if (isset($_POST['meetin_avail_days'])) {
            $meetin_avail_days = $_POST['meetin_avail_days'];
            update_post_meta($employer_id, 'employer_meetin_availble_days', $meetin_avail_days);
        } else {
            update_post_meta($employer_id, 'employer_meetin_availble_days', '');
        }
        
        $json_data = array('error' => '0', 'msg' => '<div class="alert alert-success">' . esc_html__('Settings saved successfully.', 'jobsearch-shmeets') . '</div>');
        wp_send_json($json_data);
    }

}

return new JobSearch_Sched_Meets_Dashtab_Content();
