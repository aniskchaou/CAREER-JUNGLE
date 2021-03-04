<?php
/*
  Class : EmailTemplateAjax
 */

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_EmailTemplateAjax {

// hook things up
    public function __construct() {

        // save email templates
        add_action('wp_ajax_jobsearch_email_templates_save', array($this, 'jobsearch_email_templates_save_callback'));
        add_action('wp_ajax_nopriv_jobsearch_email_templates_save', array($this, 'jobsearch_email_templates_save_callback'));
    }

    static function jobsearch_email_templates_save_callback() {
        global $sitepress;
        $post_data = $_POST;
        $error = 0;
        $email_template_entity = isset($post_data['entitytype']) ? $post_data['entitytype'] : '';
        $error_msg = '';
        if ($error == 0) {
            //
            if ( function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') ) {
                $lang_code = $sitepress->get_current_language();
                $temp_trnas = get_option('jobsearch_translate_email_templates');
                $temp_trnas = !empty($temp_trnas) ? $temp_trnas : array();
                
                $global_sender_name = isset($post_data['jobsearch_email_template_sender_name']) ? $post_data['jobsearch_email_template_sender_name'] : '';
                $temp_trnas['global_settings']['lang_' . $lang_code]['sender_name'] = $global_sender_name;
                
                if (isset($post_data['jobsearch_email_template']) && !empty($post_data['jobsearch_email_template'])) {
                    
                    foreach($post_data['jobsearch_email_template'] as $template_key => $template_val) {
                        $subject = isset($template_val['subject']) ? $template_val['subject'] : '';
                        $content = isset($template_val['content']) ? $template_val['content'] : '';
                        $template_type_id = isset($template_val['type']) ? $template_val['type'] : '';
                        $temp_trnas[$template_type_id]['lang_' . $lang_code]['subject'] = $subject;
                        $temp_trnas[$template_type_id]['lang_' . $lang_code]['content'] = $content;
                    }
                    update_option('jobsearch_translate_email_templates', $temp_trnas);
                }
            }
            //
            
            update_option("jobsearch_" . $email_template_entity, $post_data);
            $error = 0;
            $error_msg = esc_html__('Email templates have been saved successfully', 'wp-jobsearch');
        }
        echo json_encode(array('msg' => $error_msg, 'error' => $error));
        wp_die();
    }

}

// class Jobsearch_EmailTemplateAjax 
$Jobsearch_EmailTemplateAjax_obj = new Jobsearch_EmailTemplateAjax();
global $Jobsearch_EmailTemplateAjax_obj;