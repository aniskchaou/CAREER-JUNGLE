<?php
/*
  Class : Jobsearch_Package_Custom_Fields
 */

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main class
class Jobsearch_Package_Custom_Fields {

    // hook things up
    public function __construct() {

        //
        add_filter('jobsearch_get_job_package_fields_list', array($this, 'job_package_fields'));
        add_filter('jobsearch_get_featured_jobs_package_fields_list', array($this, 'featured_jobs_package_fields'));
        add_filter('jobsearch_get_all_in_one_package_fields_list', array($this, 'all_in_one_package_fields'));
        add_filter('jobsearch_get_cv_package_fields_list', array($this, 'cv_package_fields'));
        add_filter('jobsearch_get_candidate_package_fields_list', array($this, 'candidate_package_fields'));
        add_filter('jobsearch_get_feature_job_package_fields_list', array($this, 'feature_job_package_fields'));
        add_filter('jobsearch_get_promote_profile_package_fields_list', array($this, 'promote_profile_package_fields'));
        add_filter('jobsearch_get_urgent_pkg_package_fields_list', array($this, 'urgent_pkg_package_fields'));
        
        //
        add_filter('jobsearch_get_cand_profpkg_cfields_list', array($this, 'cand_profpkg_fields'));
        add_filter('jobsearch_get_emp_profpkg_cfields_list', array($this, 'emp_profpkg_fields'));
    }
    
    public function job_package_fields() {
        $fields = array(
            'package_expiry_time',
            'package_expiry_time_unit',
            'num_of_jobs',
            'job_expiry_time',
            'job_expiry_time_unit',
        );
        
        return apply_filters('jobsearch_add_job_package_fields_list', $fields);
    }
    
    public function featured_jobs_package_fields() {
        $fields = array(
            'package_expiry_time',
            'package_expiry_time_unit',
            'num_of_fjobs',
            'feat_job_credits',
            'fjob_expiry_time',
            'fjob_expiry_time_unit',
            'fcred_expiry_time',
            'fcred_expiry_time_unit',
        );
        
        return apply_filters('jobsearch_add_featured_jobs_package_fields_list', $fields);
    }
    
    public function all_in_one_package_fields() {
        $fields = array(
            'package_expiry_time',
            'package_expiry_time_unit',
            'allin_num_jobs',
            'allin_num_fjobs',
            'allinjob_expiry_time',
            'allinjob_expiry_time_unit',
            'fall_cred_expiry_time',
            'fall_cred_expiry_time_unit',
            'allin_num_cvs',
            'allinview_consume_cvs',
        );
        
        return apply_filters('jobsearch_add_all_in_one_package_fields_list', $fields);
    }
    
    public function candidate_package_fields() {
        $fields = array(
            'package_expiry_time',
            'package_expiry_time_unit',
            'num_of_apps',
        );
        
        return apply_filters('jobsearch_add_candidate_package_fields_list', $fields);
    }
    
    public function cv_package_fields() {
        $fields = array(
            'package_expiry_time',
            'package_expiry_time_unit',
            'num_of_cvs',
            'onview_consume_cvs',
        );
        
        return apply_filters('jobsearch_add_cv_package_fields_list', $fields);
    }
    
    public function feature_job_package_fields() {
        $fields = array(
            'package_expiry_time',
            'package_expiry_time_unit',
        );
        
        return apply_filters('jobsearch_add_feature_job_package_fields_list', $fields);
    }
    
    public function promote_profile_package_fields() {
        $fields = array(
            'package_expiry_time',
            'package_expiry_time_unit',
        );
        
        return apply_filters('jobsearch_add_promote_profile_package_fields_list', $fields);
    }
    
    public function urgent_pkg_package_fields() {
        $fields = array(
            'package_expiry_time',
            'package_expiry_time_unit',
        );
        
        return apply_filters('jobsearch_add_urgent_pkg_package_fields_list', $fields);
    }
    
    public function cand_profpkg_fields() {
        $fields = array(
            'package_expiry_time',
            'package_expiry_time_unit',
            'candprof_num_apps',
            'candprof_promote_profile',
            'candprof_promote_expiry_time',
            'candprof_promote_expiry_time_unit',
        );
        
        return apply_filters('jobsearch_add_cand_profpkg_fields_list', $fields);
    }
    
    public function emp_profpkg_fields() {
        $fields = array(
            'package_expiry_time',
            'package_expiry_time_unit',
            'emprof_num_jobs',
            'emprof_num_fjobs',
            'emprofjob_expiry_time',
            'emprofjob_expiry_time_unit',
            'emprof_fcred_expiry_time',
            'emprof_fcred_expiry_time_unit',
            'emprof_num_cvs',
            'emprofview_consume_cvs',
            'emprof_promote_profile',
            'emprof_promote_expiry_time',
            'emprof_promote_expiry_time_unit',
        );
        
        return apply_filters('jobsearch_add_emp_profpkg_fields_list', $fields);
    }

    // default fields set
    public function init_fields($fields_type = '') {

        global $jobsearch_form_fields;
        
        ob_start();
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Reviews on Job', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'reviews_on_job',
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>
        <?php
        $field_html = ob_get_clean();
        $fields_set[] = array(
            'type' => 'job_package',
            'html' => $field_html,
        );

        $fields_set = apply_filters('jobsearch_package_custom_fields_set', $fields_set);

        //return $this->render_fields_type_wise($fields_set, $fields_type);
    }

    // render fields by type
    private function render_fields_type_wise($fields = array(), $type = '') {

        if ($type != '') {
            $ret_html = '';
            foreach ($fields as $field) {
                if (isset($field['type']) && $field['type'] == $type) {
                    $ret_html .= isset($field['html']) ? $field['html'] . "\n" : '';
                }
            }
            return $ret_html;
        }
    }

}

global $Jobsearch_Package_Custom_Fields;
$Jobsearch_Package_Custom_Fields = new Jobsearch_Package_Custom_Fields();
