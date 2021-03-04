<?php
namespace WP_Jobsearch;

if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('Package_Limits')) {

    class Package_Limits {

        // hook things up
        public function __construct() {
            add_filter('jobsearch_package_fields_arr_before_order_set', array($this, 'cand_pkg_fields_to_order_meta'), 11, 4);
        }

        public static function cand_field_is_locked($field_name) {
            global $wpdb;
            
            $user_id = get_current_user_id();
            $jobsearch__options = get_option('jobsearch_plugin_options');
            $cand_pkgbase_profile = isset($jobsearch__options['cand_pkg_base_profile']) ? $jobsearch__options['cand_pkg_base_profile'] : '';

            $field_locked = true;

            if ($cand_pkgbase_profile != 'on') {
                return false;
            }

            $user_linked_ordrid = get_user_meta($user_id, 'att_profpckg_orderid', true);
            $user_linked_pkgid = get_post_meta($user_linked_ordrid, 'jobsearch_order_package', true);
            if ($user_linked_pkgid > 0) {
                $subs_pkg_orderid = jobsearch_cand_profile_pckg_is_subscribed($user_linked_pkgid, $user_id);
            }
            $bprofile_field_names = array(
                'profile_fields|cover_img',
                'profile_fields|profile_url',
                'profile_fields|public_view',
                'profile_fields|date_of_birth',
                'profile_fields|phone',
                'profile_fields|sector',
                'profile_fields|job_title',
                'profile_fields|salary',
                'profile_fields|about_desc',
            );
            if (in_array($field_name, $bprofile_field_names)) {
                $options_key = 'cand_pkgbase_profile_defields';
                $order_meta_index = 'pbase_profile';

                $field_name_exp = explode('|', $field_name);
                $field_index_name = isset($field_name_exp[1]) ? $field_name_exp[1] : '';
            }

            //
            $cand_pkgbase_dashsecs_arr = apply_filters('jobsearch_cand_dash_menu_in_opts', array(
                'my_profile' => __('My Profile', 'wp-jobsearch'),
                'my_resume' => __('My Resume', 'wp-jobsearch'),
                'applied_jobs' => __('Applied Jobs', 'wp-jobsearch'),
                'cv_manager' => __('CV Manager', 'wp-jobsearch'),
                'fav_jobs' => __('Favorite Jobs', 'wp-jobsearch'),
                'packages' => __('Packages', 'wp-jobsearch'),
                'transactions' => __('Transactions', 'wp-jobsearch'),
                'my_emails' => __('My Emails', 'wp-jobsearch'),
                'following' => __('Following', 'wp-jobsearch'),
                'change_password' => __('Change Password', 'wp-jobsearch'),
            ));
            //var_dump($cand_pkgbase_dashsecs_arr); die;
            $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
            $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
            $post_ids_query .= " ON postmeta.post_id = posts.ID";
            $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
            $post_ids_query .= " AND ((postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='cand') OR (postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='both'));";

            $cusmenu_post_ids = $wpdb->get_col($post_ids_query);
            
            if (!empty($cusmenu_post_ids)) {
                foreach ($cusmenu_post_ids as $cust_dashpage) {
                    $the_page = get_post($cust_dashpage);
                    if (isset($the_page->ID)) {
                        $cand_pkgbase_dashsecs_arr[$the_page->post_name] = $the_page->post_title;
                    }
                }
            }
            $bdashtabs_field_names = array();
            foreach ($cand_pkgbase_dashsecs_arr as $dash_sec_key => $dash_sec_label) {
                $bdashtabs_field_names[] = 'dashtab_fields|' . $dash_sec_key;
            }
            if (in_array($field_name, $bdashtabs_field_names)) {
                $options_key = 'cand_pkgbase_dashtabs_defields';
                $order_meta_index = 'pbase_dashtabs';

                $field_name_exp = explode('|', $field_name);
                $field_index_name = isset($field_name_exp[1]) ? $field_name_exp[1] : '';
            }
            
            //
            $candidate_social_mlinks = isset($jobsearch__options['candidate_social_mlinks']) ? $jobsearch__options['candidate_social_mlinks'] : '';
            $cand_pkgbase_social_arr = array(
                'facebook' => __('Facebook', 'wp-jobsearch'),
                'twitter' => __('Twitter', 'wp-jobsearch'),
                'google_plus' => __('Google Plus', 'wp-jobsearch'),
                'linkedin' => __('Linkedin', 'wp-jobsearch'),
                'dribbble' => __('Dribbble', 'wp-jobsearch'),
            );
            if (!empty($candidate_social_mlinks)) {
                if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
                    $field_counter = 0;
                    foreach ($candidate_social_mlinks['title'] as $cand_social_mlink) {
                        $cand_pkgbase_social_arr['dynm_social' . $field_counter] = $cand_social_mlink;
                        $field_counter++;
                    }
                }
            }
            $bsocialinks_field_names = array();
            foreach ($cand_pkgbase_social_arr as $socilink_key => $socilink_label) {
                $bsocialinks_field_names[] = 'social_links|' . $socilink_key;
            }
            if (in_array($field_name, $bsocialinks_field_names)) {
                $options_key = 'cand_pkgbase_social_defields';
                $order_meta_index = 'pbase_social';

                $field_name_exp = explode('|', $field_name);
                $field_index_name = isset($field_name_exp[1]) ? $field_name_exp[1] : '';
            }
            
            //
            $cand_custom_fields_saved_data = get_option('jobsearch_custom_field_candidate');
            if (is_array($cand_custom_fields_saved_data) && sizeof($cand_custom_fields_saved_data) > 0) {
                $cand_pkgbase_cusfileds_arr = array();
                foreach ($cand_custom_fields_saved_data as $cand_cus_field_key => $cand_cus_field_kdata) {
                    $cusfield_label = isset($cand_cus_field_kdata['label']) ? $cand_cus_field_kdata['label'] : '';
                    $cusfield_name = isset($cand_cus_field_kdata['name']) ? $cand_cus_field_kdata['name'] : '';
                    if ($cusfield_label != '' && $cusfield_name != '') {
                        $cand_pkgbase_cusfileds_arr[$cusfield_name] = $cusfield_label;
                    }
                }
                $bcusfields_field_names = array();
                if (!empty($cand_pkgbase_cusfileds_arr)) {
                    foreach ($cand_pkgbase_cusfileds_arr as $cus_field_key => $cus_field_label) {
                        $bcusfields_field_names[] = 'cusfields|' . $cus_field_key;
                    }
                }
                if (in_array($field_name, $bcusfields_field_names)) {
                    $options_key = 'cand_pkgbase_custom_defields';
                    $order_meta_index = 'pbase_cusfields';

                    $field_name_exp = explode('|', $field_name);
                    $field_index_name = isset($field_name_exp[1]) ? $field_name_exp[1] : '';
                }
            }
            
            // Gen On/Off Fields
            $gen_onoff_fields = array(
                'stats_defields',
                'location_defields',
                'coverltr_defields',
                'resmedu_defields',
                'resmexp_defields',
                'resmport_defields',
                'resmskills_defields',
                'resmawards_defields',
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
                $options_key = 'cand_pkgbase_' . $field_name;
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

        public function cand_pkg_fields_to_order_meta($packge_fields_arr, $order_id, $package_id, $pkg_type) {

            if ($pkg_type == 'candidate_profile') {
                $cand_pkg_fields = array(
                    'pbase_profile' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_profile', true)),
                    'pbase_social' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_social', true)),
                    'pbase_cusfields' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_cusfields', true)),
                    'pbase_dashtabs' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_dashtabs', true)),
                    'pbase_stats' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_stats', true)),
                    'pbase_location' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_location', true)),
                    'pbase_coverltr' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_coverltr', true)),
                    'pbase_resmedu' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_resmedu', true)),
                    'pbase_resmexp' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_resmexp', true)),
                    'pbase_resmport' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_resmport', true)),
                    'pbase_resmskills' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_resmskills', true)),
                    'pbase_resmawards' => (get_post_meta($package_id, 'jobsearch_field_cand_pbase_resmawards', true)),
                );
                $packge_fields_arr['jobsearch_cand_ppkg_fields_list'] = $cand_pkg_fields;

            } else if ($pkg_type == 'employer_profile') {
                $emp_pkg_fields = array(
                    'pbase_profile' => (get_post_meta($package_id, 'jobsearch_field_emp_pbase_profile', true)),
                    'pbase_social' => (get_post_meta($package_id, 'jobsearch_field_emp_pbase_social', true)),
                    'pbase_cusfields' => (get_post_meta($package_id, 'jobsearch_field_emp_pbase_cusfields', true)),
                    'pbase_dashtabs' => (get_post_meta($package_id, 'jobsearch_field_emp_pbase_dashtabs', true)),
                    'pbase_stats' => (get_post_meta($package_id, 'jobsearch_field_emp_pbase_stats', true)),
                    'pbase_location' => (get_post_meta($package_id, 'jobsearch_field_emp_pbase_location', true)),
                    'pbase_accmembs' => (get_post_meta($package_id, 'jobsearch_field_emp_pbase_accmembs', true)),
                    'pbase_team' => (get_post_meta($package_id, 'jobsearch_field_emp_pbase_team', true)),
                    'pbase_award' => (get_post_meta($package_id, 'jobsearch_field_emp_pbase_award', true)),
                    'pbase_affiliation' => (get_post_meta($package_id, 'jobsearch_field_emp_pbase_affiliation', true)),
                    'pbase_gphotos' => (get_post_meta($package_id, 'jobsearch_field_emp_pbase_gphotos', true)),
                );
                $packge_fields_arr['jobsearch_emp_ppkg_fields_list'] = $emp_pkg_fields;
            }

            return $packge_fields_arr;
        }

        public static function dashtab_locked_html($tab, $icon, $name) {

            ob_start();
            ?>
            <a href="javascript:void(0);">
                <i class="<?php echo ($icon) ?>"></i>
                <?php echo ($name) ?> <i class="fa fa-lock"></i>
            </a>
            <?php

            $html = ob_get_clean();
            return $html;
        }
        
        public static function emp_field_is_locked($field_name) {
            global $wpdb;
            $user_id = get_current_user_id();
            $jobsearch__options = get_option('jobsearch_plugin_options');
            $emp_pkgbase_profile = isset($jobsearch__options['emp_pkg_base_profile']) ? $jobsearch__options['emp_pkg_base_profile'] : '';

            $field_locked = true;

            if ($emp_pkgbase_profile != 'on') {
                return false;
            }

            $user_linked_ordrid = get_user_meta($user_id, 'att_profpckg_orderid', true);
            $user_linked_pkgid = get_post_meta($user_linked_ordrid, 'jobsearch_order_package', true);
            if ($user_linked_pkgid > 0) {
                $subs_pkg_orderid = jobsearch_emp_profile_pckg_is_subscribed($user_linked_pkgid, $user_id);
            }
            $bprofile_field_names = array(
                'profile_fields|jobs_cover_img',
                'profile_fields|profile_url',
                'profile_fields|public_view',
                'profile_fields|phone',
                'profile_fields|website',
                'profile_fields|sector',
                'profile_fields|founded_date',
                'profile_fields|about_company',
            );
            if (in_array($field_name, $bprofile_field_names)) {
                $options_key = 'emp_pkgbase_profile_defields';
                $order_meta_index = 'pbase_profile';

                $field_name_exp = explode('|', $field_name);
                $field_index_name = isset($field_name_exp[1]) ? $field_name_exp[1] : '';
            }

            //
            $emp_pkgbase_dashsecs_arr = apply_filters('jobsearch_emp_dash_menu_in_opts', array(
                'company_profile' => __('Company Profile', 'wp-jobsearch'),
                'post_new_job' => __('Post a New Job', 'wp-jobsearch'),
                'manage_jobs' => __('Manage Jobs', 'wp-jobsearch'),
                'all_applicants' => __('All Applicants', 'wp-jobsearch'),
                'saved_candidates' => __('Saved Candidates', 'wp-jobsearch'),
                'packages' => __('Packages', 'wp-jobsearch'),
                'transactions' => __('Transactions', 'wp-jobsearch'),
                'my_emails' => __('My Emails', 'wp-jobsearch'),
                'followers' => __('Followers', 'wp-jobsearch'),
                'change_password' => __('Change Password', 'wp-jobsearch'),
            ));
            $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
            $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
            $post_ids_query .= " ON postmeta.post_id = posts.ID";
            $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
            $post_ids_query .= " AND ((postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='emp') OR (postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='both'));";

            $cusmenu_post_ids = $wpdb->get_col($post_ids_query);
            
            if (!empty($cusmenu_post_ids)) {
                foreach ($cusmenu_post_ids as $cust_dashpage) {
                    $the_page = get_post($cust_dashpage);
                    if (isset($the_page->ID)) {
                        $emp_pkgbase_dashsecs_arr[$the_page->post_name] = $the_page->post_title;
                    }
                }
            }
            $bdashtabs_field_names = array();
            foreach ($emp_pkgbase_dashsecs_arr as $dash_sec_key => $dash_sec_label) {
                $bdashtabs_field_names[] = 'dashtab_fields|' . $dash_sec_key;
            }
            if (in_array($field_name, $bdashtabs_field_names)) {
                $options_key = 'emp_pkgbase_dashtabs_defields';
                $order_meta_index = 'pbase_dashtabs';

                $field_name_exp = explode('|', $field_name);
                $field_index_name = isset($field_name_exp[1]) ? $field_name_exp[1] : '';
            }
            
            //
            $employer_social_mlinks = isset($jobsearch__options['employer_social_mlinks']) ? $jobsearch__options['employer_social_mlinks'] : '';
            $emp_pkgbase_social_arr = array(
                'facebook' => __('Facebook', 'wp-jobsearch'),
                'twitter' => __('Twitter', 'wp-jobsearch'),
                'google_plus' => __('Google Plus', 'wp-jobsearch'),
                'linkedin' => __('Linkedin', 'wp-jobsearch'),
                'dribbble' => __('Dribbble', 'wp-jobsearch'),
            );
            if (!empty($employer_social_mlinks)) {
                if (isset($employer_social_mlinks['title']) && is_array($employer_social_mlinks['title'])) {
                    $field_counter = 0;
                    foreach ($employer_social_mlinks['title'] as $emp_social_mlink) {
                        $emp_pkgbase_social_arr['dynm_social' . $field_counter] = $emp_social_mlink;
                        $field_counter++;
                    }
                }
            }
            $bsocialinks_field_names = array();
            foreach ($emp_pkgbase_social_arr as $socilink_key => $socilink_label) {
                $bsocialinks_field_names[] = 'social_links|' . $socilink_key;
            }
            if (in_array($field_name, $bsocialinks_field_names)) {
                $options_key = 'emp_pkgbase_social_defields';
                $order_meta_index = 'pbase_social';

                $field_name_exp = explode('|', $field_name);
                $field_index_name = isset($field_name_exp[1]) ? $field_name_exp[1] : '';
            }
            
            //
            $emp_custom_fields_saved_data = get_option('jobsearch_custom_field_employer');
            if (is_array($emp_custom_fields_saved_data) && sizeof($emp_custom_fields_saved_data) > 0) {
                $emp_pkgbase_cusfileds_arr = array();
                foreach ($emp_custom_fields_saved_data as $emp_cus_field_key => $emp_cus_field_kdata) {
                    $cusfield_label = isset($emp_cus_field_kdata['label']) ? $emp_cus_field_kdata['label'] : '';
                    $cusfield_name = isset($emp_cus_field_kdata['name']) ? $emp_cus_field_kdata['name'] : '';
                    if ($cusfield_label != '' && $cusfield_name != '') {
                        $emp_pkgbase_cusfileds_arr[$cusfield_name] = $cusfield_label;
                    }
                }
                $bcusfields_field_names = array();
                if (!empty($emp_pkgbase_cusfileds_arr)) {
                    foreach ($emp_pkgbase_cusfileds_arr as $cus_field_key => $cus_field_label) {
                        $bcusfields_field_names[] = 'cusfields|' . $cus_field_key;
                    }
                }
                if (in_array($field_name, $bcusfields_field_names)) {
                    $options_key = 'emp_pkgbase_custom_defields';
                    $order_meta_index = 'pbase_cusfields';

                    $field_name_exp = explode('|', $field_name);
                    $field_index_name = isset($field_name_exp[1]) ? $field_name_exp[1] : '';
                }
            }
            
            // Gen On/Off Fields
            $gen_onoff_fields = array(
                'stats_defields',
                'location_defields',
                'accmembs_defields',
                'team_defields',
                'award_defields',
                'affiliation_defields',
                'gphotos_defields',
            );

            if (isset($field_index_name)) {
                $options_allowfield_arr = isset($jobsearch__options[$options_key]) ? $jobsearch__options[$options_key] : '';
                if (!empty($options_allowfield_arr) && is_array($options_allowfield_arr) && in_array($field_index_name, $options_allowfield_arr)) {
                    $field_locked = false;
                }
                if (isset($subs_pkg_orderid) && $subs_pkg_orderid > 0) {
                    $allowfield_arr = get_post_meta($subs_pkg_orderid, 'jobsearch_emp_ppkg_fields_list', true);
                    if (isset($allowfield_arr[$order_meta_index]) && is_array($allowfield_arr[$order_meta_index]) && in_array($field_index_name, $allowfield_arr[$order_meta_index])) {
                        $field_locked = false;
                    }
                }
            } else if ($field_name != '' && in_array($field_name, $gen_onoff_fields)) {
                $options_key = 'emp_pkgbase_' . $field_name;
                $order_meta_index = 'pbase_' . (str_replace(array('_defields'), array(''), $field_name));
                
                $options_allowfield = isset($jobsearch__options[$options_key]) ? $jobsearch__options[$options_key] : '';
                if ($options_allowfield == 'on') {
                    $field_locked = false;
                }
                if (isset($subs_pkg_orderid) && $subs_pkg_orderid > 0) {
                    $pkgorder_allowfield = get_post_meta($subs_pkg_orderid, 'jobsearch_emp_ppkg_fields_list', true);
                    if (isset($pkgorder_allowfield[$order_meta_index]) && $pkgorder_allowfield[$order_meta_index] == 'on') {
                        $field_locked = false;
                    }
                }
            }

            return $field_locked;
        }

        public static function emp_gen_locked_html() {

            $html = esc_html__('This Field is locked', 'wp-jobsearch');
            return $html;
        }

        public function emp_field_locked_html($cus_html = '') {

            $html = self::emp_gen_locked_html();
            if ($cus_html != '') {
                $html = $cus_html;
            }

            return $html;
        }

    }

    return new Package_Limits();
}
