<?php

/**
 * form fields class
 * html fields
 * @return object
 */
class JobSearch_Form_Fields
{

    public $prefix;

    /**
     * consttruct function
     *
     * initialize
     */
    public function __construct()
    {
        $this->prefix = 'jobsearch_field_';
        add_action('save_post', array($this, 'save_meta_fields'));
    }

    /**
     * Saving meta fields
     * with save_post hook
     */
    public function save_meta_fields($post_id = '')
    {
        global $post, $pagenow;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $post_type = '';
        if ($pagenow == 'post.php') {
            $post_type = get_post_type();
        }

        $_POST = jobsearch_input_post_vals_validate($_POST);

        foreach ($_POST as $key => $value) {
            if (strstr($key, $this->prefix)) {
                //
                $value = apply_filters('jobsearch_form_fields_value', $value, $key);

                update_post_meta($post_id, $key, $value);
            } else {
                if ($post_type != 'page' && $post_type != 'post' && $post_type != 'shop_coupon' && $post_type != 'product' && $post_type != 'resm-builder' && strpos($key, 'pt_user') === false) {
                    if (!strstr($key, 'jobsearch_cfupfiles_')) {
                        update_post_meta($post_id, $key, $value);
                    }
                }
            }
        }
    }

    /**
     * input text field
     *
     * @return markup
     */
    public function input_field($params = array())
    {
        extract($params);

        global $post;

        $prefix = $this->prefix;
        $prop_name = '';
        $prop_id = '';
        $prop_class = '';
        $prop_value = '';
        $value = '';
        $extra_attr = '';
        $post_ID = isset($post->ID) ? $post->ID : '';
        if (isset($name) && $name != '') {
            $db_value = get_post_meta($post_ID, $prefix . $name, true);
        }

        if (isset($id) && $id != '') {
            $prop_id = " id=\"{$id}\"";
        }
        if (isset($name) && $name != '') {
            $prop_name = " name=\"{$prefix}{$name}\"";
        }
        if (isset($cus_id) && $cus_id != '') {
            $prop_id = " id=\"{$cus_id}\"";
        }
        if (isset($cus_name) && $cus_name != '') {
            $prop_name = " name=\"{$cus_name}\"";
        }
        if (isset($classes) && $classes != '') {
            $prop_class = " class=\"{$classes}\"";
        }
        if (isset($ext_attr) && $ext_attr != '') {
            $extra_attr = $ext_attr;
        }

        if (isset($std) && $std != '') {
            $value = $std;
            $value = jobsearch_esc_html($value);
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($db_value)) {
            $value = $db_value == '' && (isset($std) && $std != '') ? $std : $db_value;
            $value = jobsearch_esc_html($value);
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($force_std) && $force_std != '') {
            $value = $force_std;
            $value = jobsearch_esc_html($value);
            $prop_value = " value=\"{$value}\"";
        }
        $ext_attr_str = 'text';

        $html = "<input type=\"{$ext_attr_str}\"{$prop_id}{$prop_name}{$prop_class}{$prop_value}{$extra_attr} />";

        if (isset($field_desc) && $field_desc != '') {
            $html .= "<p class=\"field-description\">{$field_desc}</p>";
        }

        if (isset($return) && $return === true) {
            return $html;
        } else {
            echo force_balance_tags($html);
        }
    }

    /**
     * input hidden field
     *
     * @return markup
     */
    public function input_hidden_field($params = array())
    {
        extract($params);

        global $post;

        $prefix = $this->prefix;
        $prop_name = '';
        $prop_id = '';
        $prop_class = '';
        $prop_value = '';
        $value = '';
        $extra_attr = '';
        $post_ID = isset($post->ID) ? $post->ID : '';
        if (isset($name) && $name != '') {
            $db_value = get_post_meta($post_ID, $prefix . $name, true);
        }

        if (isset($id) && $id != '') {
            $prop_id = " id=\"{$id}\"";
        }
        if (isset($name) && $name != '') {
            $prop_name = " name=\"{$prefix}{$name}\"";
        }
        if (isset($cus_id) && $cus_id != '') {
            $prop_id = " id=\"{$cus_id}\"";
        }
        if (isset($cus_name) && $cus_name != '') {
            $prop_name = " name=\"{$cus_name}\"";
        }
        if (isset($classes) && $classes != '') {
            $prop_class = " class=\"{$classes}\"";
        }
        if (isset($ext_attr) && $ext_attr != '') {
            $extra_attr = " $ext_attr";
        }

        if (isset($std) && $std != '') {
            $value = $std;
            $value = jobsearch_esc_html($value);
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($db_value)) {
            $value = $db_value;
            $value = jobsearch_esc_html($value);
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($force_std) && $force_std != '') {
            $value = $force_std;
            $value = jobsearch_esc_html($value);
            $prop_value = " value=\"{$value}\"";
        }

        $html = "<input type=\"hidden\"{$prop_id}{$prop_name}{$prop_class}{$prop_value}{$extra_attr} />";

        if (isset($return) && $return === true) {
            return $html;
        } else {
            echo force_balance_tags($html);
        }
    }

    /**
     * date field
     *
     * @return markup
     */
    public function date_field($params = array())
    {
        extract($params);

        global $post;
        $prefix = $this->prefix;
        $prop_name = '';
        $prop_id = '';
        $prop_class = '';
        $prop_value = '';
        $value = '';
        $extra_attr = '';

        $date_format = 'd-m-Y H:i';
        if (isset($format) && $format != '') {
            $date_format = $format;
        }

        $date_normal = false;
        if (isset($normal) && $normal === true) {
            $date_normal = true;
        }

        if (isset($name) && $name != '') {
            $db_value = get_post_meta($post->ID, $prefix . $name, true);
        }

        if (isset($id) && $id != '') {
            $prop_id = " id=\"{$id}\"";
        }
        if (isset($name) && $name != '') {
            $prop_name = " name=\"{$prefix}{$name}\"";
        }
        if (isset($cus_id) && $cus_id != '') {
            $prop_id = " id=\"{$cus_id}\"";
        }
        if (isset($cus_name) && $cus_name != '') {
            $prop_name = " name=\"{$cus_name}\"";
        }
        if (isset($classes) && $classes != '') {
            $prop_class = " class=\"{$classes}\"";
        }
        if (isset($ext_attr) && $ext_attr != '') {
            $extra_attr = " $ext_attr";
        }

        if (isset($std) && $std != '') {
            if ($date_normal === true) {
                $value = $std;
            } else {
                $value = $std != '' ? date($date_format, $std) : '';
            }
            $value = jobsearch_esc_html($value);
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($db_value)) {
            if ($date_normal === true) {
                $value = $db_value;
            } else {
                $value = $db_value != '' ? date($date_format, $db_value) : '';
            }
            $value = jobsearch_esc_html($value);
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($force_std) && $force_std != '') {
            if ($date_normal === true) {
                $value = $force_std;
            } else {
                $value = $force_std != '' ? date($date_format, $force_std) : '';
            }
            $value = jobsearch_esc_html($value);
            $prop_value = " value=\"{$value}\"";
        }

        $html = "<input type=\"text\"{$prop_id}{$prop_name}{$prop_class}{$prop_value}{$extra_attr} />";

        if (isset($field_desc) && $field_desc != '') {
            $html .= "<p class=\"field-description\">{$field_desc}</p>";
        }

        if (isset($return) && $return === true) {
            return $html;
        } else {
            echo force_balance_tags($html);
        }
    }

    /**
     * textarea text field
     *
     * @return markup
     */
    public function textarea_field($params = array())
    {
        extract($params);

        global $post;

        $prefix = $this->prefix;
        $prop_name = '';
        $prop_id = '';
        $prop_class = '';
        $prop_value = '';
        $value = '';
        $extra_attr = '';
        if (isset($name) && $name != '' && isset($post->ID)) {
            $db_value = get_post_meta($post->ID, $prefix . $name, true);
        }

        if (isset($id) && $id != '') {
            $prop_id = " id=\"{$id}\"";
        }
        if (isset($name) && $name != '') {
            $prop_name = " name=\"{$prefix}{$name}\"";
        }
        if (isset($cus_id) && $cus_id != '') {
            $prop_id = " id=\"{$cus_id}\"";
        }
        if (isset($cus_name) && $cus_name != '') {
            $prop_name = " name=\"{$cus_name}\"";
        }
        if (isset($classes) && $classes != '') {
            $prop_class = " class=\"{$classes}\"";
        }
        if (isset($ext_attr) && $ext_attr != '') {
            $extra_attr = " $ext_attr";
        }
        if (isset($std) && $std != '') {
            $value = $std;
        }
        if (isset($db_value)) {
            $value = $db_value == '' && (isset($std) && $std != '') ? $std : $db_value;
        }
        if (isset($force_std) && $force_std != '') {
            $value = $force_std;
        }

        $value = jobsearch_esc_html($value);

        $html = "<textarea {$prop_id}{$prop_name}{$prop_class} {$extra_attr}>{$value}</textarea>";

        if (isset($field_desc) && $field_desc != '') {
            $html .= "<p class=\"field-description\">{$field_desc}</p>";
        }

        if (isset($return) && $return === true) {
            return $html;
        } else {
            echo force_balance_tags($html);
        }
    }

    /**
     * image field
     *
     * @return markup
     */
    public function image_upload_field($params = array())
    {
        extract($params);

        global $post, $pagenow;

        $prefix = $this->prefix;
        $prop_name = '';
        $db_value = '';
        $prop_id = '';
        $prop_id_name = '';
        $prop_class = '';
        $prop_value = '';
        $value = '';

        if (isset($name) && $name != '' && $pagenow == 'post.php') {
            $db_value = get_post_meta($post->ID, $prefix . $name, true);
        }

        if (isset($id) && $id != '') {
            $prop_id = " id=\"{$id}\"";
            $prop_id_name = " name=\"{$id}\"";
        }
        if (isset($name) && $name != '') {
            $prop_name = " name=\"{$prefix}{$name}\"";
        }
        if (isset($cus_id) && $cus_id != '') {
            $prop_id = " id=\"{$cus_id}\"";
        }
        if (isset($cus_name) && $cus_name != '') {
            $prop_name = " name=\"{$cus_name}\"";
        }
        if (isset($classes) && $classes != '') {
            $prop_class = " class=\"{$classes}\"";
        }

        if (isset($std) && $std != '') {
            $value = $std;
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($db_value)) {
            $value = $db_value;
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($force_std) && $force_std != '') {
            $value = $force_std;
            $prop_value = " value=\"{$value}\"";
        }

        $image_display = $value == '' ? 'none' : 'block';

        $html = '
	<div id="' . $id . '-box" class="jobsearch-browse-med-image" style="display: ' . $image_display . ';">
            <a class="jobsearch-rem-media-b" data-id="' . $id . '"><i class="dashicons dashicons-no-alt"></i></a>
            <img id="' . $id . '-img" src="' . $value . '" alt="" />
        </div>';

        $html .= "<input type=\"hidden\"{$prop_id}{$prop_name}{$prop_class}{$prop_value} />";
        $html .= "<input type=\"button\" class=\"jobsearch-upload-media jobsearch-bk-btn\" {$prop_id_name} value=\"" . __('Browse', 'wp-jobsearch') . "\" />";

        if (isset($field_desc) && $field_desc != '') {
            $html .= "<p class=\"field-description\">{$field_desc}</p>";
        }

        if (isset($return) && $return === true) {
            return $html;
        } else {
            echo force_balance_tags($html);
        }
    }

    /**
     * file upload field
     *
     * @return markup
     */
    public function file_upload_field($params = array())
    {
        extract($params);

        global $post, $pagenow;

        $prefix = $this->prefix;
        $prop_name = '';
        $db_value = '';
        $prop_id = '';
        $prop_id_name = '';
        $prop_class = '';
        $prop_value = '';
        $value = '';

        if (isset($name) && $name != '' && $pagenow == 'post.php') {
            $db_value = get_post_meta($post->ID, $prefix . $name, true);
        }

        if (isset($id) && $id != '') {
            $prop_id = " id=\"{$id}\"";
            $prop_id_name = " name=\"{$id}\"";
        }
        if (isset($name) && $name != '') {
            $prop_name = " name=\"{$prefix}{$name}\"";
        }
        if (isset($cus_id) && $cus_id != '') {
            $prop_id = " id=\"{$cus_id}\"";
        }
        if (isset($cus_name) && $cus_name != '') {
            $prop_name = " name=\"{$cus_name}\"";
        }
        if (isset($classes) && $classes != '') {
            $prop_class = " class=\"{$classes}\"";
        }

        if (isset($std) && $std != '') {
            $value = $std;
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($db_value)) {
            $value = $db_value;
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($force_std) && $force_std != '') {
            $value = $force_std;
            $prop_value = " value=\"{$value}\"";
        }

        $image_display = $value == '' ? 'none' : 'block';

        $html = '';

        $html .= "<input type=\"text\"{$prop_id}{$prop_name}{$prop_class}{$prop_value} />";
        $html .= "<input type=\"button\" class=\"jobsearch-upload-file jobsearch-bk-btn\" {$prop_id_name} value=\"" . __('Browse', 'wp-jobsearch') . "\" />";

        if (isset($field_desc) && $field_desc != '') {
            $html .= "<p class=\"field-description\">{$field_desc}</p>";
        }

        if (isset($return) && $return === true) {
            return $html;
        } else {
            echo force_balance_tags($html);
        }
    }

    /**
     * input text field
     *
     * @return markup
     */
    public function checkbox_field($params = array())
    {
        extract($params);
        global $post;

        $prefix = $this->prefix;
        $prop_name = '';
        $prop_id = '';
        $prop_class = '';
        $value = '';
        $prop_value = '';
        $prop_checked = '';
        $extra_attr = '';
        $checkbox_id = rand(1000000, 9999999);

        if (isset($name) && $name != '') {
            $db_value = get_post_meta($post->ID, $prefix . $name, true);
        }

        if (isset($id) && $id != '') {
            $prop_id = " id=\"{$id}\"";
        }
        if (isset($name) && $name != '') {
            $prop_name = " name=\"{$prefix}{$name}\"";
        }
        if (isset($cus_id) && $cus_id != '') {
            $prop_id = " id=\"{$cus_id}\"";
        }
        if (isset($cus_name) && $cus_name != '') {
            $prop_name = " name=\"{$cus_name}\"";
        }
        if (isset($classes) && $classes != '') {
            $prop_class = " class=\"{$classes}\"";
        }
        if (isset($ext_attr) && $ext_attr != '') {
            $extra_attr = " $ext_attr";
        }
        if (isset($std) && $std != '') {
            $value = $std;
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($db_value)) {
            $value = $db_value;
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($force_std) && $force_std != '') {
            $value = $force_std;
            $prop_value = " value=\"{$value}\"";
        }

        if ($value == 'on') {
            $prop_checked = " checked=\"checked\"";
        }
        $html = '';
        if (isset($simple) && $simple == true) {

            if ($value == '') {
                $html .= '<input type="checkbox" ' . $prop_id . $prop_name . ' ' . $prop_class . ' ' . $prop_checked . ' ' . $extra_attr . ' />';
            } else {
                $html .= '<input type="checkbox" ' . $prop_id . $prop_name . ' ' . $prop_class . ' ' . $prop_checked . ' ' . $prop_value . ' ' . $extra_attr . ' />';
            }
        } else {
            $html .= "<div class=\"onoff-button\">";
            $html .= "<input id=\"onoff-{$checkbox_id}\" type=\"checkbox\"{$prop_id}{$prop_class}{$prop_checked} />";
            $html .= "<label for=\"onoff-{$checkbox_id}\"></label>";
            if ($prop_name != '') {
                $html .= "<input type=\"hidden\"{$prop_name}{$prop_value} />";
            } else if ($prop_id != '') {
                $html .= "<input type=\"hidden\"{$prop_id}{$prop_value} />";
            }
            $html .= "</div>";
        }
        if (isset($field_desc) && $field_desc != '') {
            $html .= "<p class=\"field-description\">{$field_desc}</p>";
        }

        if (isset($return) && $return === true) {
            return $html;
        } else {
            echo force_balance_tags($html);
        }
    }

    /**
     * input radio field
     *
     * @return markup
     */
    public function radio_field($params = array())
    {
        extract($params);

        global $post;

        $prefix = $this->prefix;
        $prop_name = '';
        $prop_id = '';
        $prop_class = '';
        $value = '';
        $prop_value = '';
        $prop_checked = '';
        $extra_attr = '';

        $checkbox_id = rand(1000000, 9999999);

        if (isset($name) && $name != '') {
            $db_value = get_post_meta($post->ID, $prefix . $name, true);
        }

        if (isset($id) && $id != '') {
            $prop_id = " id=\"{$id}\"";
        }
        if (isset($name) && $name != '') {
            $prop_name = " name=\"{$prefix}{$name}\"";
        }
        if (isset($cus_id) && $cus_id != '') {
            $prop_id = " id=\"{$cus_id}\"";
        }
        if (isset($cus_name) && $cus_name != '') {
            $prop_name = " name=\"{$cus_name}\"";
        }
        if (isset($classes) && $classes != '') {
            $prop_class = " class=\"{$classes}\"";
        }
        if (isset($ext_attr) && $ext_attr != '') {
            $extra_attr = " $ext_attr";
        }
        if (isset($std) && $std != '') {
            $value = $std;
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($db_value)) {
            $value = $db_value;
            $prop_value = " value=\"{$value}\"";
        }
        if (isset($force_std) && $force_std != '') {
            $value = $force_std;
            $prop_value = " value=\"{$value}\"";
        }

        if ($value == 'on') {
            $prop_checked = " checked=\"checked\"";
        }
        $html = '';

        if ($value == '') {
            $html .= '<input type="radio" ' . $prop_id . $prop_name . ' ' . $prop_class . ' ' . $prop_checked . ' ' . $extra_attr . ' />';
        } else {
            $html .= '<input type="radio" ' . $prop_id . $prop_name . ' ' . $prop_class . ' ' . $prop_checked . ' ' . $prop_value . ' ' . $extra_attr . ' />';
        }

        if (isset($field_desc) && $field_desc != '') {
            $html .= "<p class=\"field-description\">{$field_desc}</p>";
        }

        if (isset($return) && $return === true) {
            return $html;
        } else {
            echo force_balance_tags($html);
        }
    }

    /**
     * Simple Checkbox
     *
     */

    /**
     * select option field
     *
     * @return markup
     */
    public function select_field($params = array())
    {
        extract($params);

        global $post, $pagenow;

        $prefix = $this->prefix;
        $prop_name = '';
        $prop_id = '';
        $prop_class = '';
        $value = '';
        $db_value = '';
        $extra_attr = '';

        if (isset($name) && $name != '' && $pagenow == 'post.php') {
            $db_value = get_post_meta($post->ID, $prefix . $name, true);
        }

        if (isset($id) && $id != '') {
            $prop_id = " id=\"{$id}\"";
        }
        if (isset($name) && $name != '') {
            $prop_name = " name=\"{$prefix}{$name}\"";
        }
        if (isset($cus_id) && $cus_id != '') {
            $prop_id = " id=\"{$cus_id}\"";
        }
        if (isset($cus_name) && $cus_name != '') {
            $prop_name = " name=\"{$cus_name}\"";
        }
        if (isset($classes) && $classes != '') {
            $prop_class = " class=\"{$classes}\"";
        }
        if (isset($ext_attr) && $ext_attr != '') {
            $extra_attr = " $ext_attr";
        }
        //echo $std;echo '-<';
        if (isset($std) && $std != '') {
            $value = $std;
        }
        if (isset($db_value)) {
            $value = $db_value;
        }
        if (isset($force_std) && $force_std != '') {
            $value = $force_std;
        }
        //echo $value;
        $html = "<select{$prop_id}{$prop_name}{$prop_class}{$extra_attr}>";
        if (isset($options) && is_array($options)) {
            foreach ($options as $opton_key => $opton_val) {
                $selected = $value == $opton_key ? ' selected="selected"' : '';
                $html .= "<option{$selected} value=\"{$opton_key}\">{$opton_val}</option>" . "\n";
            }
        }
        $html .= "</select>";

        if (isset($field_desc) && $field_desc != '') {
            $html .= "<p class=\"field-description\">{$field_desc}</p>";
        }

        if (isset($return) && $return === true) {
            return $html;
        } else {
            echo force_balance_tags($html);
        }
    }

    /**
     * multi select option field
     *
     * @return markup
     */
    public function multi_select_field($params = array())
    {
        extract($params);

        global $post, $pagenow;

        $prefix = $this->prefix;
        $prop_name = '';
        $prop_id = '';
        $prop_class = '';
        $value = '';
        $db_value = '';
        $extra_attr = '';

        if (isset($db_name) && $db_name != '' && $pagenow == 'post.php') {
            $db_value = get_post_meta($post->ID, $prefix . $db_name, true);
        }

        if (isset($id) && $id != '') {
            $prop_id = " id=\"{$id}\"";
        }
        if (isset($name) && $name != '') {
            $prop_name = " name=\"{$prefix}{$name}\"";
        }
        if (isset($cus_id) && $cus_id != '') {
            $prop_id = " id=\"{$cus_id}\"";
        }
        if (isset($cus_name) && $cus_name != '') {
            $prop_name = " name=\"{$cus_name}\"";
        }
        if (isset($classes) && $classes != '') {
            $prop_class = " class=\"{$classes}\"";
        }
        if (isset($ext_attr) && $ext_attr != '') {
            $extra_attr = " $ext_attr";
        }

        if (isset($std) && $std != '') {
            $value = $std;
        }
        if (isset($db_value) && isset($post->ID)) {
            if (metadata_exists('post', $post->ID, ($prefix . $db_name))) {
                $value = $db_value;
            }
        }
        if (isset($force_std) && $force_std != '') {
            $value = $force_std;
        }
        //echo 'in field file--';
        //print_r($value);echo 'end field file';
        $html = "<select{$prop_id}{$prop_name}{$prop_class}{$extra_attr} multiple=\"multiple\">";
        if (isset($options) && is_array($options)) {
            foreach ($options as $opton_key => $opton_val) {
                $selected = '';
                if (is_array($value) && in_array($opton_key, $value)) {

                    $selected = ' selected="selected"';
                }
                $html .= "<option{$selected} value=\"{$opton_key}\">{$opton_val}</option>" . "\n";
            }
        }
        $html .= "</select>";
        //exit;
        if (isset($field_desc) && $field_desc != '') {
            $html .= "<p class=\"field-description\">{$field_desc}</p>";
        }

        if (isset($return) && $return === true) {
            return $html;
        } else {
            echo force_balance_tags($html);
        }
    }

}

global $jobsearch_form_fields;
$jobsearch_form_fields = new JobSearch_Form_Fields();
