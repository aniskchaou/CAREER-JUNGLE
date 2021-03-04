<?php

/**
 * Directory Plus JobApplications Module
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Jobsearch_JobApplication')) {

    class Jobsearch_JobApplication {

        public function __construct() {
            add_action('init', array($this, 'init'), 12);
            Jobsearch_JobApplication::load_files();
        }

        static function load_files() {
            // email template
            include plugin_dir_path(dirname(__FILE__)) . 'job-application/include/apply-job-email-template.php';
            include plugin_dir_path(dirname(__FILE__)) . 'job-application/include/apply-job-email-tocand.php';
            include plugin_dir_path(dirname(__FILE__)) . 'job-application/include/quick-apply-job-load.php';
            //
            include plugin_dir_path(dirname(__FILE__)) . 'job-application/include/job-application-load.php';
        }

        public function init() {


            // Add hook for dashboard member top menu links.

            add_action('jobsearch_job_application_abc', array($this, 'jobsearch_job_application_abc_callback'), 10, 1);
            add_action('wp_enqueue_scripts', array($this, 'jobsearch_job_application_enqueue_scripts'), 52);
        }

        public function jobsearch_job_application_enqueue_scripts() {
            global $sitepress, $jobsearch_plugin_options;

            $is_page = is_page();
            $page_content = '';
            if ($is_page) {
                $page_id = get_the_ID();
                $page_post = get_post($page_id);
                $page_content = isset($page_post->post_content) ? $page_post->post_content : '';
            }

            $file_sizes_arr = array(
                '5120' => __('5Mb', 'wp-jobsearch'),
                '10120' => __('10Mb', 'wp-jobsearch'),
                '50120' => __('50Mb', 'wp-jobsearch'),
                '100120' => __('100Mb', 'wp-jobsearch'),
                '200120' => __('200Mb', 'wp-jobsearch'),
                '300120' => __('300Mb', 'wp-jobsearch'),
                '500120' => __('500Mb', 'wp-jobsearch'),
                '1000120' => __('1Gb', 'wp-jobsearch'),
            );
            $cvfile_size = '5120';
            $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
            $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
            if (isset($file_sizes_arr[$cand_cv_file_size])) {
                $cvfile_size = $cand_cv_file_size;
                $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
            }
            
            $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

            if (empty($cand_files_types)) {
                $cand_files_types = array(
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/pdf',
                );
            }
            $cand_files_types_json = json_encode($cand_files_types);
            
            $sutable_files_arr = array();
            $file_typs_comarr = array(
                'text/plain' => __('text', 'wp-jobsearch'),
                'image/jpeg' => __('jpeg', 'wp-jobsearch'),
                'image/png' => __('png', 'wp-jobsearch'),
                'application/msword' => __('doc', 'wp-jobsearch'),
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __('docx', 'wp-jobsearch'),
                'application/vnd.ms-excel' => __('xls', 'wp-jobsearch'),
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => __('xlsx', 'wp-jobsearch'),
                'application/pdf' => __('pdf', 'wp-jobsearch'),
            );
            foreach ($file_typs_comarr as $file_typ_key => $file_typ_comar) {
                if (in_array($file_typ_key, $cand_files_types)) {
                    $sutable_files_arr[] = '.' . $file_typ_comar;
                }
            }
            $sutable_files_str = implode(', ', $sutable_files_arr);

            $admin_ajax_url = admin_url('admin-ajax.php');
            if ( function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') ) {
                $lang_code = $sitepress->get_current_language();
                $admin_ajax_url = add_query_arg(array('lang' => $lang_code), $admin_ajax_url);
            }
        
            // Enqueue JS
            wp_register_script('jobsearch-job-application-functions-script', plugins_url('assets/js/job-application-functions.js', __FILE__), '', '', true);
            wp_localize_script('jobsearch-job-application-functions-script', 'jobsearch_job_application', array(
                'admin_url' => $admin_ajax_url,
                'error_msg' => esc_html__('There is some error.', 'wp-jobsearch'),
                'confirm_msg' => esc_html__('jobsearch_job_application_conform_msg'),
                'cvfile_size_allow' => $cvfile_size,
                'cvfile_size_err' => sprintf(esc_html__('File size should not greater than %s.', 'wp-jobsearch'), $cvfile_size_str),
                'com_img_size' => esc_html__('Image size should not greater than 1 MB.', 'wp-jobsearch'),
                'com_file_size' => esc_html__('File size should not greater than 1 MB.', 'wp-jobsearch'),
                'cv_file_types' => sprintf(esc_html__('Suitable files are %s.', 'wp-jobsearch'), $sutable_files_str),
                'cvdoc_file_types' => stripslashes($cand_files_types_json),
            ));
            
            if (is_singular('job')) {
                wp_enqueue_script('jobsearch-job-application-functions-script');
            }
        }

        public function jobsearch_job_application_abc_callback($permissions = array(), $abc) {
            
        }

    }

    global $jobsearch_job_application;
    $jobsearch_job_application = new Jobsearch_JobApplication();
}

function jobsearch_job_applicants_sort_list($job_id, $sort_by = '', $list_meta_key = 'jobsearch_job_applicants_list') {

    $job_applicants_list = get_post_meta($job_id, $list_meta_key, true);
    if (is_array($job_applicants_list)) {
        $job_applicants_list = $job_applicants_list;
    } else if ($job_applicants_list != '') {
        $job_applicants_list = explode(',', $job_applicants_list);
    }

    $retrn_array = $ret_array = array();

    if (!empty($job_applicants_list)) {
        if ($sort_by == 'alphabetic' && !empty($job_applicants_list)) {
            $appl_array = $appl_ret_array = array();
            foreach ($job_applicants_list as $job_applicant) {
                $appl_array[$job_applicant] = get_the_title($job_applicant);
            }
            asort($appl_array);
            if (!empty($appl_array)) {
                foreach ($appl_array as $appl_arr_key => $appl_arr) {
                    $appl_ret_array[] = $appl_arr_key;
                }
            }
            $ret_array = $appl_ret_array;
        } else if ($sort_by == 'salary' && !empty($job_applicants_list)) {
            $appl_array = $appl_ret_array = array();
            foreach ($job_applicants_list as $job_applicant) {
                $apl_salary = get_post_meta($job_applicant, 'jobsearch_field_candidate_salary', true);
                $apl_salary = $apl_salary > 0 ? $apl_salary : 0;
                $appl_array[$job_applicant] = $apl_salary;
            }
            arsort($appl_array);
            if (!empty($appl_array)) {
                foreach ($appl_array as $appl_arr_key => $appl_arr) {
                    $appl_ret_array[] = $appl_arr_key;
                }
            }
            $ret_array = $appl_ret_array;
        } else if ($sort_by == 'viewed' && !empty($job_applicants_list)) {
            $viewed_candidates = get_post_meta($job_id, 'jobsearch_viewed_candidates', true);
            if (empty($viewed_candidates)) {
                $viewed_candidates = array();
            }
            $appl_array = $appl_ret_array = array();
            foreach ($job_applicants_list as $job_applicant) {
                if (in_array($job_applicant, $viewed_candidates)) {
                    $appl_array[] = $job_applicant;
                } else {
                    $appl_ret_array[] = $job_applicant;
                }
            }
            $merge_arr = array_merge($appl_array, $appl_ret_array);
            $ret_array = $merge_arr;
        } else if ($sort_by == 'unviewed' && !empty($job_applicants_list)) {
            $viewed_candidates = get_post_meta($job_id, 'jobsearch_viewed_candidates', true);
            if (empty($viewed_candidates)) {
                $viewed_candidates = array();
            }
            $appl_array = $appl_ret_array = array();
            foreach ($job_applicants_list as $job_applicant) {
                if (in_array($job_applicant, $viewed_candidates)) {
                    $appl_array[] = $job_applicant;
                } else {
                    $appl_ret_array[] = $job_applicant;
                }
            }
            $merge_arr = array_merge($appl_ret_array, $appl_array);
            $ret_array = $merge_arr;
        } else if ($sort_by == 'recent' && !empty($job_applicants_list)) {
            arsort($job_applicants_list);
            $ret_array = $job_applicants_list;
        } else {
            $ret_array = $job_applicants_list;
        }

        $retrn_array = jobsearch_is_post_ids_array($ret_array, 'candidate');
        $retrn_array = apply_filters('jobsearch_applicants_sortby_list_arry', $retrn_array, $job_id, $sort_by);
    }

    return $retrn_array;
}
