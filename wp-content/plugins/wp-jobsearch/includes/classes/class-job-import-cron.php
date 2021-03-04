<?php
// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Job_Integ_Import_Cron {

    // hook things up
    public function __construct() {
        //
        add_action('jobsearch_job_import_schedules_cron', array($this, 'job_import_schedules_cron'));
    }
    
    public function job_import_schedules_cron() {
        global $JobSearch_Indeed_Jobs_Hooks_obj, $JobSearch_CareerJet_Jobs_Hooks_obj, $JobSearch_Ziprecruiter_Jobs_Hooks_obj, $JobSearch_CareerBuilder_Jobs_Hooks_obj;
        $all_schedules = get_option('jobsearch_job_integration_schedules');
        if (!empty($all_schedules)) {
            $update_scheds = false;
            $current_time = current_time('timestamp');
            foreach ($all_schedules as $schedule_id => $schedule_itm) {
                $next_run = isset($schedule_itm['next_run']) ? $schedule_itm['next_run'] : '';
                $import_status = isset($schedule_itm['schedule_status']) ? $schedule_itm['schedule_status'] : '';
                if ($next_run > 0 && $next_run <= $current_time && $import_status != 'inactive') {
                    $import_from = isset($schedule_itm['schedule_import_from']) ? $schedule_itm['schedule_import_from'] : '';
                    $import_on_days = isset($schedule_itm['schedule_import_on']) ? $schedule_itm['schedule_import_on'] : '';
                    $import_location = isset($schedule_itm['schedule_import_location']) ? $schedule_itm['schedule_import_location'] : '';
                    $import_keyword = isset($schedule_itm['schedule_import_keyword']) ? $schedule_itm['schedule_import_keyword'] : '';
                    $import_expired_days = isset($schedule_itm['schedule_import_expire_on']) ? $schedule_itm['schedule_import_expire_on'] : '';
                    $import_company_id = isset($schedule_itm['job_username']) ? $schedule_itm['job_username'] : '';
                    
                    //
                    if ($import_from == 'indeed') {
                        $_POST['q'] = $import_keyword;
                        $_POST['l'] = $import_location;
                        $_POST['co'] = '';
                        $_POST['jt'] = '';
                        $_POST['start'] = 0;
                        $_POST['limit'] = 25;
                        $_POST['job_username'] = $import_company_id;
                        $_POST['expire_days'] = $import_expired_days;
                        $JobSearch_Indeed_Jobs_Hooks_obj->jobsearch_import_indeed_jobs();
                    } else if ($import_from == 'careerjet') {
                        $_POST['keywords'] = $import_keyword;
                        $_POST['location'] = $import_location;
                        $_POST['page'] = '1';
                        $_POST['job_username'] = $import_company_id;
                        $_POST['expire_days'] = $import_expired_days;
                        $JobSearch_CareerJet_Jobs_Hooks_obj->jobsearch_import_careerjet_jobs();
                    } else if ($import_from == 'ziprecruiter') {
                        $_POST['keyword'] = $import_keyword;
                        $_POST['location'] = $import_location;
                        $_POST['per_page'] = '20';
                        $_POST['radius'] = '20';
                        $_POST['job_username'] = $import_company_id;
                        $_POST['expire_days'] = $import_expired_days;
                        $JobSearch_Ziprecruiter_Jobs_Hooks_obj->jobsearch_import_ziprecruiter_jobs();
                    } else if ($import_from == 'careerbuilder') {
                        $_POST['keyword'] = $import_keyword;
                        $_POST['location'] = $import_location;
                        $_POST['per_page'] = '20';
                        $_POST['job_username'] = $import_company_id;
                        $_POST['expire_days'] = $import_expired_days;
                        $JobSearch_CareerBuilder_Jobs_Hooks_obj->jobsearch_import_careerbuilder_jobs();
                    }
                    do_action('jobsearch_job_import_schedule_cronruner', $import_from, $schedule_itm);
                    //
                    if ($import_on_days > 0) {
                        $update_scheds = true;
                        $to_import_days = absint($import_on_days);
                        $to_schedule_time = strtotime("+" . $to_import_days . " days", $current_time);
                        $all_schedules[$schedule_id]['next_run'] = $to_schedule_time;
                    }
                }
            }
            
            if ($update_scheds) {
                update_option('jobsearch_job_integration_schedules', $all_schedules);
            }
        }
    }

}

return new Jobsearch_Job_Integ_Import_Cron();
