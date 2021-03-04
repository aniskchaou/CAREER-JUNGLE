<?php

namespace WP_Jobsearch;

if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('Candidate_Profile_Restriction')) {

    class Candidate_Profile_Restriction {

        // hook things up
        public function __construct() {
            add_filter('jobsearch_package_fields_arr_before_order_set', array($this, 'cand_pkg_fields_to_order_meta'), 11, 4);
        }

        public static function cand_field_is_locked($field_name, $page_view = 'page', $post_id = 0) {

            if ($post_id > 0) {
                $_post_id = $post_id;
            } else {
                $_post_id = get_the_ID();
            }
            
            $user_id = get_current_user_id();
            $user_isemp_member = false;
            if (jobsearch_user_isemp_member($user_id)) {
                $employer_id = jobsearch_user_isemp_member($user_id);
                $user_id = jobsearch_get_employer_user_id($employer_id);
                $user_isemp_member = true;
            }
            //
            $jobsearch__options = get_option('jobsearch_plugin_options');
            if ($page_view == 'detail_page') {
                $emp_cvpbase_restrictions = isset($jobsearch__options['emp_cv_pkgbase_restrictions']) ? $jobsearch__options['emp_cv_pkgbase_restrictions'] : '';
            } else {
                $emp_cvpbase_restrictions = isset($jobsearch__options['emp_cv_pkgbase_restrictions_list']) ? $jobsearch__options['emp_cv_pkgbase_restrictions_list'] : '';
            }

            $field_locked = true;

            if ($emp_cvpbase_restrictions != 'on') {
                return false;
            }
            
            $restrict_cand_type = isset($jobsearch__options['restrict_candidates_for_users']) ? $jobsearch__options['restrict_candidates_for_users'] : '';
            
            $user_is_employer = jobsearch_user_is_employer($user_id);
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            
            if ($restrict_cand_type == 'register' && $user_is_employer) {
                return false;
            }
            if ($restrict_cand_type == 'register_empcand' && ($user_is_employer || $user_is_candidate)) {
                return false;
            }
            
            if ($restrict_cand_type == 'only_applicants') {
                return false;
            }

            $subs_pkg_orderid =  jobsearch_employer_first_subscribed_cv_pkg($user_id);
            if (!$subs_pkg_orderid) {
                $subs_pkg_orderid = jobsearch_allin_first_pkg_subscribed($user_id, 'cvs');
            }
            if (!$subs_pkg_orderid) {
                $subs_pkg_orderid = jobsearch_emprof_first_pkg_subscribed($user_id, 'cvs');
            }
            
            if ($restrict_cand_type == 'register_resume') {
                $to_return_val = true;
                if ($user_is_employer) {
                    $employer_id = jobsearch_get_user_employer_id($user_id);

                    $emp_frstcv_pkg = $subs_pkg_orderid;
                    if ($emp_frstcv_pkg) {
                        $emp_ordercands_list = get_post_meta($emp_frstcv_pkg, 'jobsearch_order_cvs_list', true);
                        $emp_ordercands_list = $emp_ordercands_list != '' ? explode(',', $emp_ordercands_list) : array();
                        if (!empty($emp_ordercands_list) && in_array($_post_id, $emp_ordercands_list) && $field_locked === false) {
                            $to_return_val = false;
                        }
                    }
                    $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);
                    $employer_resumes_list = $employer_resumes_list != '' ? explode(',', $employer_resumes_list) : array();
                    if (!empty($employer_resumes_list) && in_array($_post_id, $employer_resumes_list) && $field_locked === false) {
                        $to_return_val = false;
                    }
                }
                //$field_locked = $to_return_val;
            }
            
            $bprofile_field_names = array(
                'profile_fields|display_name',
                'profile_fields|profile_img',
                'profile_fields|cover_img',
                'profile_fields|date_of_birth',
                'profile_fields|email',
                'profile_fields|phone',
                'profile_fields|sector',
                'profile_fields|job_title',
                'profile_fields|salary',
                'profile_fields|about_desc',
            );
            if (in_array($field_name, $bprofile_field_names)) {
                $options_key = 'cv_pkgbase_profile_defields';
                $order_meta_index = 'pbase_profile';

                $field_name_exp = explode('|', $field_name);
                $field_index_name = isset($field_name_exp[1]) ? $field_name_exp[1] : '';
            }
            
            // Gen On/Off Fields
            $gen_onoff_fields = array(
                'socialicons_defields',
                'customfields_defields',
                'address_defields',
                'contactfrm_defields',
                'skills_defields',
                'edu_defields',
                'exp_defields',
                'port_defields',
                'expertise_defields',
                'awards_defields',
            );

            if (isset($field_index_name)) {
                $options_allowfield_arr = isset($jobsearch__options[$options_key]) ? $jobsearch__options[$options_key] : '';
                if (!empty($options_allowfield_arr) && is_array($options_allowfield_arr) && in_array($field_index_name, $options_allowfield_arr)) {
                    $field_locked = false;
                }
                if (isset($subs_pkg_orderid) && $subs_pkg_orderid > 0) {
                    $allowfield_arr = get_post_meta($subs_pkg_orderid, 'jobsearch_cand_ppkg_fields_list', true);
                    if (isset($allowfield_arr[$order_meta_index]) && is_array($allowfield_arr[$order_meta_index]) && in_array($field_index_name, $allowfield_arr[$order_meta_index])) {
                        $field_locked = false;
                    }
                }
            } else if ($field_name != '' && in_array($field_name, $gen_onoff_fields)) {
                $options_key = 'cv_pkgbase_' . $field_name;
                $order_meta_index = 'pbase_' . (str_replace(array('_defields'), array(''), $field_name));
                
                $options_allowfield = isset($jobsearch__options[$options_key]) ? $jobsearch__options[$options_key] : '';
                
                if ($options_allowfield == 'on') {
                    $field_locked = false;
                }
                if (isset($subs_pkg_orderid) && $subs_pkg_orderid > 0) {
                    $pkgorder_allowfield = get_post_meta($subs_pkg_orderid, 'jobsearch_cand_ppkg_fields_list', true);
                    if (isset($pkgorder_allowfield[$order_meta_index]) && $pkgorder_allowfield[$order_meta_index] == 'on') {
                        $field_locked = false;
                    }
                }
            }

            return $field_locked;
        }

        public static function cand_gen_locked_html() {

            $html = esc_html__('This Field is locked', 'wp-jobsearch');
            return $html;
        }

        public function cand_field_locked_html($cus_html = '') {

            $html = self::cand_gen_locked_html();
            if ($cus_html != '') {
                $html = $cus_html;
            }

            return $html;
        }

        public static function cand_restrict_display_name() {

            $html = esc_html__('Unlock to reveal name', 'wp-jobsearch');
            return $html;
        }

        public function cand_pkg_fields_to_order_meta($packge_fields_arr, $order_id, $package_id, $pkg_type) {

            $jobsearch__options = get_option('jobsearch_plugin_options');
            
            if ($pkg_type == 'cv') {
                $cand_pkg_fields = array(
                    'pbase_profile' => (get_post_meta($package_id, 'jobsearch_field_empcv_pbase_profile', true)),
                    'pbase_socialicons' => (get_post_meta($package_id, 'jobsearch_field_empcv_pbase_socialicons', true)),
                    'pbase_customfields' => (get_post_meta($package_id, 'jobsearch_field_empcv_pbase_customfields', true)),
                    'pbase_address' => (get_post_meta($package_id, 'jobsearch_field_empcv_pbase_address', true)),
                    'pbase_contactfrm' => (get_post_meta($package_id, 'jobsearch_field_empcv_pbase_contactfrm', true)),
                    'pbase_skills' => (get_post_meta($package_id, 'jobsearch_field_empcv_pbase_skills', true)),
                    'pbase_edu' => (get_post_meta($package_id, 'jobsearch_field_empcv_pbase_edu', true)),
                    'pbase_exp' => (get_post_meta($package_id, 'jobsearch_field_empcv_pbase_exp', true)),
                    'pbase_port' => (get_post_meta($package_id, 'jobsearch_field_empcv_pbase_port', true)),
                    'pbase_expertise' => (get_post_meta($package_id, 'jobsearch_field_empcv_pbase_expertise', true)),
                    'pbase_awards' => (get_post_meta($package_id, 'jobsearch_field_empcv_pbase_awards', true)),
                );
                $packge_fields_arr['jobsearch_cand_ppkg_fields_list'] = $cand_pkg_fields;
                //
            }
            return $packge_fields_arr;
        }
    }

    return new Candidate_Profile_Restriction();
}
