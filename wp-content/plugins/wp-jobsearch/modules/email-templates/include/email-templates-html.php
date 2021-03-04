<?php
/*
  Class : EmailTemplateHTML
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_EmailTemplateHTML {

// hook things up
    public function __construct() {
        add_filter('jobsearch_email_template_fields_html', array($this, 'jobsearch_email_template_fields_html_callback'), 1, 6);
    }

    public function jobsearch_email_template_fields_html_callback($html, $rand, $prefix, $email_template_prefix, $default_var, $email_all_templates_saved_data) {
        global $sitepress;
        ob_start();

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $email_template_name_slug = $prefix . '[' . $email_template_prefix . ']';
        $email_template_name_slug_id = $prefix . '_' . $email_template_prefix . '';
        $content_id = $email_template_name_slug_id . '_content';
        $content_default_val = isset($default_var['default_content']) ? $default_var['default_content'] : '';
        $default_subject_val = isset($default_var['default_subject']) ? $default_var['default_subject'] : '';
        $default_recipients_val = isset($default_var['default_recipients']) ? $default_var['default_recipients'] : '';
        $switch_default_label = isset($default_var['switch_label']) ? $default_var['switch_label'] : '';
        $codes = isset($default_var['codes']) ? $default_var['codes'] : '';
        $group = isset($default_var['group']) ? $default_var['group'] : '';
        $type = isset($default_var['type']) ? $default_var['type'] : '';
        $filter = isset($default_var['filter']) ? $default_var['filter'] : '';
        $switch_value = 0;
        $subject_value = '';
        $recipients_value = '';

        $content_value = $content_default_val;
        $subject_value = $default_subject_val;
        $recipients_value = $default_recipients_val;

        if (isset($email_all_templates_saved_data[$prefix][$email_template_prefix]) && !empty($email_all_templates_saved_data[$prefix][$email_template_prefix])) {
            $switch_value = isset($email_all_templates_saved_data[$prefix][$email_template_prefix]['switch']) ? $email_all_templates_saved_data[$prefix][$email_template_prefix]['switch'] : '';
            $subject_value = isset($email_all_templates_saved_data[$prefix][$email_template_prefix]['subject']) ? $email_all_templates_saved_data[$prefix][$email_template_prefix]['subject'] : '';
            $recipients_value = isset($email_all_templates_saved_data[$prefix][$email_template_prefix]['recipients']) ? $email_all_templates_saved_data[$prefix][$email_template_prefix]['recipients'] : '';
            $content_value = isset($email_all_templates_saved_data[$prefix][$email_template_prefix]['content']) ? str_replace('\"', '"', $email_all_templates_saved_data[$prefix][$email_template_prefix]['content']) : '';
        }

        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $temp_trnaslated = get_option('jobsearch_translate_email_templates');
            if (isset($temp_trnaslated[$type]['lang_' . $lang_code]['subject'])) {
                $subject_value = $temp_trnaslated[$type]['lang_' . $lang_code]['subject'];
            }
            if (isset($temp_trnaslated[$type]['lang_' . $lang_code]['content'])) {
                $content_value = $temp_trnaslated[$type]['lang_' . $lang_code]['content'];
                $content_value = str_replace(array('\"'), array('"'), $content_value);
                $content_value = jobsearch_remove_extra_slashes($content_value);
            }
        } else {
            $subject_value = jobsearch_remove_extra_slashes($subject_value);
            $content_value = jobsearch_remove_extra_slashes($content_value);
        }

        $switch_value_selected = isset($switch_value) && $switch_value != '' ? 'checked="checked"' : '';
        ?>
        <div class="jobsearch-element-field">
            <input name="<?php echo esc_html($email_template_name_slug); ?>[group]" value="<?php echo esc_html($group); ?>" type="hidden">
            <input name="<?php echo esc_html($email_template_name_slug); ?>[type]" value="<?php echo esc_html($type); ?>" type="hidden">
            <input name="<?php echo esc_html($email_template_name_slug); ?>[filter]" value="<?php echo esc_html($filter); ?>" type="hidden">
            <div class="jobsearch-email-template-container jobsearch-email-template-text-container">
                <div class="field-intro">
                    <a href="javascript:void(0);" class="<?php echo esc_html($email_template_name_slug_id . $rand); ?>"><?php echo esc_html($switch_default_label); ?></a>
                    <div class="onoff-button">
                        <input id="onoff-<?php echo esc_html($rand); ?>" name="<?php echo esc_html($email_template_name_slug); ?>[switch]" value="1" <?php echo esc_html($switch_value_selected); ?> type="checkbox">
                        <label for="onoff-<?php echo esc_html($rand); ?>"></label>
                    </div>
                </div>
                <div class="field-data <?php echo esc_html($email_template_name_slug_id); ?>-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                    <div class="jobsearch-email-template_right">
                        <label>
                            <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>:
                        </label>
                        <div class="input-field">
                            <input type="text" name="<?php echo esc_html($email_template_name_slug); ?>[subject]" value="<?php echo esc_html($subject_value); ?>" />
                        </div>
                        <label>
                            <?php echo esc_html__('Test Email Address', 'wp-jobsearch'); ?>:
                        </label>
                        <div class="input-field">
                            <input type="text" name="<?php echo esc_html($email_template_name_slug); ?>[recipients]" value="<?php echo esc_html($recipients_value); ?>" />
                        </div>
                        <label>
                            <?php echo esc_html__('Content', 'wp-jobsearch'); ?>:
                        </label>
                        <div class="input-field">
                            <?php
                            $settings = array(
                                'textarea_name' => esc_html($email_template_name_slug) . '[content]',
                                'media_buttons' => true,
                                'editor_height' => '450px',
                                'tinymce' => array(
                                    'theme_advanced_buttons1' => 'formatselect,|,bold,italic,underline,|,' .
                                    'bullist,blockquote,|,justifyleft,justifycenter' .
                                    ',justifyright,justifyfull,|,link,unlink,|' .
                                    ',spellchecker,wp_fullscreen,wp_adv'
                                )
                            );
                            wp_editor($content_value, $content_id, $settings);
                            ?>
                        </div>
                    </div>

                    <div class="jobsearch-email-template_left">
                        <?php
                        // default codes
                        $default_codes = JobSearch_plugin::$codes;

                        if (!empty($default_codes) && count($default_codes) > 0) {
                            ?>
                            <div class="input-field">
                                <label>
                                    <?php echo esc_html__('General Codes', 'wp-jobsearch'); ?>:
                                </label>
                                <div class="jobsearch-email-template_left-wrap">
                                    <table class="email-template-codes">
                                        <tbody>
                                            <?php
                                            foreach ($default_codes as $code_var => $code_val) {
                                                $var = isset($code_val['var']) ? $code_val['var'] : '';
                                                $display_text = isset($code_val['display_text']) ? $code_val['display_text'] : '';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a class="add-email-var" data-variable="<?php echo esc_html($var); ?>" data-editorid="<?php echo esc_html($content_id); ?>"><?php echo esc_html($var); ?></a> <span>- <?php echo esc_html($display_text); ?></span>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>                                        
                            </div>
                            <?php
                        }

                        if (!empty($codes) && count($codes) > 0) {
                            ?>
                            <div class="input-field">
                                <label>
                                    <?php echo esc_html__('Codes', 'wp-jobsearch'); ?>:
                                </label>
                                <div class="jobsearch-email-template_left-wrap">
                                    <table class="email-template-codes">
                                        <tbody>
                                            <?php
                                            foreach ($codes as $code_var => $code_val) {
                                                $var = isset($code_val['var']) ? $code_val['var'] : '';
                                                $display_text = isset($code_val['display_text']) ? $code_val['display_text'] : '';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a class="add-email-var" data-variable="<?php echo esc_html($var); ?>" data-editorid="<?php echo esc_html($content_id); ?>"><?php echo esc_html($var); ?></a> <span>- <?php echo esc_html($display_text); ?></span>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>                                     
                                </div>
                                <div class="jobsearch-email-reset-area">
                                    <a href="javascript:void(0);" class="jobsearch-email-reset-content" data-variable='<?php echo esc_html($content_default_val); ?>' data-editorid="<?php echo esc_html($content_id); ?>" data-confirmmsg="<?php echo esc_html__('Are you sure you want to reset content?', 'wp-jobsearch'); ?>"><?php echo esc_html__('Reset Content', 'wp-jobsearch'); ?></a>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <script>
                    jQuery(document).ready(function () {
                        jQuery(document).on('click', '.<?php echo esc_html($email_template_name_slug_id . $rand); ?>', function () {
                            jQuery('.<?php echo esc_html($email_template_name_slug_id); ?>-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                        });
                    });
                </script>
            </div>

        </div>
        <?php
        $html .= ob_get_clean();
        return $html;
    }

}

// class Jobsearch_EmailTemplateHTML 
$Jobsearch_EmailTemplateHTML_obj = new Jobsearch_EmailTemplateHTML();
global $Jobsearch_EmailTemplateHTML_obj;
