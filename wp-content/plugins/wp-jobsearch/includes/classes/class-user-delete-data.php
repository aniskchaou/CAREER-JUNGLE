<?php
if (!defined('ABSPATH')) {
    die;
}

class JobSearch_Delete_User_Profile_Data {
    
    public function __construct() {
        //
        add_action('jobsearch_candidate_delete_all_profile_data', array($this, 'candidate_delete_profile_data'), 15, 1);
        add_action('jobsearch_employer_delete_all_profile_data', array($this, 'employer_delete_profile_data'), 15, 1);
        
        //
        add_action('before_delete_post', array($this, 'before_delete_post'), 15, 2);
        add_action('delete_user', array($this, 'delete_user_call'), 15);
    }
    
    public function candidate_delete_profile_data($candidate_id) {
        global $jobsearch_done_userdel;
        
        $user_avatar = get_post_meta($candidate_id, 'jobsearch_user_avatar_url', true);
        $user_cover_image = get_post_meta($candidate_id, 'jobsearch_user_cover_imge', true);
        $portfolio_imgs = get_post_meta($candidate_id, 'jobsearch_portfolio_imgs_arr', true);
        
        $single_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);
        $candidate_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
        
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        WP_Filesystem();
        global $wp_filesystem;

        if (!empty($user_avatar) && isset($user_avatar['file_path'])) {
            $root_folder = $user_avatar['file_path'];
            $wp_filesystem->rmdir($root_folder, true);
        }
        if (!empty($user_cover_image) && isset($user_cover_image['file_path'])) {
            $root_folder = $user_cover_image['file_path'];
            $wp_filesystem->rmdir($root_folder, true);
        }
        if (!empty($portfolio_imgs)) {
            foreach ($portfolio_imgs as $portfolio_img) {
                if (!empty($portfolio_img) && isset($portfolio_img['path'])) {
                    $pimg_path = $portfolio_img['path'];
                    $root_folder = plugin_dir_path($pimg_path);
                    $wp_filesystem->rmdir($root_folder, true);
                }
            }
        }
        if (!empty($single_cv_file) && isset($single_cv_file['file_url'])) {
            $file_url = $single_cv_file['file_url'];
            $root_folder = plugin_dir_path($file_url);
            $root_folder = str_replace(get_site_url() . '/', ABSPATH, $root_folder);
            $wp_filesystem->rmdir($root_folder, true);
        }
        if (!empty($candidate_cv_files)) {
            foreach ($candidate_cv_files as $candidate_cv_file) {
                if (!empty($candidate_cv_file) && isset($candidate_cv_file['file_url'])) {
                    $file_url = $candidate_cv_file['file_url'];
                    $root_folder = plugin_dir_path($file_url);
                    $root_folder = str_replace(get_site_url() . '/', ABSPATH, $root_folder);
                    $wp_filesystem->rmdir($root_folder, true);
                }
            }
        }
        
        //
        $attach_user = get_post_meta($candidate_id, 'jobsearch_user_id', true);
        $atach_user_obj = get_user_by('ID', $attach_user);
        
        if ($attach_user > 0 && $jobsearch_done_userdel !== true && isset($atach_user_obj->roles) && in_array('jobsearch_candidate', (array)$atach_user_obj->roles)) {
            //wp_delete_user($attach_user);
        }
    }
    
    public function employer_delete_profile_data($employer_id) {
        global $jobsearch_done_userdel;
        
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
        if (isset($jobs_query->posts) && !empty($jobs_query->posts)) {
            $all_posts = $jobs_query->posts;
            foreach ($all_posts as $_post_id) {
                wp_delete_post($_post_id, true);
            }
        }
        wp_reset_postdata();
        
        $attach_user = get_post_meta($employer_id, 'jobsearch_user_id', true);
        $atach_user_obj = get_user_by('ID', $attach_user);
        
        if ($attach_user > 0 && $jobsearch_done_userdel !== true && isset($atach_user_obj->roles) && in_array('jobsearch_employer', (array)$atach_user_obj->roles)) {
            //wp_delete_user($attach_user);
        }
    }
    
    public function before_delete_post($post_id, $post_obj) {
        global $jobsearch_done_membpostdel;
        if ($post_obj->post_type == 'candidate') {
            $jobsearch_done_membpostdel = true;
            do_action('jobsearch_candidate_delete_all_profile_data', $post_id);
        }
        
        if ($post_obj->post_type == 'employer') {
            $jobsearch_done_membpostdel = true;
            do_action('jobsearch_employer_delete_all_profile_data', $post_id);
        }
    }
    
    public function delete_user_call($user_id) {
        global $jobsearch_done_userdel, $jobsearch_done_membpostdel;
        $user_employer_id = get_user_meta($user_id, 'jobsearch_employer_id', true);
        $user_candidate_id = get_user_meta($user_id, 'jobsearch_candidate_id', true);
        if ($user_candidate_id > 0 && get_post_type($user_candidate_id) == 'candidate') {
            $jobsearch_done_userdel = true;
            wp_delete_post($user_candidate_id, true);
        }
        if ($user_employer_id > 0 && get_post_type($user_employer_id) == 'employer') {
            $jobsearch_done_userdel = true;
            wp_delete_post($user_employer_id, true);
        }
    }

}

return new JobSearch_Delete_User_Profile_Data();
