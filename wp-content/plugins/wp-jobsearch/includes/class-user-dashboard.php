<?php

class JobSearch_User_Dashboard_Settings
{

    // Dashboard Path
    protected $dashboard_template;
    // Candidate Dashboard Path
    protected $candidate_dashboard_template;
    // Employer Dashboard Path
    protected $employer_dashboard_template;
    // user types
    protected $dashboard_user_types = array('candidate', 'employer');

    /*
     * Class Construct
     * @return
     */

    public function __construct() {
        $this->dashboard_template = 'user-dashboard';
        $this->candidate_dashboard_template = 'candidate';
        $this->employer_dashboard_template = 'employer';

        add_action('wp_ajax_jobsearch_user_dashboard_show_template', array($this, 'show_template_part'));
        add_action('jobsearch_user_dashboard_header', array($this, 'dashboard_header'));
        
        add_action('jobsearch_enqueue_dashboard_styles', array($this, 'dashboard_styles'));

        add_action('wp_ajax_jobsearch_employer_cover_img_remove', array($this, 'employer_cover_img_remove'));
        add_action('wp_ajax_jobsearch_candidate_cover_img_remove', array($this, 'candidate_cover_img_remove'));

        add_action('wp_ajax_jobsearch_user_update_profileslug', array($this, 'user_update_profileslug'));

        add_action('wp_ajax_jobsearch_add_duplicate_post_byuser', array($this, 'duplicate_job'));
        
        add_action('wp_ajax_jobsearch_userdash_change_email_read_status', array($this, 'change_email_read_status'));

        //
        add_action('wp_ajax_jobsearch_dashboard_updating_user_avatar_img', array($this, 'user_avatar_upload_ajax'));
        add_action('wp_ajax_jobsearch_userdash_profile_delete_pthumb', array($this, 'user_avatar_profile_delete_pthumb'));
        add_action('wp_ajax_jobsearch_dashboard_updating_employer_cover_img', array($this, 'employer_cover_img_upload'));
        add_action('wp_ajax_jobsearch_dashboard_updating_candidate_cover_img', array($this, 'candidate_cover_img_upload'));

        add_action('wp_ajax_jobsearch_dashboard_updating_candidate_cv_file', array($this, 'candidate_cv_upload_ajax'));
        //
        add_action('wp_ajax_jobsearch_dashboard_adding_portfolio_img_url', array($this, 'dashboard_adding_portfolio_img_url'));
        //
        add_action('wp_ajax_jobsearch_dashboard_adding_team_img_url', array($this, 'dashboard_adding_team_img_url'));

        //
        add_action('wp_ajax_jobsearch_user_dashboard_candidate_delete', array($this, 'user_candidate_delete'));
        //
        add_action('wp_ajax_jobsearch_remove_user_fav_candidate_from_list', array($this, 'remove_user_fav_candidate_from_list'));

        add_action('wp_ajax_jobsearch_remove_user_applied_candidate_from_list', array($this, 'remove_user_applied_candidate_from_list'));

        add_action('wp_ajax_jobsearch_remove_user_fav_job_from_list', array($this, 'remove_candidate_fav_job_from_list'));

        add_action('wp_ajax_jobsearch_remove_user_applied_job_from_list', array($this, 'remove_candidate_applied_job_from_list'));
        //
        add_action('wp_ajax_jobsearch_userdash_rem_emp_followin', array($this, 'canddash_rem_emp_followin'));
        //
        add_action('wp_ajax_jobsearch_add_resume_education_to_list', array($this, 'add_resume_education_to_list'));
        //
        add_action('wp_ajax_jobsearch_add_resume_experience_to_list', array($this, 'add_resume_experience_to_list'));
        //
        add_action('wp_ajax_jobsearch_add_resume_skill_to_list', array($this, 'add_resume_skill_to_list'));
        //
        add_action('wp_ajax_jobsearch_add_resume_lang_to_list', array($this, 'add_resume_lang_to_list'));
        //
        add_action('wp_ajax_jobsearch_add_resume_portfolio_to_list', array($this, 'add_resume_portfolio_to_list'));
        //
        add_action('wp_ajax_jobsearch_add_team_member_to_list', array($this, 'add_team_member_to_list'));
        //
        add_action('wp_ajax_jobsearch_add_emp_awards_to_list', array($this, 'add_emp_awards_to_list'));
        //
        add_action('wp_ajax_jobsearch_add_emp_affiliations_to_list', array($this, 'add_emp_affiliations_to_list'));
        //
        add_action('wp_ajax_jobsearch_add_resume_award_to_list', array($this, 'add_resume_award_to_list'));
        //
        add_action('wp_ajax_jobsearch_candidate_contact_form_submit', array($this, 'candidate_contact_form_submit'));
        add_action('wp_ajax_nopriv_jobsearch_candidate_contact_form_submit', array($this, 'candidate_contact_form_submit'));
        //
        add_action('wp_ajax_jobsearch_employer_contact_form_submit', array($this, 'employer_contact_form_submit'));
        add_action('wp_ajax_nopriv_jobsearch_employer_contact_form_submit', array($this, 'employer_contact_form_submit'));

        add_action('wp_ajax_jobsearch_act_user_cv_delete', array($this, 'candidate_cv_delete_ajax'));

        add_action('wp_ajax_jobsearch_user_profile_delete_for', array($this, 'user_profile_delete_for'));

        //
        add_action('wp_ajax_jobsearch_doing_mjobs_feature_job', array($this, 'doing_mjobs_feature_job'));
        add_action('wp_ajax_nopriv_jobsearch_doing_mjobs_feature_job', array($this, 'doing_mjobs_feature_job'));
        
        // Email change by user check validation
        add_action('wp_ajax_jobsearch_user_change_email_check_avail', array($this, 'user_change_email_check_avail'));
        add_action('wp_ajax_nopriv_jobsearch_user_change_email_check_avail', array($this, 'user_change_email_check_avail'));

        //
        add_action('wp_ajax_jobsearch_doing_feat_job_with_alorder', array($this, 'doing_feat_job_with_alorder'));
        add_action('wp_ajax_nopriv_jobsearch_doing_feat_job_with_alorder', array($this, 'doing_feat_job_with_alorder'));
    }
    
    public function dashboard_styles() {
        global $jobsearch_plugin_options;
        $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';
        if ($location_map_type == 'mapbox') {
            wp_enqueue_style('mapbox-style', 'https://api.tiles.mapbox.com/mapbox-gl-js/v1.6.0/mapbox-gl.css', array(), JobSearch_plugin::get_version());
        }
        wp_enqueue_style('fancybox', jobsearch_plugin_get_url('css/fancybox.css'), array(), JobSearch_plugin::get_version());
        wp_enqueue_style('jobsearch-intlTelInput', jobsearch_plugin_get_url('css/intlTelInput.css'), array(), JobSearch_plugin::get_version());
        wp_enqueue_style('jobsearch-morris', jobsearch_plugin_get_url('css/morris.css'), array(), JobSearch_plugin::get_version());
        wp_enqueue_style('jobsearch-tag-it', jobsearch_plugin_get_url('css/jquery.tagit.css'), array(), JobSearch_plugin::get_version());
        wp_enqueue_style('dropzone-style', jobsearch_plugin_get_url('css/dropzone.min.css'), array(), JobSearch_plugin::get_version());
        wp_enqueue_style('datetimepicker-style', jobsearch_plugin_get_url('css/jquery.datetimepicker.css'), array(), JobSearch_plugin::get_version());
        //
        do_action('jobsearch_dashbord_instyles_list_aftr');
    }
    
    public function canddash_rem_emp_followin() {
        if (isset($_POST['emp_id']) && $_POST['emp_id'] != '') {
            $emp_id = $_POST['emp_id'];
            $emp_user_id = jobsearch_get_employer_user_id($emp_id);
            $user_id = get_current_user_id();
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            $cand_followin_list = get_post_meta($candidate_id, 'jobsearch_cand_followins_list', true);
            $cand_followin_list = $cand_followin_list != '' ? explode(',', $cand_followin_list) : '';
            if (!empty($cand_followin_list) && is_array($cand_followin_list) && in_array($emp_id, $cand_followin_list)) {
                $new_followin_list = array();
                foreach ($cand_followin_list as $folow_emp_id) {
                    if ($folow_emp_id != $emp_id) {
                        $new_followin_list[] = $folow_emp_id;
                    }
                }
                if (!empty($new_followin_list)) {
                    $cand_followin_list = implode(',', $new_followin_list);
                } else {
                    $cand_followin_list = '';
                }
                update_post_meta($candidate_id, 'jobsearch_cand_followins_list', $cand_followin_list);
                //
                jobsearch_remove_user_meta_list($candidate_id, 'jobsearch-user-followins-list', $emp_user_id);
            }
            
            echo json_encode(array('success' => '1'));
            die;
        }
        echo json_encode(array('success' => '0'));
        die;
    }

    public function user_update_profileslug() {
        if (jobsearch_candidate_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to do this.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        if (jobsearch_employer_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to do this.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        if (isset($_POST['updte_slug']) && $_POST['updte_slug'] != '') {
            $user_profile_slug = sanitize_text_field($_POST['updte_slug']);
            $user_profile_slug = sanitize_title($user_profile_slug);
            $user_id = get_current_user_id();
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            if ($user_is_candidate) {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                $up_post = array(
                    'ID' => $candidate_id,
                    'post_name' => $user_profile_slug,
                );
                wp_update_post($up_post);

                //
                $post_obj = get_post($candidate_id);
                $user_profile_url = isset($post_obj->post_name) ? $post_obj->post_name : '';
                echo json_encode(array('suc' => '1', 'updated_slug' => urldecode($user_profile_url)));
                die;
            }
            $user_is_employer = jobsearch_user_is_employer($user_id);
            if ($user_is_employer) {
                $employer_id = jobsearch_get_user_employer_id($user_id);
                $up_post = array(
                    'ID' => $employer_id,
                    'post_name' => $user_profile_slug,
                );
                wp_update_post($up_post);

                //
                $post_obj = get_post($employer_id);
                $user_profile_url = isset($post_obj->post_name) ? $post_obj->post_name : '';
                echo json_encode(array('suc' => '1', 'updated_slug' => urldecode($user_profile_url)));
                die;
            }
        }
        echo json_encode(array('suc' => '0'));
        die;
    }

    /*
     * User profile delete
     * @return bool
     */

    public function user_profile_delete_for()
    {
        global $jobsearch_plugin_options;
        $u_pass = isset($_POST['u_pass']) ? $_POST['u_pass'] : '';
        $user_id = get_current_user_id();
        $user_obj = get_user_by('ID', $user_id);
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        if ($u_pass == '') {
            echo json_encode(array('success' => '0', 'msg' => esc_html__('Please Enter the password.', 'wp-jobsearch')));
            wp_die();
        }
        if ($user_obj && wp_check_password($u_pass, $user_obj->data->user_pass, $user_id)) {
            // good
        } else {
            echo json_encode(array('success' => '0', 'msg' => esc_html__('Please Enter the correct password.', 'wp-jobsearch')));
            wp_die();
        }

        if ($user_is_employer) {
            $employer_id = jobsearch_get_user_employer_id($user_id);
            //
            $demo_employer = isset($jobsearch_plugin_options['demo_employer']) ? $jobsearch_plugin_options['demo_employer'] : '';
            if ($demo_employer != '') {
                $_demo_user_obj = get_user_by('login', $demo_employer);
                $_demo_user_id = isset($_demo_user_obj->ID) ? $_demo_user_obj->ID : '';
                if ($user_id == $_demo_user_id) {
                    echo json_encode(array('success' => '0', 'msg' => esc_html__('You are not allowed to delete profile.', 'wp-jobsearch')));
                    wp_die();
                }
            }
            //
            $args = array(
                'post_type' => 'job',
                'posts_per_page' => '-1',
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_posted_by',
                        'value' => $employer_id,
                        'compare' => '=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            wp_reset_postdata();
            if (isset($jobs_query->posts) && !empty($jobs_query->posts)) {
                $all_posts = $jobs_query->posts;
                foreach ($all_posts as $_post_id) {
                    wp_delete_post($_post_id, true);
                }
            }
            wp_delete_user($user_id);
            wp_delete_post($employer_id, true);
            echo json_encode(array('success' => '1', 'msg' => esc_html__('Your profile deleted successfully.', 'wp-jobsearch')));
        } else if ($user_is_candidate) {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            //
            $demo_candidate = isset($jobsearch_plugin_options['demo_candidate']) ? $jobsearch_plugin_options['demo_candidate'] : '';
            if ($demo_candidate != '') {
                $_demo_user_obj = get_user_by('login', $demo_candidate);
                $_demo_user_id = isset($_demo_user_obj->ID) ? $_demo_user_obj->ID : '';
                if ($user_id == $_demo_user_id) {
                    echo json_encode(array('success' => '0', 'msg' => esc_html__('You are not allowed to delete profile.', 'wp-jobsearch')));
                    wp_die();
                }
            }
            //
            wp_delete_user($user_id);
            wp_delete_post($candidate_id, true);
            echo json_encode(array('success' => '1', 'msg' => esc_html__('Your profile deleted successfully.', 'wp-jobsearch')));
        } else {
            wp_delete_user($user_id);
            echo json_encode(array('success' => '1', 'msg' => esc_html__('Your profile deleted successfully.', 'wp-jobsearch')));
        }
        wp_die();
    }

    /*
     * User profile info
     * @return html
     */

    public function show_template_part($user_type = '', $template_name = '')
    {

        $ajax = false;
        if ($user_type == '' && $template_name == '') {
            $ajax = true;
            $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';
            $template_name = isset($_POST['template_name']) ? $_POST['template_name'] : '';
        }

        $template_ext = '';
        if ($user_type == 'employer') {
            $template_ext = $this->employer_dashboard_template;
        }
        if ($user_type == 'candidate') {
            $template_ext = $this->candidate_dashboard_template;
        }

        if ($user_type != '' && !in_array($user_type, $this->dashboard_user_types)) {
            return false;
        }

        ob_start();
        jobsearch_get_template_part('user', $template_name, $this->dashboard_template . ($template_ext != '' ? '/' . $template_ext : ''));
        $html = ob_get_clean();

        if ($ajax == true) {
            echo json_encode(array('template_html' => $html));
            wp_die();
        } else {
            return $html;
        }
    }

    /*
     * User dashboard header
     * @return html
     */

    public function dashboard_header()
    {
        global $jobsearch_plugin_options, $diff_form_errs;

        $diff_form_errs = array();

        $signup_page_id = isset($jobsearch_plugin_options['user-login-template-page']) ? $jobsearch_plugin_options['user-login-template-page'] : '';
        $signup_page_id = jobsearch__get_post_id($signup_page_id, 'page');
        $signup_page_url = get_permalink($signup_page_id);

        if (!is_user_logged_in()) {
            if ($signup_page_id > 0 && !empty($signup_page_url)) {
                wp_safe_redirect($signup_page_url);
            } else {
                wp_safe_redirect(home_url('/'));
            }
        }

        $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_id = $user_dashboard_page = jobsearch__get_post_id($page_id, 'page');
        $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

        $current_user = wp_get_current_user();
        $user_id = get_current_user_id();
        $user_obj = get_user_by('ID', $user_id);

        $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
        $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
        $user_email = isset($user_obj->user_email) ? $user_obj->user_email : '';

        //
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);
        //
        if ($user_is_employer) {
            $employer_id = jobsearch_get_user_employer_id($user_id);
            if (jobsearch_employer_not_allow_to_mod()) {
                $diff_form_errs['user_not_allow_mod'] = true;
                return false;
            }
        } else if ($user_is_candidate) {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            if (jobsearch_candidate_not_allow_to_mod()) {
                $diff_form_errs['user_not_allow_mod'] = true;
                return false;
            }
        }
        //

        if (isset($_POST['user_resume_form']) && $_POST['user_resume_form'] == '1') {
            $_POST = jobsearch_input_post_vals_validate($_POST);
            
            if (isset($_POST['jobsearch_field_resume_cover_letter'])) {
                update_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', jobsearch_esc_wp_editor($_POST['jobsearch_field_resume_cover_letter']));
            }

            // candidate skills
            $cand_skills_switch = isset($jobsearch_plugin_options['cand_skills_switch']) ? $jobsearch_plugin_options['cand_skills_switch'] : '';
            if ($cand_skills_switch == 'on') {
                $cand_max_skills_allow = isset($jobsearch_plugin_options['cand_max_skills']) && $jobsearch_plugin_options['cand_max_skills'] > 0 ? $jobsearch_plugin_options['cand_max_skills'] : 5;
                $tags_limit = $cand_max_skills_allow;
                $cand_skills = isset($_POST['get_cand_skills']) && !empty($_POST['get_cand_skills']) ? $_POST['get_cand_skills'] : array();
                if (absint($tags_limit) > 0 && !empty($cand_skills) && count($cand_skills) > $tags_limit) {
                    $cand_skills = array_slice($cand_skills, 0, $tags_limit, true);
                }
                wp_set_post_terms($candidate_id, $cand_skills, 'skill', FALSE);
                update_post_meta($candidate_id, 'jobsearch_cand_skills', $cand_skills);
            }

            //
            if (isset($_POST['jobsearch_field_education_title'])) {
                update_post_meta($candidate_id, 'jobsearch_field_education_title', jobsearch_esc_html($_POST['jobsearch_field_education_title']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_education_title', '');
            }
            if (isset($_POST['jobsearch_field_education_start_date'])) {
                update_post_meta($candidate_id, 'jobsearch_field_education_start_date', jobsearch_esc_html($_POST['jobsearch_field_education_start_date']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_education_start_date', '');
            }
            if (isset($_POST['jobsearch_field_education_end_date'])) {
                update_post_meta($candidate_id, 'jobsearch_field_education_end_date', jobsearch_esc_html($_POST['jobsearch_field_education_end_date']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_education_end_date', '');
            }
            if (isset($_POST['jobsearch_field_education_date_prsnt'])) {
                update_post_meta($candidate_id, 'jobsearch_field_education_date_prsnt', jobsearch_esc_html($_POST['jobsearch_field_education_date_prsnt']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_education_date_prsnt', '');
            }
            if (isset($_POST['jobsearch_field_education_academy'])) {
                update_post_meta($candidate_id, 'jobsearch_field_education_academy', jobsearch_esc_html($_POST['jobsearch_field_education_academy']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_education_academy', '');
            }
            if (isset($_POST['jobsearch_field_education_description'])) {
                update_post_meta($candidate_id, 'jobsearch_field_education_description', jobsearch_esc_html($_POST['jobsearch_field_education_description']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_education_description', '');
            }

            //
            if (isset($_POST['jobsearch_field_experience_title'])) {
                update_post_meta($candidate_id, 'jobsearch_field_experience_title', jobsearch_esc_html($_POST['jobsearch_field_experience_title']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_experience_title', '');
            }
            if (isset($_POST['jobsearch_field_experience_start_date'])) {
                update_post_meta($candidate_id, 'jobsearch_field_experience_start_date', jobsearch_esc_html($_POST['jobsearch_field_experience_start_date']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_experience_start_date', '');
            }
            if (isset($_POST['jobsearch_field_experience_end_date'])) {
                update_post_meta($candidate_id, 'jobsearch_field_experience_end_date', jobsearch_esc_html($_POST['jobsearch_field_experience_end_date']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_experience_end_date', '');
            }
            if (isset($_POST['jobsearch_field_experience_date_prsnt'])) {
                update_post_meta($candidate_id, 'jobsearch_field_experience_date_prsnt', jobsearch_esc_html($_POST['jobsearch_field_experience_date_prsnt']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_experience_date_prsnt', '');
            }
            if (isset($_POST['jobsearch_field_experience_company'])) {
                update_post_meta($candidate_id, 'jobsearch_field_experience_company', jobsearch_esc_html($_POST['jobsearch_field_experience_company']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_experience_company', '');
            }
            if (isset($_POST['jobsearch_field_experience_description'])) {
                update_post_meta($candidate_id, 'jobsearch_field_experience_description', jobsearch_esc_html($_POST['jobsearch_field_experience_description']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_experience_description', '');
            }

            //
            if (isset($_POST['jobsearch_field_skill_title'])) {
                update_post_meta($candidate_id, 'jobsearch_field_skill_title', jobsearch_esc_html($_POST['jobsearch_field_skill_title']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_skill_title', '');
            }
            if (isset($_POST['jobsearch_field_skill_percentage'])) {
                update_post_meta($candidate_id, 'jobsearch_field_skill_percentage', jobsearch_esc_html($_POST['jobsearch_field_skill_percentage']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_skill_percentage', '');
            }
            if (isset($_POST['jobsearch_field_skill_desc'])) {
                update_post_meta($candidate_id, 'jobsearch_field_skill_desc', jobsearch_esc_html($_POST['jobsearch_field_skill_desc']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_skill_desc', '');
            }

            //
            if (isset($_POST['jobsearch_field_lang_title'])) {
                update_post_meta($candidate_id, 'jobsearch_field_lang_title', jobsearch_esc_html($_POST['jobsearch_field_lang_title']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_lang_title', '');
            }
            if (isset($_POST['jobsearch_field_lang_percentage'])) {
                update_post_meta($candidate_id, 'jobsearch_field_lang_percentage', jobsearch_esc_html($_POST['jobsearch_field_lang_percentage']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_lang_percentage', '');
            }
            if (isset($_POST['jobsearch_field_lang_level'])) {
                update_post_meta($candidate_id, 'jobsearch_field_lang_level', jobsearch_esc_html($_POST['jobsearch_field_lang_level']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_lang_level', '');
            }

            //
            if (isset($_POST['jobsearch_field_award_title'])) {
                update_post_meta($candidate_id, 'jobsearch_field_award_title', jobsearch_esc_html($_POST['jobsearch_field_award_title']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_award_title', '');
            }
            if (isset($_POST['jobsearch_field_award_year'])) {
                update_post_meta($candidate_id, 'jobsearch_field_award_year', jobsearch_esc_html($_POST['jobsearch_field_award_year']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_award_year', '');
            }
            if (isset($_POST['jobsearch_field_award_description'])) {
                update_post_meta($candidate_id, 'jobsearch_field_award_description', jobsearch_esc_html($_POST['jobsearch_field_award_description']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_award_description', '');
            }

            //
            if (isset($_POST['jobsearch_field_portfolio_title'])) {
                update_post_meta($candidate_id, 'jobsearch_field_portfolio_title', jobsearch_esc_html($_POST['jobsearch_field_portfolio_title']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_portfolio_title', '');
            }
            if (isset($_POST['jobsearch_field_portfolio_image'])) {
                update_post_meta($candidate_id, 'jobsearch_field_portfolio_image', jobsearch_esc_html($_POST['jobsearch_field_portfolio_image']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_portfolio_image', '');
            }
            if (isset($_POST['jobsearch_field_portfolio_url'])) {
                update_post_meta($candidate_id, 'jobsearch_field_portfolio_url', jobsearch_esc_html($_POST['jobsearch_field_portfolio_url']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_portfolio_url', '');
            }
            if (isset($_POST['jobsearch_field_portfolio_vurl'])) {
                update_post_meta($candidate_id, 'jobsearch_field_portfolio_vurl', jobsearch_esc_html($_POST['jobsearch_field_portfolio_vurl']));
            } else {
                update_post_meta($candidate_id, 'jobsearch_field_portfolio_vurl', '');
            }

            jobsearch_candidate_skill_percent_count($user_id, 'none');
            jobsearch_addto_candidate_exp_inyears($candidate_id);

            do_action('jobsearch_candidate_dash_resume_save_after', $candidate_id);
        }

        if (isset($_POST['employer_shrestypes_form']) && $_POST['employer_shrestypes_form'] == '1') {
            if (isset($_POST['emp_ressh_types'])) {
                $emp_ressh_types = $_POST['emp_ressh_types'];
                if (!empty($emp_ressh_types) && is_array($emp_ressh_types) && $user_is_employer) {

                    $emp_resshtyps_actarr = array();
                    $emp_ressh_tcount = 1;
                    foreach ($emp_ressh_types as $emp_ressh_type) {
                        if ($emp_ressh_type != '') {
                            $emp_resshtyps_actarr['cat_' . $emp_ressh_tcount] = sanitize_text_field($emp_ressh_type);
                            $emp_ressh_tcount++;
                        }
                    }

                    update_post_meta($employer_id, 'emp_resumesh_types', ($emp_resshtyps_actarr));
                }
            }
        }

        if (isset($_POST['user_settings_form']) && $_POST['user_settings_form'] == '1') {

            $_POST = jobsearch_input_post_vals_validate($_POST);
            
            if (isset($_POST['user_email_field'])) {
                $user_email_input = $_POST['user_email_field'];
                if ($user_email_input != '' && filter_var($user_email_input, FILTER_VALIDATE_EMAIL)) {
                    if (email_exists($user_email_input) && $user_email_input != $user_email) {
                        $diff_form_errs['user_email_error'] = esc_html__('This email address is already taken.', 'wp-jobsearch');
                    }
                } else {
                    $diff_form_errs['user_email_error'] = esc_html__('Please enter the correct email address.', 'wp-jobsearch');
                }
            }
            
            if (empty($diff_form_errs)) {
                global $allowedposttags;
                /*
                 * Allowed Tags for wp editor
                 * */

                $allowed_atts = array(
                    'align' => array(),
                    'class' => array(),
                    'type' => array(),
                    'id' => array(),
                    'style' => array(),
                    'src' => array(),
                    'alt' => array(),
                    'href' => array(),
                    'rel' => array(),
                    'target' => array(),
                    'width' => array(),
                    'height' => array(),
                    'title' => array(),
                    'data' => array(),
                );
                $allowedposttags['form'] = $allowed_atts;
                $allowedposttags['label'] = $allowed_atts;
                $allowedposttags['input'] = $allowed_atts;
                $allowedposttags['textarea'] = $allowed_atts;
                $allowedposttags['iframe'] = $allowed_atts;
                $allowedposttags['style'] = $allowed_atts;
                $allowedposttags['strong'] = $allowed_atts;
                $allowedposttags['small'] = $allowed_atts;
                $allowedposttags['table'] = $allowed_atts;
                $allowedposttags['span'] = $allowed_atts;
                $allowedposttags['abbr'] = $allowed_atts;
                $allowedposttags['code'] = $allowed_atts;
                $allowedposttags['pre'] = $allowed_atts;
                $allowedposttags['div'] = $allowed_atts;
                $allowedposttags['img'] = $allowed_atts;
                $allowedposttags['h1'] = $allowed_atts;
                $allowedposttags['h2'] = $allowed_atts;
                $allowedposttags['h3'] = $allowed_atts;
                $allowedposttags['h4'] = $allowed_atts;
                $allowedposttags['h5'] = $allowed_atts;
                $allowedposttags['h6'] = $allowed_atts;
                $allowedposttags['ol'] = $allowed_atts;
                $allowedposttags['ul'] = $allowed_atts;
                $allowedposttags['li'] = $allowed_atts;
                $allowedposttags['em'] = $allowed_atts;
                $allowedposttags['hr'] = $allowed_atts;
                $allowedposttags['br'] = $allowed_atts;
                $allowedposttags['tr'] = $allowed_atts;
                $allowedposttags['td'] = $allowed_atts;
                $allowedposttags['p'] = $allowed_atts;
                $allowedposttags['a'] = $allowed_atts;
                $allowedposttags['b'] = $allowed_atts;
                $allowedposttags['i'] = $allowed_atts;

                $allowed_tags = wp_kses_allowed_html('post');
                $display_name = isset($_POST['display_name']) ? jobsearch_esc_html($_POST['display_name']) : '';
                $user_bio = isset($_POST['user_bio']) ? wp_kses(stripslashes_deep($_POST['user_bio']), $allowed_tags) : '';
                $user_website = isset($_POST['user_website']) ? jobsearch_esc_html($_POST['user_website']) : '';
                $u_firstname = isset($_POST['u_firstname']) ? jobsearch_esc_html($_POST['u_firstname']) : $user_obj->first_name;
                $u_lastname = isset($_POST['u_lastname']) ? jobsearch_esc_html($_POST['u_lastname']) : $user_obj->last_name;
                if ($u_firstname != '' && $display_name == '') {
                    $display_name = $u_firstname;
                    if ($u_lastname != '') {
                        $display_name .= ' ' . $u_lastname;
                    }
                }
                $user_def_array = array(
                    'ID' => $user_id,
                    'first_name' => $u_firstname,
                    'last_name' => $u_lastname,
                    'description' => $user_bio,
                    'user_url' => $user_website,
                );
                if (isset($display_name) && $display_name != '') {
                    $user_def_array['display_name'] = $display_name;
                }
                if (isset($_POST['user_email_field']) && $_POST['user_email_field'] != '') {
                    $user_def_array['user_email'] = $_POST['user_email_field'];
                }

                wp_update_user($user_def_array);
                //

                if ($user_is_candidate) {

                    if (isset($_POST['cand_user_facebook_url'])) {
                        $fb_social_url = esc_url($_POST['cand_user_facebook_url']);
                        update_post_meta($candidate_id, 'jobsearch_field_user_facebook_url', $fb_social_url);
                    }
                    if (isset($_POST['cand_user_twitter_url'])) {
                        $twitter_social_url = esc_url($_POST['cand_user_twitter_url']);
                        update_post_meta($candidate_id, 'jobsearch_field_user_twitter_url', $twitter_social_url);
                    }
                    if (isset($_POST['cand_user_linkedin_url'])) {
                        $linkedin_social_url = esc_url($_POST['cand_user_linkedin_url']);
                        update_post_meta($candidate_id, 'jobsearch_field_user_linkedin_url', $linkedin_social_url);
                    }
                    if (isset($_POST['cand_user_dribbble_url'])) {
                        $dribbble_social_url = esc_url($_POST['cand_user_dribbble_url']);
                        update_post_meta($candidate_id, 'jobsearch_field_user_dribbble_url', $dribbble_social_url);
                    }

                    // Dynamic Candidate Social Fields /////
                    $candidate_social_mlinks = isset($jobsearch_plugin_options['candidate_social_mlinks']) ? $jobsearch_plugin_options['candidate_social_mlinks'] : '';
                    if (!empty($candidate_social_mlinks)) {
                        if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
                            $field_counter = 0;
                            foreach ($candidate_social_mlinks['title'] as $field_title_val) {
                                if (isset($_POST['candidate_dynm_social' . $field_counter])) {
                                    $msocil_linkurl = esc_url($_POST['candidate_dynm_social' . $field_counter]);
                                    update_post_meta($candidate_id, 'jobsearch_field_dynm_social' . $field_counter, $msocil_linkurl);
                                }
                                $field_counter++;
                            }
                        }
                    }
                    //
                    // Public profile view saving
                    if (isset($_POST['jobsearch_field_user_public_pview']) && !empty($_POST['jobsearch_field_user_public_pview'])) {
                        $user_public_pview = $_POST['jobsearch_field_user_public_pview'];
                        if ($user_public_pview == 'no') {
                            $user_up_post = array(
                                'ID' => $candidate_id,
                                'post_type' => 'candidate',
                                'post_status' => 'draft',
                            );
                            wp_update_post($user_up_post);
                        } else {
                            $user_up_post = array(
                                'ID' => $candidate_id,
                                'post_type' => 'candidate',
                                'post_status' => 'publish',
                            );
                            wp_update_post($user_up_post);
                        }
                    }
                    // updating user email to member
                    update_post_meta($candidate_id, 'jobsearch_field_user_email', ($user_email));
                    //
                    if ($display_name != '') {
                        $up_post = array(
                            'ID' => $candidate_id,
                            'post_title' => wp_strip_all_tags($display_name),
                        );
                        wp_update_post($up_post);
                        //
                        update_post_meta($candidate_id, 'member_display_name', wp_strip_all_tags($display_name));
                    }

                    $up_post = array(
                        'ID' => $candidate_id,
                        'post_content' => $user_bio,
                    );
                    wp_update_post($up_post);

                    //
                    if (isset($_POST['user_sector'])) {
                        $user_sector = ($_POST['user_sector']);
                        $user_sector = is_array($user_sector) ? $user_sector : array($user_sector);
                        wp_set_post_terms($candidate_id, $user_sector, 'sector', false);
                    }

                    //
                    if (isset($_POST['candidate_salary_type'])) {
                        update_post_meta($candidate_id, 'jobsearch_field_candidate_salary_type', jobsearch_esc_html($_POST['candidate_salary_type']));
                    }
                    if (isset($_POST['candidate_salary'])) {
                        update_post_meta($candidate_id, 'jobsearch_field_candidate_salary', jobsearch_esc_html($_POST['candidate_salary']));
                    }

                    //
                    // candidate salary currency
                    if (isset($_POST['candidate_salary_currency'])) {
                        $candidate_salary_type = jobsearch_esc_html($_POST['candidate_salary_currency']);
                        update_post_meta($candidate_id, 'jobsearch_field_candidate_salary_currency', $candidate_salary_type);
                    }
                    // candidate salary currency pos
                    if (isset($_POST['candidate_salary_pos'])) {
                        $candidate_salary_type = jobsearch_esc_html($_POST['candidate_salary_pos']);
                        update_post_meta($candidate_id, 'jobsearch_field_candidate_salary_pos', $candidate_salary_type);
                    }
                    // candidate salary currency decimal
                    if (isset($_POST['candidate_salary_deci'])) {
                        $candidate_salary_type = jobsearch_esc_html($_POST['candidate_salary_deci']);
                        update_post_meta($candidate_id, 'jobsearch_field_candidate_salary_deci', $candidate_salary_type);
                    }
                    // candidate salary currency sep
                    if (isset($_POST['candidate_salary_sep'])) {
                        $candidate_salary_type = jobsearch_esc_html($_POST['candidate_salary_sep']);
                        update_post_meta($candidate_id, 'jobsearch_field_candidate_salary_sep', $candidate_salary_type);
                    }

                    //
                    if (isset($_POST['jobsearch_field_user_dob_whole']) && $_POST['jobsearch_field_user_dob_whole'] != '') {
                        $whole_dob = $_POST['jobsearch_field_user_dob_whole'];
                        $whole_dob_dd = date_i18n('d', strtotime($whole_dob));
                        $whole_dob_mm = date_i18n('m', strtotime($whole_dob));
                        $whole_dob_yy = date_i18n('Y', strtotime($whole_dob));
                        update_post_meta($candidate_id, 'jobsearch_field_user_dob_dd', jobsearch_esc_html($whole_dob_dd));
                        update_post_meta($candidate_id, 'jobsearch_field_user_dob_mm', jobsearch_esc_html($whole_dob_mm));
                        update_post_meta($candidate_id, 'jobsearch_field_user_dob_yy', jobsearch_esc_html($whole_dob_yy));
                    } else {
                        update_post_meta($candidate_id, 'jobsearch_field_user_dob_dd', '');
                        update_post_meta($candidate_id, 'jobsearch_field_user_dob_mm', '');
                        update_post_meta($candidate_id, 'jobsearch_field_user_dob_yy', '');
                    }

                    if (isset($_POST['user_phone'])) {
                        $user_inp_phone = $_POST['user_phone'];
                        $user_dial_code = isset($_POST['dial_code']) ? $_POST['dial_code'] : '';
                        $contry_iso_code = isset($_POST['contry_iso_code']) ? $_POST['contry_iso_code'] : '';
                        if ($user_dial_code == '') {
                            $user_dial_code = get_post_meta($candidate_id, 'jobsearch_field_user_dial_code', true);
                        }
                        if ($contry_iso_code == '') {
                            $contry_iso_code = get_post_meta($candidate_id, 'jobsearch_field_contry_iso_code', true);
                        }
                        if ($user_dial_code != '') {
                            update_post_meta($candidate_id, 'jobsearch_field_user_phone', $user_dial_code . jobsearch_esc_html($user_inp_phone));
                            update_post_meta($candidate_id, 'jobsearch_field_user_justphone', jobsearch_esc_html($user_inp_phone));
                            update_post_meta($candidate_id, 'jobsearch_field_user_dial_code', $user_dial_code);
                            update_post_meta($candidate_id, 'jobsearch_field_contry_iso_code', $contry_iso_code);
                        } else {
                            update_post_meta($candidate_id, 'jobsearch_field_user_phone', jobsearch_esc_html($user_inp_phone));
                        }
                    }

                    // Cus Fields Upload Files /////
                    do_action('jobsearch_custom_field_upload_files_save', $candidate_id, 'candidate');

                    //
                    jobsearch_candidate_skill_percent_count($user_id, 'none');
                    //

                    do_action('jobsearch_candidate_profile_save_after', $candidate_id);
                    do_action('jobsearch_user_data_save_onprofile', $user_id, $candidate_id, 'candidate');
                } else if ($user_is_employer) {

                    if (isset($_POST['emp_user_facebook_url'])) {
                        $fb_social_url = esc_url($_POST['emp_user_facebook_url']);
                        update_post_meta($employer_id, 'jobsearch_field_user_facebook_url', $fb_social_url);
                    }
                    if (isset($_POST['emp_user_twitter_url'])) {
                        $twitter_social_url = esc_url($_POST['emp_user_twitter_url']);
                        update_post_meta($employer_id, 'jobsearch_field_user_twitter_url', $twitter_social_url);
                    }
                    if (isset($_POST['emp_user_linkedin_url'])) {
                        $linkedin_social_url = esc_url($_POST['emp_user_linkedin_url']);
                        update_post_meta($employer_id, 'jobsearch_field_user_linkedin_url', $linkedin_social_url);
                    }
                    if (isset($_POST['emp_user_dribbble_url'])) {
                        $dribbble_social_url = esc_url($_POST['emp_user_dribbble_url']);
                        update_post_meta($employer_id, 'jobsearch_field_user_dribbble_url', $dribbble_social_url);
                    }
                    //
                    // Dynamic Employer Social Fields /////
                    $employer_social_mlinks = isset($jobsearch_plugin_options['employer_social_mlinks']) ? $jobsearch_plugin_options['employer_social_mlinks'] : '';
                    if (!empty($employer_social_mlinks)) {
                        if (isset($employer_social_mlinks['title']) && is_array($employer_social_mlinks['title'])) {
                            $field_counter = 0;
                            foreach ($employer_social_mlinks['title'] as $field_title_val) {
                                if (isset($_POST['employer_dynm_social' . $field_counter])) {
                                    $msocil_linkurl = esc_url($_POST['employer_dynm_social' . $field_counter]);
                                    update_post_meta($employer_id, 'jobsearch_field_dynm_social' . $field_counter, $msocil_linkurl);
                                }
                                $field_counter++;
                            }
                        }
                    }
                    //
                    // Public profile view saving
                    if (isset($_POST['jobsearch_field_user_public_pview']) && !empty($_POST['jobsearch_field_user_public_pview'])) {
                        $user_public_pview = $_POST['jobsearch_field_user_public_pview'];
                        if ($user_public_pview == 'no') {
                            $user_up_post = array(
                                'ID' => $employer_id,
                                'post_status' => 'draft',
                            );
                            wp_update_post($user_up_post);
                        } else {
                            $user_up_post = array(
                                'ID' => $employer_id,
                                'post_status' => 'publish',
                            );
                            wp_update_post($user_up_post);
                        }
                    }
                    //
                    // Gallery ////////////////////////
                    $gal_ids_arr = array();

                    $max_gal_imgs_allow = isset($jobsearch_plugin_options['max_gal_imgs_allow']) && $jobsearch_plugin_options['max_gal_imgs_allow'] > 0 ? $jobsearch_plugin_options['max_gal_imgs_allow'] : 5;
                    $number_of_gal_imgs = $max_gal_imgs_allow;

                    if (isset($_POST['company_gallery_imgs']) && !empty($_POST['company_gallery_imgs'])) {
                        $gal_ids_arr = array_merge($gal_ids_arr, $_POST['company_gallery_imgs']);
                    }

                    $gal_imgs_count = 0;
                    if (!empty($gal_ids_arr)) {
                        $gal_imgs_count = sizeof($gal_ids_arr);
                    }

                    if (!empty($gal_ids_arr) && $number_of_gal_imgs > 0) {
                        $gal_ids_arr = array_slice($gal_ids_arr, 0, $number_of_gal_imgs, true);
                    }

                    update_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs', $gal_ids_arr);

                    //
                    // updating user email to member
                    update_post_meta($employer_id, 'jobsearch_field_user_email', ($user_email));

                    //
                    $display_name = isset($_POST['display_name']) ? jobsearch_esc_html(sanitize_text_field($_POST['display_name'])) : '';

                    //
                    if ($display_name != '') {
                        $up_post = array(
                            'ID' => $employer_id,
                            'post_title' => wp_strip_all_tags($display_name),
                        );
                        wp_update_post($up_post);
                        //
                        update_post_meta($employer_id, 'member_display_name', wp_strip_all_tags($display_name));
                    }

                    $up_post = array(
                        'ID' => $employer_id,
                        'post_content' => $user_bio,
                    );

                    wp_update_post($up_post);

                    //
                    if (isset($_POST['user_sector'])) {
                        $user_sector = ($_POST['user_sector']);
                        $user_sector = is_array($user_sector) ? $user_sector : array($user_sector);
                        wp_set_post_terms($employer_id, $user_sector, 'sector', false);
                    }

                    //
                    if (isset($_POST['user_dob_dd'])) {
                        update_post_meta($employer_id, 'jobsearch_field_user_dob_dd', jobsearch_esc_html($_POST['user_dob_dd']));
                    }
                    if (isset($_POST['user_dob_mm'])) {
                        update_post_meta($employer_id, 'jobsearch_field_user_dob_mm', jobsearch_esc_html($_POST['user_dob_mm']));
                    }
                    if (isset($_POST['user_dob_yy'])) {
                        update_post_meta($employer_id, 'jobsearch_field_user_dob_yy', jobsearch_esc_html($_POST['user_dob_yy']));
                    }

                    if (isset($_POST['user_phone'])) {
                        $user_inp_phone = $_POST['user_phone'];
                        $user_dial_code = isset($_POST['dial_code']) ? $_POST['dial_code'] : '';
                        $contry_iso_code = isset($_POST['contry_iso_code']) ? $_POST['contry_iso_code'] : '';
                        if ($user_dial_code == '') {
                            $user_dial_code = get_post_meta($employer_id, 'jobsearch_field_user_dial_code', true);
                        }
                        if ($contry_iso_code == '') {
                            $contry_iso_code = get_post_meta($employer_id, 'jobsearch_field_contry_iso_code', true);
                        }
                        if ($user_dial_code != '') {
                            update_post_meta($employer_id, 'jobsearch_field_user_phone', $user_dial_code . jobsearch_esc_html($user_inp_phone));
                            update_post_meta($employer_id, 'jobsearch_field_user_justphone', jobsearch_esc_html($user_inp_phone));
                            update_post_meta($employer_id, 'jobsearch_field_user_dial_code', $user_dial_code);
                            update_post_meta($employer_id, 'jobsearch_field_contry_iso_code', $contry_iso_code);
                        } else {
                            update_post_meta($employer_id, 'jobsearch_field_user_phone', jobsearch_esc_html($user_inp_phone));
                        }
                    }

                    if (isset($_POST['jobsearch_field_affiliation_title'])) {
                        update_post_meta($employer_id, 'jobsearch_field_affiliation_title', jobsearch_esc_html($_POST['jobsearch_field_affiliation_title']));
                    } else {
                        update_post_meta($employer_id, 'jobsearch_field_affiliation_title', '');
                    }
                    if (isset($_POST['jobsearch_field_affiliation_image'])) {
                        update_post_meta($employer_id, 'jobsearch_field_affiliation_image', jobsearch_esc_html($_POST['jobsearch_field_affiliation_image']));
                    } else {
                        update_post_meta($employer_id, 'jobsearch_field_affiliation_image', '');
                    }

                    if (isset($_POST['jobsearch_field_award_title'])) {
                        update_post_meta($employer_id, 'jobsearch_field_award_title', jobsearch_esc_html($_POST['jobsearch_field_award_title']));
                    } else {
                        update_post_meta($employer_id, 'jobsearch_field_award_title', '');
                    }
                    if (isset($_POST['jobsearch_field_award_image'])) {
                        update_post_meta($employer_id, 'jobsearch_field_award_image', jobsearch_esc_html($_POST['jobsearch_field_award_image']));
                    } else {
                        update_post_meta($employer_id, 'jobsearch_field_award_image', '');
                    }

                    // Cus Fields Upload Files /////
                    do_action('jobsearch_custom_field_upload_files_save', $employer_id, 'employer');

                    //

                    do_action('jobsearch_employer_profile_save_after', $employer_id);
                    do_action('jobsearch_user_data_save_onprofile', $user_id, $employer_id, 'employer');
                }

                jobsearch_onuser_update_wc_update($user_id);
            }
        }
        //
        if (isset($_POST['user_password_change_form']) && $_POST['user_password_change_form'] == '1') {

            $old_pass = isset($_POST['old_pass']) ? $_POST['old_pass'] : '';
            $new_pass = isset($_POST['new_pass']) ? $_POST['new_pass'] : '';

            $security_switch = isset($jobsearch_plugin_options['security-questions-switch']) ? $jobsearch_plugin_options['security-questions-switch'] : '';

            $security_questions = isset($jobsearch_plugin_options['jobsearch-security-questions']) ? $jobsearch_plugin_options['jobsearch-security-questions'] : '';

            if ($security_switch == 'on') {
                //
                if (jobsearch_user_isemp_member($user_id)) {
                    $sec_questions = get_user_meta($user_id, 'user_security_questions', true);
                } else {
                    if ($user_is_employer) {
                        $sec_questions = get_post_meta($employer_id, 'user_security_questions', true);
                    } else {
                        $sec_questions = get_post_meta($candidate_id, 'user_security_questions', true);
                    }
                }

                if (!empty($security_questions) && sizeof($security_questions) >= 3 && empty($sec_questions)) {
                    $input_quest_answers = isset($_POST['user_security_questions']) ? $_POST['user_security_questions'] : '';
                    $_input_ques = isset($input_quest_answers['questions']) ? $input_quest_answers['questions'] : '';
                    $_input_answers = isset($input_quest_answers['answers']) ? $input_quest_answers['answers'] : '';
                    $minimum_ans_num = 2;
                    if (!empty($_input_answers)) {
                        $_fill_ans_count = 0;
                        foreach ($_input_answers as $_inp_ans) {
                            $_fill_ans_count = $_inp_ans != '' ? $_fill_ans_count + 1 : $_fill_ans_count;
                        }
                        if ($_fill_ans_count < $minimum_ans_num) {
                            $diff_form_errs['min_questions_err'] = $minimum_ans_num;
                        }
                    }
                    //
                } else if (!empty($security_questions) && sizeof($security_questions) >= 3 && !empty($sec_questions) && $old_pass != '' && $new_pass != '') {
                    $answer_to_ques = isset($sec_questions['answers']) ? $sec_questions['answers'] : '';
                    $input_quest_answers = isset($_POST['user_security_quests']) ? $_POST['user_security_quests'] : '';
                    $_input_answers = isset($input_quest_answers['answers']) ? $input_quest_answers['answers'] : '';
                    if (!empty($_input_answers) && !empty($answer_to_ques)) {
                        $ans_count = 0;
                        foreach ($_input_answers as $_inp_ans) {
                            $ans_to_ques = isset($sec_questions['answers'][$ans_count]) ? $sec_questions['answers'][$ans_count] : '';
                            $ans_to_ques = base64_decode($ans_to_ques);
                            if ($ans_to_ques != '' && $ans_to_ques != $_inp_ans) {
                                $diff_form_errs['wrong_ans_err'] = true;
                            }
                            $ans_count++;
                        }
                    }
                }

                if (empty($diff_form_errs)) {
                    $to_save_secqusts = '';
                    if (isset($_POST['user_security_questions']) && !empty($_POST['user_security_questions'])) {
                        $to_save_secqusts = $_POST['user_security_questions'];
                        if (isset($to_save_secqusts['answers']) && !empty($to_save_secqusts['answers'])) {
                            $to_save_secqustsans = $to_save_secqusts['answers'];
                            $answr_counter = 0;
                            foreach ($to_save_secqustsans as $answr_item) {
                                $answr_item = base64_encode($answr_item);
                                $to_save_secqusts['answers'][$answr_counter] = $answr_item;
                                $answr_counter++;
                            }
                        }
                    }
                    if (jobsearch_user_isemp_member($user_id)) {
                        $sec_questions = get_user_meta($user_id, 'user_security_questions', true);
                        if (isset($_POST['user_security_questions'])) {
                            update_user_meta($user_id, 'user_security_questions', ($to_save_secqusts));
                        }
                    } else {
                        if ($user_is_employer) {
                            $sec_questions = get_post_meta($employer_id, 'user_security_questions', true);
                            //
                            if (isset($_POST['user_security_questions'])) {
                                update_post_meta($employer_id, 'user_security_questions', ($to_save_secqusts));
                            }
                            //
                        } else {
                            $sec_questions = get_post_meta($candidate_id, 'user_security_questions', true);
                            //
                            if (isset($_POST['user_security_questions'])) {
                                update_post_meta($candidate_id, 'user_security_questions', ($to_save_secqusts));
                            }
                            //
                        }
                    }
                }
            }

            if ($old_pass != '' && $new_pass != '') {
                if ($user_obj && wp_check_password($old_pass, $user_obj->data->user_pass, $user_obj->ID)) {
                    //
                } else {
                    $diff_form_errs['old_pass_not_matched'] = true;
                }
            }

            if (empty($diff_form_errs)) {
                $old_pass = isset($_POST['old_pass']) ? $_POST['old_pass'] : '';
                $new_pass = isset($_POST['new_pass']) ? $_POST['new_pass'] : '';
                $pass_changed = false;
                if ($old_pass != '' && $new_pass != '') {
                    if ($user_obj && wp_check_password($old_pass, $user_obj->data->user_pass, $user_obj->ID)) {
                        $user_def_array = array('ID' => $user_id);
                        $user_def_array['user_pass'] = $new_pass;
                        wp_update_user($user_def_array);
                        $pass_changed = true;
                    } else {
                        $diff_form_errs['old_pass_not_matched'] = true;
                    }
                }
            }
            //
        }
        //
    }

    public function user_avatar_profile_delete_pthumb()
    {
        $cur_user_id = get_current_user_id();
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
        if (jobsearch_candidate_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete profile image.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        if (jobsearch_employer_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete profile image.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        if ($cur_user_id == $user_id) {
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            $user_is_employer = jobsearch_user_is_employer($user_id);
            if ($user_is_employer) {
                $employer_id = jobsearch_get_user_employer_id($user_id);

                //
                $def_img_url = get_avatar_url($user_id, array('size' => 132));
                $def_img_url = $def_img_url == '' ? jobsearch_employer_image_placeholder() : $def_img_url;

                if (has_post_thumbnail($employer_id)) {
                    $attachment_id = get_post_thumbnail_id($employer_id);
                    wp_delete_attachment($attachment_id, true);
                }
                echo json_encode(array('success' => '1', 'img_url' => $def_img_url));
                wp_die();
            } else {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);

                //
                $def_img_url = get_avatar_url($user_id, array('size' => 132));
                $def_img_url = $def_img_url == '' ? jobsearch_candidate_image_placeholder() : $def_img_url;

                jobsearch_remove_cand_photo_foldr($candidate_id);
                
                if (has_post_thumbnail($candidate_id)) {
                    $attachment_id = get_post_thumbnail_id($candidate_id);
                    wp_delete_attachment($attachment_id, true);
                }
                echo json_encode(array('success' => '1', 'img_url' => $def_img_url));
                wp_die();
            }
            echo json_encode(array('success' => '0'));
            wp_die();
        }
        wp_die();
    }

    public function user_avatar_upload_ajax()
    {

        $user_id = get_current_user_id();

        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        if (jobsearch_candidate_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to upload a profile image.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        if (jobsearch_employer_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to upload a profile image.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        
        if ($user_is_employer) {
            $employer_id = jobsearch_get_user_employer_id($user_id);
            
            if (has_post_thumbnail($employer_id)) {
                $attachment_id = get_post_thumbnail_id($employer_id);
                wp_delete_attachment($attachment_id, true);
            }
            $atach_id = jobsearch_insert_upload_attach('avatar_file', $employer_id);
        } else {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            $file_urls = jobsearch_insert_candupload_attach('avatar_file', $candidate_id);
        }
        
        if ($user_is_employer) {
            if ($atach_id > 0) {
                do_action('jobsearch_aftr_user_uploaded_profile_pic', $atach_id, $user_id);
                $user_thumbnail_image = wp_get_attachment_image_src($atach_id, 'thumbnail');
                $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';

                echo json_encode(array('imgUrl' => $user_def_avatar_url, 'err_msg' => ''));
                die;
            }
        } else if (!empty($file_urls)) {
            
            do_action('jobsearch_aftr_user_uploaded_profile_pic', $file_urls, $user_id);

            jobsearch_remove_cand_photo_foldr($candidate_id);
            $folder_path = $file_urls['path'];
            $img_url = $file_urls['crop'];
            $orig_img_url = $file_urls['orig'];
            
            $file_uniqid = jobsearch_get_unique_folder_byurl($img_url);
            
            $filename = basename($orig_img_url);
            $filetype = wp_check_filetype($filename, null);
            $fileuplod_time = current_time('timestamp');

            $arg_arr = array(
                'file_name' => $filename,
                'mime_type' => $filetype,
                'time' => $fileuplod_time,
                'orig_file_url' => $orig_img_url,
                'file_url' => $img_url,
                'file_path' => $folder_path,
                'file_id' => $file_uniqid,
            );
            update_post_meta($candidate_id, 'jobsearch_user_avatar_url', $arg_arr);

            $img_url = apply_filters('wp_jobsearch_cand_profile_img_url', $img_url, $candidate_id, '150');
            
            echo json_encode(array('imgUrl' => $img_url, 'err_msg' => ''));
            die;
        }
        echo json_encode(array('err_msg' => esc_html__('There is a problem uploading profile image.', 'wp-jobsearch')));
        wp_die();
    }

    public function candidate_cover_img_upload()
    {
        $user_id = get_current_user_id();
        $user_is_candiadte = jobsearch_user_is_candidate($user_id);
        if (jobsearch_candidate_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to upload a cover image.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        if ($user_is_candiadte) {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            $atach_urls = jobsearch_insert_candupload_attach('user_cvr_photo_cand', $candidate_id, 'cover_img');
        }

        if (isset($atach_urls) && !empty($atach_urls)) {
            
            jobsearch_remove_cand_photo_foldr($candidate_id, 'cover_img');
            
            $folder_path = $atach_urls['path'];
            $img_url = $atach_urls['orig'];
            
            $file_uniqid = jobsearch_get_unique_folder_byurl($img_url);
            
            $filename = basename($img_url);
            $filetype = wp_check_filetype($filename, null);
            $fileuplod_time = current_time('timestamp');

            $arg_arr = array(
                'file_name' => $filename,
                'mime_type' => $filetype,
                'time' => $fileuplod_time,
                'file_url' => $img_url,
                'file_path' => $folder_path,
                'file_id' => $file_uniqid,
            );
            update_post_meta($candidate_id, 'jobsearch_user_cover_imge', $arg_arr);

            $img_url = apply_filters('wp_jobsearch_cand_ccovr_img_url', $img_url, $candidate_id);
            
            echo json_encode(array('imgUrl' => $img_url));
        }
        wp_die();
    }

    public function candidate_cover_img_remove()
    {

        $user_id = get_current_user_id();

        $user_is_candidate = jobsearch_user_is_candidate($user_id);

        if (jobsearch_candidate_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete the cover image.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        if ($user_is_candidate) {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            JobSearchMultiPostThumbnails::remove_front_thumbnail($candidate_id, 'cover-image');
            jobsearch_remove_cand_photo_foldr($candidate_id, 'cover_img');
            echo json_encode(array('success' => '1'));
        }

        wp_die();
    }

    public function employer_cover_img_upload()
    {

        $user_id = get_current_user_id();

        $user_is_employer = jobsearch_user_is_employer($user_id);

        if (jobsearch_employer_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to upload a cover image.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        if ($user_is_employer) {
            $employer_id = jobsearch_get_user_employer_id($user_id);
            JobSearchMultiPostThumbnails::remove_front_thumbnail($employer_id, 'cover-image');
            $atach_id = jobsearch_insert_upload_attach('user_cvr_photo', 0);
        }

        if (isset($atach_id) && $atach_id > 0) {
            if (class_exists('JobSearchMultiPostThumbnails')) {
                JobSearchMultiPostThumbnails::set_front_thumbnail($employer_id, $atach_id, 'cover-image');
            }

            $user_thumbnail_image = wp_get_attachment_image_src($atach_id, 'full');
            $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';

            echo json_encode(array('imgUrl' => $user_def_avatar_url));
        }
        wp_die();
    }

    public function employer_cover_img_remove()
    {

        $user_id = get_current_user_id();

        $user_is_employer = jobsearch_user_is_employer($user_id);

        if (jobsearch_employer_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to upload a cover image.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        if ($user_is_employer) {
            $employer_id = jobsearch_get_user_employer_id($user_id);
            JobSearchMultiPostThumbnails::remove_front_thumbnail($employer_id, 'cover-image');
            echo json_encode(array('success' => '1'));
        }

        wp_die();
    }

    public function candidate_cv_upload_ajax()
    {

        global $jobsearch_plugin_options;

        $user_id = get_current_user_id();

        $user_is_candidate = jobsearch_user_is_candidate($user_id);

        if ($user_is_candidate) {
            if (jobsearch_candidate_not_allow_to_mod()) {
                $msg = esc_html__('You are not allowed to upload files.', 'wp-jobsearch');
                echo json_encode(array('err_msg' => $msg));
                die;
            }
            $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';

            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            
            $_file_key_name = 'file';
            if (isset($_FILES['candidate_cv_file'])) {
                $_file_key_name = 'candidate_cv_file';
            }

            $atach_url = jobsearch_upload_candidate_cv($_file_key_name, $candidate_id);

            if ($atach_url != '') {
                $file_url = $atach_url;

                $file_uniqid = uniqid();

                $filename = basename($file_url);
                $filetype = wp_check_filetype($filename, null);
                $fileuplod_time = current_time('timestamp');

                if ($multiple_cv_files_allow == 'on') {
                    $arg_arr = array(
                        'file_name' => $filename,
                        'mime_type' => $filetype,
                        'time' => $fileuplod_time,
                        'file_url' => $file_url,
                        'file_id' => $file_uniqid,
                        'primary' => '',
                    );
                    $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
                    $ca_jat_cv_files = get_post_meta($candidate_id, 'jobsearch_field_user_cv_attachments', true);
                    $ca_at_cv_files = !empty($ca_at_cv_files) ? $ca_at_cv_files : array();
                    $ca_jat_cv_files = !empty($ca_jat_cv_files) ? $ca_jat_cv_files : array();

                    $ca_at_cv_files[] = $arg_arr;
                    $ca_jat_cv_files[] = $arg_arr;
                    update_post_meta($candidate_id, 'candidate_cv_files', $ca_at_cv_files);
                    update_post_meta($candidate_id, 'jobsearch_field_user_cv_attachments', $ca_jat_cv_files);
                } else {
                    $arg_arr = array(
                        'file_name' => $filename,
                        'mime_type' => $filetype,
                        'time' => $fileuplod_time,
                        'file_url' => $file_url,
                        'file_id' => $file_uniqid,
                    );
                    update_post_meta($candidate_id, 'candidate_cv_file', $arg_arr);
                    update_post_meta($candidate_id, 'jobsearch_field_user_cv_attachment', $file_url);
                }

                $cv_file_title = $filename;

                $attach_date = $fileuplod_time;
                $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';

                if ($attach_mime == 'application/pdf') {
                    $attach_icon = 'fa fa-file-pdf-o';
                } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    $attach_icon = 'fa fa-file-word-o';
                } else if ($attach_mime == 'text/plain') {
                    $attach_icon = 'fa fa-file-text-o';
                } else if ($attach_mime == 'application/vnd.ms-excel' || $attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                    $attach_icon = 'fa fa-file-excel-o';
                } else if ($attach_mime == 'image/jpeg' || $attach_mime == 'image/png') {
                    $attach_icon = 'fa fa-file-image-o';
                } else {
                    $attach_icon = 'fa fa-file-word-o';
                }

                $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_uniqid, $candidate_id);

                ob_start();
                ?>
                <div class="jobsearch-cv-manager-list"<?php echo($multiple_cv_files_allow == 'on' ? '' : ' style="display:none;"') ?>>
                    <ul class="jobsearch-row">
                        <li class="jobsearch-column-12">
                            <div class="jobsearch-cv-manager-wrap">
                                <a class="jobsearch-cv-manager-thumb"><i class="<?php echo($attach_icon) ?>"></i></a>
                                <div class="jobsearch-cv-manager-text">
                                    <div class="jobsearch-cv-manager-left">
                                        <h2><a href="<?php echo($file_url) ?>" oncontextmenu="javascript: return false;"
                                               onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                               download="<?php echo($filename) ?>"><?php echo($filename) ?></a></h2>
                                        <?php
                                        if ($attach_date != '') {
                                            ?>
                                            <ul>
                                                <li>
                                                    <i class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), ($attach_date)) . ' ' . date_i18n(get_option('time_format'), ($attach_date)) ?>
                                                </li>
                                            </ul>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <a href="javascript:void(0);"
                                       class="jobsearch-cv-manager-link jobsearch-del-user-cv"
                                       data-id="<?php echo($file_uniqid) ?>"><i
                                                class="jobsearch-icon jobsearch-rubbish"></i></a>
                                    <a href="<?php echo($file_url) ?>"
                                       class="jobsearch-cv-manager-link jobsearch-cv-manager-download"
                                       oncontextmenu="javascript: return false;"
                                       onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                       download="<?php echo($filename) ?>"><i
                                                class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <?php
                $file_html = ob_get_clean();

                echo json_encode(array('fileUrl' => $file_url, 'filehtml' => $file_html));
            }
        }
        wp_die();
    }

    public function candidate_cv_delete_ajax()
    {
        global $jobsearch_plugin_options;

        $user_id = get_current_user_id();

        $user_is_candidate = jobsearch_user_is_candidate($user_id);

        $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';

        if ($user_is_candidate) {
            if (jobsearch_candidate_not_allow_to_mod()) {
                $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
                echo json_encode(array('err_msg' => $msg));
                die;
            }
            $candidate_id = jobsearch_get_user_candidate_id($user_id);

            if ($multiple_cv_files_allow == 'on') {
                $attach_id = isset($_POST['attach_id']) ? $_POST['attach_id'] : '';
                $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
                $ca_jat_cv_files = get_post_meta($candidate_id, 'jobsearch_field_user_cv_attachments', true);
                $ca_at_cv_files = !empty($ca_at_cv_files) ? $ca_at_cv_files : array();
                $ca_jat_cv_files = !empty($ca_jat_cv_files) ? $ca_jat_cv_files : array();

                $newca_atcv_files = array();
                if (!empty($ca_at_cv_files)) {
                    foreach ($ca_at_cv_files as $ca_atcv_file) {
                        if ($ca_atcv_file['file_id'] != $attach_id) {
                            $newca_atcv_files[] = $ca_atcv_file;
                        }
                    }
                }

                $newca_jatcv_files = array();
                if (!empty($ca_jat_cv_files)) {
                    foreach ($ca_jat_cv_files as $ca_jatcv_file) {
                        if ($ca_jatcv_file['file_id'] != $attach_id) {
                            $newca_jatcv_files[] = $ca_jatcv_file;
                        }
                    }
                }

                update_post_meta($candidate_id, 'candidate_cv_files', $newca_atcv_files);

                update_post_meta($candidate_id, 'jobsearch_field_user_cv_attachments', $newca_jatcv_files);

            } else {

                update_post_meta($candidate_id, 'candidate_cv_file', '');
                update_post_meta($candidate_id, 'jobsearch_field_user_cv_attachment', '');
            }

            echo json_encode(array('delete' => '1'));
        }
        wp_die();
    }

    public function user_candidate_delete()
    {
        $candidate_id = isset($_POST['candidate_id']) ? ($_POST['candidate_id']) : '';
        if (jobsearch_employer_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        if (jobsearch_is_employer_job($candidate_id)) {

            wp_delete_post($candidate_id, true);
            echo json_encode(array('msg' => 'deleted'));
        }
        wp_die();
    }

    public function remove_user_fav_candidate_from_list()
    {
        $candidate_id = isset($_POST['candidate_id']) ? ($_POST['candidate_id']) : '';

        $user_id = get_current_user_id();
        if (jobsearch_candidate_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
        if ($candidate_id > 0) {
            $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
            if ($candidate_fav_jobs_list != '') {
                $candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);
                if (in_array($candidate_id, $candidate_fav_jobs_list)) {
                    $candidate_key = array_search($candidate_id, $candidate_fav_jobs_list);
                    unset($candidate_fav_jobs_list[$candidate_key]);

                    $candidate_fav_jobs_list = implode(',', $candidate_fav_jobs_list);
                    update_post_meta($candidate_id, 'jobsearch_fav_jobs_list', $candidate_fav_jobs_list);
                }
            }
        }
        echo json_encode(array('msg' => esc_html__('removed.', 'wp-jobsearch')));
        die;
    }

    public function remove_user_applied_candidate_from_list()
    {
        $candidate_id = isset($_POST['candidate_id']) ? ($_POST['candidate_id']) : '';
        $candidate_key = isset($_POST['candidate_key']) ? ($_POST['candidate_key']) : '';

        $user_id = get_current_user_id();
        $user_applied_jobs_list = get_user_meta($user_id, 'jobsearch-user-jobs-applied-list', true);

        if (jobsearch_candidate_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }

        if (!empty($user_applied_jobs_list)) {

            $finded_row = jobsearch_find_in_multiarray($candidate_id, $user_applied_jobs_list, 'post_id');

            if ($finded_row) {
                $user_applied_jobs_list = remove_index_from_array($user_applied_jobs_list, $finded_row);
                update_user_meta($user_id, 'jobsearch-user-jobs-applied-list', $user_applied_jobs_list);
            }
        }

        echo json_encode(array('msg' => esc_html__('removed.', 'wp-jobsearch')));
        die;
    }

    public function remove_candidate_fav_job_from_list()
    {
        $job_id = isset($_POST['job_id']) ? ($_POST['job_id']) : '';

        $user_id = get_current_user_id();
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
        $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
        $candidate_fav_jobs_list = $candidate_fav_jobs_list != '' ? explode(',', $candidate_fav_jobs_list) : array();

        if (jobsearch_candidate_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }

        if (!empty($candidate_fav_jobs_list)) {

            if (($key = array_search($job_id, $candidate_fav_jobs_list)) !== false) {
                unset($candidate_fav_jobs_list[$key]);

                $candidate_fav_jobs_list = implode(',', $candidate_fav_jobs_list);
                update_post_meta($candidate_id, 'jobsearch_fav_jobs_list', $candidate_fav_jobs_list);
            }
        }

        echo json_encode(array('msg' => esc_html__('removed.', 'wp-jobsearch')));
        die;
    }

    public function remove_candidate_applied_job_from_list()
    {
        $job_id = isset($_POST['job_id']) ? ($_POST['job_id']) : '';

        $user_id = get_current_user_id();
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
        $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();

        if (jobsearch_candidate_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }

        if (!empty($job_applicants_list)) {
            if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
                unset($job_applicants_list[$key]);

                $job_applicants_list = implode(',', $job_applicants_list);
                update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
            }
        }
        jobsearch_remove_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $user_id);

        echo json_encode(array('msg' => esc_html__('removed.', 'wp-jobsearch')));
        die;
    }
    
    public function change_email_read_status() {
        $email_id = isset($_POST['email_id']) ? $_POST['email_id'] : '';
        
        update_post_meta($email_id, 'jobsearch_email_read_status', '1');
        
        wp_send_json(array('success' => '1'));
    }

    public function add_resume_education_to_list()
    {
        $rand_num = rand(1000000, 9999999);
        $title = isset($_POST['title']) ? ($_POST['title']) : '';
        $start_date = isset($_POST['start_date']) ? ($_POST['start_date']) : '';
        $end_date = isset($_POST['end_date']) ? ($_POST['end_date']) : '';
        $present_date = isset($_POST['present_date']) ? ($_POST['present_date']) : '';
        $institute = isset($_POST['institute']) ? ($_POST['institute']) : '';
        $desc = isset($_POST['desc']) ? ($_POST['desc']) : '';

        if ($title != '' && $institute != '') {
            $html = '
            <li class="jobsearch-column-12 resume-list-item resume-list-edu">
                <div class="jobsearch-resume-education-wrap">
                    <small>' . ($start_date != '' ? date_i18n(get_option('date_format'), strtotime($start_date)) : '') . ' - ' . ($present_date == 'on' ? 'Present' : '') . ($end_date != '' && $present_date != 'on' ? date_i18n(get_option('date_format'), strtotime($end_date)) : '') . '</small>
                    <h2><a>' . $title . '</a></h2>
                    <span>' . $institute . '</span>
                </div>
                <div class="jobsearch-resume-education-btn">
                    <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                    <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish ' . (apply_filters('jobsearch_candash_resume_edulist_itmdelclass', 'del-resume-item', $rand_num)) . '" data-id="' . $rand_num . '"></a>
                </div>
                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                        <li class="jobsearch-column-12">';
            $title_html = '<label>' . esc_html__('Title *', 'wp-jobsearch') . '</label>';
            $html .= apply_filters('jobsearch_candash_resume_edutitle_label', $title_html);
            $html .= '
                            <input name="jobsearch_field_education_title[]" type="text" value="' . $title . '">
                        </li>
                        <li class="jobsearch-column-4">
                            <label>' . esc_html__('Start Date *', 'wp-jobsearch') . '</label>
                            <input id="date-start-' . $rand_num . '" name="jobsearch_field_education_start_date[]" type="text" value="' . $start_date . '">
                        </li>
                        <li class="jobsearch-column-4 cand-edu-todatefield-' . ($rand_num) . '" ' . ($present_date == 'on' ? 'style="display: none;"' : '') . '>
                            <label>' . esc_html__('End Date', 'wp-jobsearch') . '</label>
                            <input id="date-end-' . $rand_num . '" name="jobsearch_field_education_end_date[]" type="text" value="' . $end_date . '">
                        </li>
                        <li class="jobsearch-column-4 cand-edu-prsntfield">
                            <label>' . esc_html__('Present', 'wp-jobsearch') . '</label>
                            <input class="cand-edu-prsntchkbtn" data-id="' . ($rand_num) . '" type="checkbox" ' . ($present_date == 'on' ? 'checked' : '') . '>
                            <input name="jobsearch_field_education_date_prsnt[]" type="hidden" value="' . ($present_date) . '">
                        </li>
                        <li class="jobsearch-column-12">
                            <label>' . esc_html__('Institute *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_education_academy[]" type="text" value="' . $institute . '">
                        </li>
                        <li class="jobsearch-column-12">
                            <label>' . esc_html(_x('Description', 'Resume Education Description', 'wp-jobsearch')) . '</label>
                            <textarea name="jobsearch_field_education_description[]" ' . apply_filters('jobsearch_candash_resume_edudesc_atts', '') . '>' . $desc . '</textarea>
                        </li>
                        <li class="jobsearch-column-12">
                            <input class="update-resume-list-btn" type="submit" value="' . esc_html__('Update', 'wp-jobsearch') . '">
                        </li>
                    </ul>
                </div>
            </li>';

            $ddf_arr = array('msg' => esc_html__('Added Successfully.', 'wp-jobsearch'), 'html' => apply_filters('jobsearch_cand_dash_resume_addedu_ajax_html', $html));
            $ddf_arr = apply_filters('jobsearch_dashcand_resme_eduadd_ajaxarr', $ddf_arr);
            echo json_encode($ddf_arr);
            die;
        }

        echo json_encode(array('msg' => esc_html__('Please fill the necessary fields.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }

    public function add_resume_experience_to_list()
    {
        $rand_num = rand(1000000, 99999999);
        $title = isset($_POST['title']) ? ($_POST['title']) : '';
        $start_date = isset($_POST['start_date']) ? ($_POST['start_date']) : '';
        $end_date = isset($_POST['end_date']) ? ($_POST['end_date']) : '';
        $present_date = isset($_POST['present_date']) ? ($_POST['present_date']) : '';
        $company = isset($_POST['company']) ? ($_POST['company']) : '';
        $desc = isset($_POST['desc']) ? ($_POST['desc']) : '';

        if ($title != '' && $company != '') {
            $html = '
            <li class="jobsearch-column-12 resume-list-item resume-list-exp">
                <div class="jobsearch-resume-education-wrap">
                    <small>' . ($start_date != '' ? date_i18n(get_option('date_format'), strtotime($start_date)) : '') . ' - ' . ($present_date == 'on' ? 'Present' : '') . ($end_date != '' && $present_date != 'on' ? date_i18n(get_option('date_format'), strtotime($end_date)) : '') . '</small>
                    <h2><a>' . $title . '</a></h2>
                    <span>' . $company . '</span>
                </div>
                <div class="jobsearch-resume-education-btn">
                    <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                    <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish ' . (apply_filters('jobsearch_candash_resume_explist_itmdelclass', 'del-resume-item', $rand_num)) . '" data-id="' . $rand_num . '"></a>
                </div>
                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                    <script>
                        jQuery(document).ready(function () {
                            jQuery("#date-start-' . $rand_num . '").datetimepicker({
                                timepicker: false,
                                format: "d-m-Y"
                            });
                            jQuery("#date-end-' . $rand_num . '").datetimepicker({
                                timepicker: false,
                                format: "d-m-Y"
                            });
                        });
                    </script>
                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                        <li class="jobsearch-column-12">
                            <label>' . esc_html__('Title *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_experience_title[]" type="text" value="' . $title . '">
                        </li>
                        <li class="jobsearch-column-4">
                            <label>' . esc_html__('Start Date *', 'wp-jobsearch') . '</label>
                            <input id="date-start-' . $rand_num . '" name="jobsearch_field_experience_start_date[]" type="text" value="' . $start_date . '">
                        </li>
                        <li class="jobsearch-column-4 cand-expr-todatefield-' . ($rand_num) . '" ' . ($present_date == 'on' ? 'style="display: none;"' : '') . '>
                            <label>' . esc_html__('End Date', 'wp-jobsearch') . '</label>
                            <input id="date-end-' . $rand_num . '" name="jobsearch_field_experience_end_date[]" type="text" value="' . $end_date . '">
                        </li>
                        <li class="jobsearch-column-4 cand-expr-prsntfield">
                            <label>' . esc_html__('Present', 'wp-jobsearch') . '</label>
                            <input class="cand-expr-prsntchkbtn" data-id="' . ($rand_num) . '" type="checkbox" ' . ($present_date == 'on' ? 'checked' : '') . '>
                            <input name="jobsearch_field_experience_date_prsnt[]" type="hidden" value="' . ($present_date) . '">
                        </li>
                        <li class="jobsearch-column-12">';
            $title_html = '<label>' . esc_html__('Company *', 'wp-jobsearch') . '</label>';
            $html .= apply_filters('jobsearch_candash_resume_expcompny_label', $title_html);
            $html .= '
                            <input name="jobsearch_field_experience_company[]" type="text" value="' . $company . '">
                        </li>
                        <li class="jobsearch-column-12">';
            $title_html = '<label>' . esc_html(_x('Description', 'Resume Experience Description', 'wp-jobsearch')) . '</label>';
            $html .= apply_filters('jobsearch_candash_resume_expdesc_label', $title_html);
            $html .= '
                            <textarea name="jobsearch_field_experience_description[]" ' . apply_filters('jobsearch_candash_resume_expdesc_atts', '') . '>' . $desc . '</textarea>
                        </li>
                        <li class="jobsearch-column-12">
                            <input class="update-resume-list-btn" type="submit" value="' . esc_html__('Update', 'wp-jobsearch') . '">
                        </li>
                    </ul>
                </div>
            </li>';

            $ddf_arr = array('msg' => esc_html__('Added Successfully.', 'wp-jobsearch'), 'html' => apply_filters('jobsearch_cand_dash_resume_addexp_ajax_html', $html));
            $ddf_arr = apply_filters('jobsearch_dashcand_resme_expadd_ajaxarr', $ddf_arr);
            echo json_encode($ddf_arr);
            die;
        }

        echo json_encode(array('msg' => esc_html__('Please fill the necessary fields.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }

    public function add_resume_skill_to_list()
    {
        $rand_num = rand(1000000, 99999999);
        $title = isset($_POST['title']) ? ($_POST['title']) : '';
        $skill_percentage = isset($_POST['skill_percentage']) ? ($_POST['skill_percentage']) : '';

        if ($skill_percentage < 0 || $skill_percentage > 100) {
            echo json_encode(array('msg' => esc_html__('The expertise percentage should under 1 to 100.', 'wp-jobsearch'), 'error' => '1'));
            die;
        }

        if ($title != '' && $skill_percentage != '') {
            $html = '
            <li class="jobsearch-column-12 resume-list-item resume-list-skill">
                <div class="jobsearch-add-skills-wrap">
                    <span>' . $skill_percentage . '</span>
                    <h2><a>' . $title . '</a></h2>
                </div>
                <div class="jobsearch-resume-education-btn">
                    <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                    <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish ' . (apply_filters('jobsearch_candash_resume_skilllist_itmdelclass', 'del-resume-item', $rand_num)) . '" data-id="' . $rand_num . '"></a>
                </div>
                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                        <li class="jobsearch-column-12">
                            <label>' . esc_html__('Label *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_skill_title[]" type="text" value="' . $title . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Percentage *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_skill_percentage[]" type="number" placeholder="' . esc_html__('Enter a number between 1 to 100', 'wp-jobsearch') . '" min="1" max="100" value="' . $skill_percentage . '">
                        </li>
                        <li class="jobsearch-column-12">
                            <input class="update-resume-list-btn" type="submit" value="' . esc_html__('Update', 'wp-jobsearch') . '">
                        </li>
                    </ul>
                </div>
            </li>';

            $ddf_arr = array('msg' => esc_html__('Added Successfully.', 'wp-jobsearch'), 'html' => $html);
            $ddf_arr = apply_filters('jobsearch_dashcand_resme_skilladd_ajaxarr', $ddf_arr);
            echo json_encode($ddf_arr);
            die;
        }

        echo json_encode(array('msg' => esc_html__('Please fill the necessary fields.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }
    
    public function add_resume_lang_to_list()
    {
        $rand_num = rand(1000000, 99999999);
        $title = isset($_POST['title']) ? ($_POST['title']) : '';
        $lang_level = isset($_POST['lang_level']) ? ($_POST['lang_level']) : '';
        $lang_percentage = isset($_POST['lang_percentage']) ? ($_POST['lang_percentage']) : '';

        if ($lang_percentage < 0 || $lang_percentage > 100) {
            echo json_encode(array('msg' => esc_html__('The language percentage should under 1 to 100.', 'wp-jobsearch'), 'error' => '1'));
            die;
        }

        if ($title != '') {
            $html = '
            <li class="jobsearch-column-12 resume-list-item resume-list-lang">
                <div class="jobsearch-add-skills-wrap">
                    <span>' . $lang_percentage . '</span>
                    <h2><a>' . $title . '</a></h2>
                </div>
                <div class="jobsearch-resume-education-btn">
                    <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                    <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish ' . (apply_filters('jobsearch_candash_resume_langlist_itmdelclass', 'del-resume-item', $rand_num)) . '" data-id="' . $rand_num . '"></a>
                </div>
                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Label *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_lang_title[]" type="text" value="' . $title . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Level', 'wp-jobsearch') . '</label>
                            <div class="jobsearch-profile-select">
                                <select name="jobsearch_field_lang_level[]" class="selectize-select" placeholder="' . __('Speaking Level', 'wp-jobsearch') . '">
                                    <option value="beginner" ' . ($lang_level == 'beginner' ? 'selected="selected"' : '') . '>' . esc_html__('Beginner', 'wp-jobsearch') . '</option>
                                    <option value="intermediate" ' . ($lang_level == 'intermediate' ? 'selected="selected"' : '') . '>' . esc_html__('Intermediate', 'wp-jobsearch') . '</option>
                                    <option value="proficient" ' . ($lang_level == 'proficient' ? 'selected="selected"' : '') . '>' . esc_html__('Proficient', 'wp-jobsearch') . '</option>
                                </select>
                            </div>
                        </li>
                        <li class="jobsearch-column-12">
                            <label>' . esc_html__('Percentage', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_lang_percentage[]" type="number" placeholder="' . esc_html__('Enter a number between 1 to 100', 'wp-jobsearch') . '" min="1" max="100" value="' . $lang_percentage . '">
                        </li>
                        <li class="jobsearch-column-12">
                            <input class="update-resume-list-btn" type="submit" value="' . esc_html__('Update', 'wp-jobsearch') . '">
                        </li>
                    </ul>
                </div>
            </li>';

            $ddf_arr = array('msg' => esc_html__('Added Successfully.', 'wp-jobsearch'), 'html' => $html);
            $ddf_arr = apply_filters('jobsearch_dashcand_resme_langadd_ajaxarr', $ddf_arr);
            echo json_encode($ddf_arr);
            die;
        }

        echo json_encode(array('msg' => esc_html__('Please fill the necessary fields.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }

    public function add_resume_award_to_list()
    {
        $rand_num = rand(1000000, 99999999);
        $title = isset($_POST['title']) ? ($_POST['title']) : '';
        $award_year = isset($_POST['award_year']) ? ($_POST['award_year']) : '';
        $award_desc = isset($_POST['award_desc']) ? ($_POST['award_desc']) : '';

        if ($title != '' && $award_year != '') {
            $html = '
            <li class="jobsearch-column-12 resume-list-item resume-list-award">
                <div class="jobsearch-add-skills-wrap">
                    <small>' . $award_year . '</small>
                    <h2><a>' . $title . '</a></h2>
                </div>
                <div class="jobsearch-resume-education-btn">
                    <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                    <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish ' . (apply_filters('jobsearch_candash_resume_awardlist_itmdelclass', 'del-resume-item', $rand_num)) . '" data-id="' . $rand_num . '"></a>
                </div>
                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Title *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_award_title[]" type="text" value="' . $title . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Year *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_award_year[]" type="text" value="' . $award_year . '">
                        </li>
                        <li class="jobsearch-column-12">
                            <label>' . esc_html(_x('Description', 'Resume Awards Description', 'wp-jobsearch')) . '</label>
                            <textarea name="jobsearch_field_award_description[]">' . $award_desc . '</textarea>
                        </li>
                        <li class="jobsearch-column-12">
                            <input class="update-resume-list-btn" type="submit" value="' . esc_html__('Update', 'wp-jobsearch') . '">
                        </li>
                    </ul>
                </div>
            </li>';

            echo json_encode(array('msg' => esc_html__('Added Successfully.', 'wp-jobsearch'), 'html' => $html));
            die;
        }

        echo json_encode(array('msg' => esc_html__('Please fill the necessary fields.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }

    public function add_resume_portfolio_to_list()
    {
        $rand_num = rand(1000000, 99999999);
        $title = isset($_POST['title']) ? ($_POST['title']) : '';
        $portfolio_img = isset($_POST['portfolio_img']) ? ($_POST['portfolio_img']) : '';
        $portfolio_url = isset($_POST['portfolio_url']) ? ($_POST['portfolio_url']) : '';
        $portfolio_vurl = isset($_POST['portfolio_vurl']) ? ($_POST['portfolio_vurl']) : '';

        if ($title != '' && $portfolio_img != '') {
            $user_id = get_current_user_id();
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            $portfolio_img_src = jobsearch_get_cand_portimg_url($candidate_id, $portfolio_img);
            $html = '
            <li class="jobsearch-column-3 resume-list-item resume-list-port">
                <figure>
                    <a class="portfolio-img-holder"><span style="background-image: url(\'' . $portfolio_img_src . '\');"></span></a>
                    <figcaption>
                        <span>' . $title . '</span>
                        <div class="jobsearch-company-links">
                            <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                            <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish ' . (apply_filters('jobsearch_candash_resume_portlist_itmdelclass', 'del-resume-item', $rand_num)) . '" data-id="' . $rand_num . '"></a>
                        </div>
                    </figcaption>
                </figure>
                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                        <li class="jobsearch-column-12">
                            <label>' . esc_html__('Title *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_portfolio_title[]" type="text" value="' . $title . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Image *', 'wp-jobsearch') . '</label>
                            <div class="upload-img-holder-sec">
                                <span class="file-loader"></span>
                                <img src="' . $portfolio_img_src . '" alt="">
                                <input name="add_portfolio_img" type="file" style="display: none;">
                                <input type="hidden" class="img-upload-save-field" name="jobsearch_field_portfolio_image[]" value="' . $portfolio_img . '">
                                <a href="javascript:void(0)" class="upload-port-img-btn"><i class="jobsearch-icon jobsearch-add"></i> ' . esc_html__('Upload Photo', 'wp-jobsearch') . '</a>
                            </div>
                        </li>';

            $vurl_html = '
                        <li class="jobsearch-column-12">
                            <label>' . esc_html__('Video URL', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_portfolio_vurl[]" type="text" value="' . $portfolio_vurl . '">
                            <em>' . esc_html__('Add video URL of Youtube, Vimeo.', 'wp-jobsearch') . '</em>
                        </li>';
            $html .= apply_filters('jobsearch_cand_dash_resume_port_addaj_vurl', $vurl_html);

            $html .= '
                        <li class="jobsearch-column-12">
                            <label>' . esc_html__('URL', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_portfolio_url[]" type="text" value="' . $portfolio_url . '">
                        </li>
                        <li class="jobsearch-column-12">
                            <input class="update-resume-list-btn" type="submit" value="' . esc_html__('Update', 'wp-jobsearch') . '">
                        </li>
                    </ul>
                </div>
            </li>';

            $ddf_arr = array('msg' => esc_html__('Added Successfully.', 'wp-jobsearch'), 'html' => apply_filters('jobsearch_cand_dash_resume_addport_ajax_html', $html));
            $ddf_arr = apply_filters('jobsearch_dashcand_resme_portadd_ajaxarr', $ddf_arr);
            echo json_encode($ddf_arr);
            die;
        }

        echo json_encode(array('msg' => esc_html__('Please fill the necessary fields.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }

    public function add_team_member_to_list()
    {
        $rand_num = rand(1000000, 99999999);
        $title = isset($_POST['title']) ? ($_POST['title']) : '';
        $team_designation = isset($_POST['team_designation']) ? ($_POST['team_designation']) : '';
        $team_experience = isset($_POST['team_experience']) ? ($_POST['team_experience']) : '';
        $team_image = isset($_POST['team_image']) ? ($_POST['team_image']) : '';
        $team_facebook = isset($_POST['team_facebook']) ? ($_POST['team_facebook']) : '';
        $team_google = isset($_POST['team_google']) ? ($_POST['team_google']) : '';
        $team_twitter = isset($_POST['team_twitter']) ? ($_POST['team_twitter']) : '';
        $team_linkedin = isset($_POST['team_linkedin']) ? ($_POST['team_linkedin']) : '';
        $team_description = isset($_POST['team_description']) ? ($_POST['team_description']) : '';

        if ($title != '' && $team_designation != '' && $team_experience != '' && $team_image != '') {
            $html = '
            <li class="jobsearch-column-3">
                <figure>
                    <a class="portfolio-img-holder"><span style="background-image: url(\'' . $team_image . '\');"></span></a>
                    <figcaption>
                        <span>' . $title . '</span>
                        <div class="jobsearch-company-links">
                            <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                            <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish del-resume-item"></a>
                        </div>
                    </figcaption>
                </figure>
                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Member Title *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_team_title[]" type="text" value="' . $title . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Designation *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_team_designation[]" type="text" value="' . $team_designation . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Experience *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_team_experience[]" type="text" value="' . $team_experience . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Image *', 'wp-jobsearch') . '</label>
                            <div class="upload-img-holder-sec">
                                <span class="file-loader"></span>
                                <img src="' . $team_image . '" alt="">
                                <input name="team_image" type="file" style="display: none;">
                                <input type="hidden" class="img-upload-save-field" name="jobsearch_field_team_image[]" value="' . $team_image . '">
                                <a href="javascript:void(0)" class="upload-port-img-btn"><i class="jobsearch-icon jobsearch-add"></i> ' . esc_html__('Upload Photo', 'wp-jobsearch') . '</a>
                            </div>
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Facebook URL', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_team_facebook[]" type="text" value="' . ($team_facebook) . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Google+ URL', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_team_google[]" type="text" value="' . ($team_google) . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('TwitterURL', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_team_twitter[]" type="text" value="' . ($team_twitter) . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Linkedin URL', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_team_linkedin[]" type="text" value="' . ($team_linkedin) . '">
                        </li>
                        <li class="jobsearch-column-12">
                            <label>' . esc_html__('Description', 'wp-jobsearch') . '</label>
                            <textarea name="jobsearch_field_team_description[]">' . ($team_description) . '</textarea>
                        </li>
                        <li class="jobsearch-column-12">
                            <input class="update-resume-list-btn" type="submit" value="' . esc_html__('Update', 'wp-jobsearch') . '">
                        </li>
                    </ul>
                </div>
            </li>';

            echo json_encode(array('msg' => esc_html__('Added Successfully.', 'wp-jobsearch'), 'html' => $html));
            die;
        }

        echo json_encode(array('msg' => esc_html__('Please fill the necessary fields.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }

    public function add_emp_awards_to_list()
    {
        $rand_num = rand(1000000, 99999999);
        $title = isset($_POST['title']) ? ($_POST['title']) : '';
        $award_image = isset($_POST['award_image']) ? ($_POST['award_image']) : '';

        if ($title != '' && $award_image != '') {
            $html = '
            <li class="jobsearch-column-3">
                <figure>
                    <a class="portfolio-img-holder"><span style="background-image: url(\'' . $award_image . '\');"></span></a>
                    <figcaption>
                        <span>' . $title . '</span>
                        <div class="jobsearch-company-links">
                            <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                            <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish del-resume-item"></a>
                        </div>
                    </figcaption>
                </figure>
                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Award Title *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_award_title[]" type="text" value="' . $title . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Image *', 'wp-jobsearch') . '</label>
                            <div class="upload-img-holder-sec">
                                <span class="file-loader"></span>
                                <img src="' . $award_image . '" alt="">
                                <input name="award_image" type="file" style="display: none;">
                                <input type="hidden" class="img-upload-save-field" name="jobsearch_field_award_image[]" value="' . $award_image . '">
                                <a href="javascript:void(0)" class="upload-port-img-btn"><i class="jobsearch-icon jobsearch-add"></i> ' . esc_html__('Upload Photo', 'wp-jobsearch') . '</a>
                            </div>
                        </li>
                        <li class="jobsearch-column-12">
                            <input class="update-resume-list-btn" type="submit" value="' . esc_html__('Update', 'wp-jobsearch') . '">
                        </li>
                    </ul>
                </div>
            </li>';

            echo json_encode(array('msg' => esc_html__('Added Successfully.', 'wp-jobsearch'), 'html' => $html));
            die;
        }

        echo json_encode(array('msg' => esc_html__('Please fill the necessary fields.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }

    public function add_emp_affiliations_to_list()
    {
        $rand_num = rand(1000000, 99999999);
        $title = isset($_POST['title']) ? ($_POST['title']) : '';
        $affiliation_image = isset($_POST['affiliation_image']) ? ($_POST['affiliation_image']) : '';

        if ($title != '' && $affiliation_image != '') {
            $html = '
            <li class="jobsearch-column-3">
                <figure>
                    <a class="portfolio-img-holder"><span style="background-image: url(\'' . $affiliation_image . '\');"></span></a>
                    <figcaption>
                        <span>' . $title . '</span>
                        <div class="jobsearch-company-links">
                            <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                            <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish del-resume-item"></a>
                        </div>
                    </figcaption>
                </figure>
                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Award Title *', 'wp-jobsearch') . '</label>
                            <input name="jobsearch_field_affiliation_title[]" type="text" value="' . $title . '">
                        </li>
                        <li class="jobsearch-column-6">
                            <label>' . esc_html__('Image *', 'wp-jobsearch') . '</label>
                            <div class="upload-img-holder-sec">
                                <span class="file-loader"></span>
                                <img src="' . $affiliation_image . '" alt="">
                                <input name="affiliation_image" type="file" style="display: none;">
                                <input type="hidden" class="img-upload-save-field" name="jobsearch_field_affiliation_image[]" value="' . $affiliation_image . '">
                                <a href="javascript:void(0)" class="upload-port-img-btn"><i class="jobsearch-icon jobsearch-add"></i> ' . esc_html__('Upload Photo', 'wp-jobsearch') . '</a>
                            </div>
                        </li>
                        <li class="jobsearch-column-12">
                            <input class="update-resume-list-btn" type="submit" value="' . esc_html__('Update', 'wp-jobsearch') . '">
                        </li>
                    </ul>
                </div>
            </li>';

            echo json_encode(array('msg' => esc_html__('Added Successfully.', 'wp-jobsearch'), 'html' => $html));
            die;
        }

        echo json_encode(array('msg' => esc_html__('Please fill the necessary fields.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }

    public function dashboard_adding_portfolio_img_url() {
        
        $rand_num = rand(100000000, 999999999);

        //$atach_id = jobsearch_insert_upload_attach('add_portfolio_img');
        $atach_arr = jobsearch_upload_cand_port_img('add_portfolio_img');

        if (!empty($atach_arr)) {
            
            if (isset($_POST['pid'])) {
                $candidate_id = $_POST['pid'];
            } else {
                $user_id = get_current_user_id();
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
            }
            $post_imgs_parr = get_post_meta($candidate_id, 'jobsearch_portfolio_imgs_arr', true);
            $post_imgs_parr = empty($post_imgs_parr) ? array() : $post_imgs_parr;
            
            $post_imgs_parr[$rand_num] = $atach_arr;
            update_post_meta($candidate_id, 'jobsearch_portfolio_imgs_arr', $post_imgs_parr);
            
            $img_url = jobsearch_get_cand_portimg_url($candidate_id, $rand_num);

            echo json_encode(array('img_id' => $rand_num, 'img_url' => $img_url));
        }
        wp_die();
    }

    public function dashboard_adding_team_img_url()
    {

        $atach_id = jobsearch_insert_upload_attach('team_image');

        if ($atach_id > 0) {
            $thumb_image = wp_get_attachment_image_src($atach_id, 'thumbnail');
            $img_url = isset($thumb_image[0]) && esc_url($thumb_image[0]) != '' ? $thumb_image[0] : '';

            echo json_encode(array('img_url' => $img_url));
        }
        wp_die();
    }

    public function candidate_contact_form_submit()
    {
        global $jobsearch_plugin_options;

        $cur_user_id = get_current_user_id();

        $cnt__cand_wout_log = isset($jobsearch_plugin_options['cand_cntct_wout_login']) ? $jobsearch_plugin_options['cand_cntct_wout_login'] : '';

        $uname = isset($_POST['u_name']) ? $_POST['u_name'] : '';
        $uemail = isset($_POST['u_email']) ? $_POST['u_email'] : '';
        $uphone = isset($_POST['u_number']) ? $_POST['u_number'] : '';
        $umsg = isset($_POST['u_msg']) ? $_POST['u_msg'] : '';
        $user_id = isset($_POST['u_candidate_id']) ? $_POST['u_candidate_id'] : '';

        $user_obj = get_user_by('ID', $user_id);

        $cnt_email = $user_obj->user_email;

        $error = 0;
        $msg = '';

        if ($cnt__cand_wout_log != 'on') {
            $user_is_employer = jobsearch_user_is_employer($cur_user_id);
            if (!$user_is_employer) {
                $error = 1;
                $msg = esc_html__('Only an employer can contact this user.', 'wp-jobsearch');

                echo json_encode(array('msg' => $msg));
                wp_die();
            }
        }

        jobsearch_captcha_verify();

        if ($umsg != '' && $error == 0) {
            $umsg = esc_html($umsg);
        } else {
            $error = 1;
            $msg = esc_html__('Please type your message.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg));
            wp_die();
        }

        if ($uemail != '' && $error == 0 && filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
            $uemail = esc_html($uemail);
        } else {
            $error = 1;
            $msg = esc_html__('Please Enter a valid email.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg));
            wp_die();
        }
        if ($uname != '' && $error == 0) {
            $uname = esc_html($uname);
        } else {
            $error = 1;
            $msg = esc_html__('Please Enter your Name.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg));
            wp_die();
        }

        if ($msg == '' && $error == 0) {

            $subject = sprintf(__('%s - Contact Form Message', 'wp-jobsearch'), get_bloginfo('name'));

            $headers = "From: " . ($uemail) . "\r\n";
            $headers .= "Reply-To: " . ($uemail) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            $email_message = sprintf(esc_html__('Name: %s', 'wp-jobsearch'), $uname) . "<br>";
            $email_message .= sprintf(esc_html__('Email: %s', 'wp-jobsearch'), $uemail) . "<br>";
            $email_message .= sprintf(esc_html__('Phone Number: %s', 'wp-jobsearch'), $uphone) . "<br>";
            $email_message .= sprintf(esc_html__('Message: %s', 'wp-jobsearch'), $umsg) . "<br>";

            do_action('jobsearch_candidate_contact_form', $user_obj, $uname, $uemail, $uphone, $umsg);
            $msg = esc_html__('Mail sent successfully', 'wp-jobsearch');
        }

        echo json_encode(array('msg' => $msg));
        wp_die();
    }

    public function employer_contact_form_submit()
    {
        global $jobsearch_plugin_options;

        $cur_user_id = get_current_user_id();

        $emp_det_contact_form = isset($jobsearch_plugin_options['emp_det_contact_form']) ? $jobsearch_plugin_options['emp_det_contact_form'] : '';

        $uname = isset($_POST['u_name']) ? $_POST['u_name'] : '';
        $uemail = isset($_POST['u_email']) ? $_POST['u_email'] : '';
        $uphone = isset($_POST['u_number']) ? $_POST['u_number'] : '';
        $umsg = isset($_POST['u_msg']) ? $_POST['u_msg'] : '';
        $user_id = isset($_POST['u_employer_id']) ? $_POST['u_employer_id'] : '';

        $user_obj = get_user_by('ID', $user_id);

        $cnt_email = $user_obj->user_email;

        $error = 0;
        $msg = '';

        if ($emp_det_contact_form != 'on') {
            $user_is_candidate = jobsearch_user_is_candidate($cur_user_id);
            if (!$user_is_candidate) {
                $error = 1;
                $msg = esc_html__('Only a candidate can contact this user.', 'wp-jobsearch');
                echo json_encode(array('msg' => $msg));
                wp_die();
            } else {
                $user_candidate_id = jobsearch_get_user_candidate_id($cur_user_id);
                if ($user_candidate_id > 0) {
                    $candidate_status = get_post_meta($user_candidate_id, 'jobsearch_field_candidate_approved', true);
                    if ($candidate_status != 'on') {
                        $error = 1;
                        $msg = esc_html__('Your profile is not approved yet.', 'wp-jobsearch');
                        echo json_encode(array('msg' => $msg));
                        wp_die();
                    }
                }
            }
        }

        jobsearch_captcha_verify();

        if ($umsg != '' && $error == 0) {
            $umsg = esc_html($umsg);
        } else {
            $error = 1;
            $msg = esc_html__('Please type your message.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg));
            wp_die();
        }

        if ($uemail != '' && $error == 0 && filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
            $uemail = esc_html($uemail);
        } else {
            $error = 1;
            $msg = esc_html__('Please Enter a valid email.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg));
            wp_die();
        }
        if ($uname != '' && $error == 0) {
            $uname = esc_html($uname);
        } else {
            $error = 1;
            $msg = esc_html__('Please Enter your Name.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg));
            wp_die();
        }

        if ($msg == '' && $error == 0) {

            $subject = sprintf(__('%s - Contact Form Message', 'wp-jobsearch'), get_bloginfo('name'));

            $headers = "From: " . ($uemail) . "\r\n";
            $headers .= "Reply-To: " . ($uemail) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            $email_message = sprintf(esc_html__('Name : %s', 'wp-jobsearch'), $uname) . "<br>";
            $email_message .= sprintf(esc_html__('Email : %s', 'wp-jobsearch'), $uemail) . "<br>";
            $email_message .= sprintf(esc_html__('Phone Number: %s', 'wp-jobsearch'), $uphone) . "<br>";
            $email_message .= sprintf(esc_html__('Message : %s', 'wp-jobsearch'), $umsg) . "<br>";

            do_action('jobsearch_employer_contact_form', $user_obj, $uname, $uemail, $uphone, $umsg);
            $msg = esc_html__('Mail sent successfully', 'wp-jobsearch');
        }

        echo json_encode(array('msg' => $msg));
        wp_die();
    }

    public function pagination($total_pages = 1, $page = 1, $url = '', $return = false)
    {

        $query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';

        if ($url != '') {
            $base = $url . '?' . remove_query_arg('page_num', $query_string) . '%_%';
        } else {
            $base = get_permalink() . '?' . remove_query_arg('page_num', $query_string) . '%_%';
        }

        $pagination = paginate_links(array(
            'base' => $base, // the base URL, including query arg
            'format' => '&page_num=%#%',
            'total' => $total_pages, // the total number of pages we have
            'current' => $page, // the current page
            'end_size' => 1,
            'mid_size' => 2,
            'type' => 'array',
            'prev_text' => '<span><i class="jobsearch-icon jobsearch-arrows4"></i></span>',
            'next_text' => '<span><i class="jobsearch-icon jobsearch-arrows4"></i></span>',
        ));
        $html = '';
        if (is_array($pagination) && sizeof($pagination) > 0) {
            $html .= '<ul class="jobsearch-page-numbers">';
            foreach ($pagination as $link) {
                if (strpos($link, 'current') !== false) {
                    $html .= '<li><span class="jobsearch-page-numbers current">' . preg_replace("/[^0-9]/", "", $link) . '</span></li>';
                } else {
                    $html .= '<li>' . $link . '</li>';
                }
            }
            $html .= '</ul>';
        }

        if ($return == true) {
            return $html;
        } else {
            echo force_balance_tags($html);
        }
    }
    
    public function doing_feat_job_with_alorder() {
        $response = array();
        
        $current_date = current_time('timestamp');
        
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
        $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';
        if ($job_id > 0 && $order_id > 0) {
            $user_id = get_current_user_id();
            $is_user_member = false;
            if (jobsearch_user_isemp_member($user_id)) {
                $is_user_member = true;
                $employer_id = jobsearch_user_isemp_member($user_id);
                $user_id = jobsearch_get_employer_user_id($employer_id);
            } else {
                $employer_id = jobsearch_get_user_employer_id($user_id);
            }
            
            $order_package = get_post_meta($order_id, 'jobsearch_order_package', true);
            $order_package_obj = get_post($order_package);

            if (is_object($order_package_obj)) {
                $package_id = $order_package_obj->ID;
                $order_pkg_type = get_post_meta($package_id, 'jobsearch_field_package_type', true);
                
                $is_pkg_remain = false;
                if ($order_pkg_type == 'featured_jobs') {
                    $is_pkg_remain = jobsearch_fjobs_pckg_is_subscribed($package_id, $user_id);
                } else if ($order_pkg_type == 'emp_allin_one') {
                    $is_pkg_remain = jobsearch_allinpckg_is_subscribed($package_id, $user_id);
                } else if ($order_pkg_type == 'employer_profile') {
                    $is_pkg_remain = jobsearch_emprofpckg_is_subscribed($package_id, $user_id);
                }
                //
                if ($is_pkg_remain) {
                    //
                    if ($order_pkg_type == 'emp_allin_one') {
                        $order_add_fcred = get_post_meta($order_id, 'jobsearch_order_fjobs_list', true);
                    } else if ($order_pkg_type == 'employer_profile') {
                        $order_add_fcred = get_post_meta($order_id, 'jobsearch_order_fjobs_list', true);
                    } else if ($order_pkg_type == 'featured_jobs') {
                        $order_add_fcred = get_post_meta($order_id, 'jobsearch_order_featc_list', true);
                    }
                    if ($order_add_fcred != '') {
                        $order_add_fcred = explode(',', $order_add_fcred);
                        $order_add_fcred[] = $job_id;
                        $order_add_fcred = implode(',', $order_add_fcred);
                    } else {
                        $order_add_fcred = $job_id;
                    }
                    
                    $in_the_pkgs = false;
                    if ($order_pkg_type == 'emp_allin_one') {
                        update_post_meta($order_id, 'jobsearch_order_fjobs_list', $order_add_fcred);
                        
                        //
                        $fcred_exp_time = get_post_meta($order_id, 'fall_cred_expiry_time', true);
                        $fcred_exp_time_unit = get_post_meta($order_id, 'fall_cred_expiry_time_unit', true);
                        $in_the_pkgs = true;
                    } else if ($order_pkg_type == 'employer_profile') {
                        update_post_meta($order_id, 'jobsearch_order_fjobs_list', $order_add_fcred);
                        
                        //
                        $fcred_exp_time = get_post_meta($order_id, 'emprof_fcred_expiry_time', true);
                        $fcred_exp_time_unit = get_post_meta($order_id, 'emprof_fcred_expiry_time_unit', true);
                        $in_the_pkgs = true;
                    } else if ($order_pkg_type == 'featured_jobs') {
                        update_post_meta($order_id, 'jobsearch_order_featc_list', $order_add_fcred);
                        
                        //
                        $fcred_exp_time = get_post_meta($order_id, 'fcred_expiry_time', true);
                        $fcred_exp_time_unit = get_post_meta($order_id, 'fcred_expiry_time_unit', true);
                        $in_the_pkgs = true;
                    }
                    //
                    
                    if ($in_the_pkgs) {
                        $tofeat_expiry_time = strtotime("+" . $fcred_exp_time . " " . $fcred_exp_time_unit, $current_date);
                        if ($tofeat_expiry_time > 0 && $tofeat_expiry_time > $current_date) {
                            update_post_meta($job_id, 'jobsearch_field_job_featured', 'on');

                            $order_expiry_datetime = date('d-m-Y H:i:s', $tofeat_expiry_time);
                            update_post_meta($job_id, 'jobsearch_field_job_feature_till', $order_expiry_datetime);
                            $response['error'] = '0';
                            $response['msg'] = esc_html__('reloading...', 'wp-jobsearch');
                            echo json_encode($response);
                            wp_die();
                        }
                    }
                }
            }
        }
        $response['error'] = '1';
        $response['msg'] = esc_html__('You cannot make this job feature.', 'wp-jobsearch');
        echo json_encode($response);
        wp_die();
    }
    
    public function user_change_email_check_avail() {
        $email = $_POST['email'];
        
        $user_obj = wp_get_current_user();
        $user_email = $user_obj->user_email;
        
        if ($email != '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (email_exists($email) && $email != $user_email) {
                wp_send_json(array('error' => '1', 'msg' => esc_html__('This email address is already taken.', 'wp-jobsearch')));
            } else {
                wp_send_json(array('error' => '0', 'msg' => esc_html__('This email address is available.', 'wp-jobsearch')));
            }
        } else {
            wp_send_json(array('error' => '1', 'msg' => esc_html__('Please enter a valid email address.', 'wp-jobsearch')));
        }
    }

    public function doing_mjobs_feature_job()
    {
        $response = array();

        $pkg_id = isset($_POST['pkg_id']) ? $_POST['pkg_id'] : '';
        $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';
        if ($job_id > 0 && $pkg_id) {

            $package_id = $pkg_id;
            $pkg_charges_type = get_post_meta($package_id, 'jobsearch_field_charges_type', true);
            $pkg_attach_product = get_post_meta($package_id, 'jobsearch_package_product', true);
            if (!class_exists('WooCommerce')) {
                $response['error'] = '1';
                $response['msg'] = esc_html__('WooCommerce Plugin not exist.', 'wp-jobsearch');
                echo json_encode($response);
                wp_die();
            }
            if ($pkg_charges_type == 'paid') {
                $package_product_obj = $pkg_attach_product != '' ? get_page_by_path($pkg_attach_product, 'OBJECT', 'product') : '';

                if ($pkg_attach_product != '' && is_object($package_product_obj)) {
                    $product_id = $package_product_obj->ID;
                } else {
                    $response['error'] = '1';
                    $response['msg'] = esc_html__('Selected Package Product not found.', 'wp-jobsearch');
                    echo json_encode($response);
                    wp_die();
                }
                // add to cart and checkout
                ob_start();
                do_action('jobsearch_woocommerce_payment_checkout', $package_id, 'checkout_url', $job_id);
                $checkout_url = ob_get_clean();
            } else {
                // creating order and adding product to order
                do_action('jobsearch_create_new_job_packg_order', $package_id, $job_id);
                $response['error'] = '0';
                $reloding_script = '<script>window.location.reload(true);</script>';
                $response['msg'] = esc_html__('reloading...', 'wp-jobsearch') . $reloding_script;
                echo json_encode($response);
                wp_die();
            }

            $reloding_script = '<script>window.location.href = \'' . $checkout_url . '\';</script>';
            $response['error'] = '0';
            $response['msg'] = esc_html__('redirecting...', 'wp-jobsearch') . $reloding_script;
        } else {
            $response['error'] = '1';
            $response['msg'] = esc_html__('Please select a package first.', 'wp-jobsearch');
        }
        echo json_encode($response);
        wp_die();
    }

    public function duplicate_job() {
        global $wpdb, $jobsearch_plugin_options;

        $duplicate_jobs_allow = isset($jobsearch_plugin_options['duplicate_the_job']) ? $jobsearch_plugin_options['duplicate_the_job'] : '';

        if ($duplicate_jobs_allow != 'on') {
            die;
        }

        $free_jobs_allow = isset($jobsearch_plugin_options['free-jobs-allow']) ? $jobsearch_plugin_options['free-jobs-allow'] : '';

        $original_id = isset($_POST['origjob_id']) ? $_POST['origjob_id'] : '';
        
        $is_user_can = $is_admin = false;
        if (is_super_admin() || current_user_can('administrator')) {
            $is_user_can = true;
            $is_admin = true;
        }
        $is_admin = apply_filters('jobsearch_injobduplic_check_user_isadmin', $is_admin, $original_id);
        
        if (jobsearch_is_employer_job($original_id)) {
            $is_user_can = true;
        }

        if ($original_id > 0 && get_post_type($original_id) == 'job' && $is_user_can) {
            $duplicate = get_post($original_id, 'ARRAY_A');

            $duplicate['post_title'] = $duplicate['post_title'];
            $duplicate['post_name'] = sanitize_title($duplicate['post_name']);

            $duplicate['post_status'] = 'publish';

            $duplicate['post_type'] = 'job';

            // Set the post date
            $post_timestamp = 'duplicate';
            $timestamp = ($post_timestamp == 'duplicate') ? strtotime($duplicate['post_date']) : current_time('timestamp', 0);
            $timestamp_gmt = ($post_timestamp == 'duplicate') ? strtotime($duplicate['post_date_gmt']) : current_time('timestamp', 1);

            $duplicate['post_date'] = date('Y-m-d H:i:s', $timestamp);
            $duplicate['post_date_gmt'] = date('Y-m-d H:i:s', $timestamp_gmt);
            $duplicate['post_modified'] = date('Y-m-d H:i:s', current_time('timestamp', 0));
            $duplicate['post_modified_gmt'] = date('Y-m-d H:i:s', current_time('timestamp', 1));

            // Remove some of the keys
            unset($duplicate['ID']);
            unset($duplicate['guid']);
            unset($duplicate['comment_count']);

            // Insert the post into the database
            $duplicate_id = wp_insert_post($duplicate);

            // Duplicate all the taxonomies/terms
            $taxonomies = get_object_taxonomies($duplicate['post_type']);
            foreach ($taxonomies as $taxonomy) {
                $terms = wp_get_post_terms($original_id, $taxonomy, array('fields' => 'names'));
                wp_set_object_terms($duplicate_id, $terms, $taxonomy);
            }

            // Duplicate all the custom fields
            $custom_fields = get_post_custom($original_id);
            foreach ($custom_fields as $key => $value) {
                if (is_array($value) && count($value) > 0) {
                    foreach ($value as $i => $v) {
                        $result = $wpdb->insert($wpdb->prefix . 'postmeta', array(
                            'post_id' => $duplicate_id,
                            'meta_key' => $key,
                            'meta_value' => $v
                        ));
                    }
                }
            }
            update_post_meta($duplicate_id, 'jobsearch_field_job_publish_date', current_time('timestamp'));
            if ($free_jobs_allow != 'on') {
                if ($is_admin) {
                    $original_job_expiry = get_post_meta($original_id, 'jobsearch_field_job_expiry_date', true);
                    $to_put_expiry = strtotime("+30 day", current_time('timestamp'));
                    if ($original_job_expiry > $to_put_expiry) {
                        update_post_meta($duplicate_id, 'jobsearch_field_job_expiry_date', $original_job_expiry);
                    } else {
                        update_post_meta($duplicate_id, 'jobsearch_field_job_expiry_date', $to_put_expiry);
                    }
                } else {
                    update_post_meta($duplicate_id, 'jobsearch_field_job_expiry_date', '');
                    update_post_meta($duplicate_id, 'jobsearch_field_job_status', 'pending');
                }
            } else {
                $job_expiry_days = isset($jobsearch_plugin_options['free-job-post-expiry']) ? $jobsearch_plugin_options['free-job-post-expiry'] : '';
                // job expiry time
                if ($job_expiry_days > 0) {
                    $job_expiry_date = strtotime("+" . $job_expiry_days . " day", current_time('timestamp'));
                    update_post_meta($duplicate_id, 'jobsearch_field_job_expiry_date', $job_expiry_date);
                }
            }
            //
            update_post_meta($duplicate_id, 'jobsearch_field_job_featured', '');
            update_post_meta($duplicate_id, 'jobsearch_field_job_feature_till', '');
            //
            update_post_meta($duplicate_id, 'jobsearch_job_views_count', 0);
            update_post_meta($duplicate_id, 'attach_packages_array', '');
            update_post_meta($duplicate_id, 'jobsearch_field_job_filled', '');
            update_post_meta($duplicate_id, 'jobsearch_job_applicants_list', '');
            update_post_meta($duplicate_id, 'jobsearch_viewed_candidates', '');
            update_post_meta($duplicate_id, '_job_short_interview_list', '');
            update_post_meta($duplicate_id, '_job_reject_interview_list', '');
            //
            echo json_encode(array('duplicate' => '1'));
            die;
        }
        //
        echo json_encode(array('duplicate' => '0'));
        die;
    }

}

global $Jobsearch_User_Dashboard_Settings;
$Jobsearch_User_Dashboard_Settings = new JobSearch_User_Dashboard_Settings();
