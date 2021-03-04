<?php
/*
  Class : CustomFieldHTML
 */

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_CustomFieldHTML
{

    // hook things up
    public function __construct()
    {

        //
        add_filter('admin_footer', array($this, 'admin_footer_common_js_script'), 1);

        add_filter('jobsearch_custom_field_text_html', array($this, 'jobsearch_custom_field_text_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_video_html', array($this, 'jobsearch_custom_field_video_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_linkurl_html', array($this, 'jobsearch_custom_field_linkurl_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_upload_file_html', array($this, 'jobsearch_custom_field_upload_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_checkbox_html', array($this, 'jobsearch_custom_field_checkbox_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_dropdown_html', array($this, 'jobsearch_custom_field_dropdown_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_dependent_dropdown_html', array($this, 'jobsearch_custom_field_dependent_dropdown_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_dependent_fields_html', array($this, 'jobsearch_custom_field_dependent_fields_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_heading_html', array($this, 'jobsearch_custom_field_heading_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_textarea_html', array($this, 'jobsearch_custom_field_textarea_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_email_html', array($this, 'jobsearch_custom_field_email_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_number_html', array($this, 'jobsearch_custom_field_number_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_date_html', array($this, 'jobsearch_custom_field_date_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_range_html', array($this, 'jobsearch_custom_field_range_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_salary_html', array($this, 'jobsearch_custom_field_salary_html_callback'), 1, 3);
        add_filter('jobsearch_custom_field_actions_html', array($this, 'jobsearch_custom_field_actions_html_callback'), 1, 4);
    }

    public static function field_types_hint_text()
    {
        ob_start();
        ?>
        <div class="hint-for-field">
            <p style="margin: 12px 0 0 0; line-height: 22px;">
                <strong><?php esc_html_e('Hint', 'wp-jobsearch'); ?>::</strong>
                <br>
                <strong><?php esc_html_e('Default', 'wp-jobsearch'); ?>
                    :</strong> <?php esc_html_e('The Default field type will show everywhere.', 'wp-jobsearch'); ?>
                <br>
                <strong><?php esc_html_e('After Register User Only', 'wp-jobsearch'); ?>
                    :</strong> <?php esc_html_e('This type of field will show only for registered and logged-in users.', 'wp-jobsearch'); ?>
                <br>
                <strong><?php esc_html_e('For Admin View Only', 'wp-jobsearch'); ?>
                    :</strong> <?php esc_html_e('This type of field will only for admin data collection and does not appear on detail page. i.e. Passport Number, NIC Number or Phone number.', 'wp-jobsearch'); ?>
            </p>
        </div>
        <?php
        $html = ob_get_clean();
        return $html;
    }

    static function jobsearch_custom_field_heading_html_callback($html, $global_custom_field_counter, $field_data)
    {
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $heading_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $heading_field_enable_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $heading_field_enable_advsrch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-heading-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-editor-textcolor" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $heading_field_label != '' ? '<b>(' . $heading_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="heading-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Heading %s', 'wp-jobsearch'), $field_dyn_name), array('b' => array())); ?></a>
            </div>
            <div class="field-data" id="heading-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="heading"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>
                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-heading[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>

                <label>
                    <?php echo esc_html__('Enable in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-heading[enable-search][]">
                        <option <?php if ($heading_field_enable_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($heading_field_enable_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Enable in Advance Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-heading[enable-advsrch][]">
                        <option <?php if ($heading_field_enable_advsrch == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($heading_field_enable_advsrch == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-heading[label][]"
                           value="<?php echo esc_html($heading_field_label); ?>"/>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.heading-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#heading-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    static function jobsearch_custom_field_text_html_callback($html, $global_custom_field_counter, $field_data)
    {

        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $text_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $text_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $text_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $text_field_label = stripslashes($text_field_label);
        $text_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $text_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $text_field_enable_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $text_field_enable_advsrch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        $text_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $text_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        $text_field_collapse_search = isset($field_data['collapse-search']) ? $field_data['collapse-search'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-text-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-media-text" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $text_field_label != '' ? '<b>(' . $text_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="text-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Text Field %s', 'wp-jobsearch'), $field_dyn_name), array('b' => array())); ?></a>
            </div>
            <?php //var_dump($field_data);
            ?>
            <div class="field-data" id="text-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="text"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-text[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_text_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-text') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-text[label][]"
                           value="<?php echo esc_html($text_field_label); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-text[name][]"
                           value="<?php echo esc_html($text_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-text[placeholder][]"
                           value="<?php echo esc_html(stripslashes($text_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-text[classes][]"
                           value="<?php echo esc_html($text_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-text[required][]">
                        <option <?php if ($text_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($text_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Enable in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-text[enable-search][]">
                        <option <?php if ($text_field_enable_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($text_field_enable_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Enable in Advance Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-text[enable-advsrch][]">
                        <option <?php if ($text_field_enable_advsrch == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($text_field_enable_advsrch == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Collapse in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-text[collapse-search][]">
                        <option <?php if ($text_field_collapse_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($text_field_collapse_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);
                    //echo jobsearch_icon_picker($text_field_icon, $icon_id, 'jobsearch-custom-fields-text[icon][]');
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($text_field_icon, $icon_id, 'jobsearch-custom-fields-text[icon][]', $text_field_icon_group, 'jobsearch-custom-fields-text[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($text_field_icon, $icon_id, 'jobsearch-custom-fields-text[icon][]');
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.text-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#text-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();
        return $html;
    }

    static function jobsearch_custom_field_video_html_callback($html, $global_custom_field_counter, $field_data)
    {

        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $video_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $video_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $video_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $video_field_label = stripslashes($video_field_label);
        $video_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $video_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $video_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $video_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-video-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-media-video" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $video_field_label != '' ? '<b>(' . $video_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="video-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Video Field %s', 'wp-jobsearch'), $field_dyn_name), array('b' => array())); ?></a>
            </div>
            <?php //var_dump($field_data);
            ?>
            <div class="field-data" id="video-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="video"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-video[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_video_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-video') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-video[label][]"
                           value="<?php echo esc_html($video_field_label); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-video[name][]"
                           value="<?php echo esc_html($video_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-video[placeholder][]"
                           value="<?php echo esc_html(stripslashes($video_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-video[classes][]"
                           value="<?php echo esc_html($video_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-video[required][]">
                        <option <?php if ($video_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($video_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);

                    //echo jobsearch_icon_picker($video_field_icon, $icon_id, 'jobsearch-custom-fields-video[icon][]');
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($video_field_icon, $icon_id, 'jobsearch-custom-fields-video[icon][]', $video_field_icon_group, 'jobsearch-custom-fields-video[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($video_field_icon, $icon_id, 'jobsearch-custom-fields-video[icon][]');
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.video-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#video-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();
        return $html;
    }

    static function jobsearch_custom_field_linkurl_html_callback($html, $global_custom_field_counter, $field_data)
    {
        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $linkurl_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $linkurl_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $linkurl_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $linkurl_field_label = stripslashes($linkurl_field_label);
        $linkurl_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $linkurl_field_target = isset($field_data['link_target']) ? $field_data['link_target'] : '';
        $linkurl_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $linkurl_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $linkurl_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-linkurl-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-admin-links" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $linkurl_field_label != '' ? '<b>(' . $linkurl_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="linkurl-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('URL Field %s', 'wp-jobsearch'), $field_dyn_name), array('b' => array())); ?></a>
            </div>
            <?php //var_dump($field_data);
            ?>
            <div class="field-data" id="linkurl-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="linkurl"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-linkurl[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_linkurl_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-linkurl') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-linkurl[label][]"
                           value="<?php echo esc_html($linkurl_field_label); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-linkurl[name][]"
                           value="<?php echo esc_html($linkurl_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-linkurl[placeholder][]"
                           value="<?php echo esc_html(stripslashes($linkurl_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Target', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-linkurl[link_target][]">
                        <option <?php if ($linkurl_field_target == '_self') echo esc_html('selected'); ?>
                                value="_self"><?php echo esc_html__('Self', 'wp-jobsearch'); ?></option>
                        <option <?php if ($linkurl_field_target == '_blank') echo esc_html('selected'); ?>
                                value="_blank"><?php echo esc_html__('Blank', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-linkurl[classes][]"
                           value="<?php echo esc_html($linkurl_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-linkurl[required][]">
                        <option <?php if ($linkurl_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($linkurl_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);

                    //echo jobsearch_icon_picker($linkurl_field_icon, $icon_id, 'jobsearch-custom-fields-linkurl[icon][]');
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($linkurl_field_icon, $icon_id, 'jobsearch-custom-fields-linkurl[icon][]', $linkurl_field_icon_group, 'jobsearch-custom-fields-linkurl[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($linkurl_field_icon, $icon_id, 'jobsearch-custom-fields-linkurl[icon][]');
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.linkurl-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#linkurl-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    static function jobsearch_custom_field_upload_html_callback($html, $global_custom_field_counter, $field_data)
    {

        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $upload_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $upload_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $upload_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $upload_field_label = stripslashes($upload_field_label);
        $upload_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $upload_field_public_visi = isset($field_data['public_visible']) ? $field_data['public_visible'] : '';
        $upload_field_multifiles = isset($field_data['multi_files']) ? $field_data['multi_files'] : '';
        $upload_field_numof_files = isset($field_data['numof_files']) ? $field_data['numof_files'] : '';
        $upload_field_numof_files = $upload_field_numof_files > 0 ? $upload_field_numof_files : 5;
        $upload_field_allow_types = isset($field_data['allow_types']) ? $field_data['allow_types'] : '';
        $upload_field_allow_types = !empty($upload_field_allow_types) ? $upload_field_allow_types : array();
        $upload_field_file_size = isset($field_data['file_size']) ? $field_data['file_size'] : '';
        $upload_field_file_size = $upload_field_file_size == '' ? '5MB' : $upload_field_file_size;
        $upload_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $upload_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $upload_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-upload-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-admin-media" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $upload_field_label != '' ? '<b>(' . $upload_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="upload_file-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Upload File Field %s', 'wp-jobsearch'), $field_dyn_name), array('b' => array())); ?></a>
            </div>
            <?php //var_dump($field_data);
            ?>
            <div class="field-data" id="upload-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="upload_file"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-upload_file[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_upload_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-upload') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-upload_file[label][]"
                           value="<?php echo esc_html($upload_field_label); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-upload_file[name][]"
                           value="<?php echo esc_html($upload_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-upload_file[placeholder][]"
                           value="<?php echo esc_html(stripslashes($upload_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Multi Files', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select id="multi-cfield-selct-<?php echo absint($field_counter); ?>"
                            name="jobsearch-custom-fields-upload_file[multi_files][]">
                        <option <?php if ($upload_field_multifiles == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($upload_field_multifiles == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <div id="num-fields-holdr-<?php echo absint($field_counter); ?>" class="num_fields_holdr"
                     style="margin-bottom: 15px; display: <?php echo($upload_field_multifiles == 'yes' ? 'block' : 'none') ?>;">
                    <label>
                        <?php echo esc_html__('Number of Files', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <input name="jobsearch-custom-fields-upload_file[numof_files][]"
                               value="<?php echo absint($upload_field_numof_files); ?>"/>
                    </div>
                </div>

                <label>
                    <?php echo esc_html__('Allow File Types', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-upload_file[allow_types][<?php echo absint($field_counter); ?>][]"
                            multiple="multiple" class="uplodtypes-selectize"
                            placeholder="<?php esc_html_e('Choose File Types', 'wp-jobsearch'); ?>">
                        <option <?php echo(in_array('image/jpeg', $upload_field_allow_types) ? 'selected="selected"' : '') ?>
                                value="image/jpeg"><?php echo esc_html__('jpeg', 'wp-jobsearch'); ?></option>
                        <option <?php echo(in_array('image/png', $upload_field_allow_types) ? 'selected="selected"' : '') ?>
                                value="image/png"><?php echo esc_html__('png', 'wp-jobsearch'); ?></option>
                        <option <?php echo(in_array('text/plain', $upload_field_allow_types) ? 'selected="selected"' : '') ?>
                                value="text/plain"><?php echo esc_html__('text', 'wp-jobsearch'); ?></option>
                        <option <?php echo(in_array('application/msword', $upload_field_allow_types) ? 'selected="selected"' : '') ?>
                                value="application/msword"><?php echo esc_html__('doc', 'wp-jobsearch'); ?></option>
                        <option <?php echo(in_array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $upload_field_allow_types) ? 'selected="selected"' : '') ?>
                                value="application/vnd.openxmlformats-officedocument.wordprocessingml.document"><?php echo esc_html__('docx', 'wp-jobsearch'); ?></option>
                        <option <?php echo(in_array('application/vnd.ms-excel', $upload_field_allow_types) ? 'selected="selected"' : '') ?>
                                value="application/vnd.ms-excel"><?php echo esc_html__('xls', 'wp-jobsearch'); ?></option>
                        <option <?php echo(in_array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $upload_field_allow_types) ? 'selected="selected"' : '') ?>
                                value="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"><?php echo esc_html__('xlsx', 'wp-jobsearch'); ?></option>
                        <option <?php echo(in_array('application/pdf', $upload_field_allow_types) ? 'selected="selected"' : '') ?>
                                value="application/pdf"><?php echo esc_html__('pdf', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('File Size', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-upload_file[file_size][]"
                           value="<?php echo esc_html($upload_field_file_size); ?>"/>
                    <em><?php esc_html_e('Insert File size here only in MB i.e. 1MB, 2MB, 5MB, 50MB, 100MB.', 'wp-jobsearch'); ?></em>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-upload_file[classes][]"
                           value="<?php echo esc_html($upload_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Publicly Visible', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-upload_file[public_visible][]">
                        <option <?php if ($upload_field_public_visi == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                        <option <?php if ($upload_field_public_visi == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-upload_file[required][]">
                        <option <?php if ($upload_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($upload_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);

                    //echo jobsearch_icon_picker($upload_field_icon, $icon_id, 'jobsearch-custom-fields-upload_file[icon][]');
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($upload_field_icon, $icon_id, 'jobsearch-custom-fields-upload_file[icon][]', $upload_field_icon_group, 'jobsearch-custom-fields-upload_file[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($upload_field_icon, $icon_id, 'jobsearch-custom-fields-upload_file[icon][]');
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.upload_file-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#upload-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                    // Selectize script
                    if (jQuery('.uplodtypes-selectize').length > 0) {
                        jQuery('.uplodtypes-selectize').selectize({
                            plugins: ['remove_button'],
                        });
                    }
                    //
                });
                jQuery(document).on('change', '#multi-cfield-selct-<?php echo absint($field_counter); ?>', function () {
                    if (jQuery(this).val() == 'yes') {
                        jQuery('#num-fields-holdr-<?php echo absint($field_counter); ?>').show();
                    } else {
                        jQuery('#num-fields-holdr-<?php echo absint($field_counter); ?>').hide();
                    }
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    static function jobsearch_custom_field_email_html_callback($html, $global_custom_field_counter, $field_data)
    {
        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $email_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $email_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $email_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $email_field_label = stripslashes($email_field_label);
        $email_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $email_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $email_field_enable_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $email_field_enable_advsrch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        $email_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $email_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        $email_field_collapse_search = isset($field_data['collapse-search']) ? $field_data['collapse-search'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-email-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-email-alt" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $email_field_label != '' ? '<b>(' . $email_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="email-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Email Field %s', 'wp-jobsearch'), $field_dyn_name), array('b' => array())); ?></a>
            </div>
            <div class="field-data" id="email-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="email"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-email[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_email_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-email') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-email[label][]"
                           value="<?php echo esc_html($email_field_label); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-email[name][]"
                           value="<?php echo esc_html($email_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-email[placeholder][]"
                           value="<?php echo esc_html(stripslashes($email_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-email[classes][]"
                           value="<?php echo esc_html($email_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-email[required][]">
                        <option <?php if ($email_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($email_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Enable in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-email[enable-search][]">
                        <option <?php if ($email_field_enable_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($email_field_enable_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Enable in Advance Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-email[enable-advsrch][]">
                        <option <?php if ($email_field_enable_advsrch == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($email_field_enable_advsrch == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Collapse in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-email[collapse-search][]">
                        <option <?php if ($email_field_collapse_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($email_field_collapse_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);

                    //echo jobsearch_icon_picker($email_field_icon, $icon_id, 'jobsearch-custom-fields-email[icon][]');
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($email_field_icon, $icon_id, 'jobsearch-custom-fields-email[icon][]', $email_field_icon_group, 'jobsearch-custom-fields-email[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($email_field_icon, $icon_id, 'jobsearch-custom-fields-email[icon][]');
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.email-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#email-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    static function jobsearch_custom_field_number_html_callback($html, $global_custom_field_counter, $field_data)
    {
        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $number_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $number_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $number_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $number_field_label = stripslashes($number_field_label);
        $number_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $number_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $number_field_enable_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $number_field_enable_advsrch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        $number_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $number_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        $number_field_collapse_search = isset($field_data['collapse-search']) ? $field_data['collapse-search'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-number-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-editor-ol" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $number_field_label != '' ? '<b>(' . $number_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="number-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Number Field %s', 'wp-jobsearch'), $field_dyn_name), array('b' => array())); ?></a>
            </div>
            <div class="field-data" id="number-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="number"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-number[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_number_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-number') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-number[label][]"
                           value="<?php echo esc_html($number_field_label); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-number[name][]"
                           value="<?php echo esc_html($number_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-number[placeholder][]"
                           value="<?php echo esc_html(stripslashes($number_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-number[classes][]"
                           value="<?php echo esc_html($number_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-number[required][]">
                        <option <?php if ($number_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($number_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Enable in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-number[enable-search][]">
                        <option <?php if ($number_field_enable_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($number_field_enable_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Enable in Advance Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-number[enable-advsrch][]">
                        <option <?php if ($number_field_enable_advsrch == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($number_field_enable_advsrch == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Collapse in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-number[collapse-search][]">
                        <option <?php if ($number_field_collapse_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($number_field_collapse_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);

                    //echo jobsearch_icon_picker($number_field_icon, $icon_id, 'jobsearch-custom-fields-number[icon][]');
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($number_field_icon, $icon_id, 'jobsearch-custom-fields-number[icon][]', $number_field_icon_group, 'jobsearch-custom-fields-number[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($number_field_icon, $icon_id, 'jobsearch-custom-fields-number[icon][]');
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.number-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#number-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    static function jobsearch_custom_field_date_html_callback($html, $global_custom_field_counter, $field_data)
    {
        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();

        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $date_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $date_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $date_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $date_field_label = stripslashes($date_field_label);
        $date_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $date_field_date_format = isset($field_data['date-format']) ? $field_data['date-format'] : '';
        $date_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $date_field_enable_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $date_field_enable_advsrch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        $date_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $date_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        $date_field_collapse_search = isset($field_data['collapse-search']) ? $field_data['collapse-search'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-date-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-calendar-alt" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $date_field_label != '' ? '<b>(' . $date_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="date-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Date Field %s', 'wp-jobsearch'), $field_dyn_name), array('b' => array())); ?></a>
            </div>
            <div class="field-data" id="date-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="date"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-date[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_date_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-date') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-date[label][]"
                           value="<?php echo esc_html($date_field_label); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-date[name][]"
                           value="<?php echo esc_html($date_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-date[placeholder][]"
                           value="<?php echo esc_html(stripslashes($date_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Date Format', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input type="text" name="jobsearch-custom-fields-date[date-format][]"
                           value="<?php echo esc_html($date_field_date_format); ?>"/>
                    <div class="hint-for-field">
                        <p style="margin: 12px 0 0 0; line-height: 22px;">
                            <strong><?php echo esc_html__('Hint::', 'wp-jobsearch'); ?></strong> <?php echo esc_html__('Put date format like this "d-m-Y".', 'wp-jobsearch'); ?>
                        </p>
                    </div>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-date[classes][]"
                           value="<?php echo esc_html($date_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-date[required][]">
                        <option <?php if ($date_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($date_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Enable in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-date[enable-search][]">
                        <option <?php if ($date_field_enable_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($date_field_enable_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Enable in Advance Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-date[enable-advsrch][]">
                        <option <?php if ($date_field_enable_advsrch == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($date_field_enable_advsrch == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Collapse in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-date[collapse-search][]">
                        <option <?php if ($date_field_collapse_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($date_field_collapse_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);

                    //echo jobsearch_icon_picker($date_field_icon, $icon_id, 'jobsearch-custom-fields-date[icon][]');
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($date_field_icon, $icon_id, 'jobsearch-custom-fields-date[icon][]', $date_field_icon_group, 'jobsearch-custom-fields-date[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($date_field_icon, $icon_id, 'jobsearch-custom-fields-date[icon][]');
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.date-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#date-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    static function jobsearch_custom_field_range_html_callback($html, $global_custom_field_counter, $field_data)
    {
        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $range_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $range_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $range_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $range_field_label = stripslashes($range_field_label);
        $range_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $range_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $range_field_field_style = isset($field_data['field-style']) ? $field_data['field-style'] : '';
        $range_field_min = isset($field_data['min']) ? $field_data['min'] : '';
        $range_field_laps = isset($field_data['laps']) ? $field_data['laps'] : '';
        $range_field_interval = isset($field_data['interval']) ? $field_data['interval'] : '';
        $range_field_enable_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $range_field_enable_advsrch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        $range_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $range_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        $range_field_collapse_search = isset($field_data['collapse-search']) ? $field_data['collapse-search'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-range-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-image-flip-horizontal"
                                             aria-hidden="true"></i></span>
                <?php $field_dyn_name = $range_field_label != '' ? '<b>(' . $range_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="range-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Range Field %s', 'wp-jobsearch'), $field_dyn_name), array('b' => array())); ?></a>
            </div>
            <div class="field-data" id="range-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="range"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-range[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_range_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-range') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-range[label][]"
                           value="<?php echo esc_html($range_field_label); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-range[name][]"
                           value="<?php echo esc_html($range_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-range[placeholder][]"
                           value="<?php echo esc_html(stripslashes($range_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Min', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-range[min][]" type="number"
                           value="<?php echo esc_html($range_field_min); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Total Laps', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-range[laps][]" type="number" min="1" max="100"
                           value="<?php echo esc_html($range_field_laps); ?>"/>
                    <em><?php esc_html_e('Minimum value is 1 and maximum is 100.', 'wp-jobsearch'); ?></em>
                </div>

                <label>
                    <?php echo esc_html__('Interval', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-range[interval][]" type="number"
                           value="<?php echo esc_html($range_field_interval); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Style', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-range[field-style][]">
                        <option <?php if ($range_field_field_style == 'simple') echo esc_html('selected'); ?>
                                value="simple"><?php echo esc_html__('Simple', 'wp-jobsearch'); ?></option>
                        <option <?php if ($range_field_field_style == 'slider') echo esc_html('selected'); ?>
                                value="slider"><?php echo esc_html__('Slider', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-range[classes][]"
                           value="<?php echo esc_html($range_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-range[required][]">
                        <option <?php if ($range_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($range_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Enable in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-range[enable-search][]">
                        <option <?php if ($range_field_enable_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($range_field_enable_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Enable in Advance Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-range[enable-advsrch][]">
                        <option <?php if ($range_field_enable_advsrch == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($range_field_enable_advsrch == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Collapse in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-range[collapse-search][]">
                        <option <?php if ($range_field_collapse_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($range_field_collapse_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);

                    //echo jobsearch_icon_picker($range_field_icon, $icon_id, 'jobsearch-custom-fields-range[icon][]');
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($range_field_icon, $icon_id, 'jobsearch-custom-fields-range[icon][]', $range_field_icon_group, 'jobsearch-custom-fields-range[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($range_field_icon, $icon_id, 'jobsearch-custom-fields-range[icon][]');
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.range-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#range-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    static function jobsearch_custom_field_salary_html_callback($html, $global_custom_field_counter, $field_data) {
        global $jobsearch_plugin_options;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $salary_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $salary_field_label = stripslashes($salary_field_label);
        $salary_field_field_style = isset($field_data['field-style']) ? $field_data['field-style'] : '';
        $salary_field_min = isset($field_data['min']) ? $field_data['min'] : '';
        $salary_field_laps = isset($field_data['laps']) ? $field_data['laps'] : '';
        $salary_field_interval = isset($field_data['interval']) ? $field_data['interval'] : '';
        $salary_field_enable_advsrch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        $salary_field_collapse_search = isset($field_data['collapse-search']) ? $field_data['collapse-search'] : '';
        
        $post_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-salary-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-vault" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $salary_field_label != '' ? '<b>(' . $salary_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="salary-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Salary Field (For Search) %s', 'wp-jobsearch'), $field_dyn_name), array('b' => array())); ?></a>
            </div>
            <div class="field-data" id="salary-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="salary"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-salary[label][]"
                           value="<?php echo esc_html($salary_field_label); ?>"/>
                </div>

                <?php
                if (!empty($post_salary_types)) {
                    $slar_type_count = 1;
                    $top_tabs_html = '';
                    $intfields_html = '';
                    foreach ($post_salary_types as $post_salary_typ) {
                        
                        $cusdynm_salary_min = isset($field_data['min' . $slar_type_count]) ? $field_data['min' . $slar_type_count] : '';
                        $cusdynm_salary_interval = isset($field_data['interval' . $slar_type_count]) ? $field_data['interval' . $slar_type_count] : '';
                        $cusdynm_salary_laps = isset($field_data['laps' . $slar_type_count]) ? $field_data['laps' . $slar_type_count] : '';
                        
                        ob_start();
                        ?>
                        <a href="javascript:void(0);" class="button salaryfiltr-type-tab<?php echo ($slar_type_count == 1 ? ' active-type' : '') ?>" data-id="<?php echo ($slar_type_count) ?>"><?php echo ($post_salary_typ) ?></a>
                        <?php
                        $top_tabs_html .= ob_get_clean();
                        
                        ob_start();
                        ?>
                        <div class="salry-type-contntitm salry-type-<?php echo ($slar_type_count) ?>"<?php echo ($slar_type_count > 1 ? ' style="display: none;"' : '') ?>>
                            <label>
                                <?php echo esc_html__('Min', 'wp-jobsearch'); ?>:
                            </label>
                            <div class="input-field">
                                <input name="jobsearch-custom-fields-salary[min<?php echo ($slar_type_count) ?>][]" type="number" value="<?php echo esc_html($cusdynm_salary_min); ?>"/>
                            </div>

                            <label>
                                <?php echo esc_html__('Interval', 'wp-jobsearch'); ?>:
                            </label>
                            <div class="input-field">
                                <input name="jobsearch-custom-fields-salary[interval<?php echo ($slar_type_count) ?>][]" type="number" value="<?php echo esc_html($cusdynm_salary_interval); ?>"/>
                            </div>

                            <label>
                                <?php echo esc_html__('Total Laps', 'wp-jobsearch'); ?>:
                            </label>
                            <div class="input-field">
                                <input name="jobsearch-custom-fields-salary[laps<?php echo ($slar_type_count) ?>][]" type="number" min="1" max="100" value="<?php echo esc_html($cusdynm_salary_laps); ?>"/>
                                <em><?php esc_html_e('Minimum value is 1 and maximum is 100.', 'wp-jobsearch'); ?></em>
                            </div>
                        </div>
                        <?php
                        $intfields_html .= ob_get_clean();
                        $slar_type_count++;
                    }
                    echo '<div class="salary-filtr-toptabs">';
                    echo ($top_tabs_html);
                    echo '</div>';
                    echo '<div class="salary-filtr-tabscontent">';
                    echo ($intfields_html);
                    echo '</div>';
                } else {
                    ?>
                    <label>
                        <?php echo esc_html__('Min', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <input name="jobsearch-custom-fields-salary[min][]" type="number"
                               value="<?php echo esc_html($salary_field_min); ?>"/>
                    </div>

                    <label>
                        <?php echo esc_html__('Interval', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <input name="jobsearch-custom-fields-salary[interval][]" type="number"
                               value="<?php echo esc_html($salary_field_interval); ?>"/>
                    </div>

                    <label>
                        <?php echo esc_html__('Total Laps', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <input name="jobsearch-custom-fields-salary[laps][]" type="number" min="1" max="100" value="<?php echo esc_html($salary_field_laps); ?>"/>
                        <em><?php esc_html_e('Minimum value is 1 and maximum is 100.', 'wp-jobsearch'); ?></em>
                    </div>
                    <?php
                }
                ?>

                <label>
                    <?php echo esc_html__('Style', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-salary[field-style][]">
                        <option <?php if ($salary_field_field_style == 'simple') echo esc_html('selected'); ?>
                                value="simple"><?php echo esc_html__('Simple', 'wp-jobsearch'); ?></option>
                        <option <?php if ($salary_field_field_style == 'slider') echo esc_html('selected'); ?>
                                value="slider"><?php echo esc_html__('Slider', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Enable in Advance Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-salary[enable-advsrch][]">
                        <option <?php if ($salary_field_enable_advsrch == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($salary_field_enable_advsrch == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Collapse in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-salary[collapse-search][]">
                        <option <?php if ($salary_field_collapse_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($salary_field_collapse_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.salary-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#salary-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
                jQuery(document).on('click', '.salaryfiltr-type-tab', function () {
                    var _this = jQuery(this);
                    var this_id = _this.attr('data-id');
                    var _this_parent = _this.parent('.salary-filtr-toptabs');
                    _this_parent.find('.salaryfiltr-type-tab').attr('class', 'button salaryfiltr-type-tab');
                    _this.attr('class', 'button salaryfiltr-type-tab active-type');
                    
                    jQuery('.salary-filtr-tabscontent').find('.salry-type-contntitm').hide();
                    jQuery('.salary-filtr-tabscontent').find('.salry-type-' + this_id).removeAttr('style');
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    static function jobsearch_custom_field_checkbox_html_callback($html, $global_custom_field_counter, $field_data)
    {
        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $checkbox_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $checkbox_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $checkbox_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $checkbox_field_label = stripslashes($checkbox_field_label);
        $checkbox_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $checkbox_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $checkbox_field_enable_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $checkbox_field_enable_advsrch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        $checkbox_field_multi = isset($field_data['multi']) ? $field_data['multi'] : '';
        $checkbox_field_post_multi = isset($field_data['post-multi']) ? $field_data['post-multi'] : '';
        $checkbox_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $checkbox_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        $checkbox_field_collapse_search = isset($field_data['collapse-search']) ? $field_data['collapse-search'] : '';
        $checkbox_field_options = isset($field_data['options']) ? $field_data['options'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-checkbox-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-yes" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $checkbox_field_label != '' ? '<b>(' . $checkbox_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="checkbox-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Checkbox Field %s', 'wp-jobsearch'), stripslashes($field_dyn_name)), array('b' => array())); ?></a>
            </div>
            <div class="field-data" id="checkbox-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="checkbox"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-checkbox[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_checkbox_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-checkbox') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-checkbox[label][]"
                           value="<?php echo esc_html(stripslashes($checkbox_field_label)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-checkbox[name][]"
                           value="<?php echo esc_html($checkbox_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-checkbox[placeholder][]"
                           value="<?php echo esc_html(stripslashes($checkbox_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-checkbox[classes][]"
                           value="<?php echo esc_html($checkbox_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-checkbox[required][]">
                        <option <?php if ($checkbox_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($checkbox_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Enable in filter Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-checkbox[enable-search][]">
                        <option <?php if ($checkbox_field_enable_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($checkbox_field_enable_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Enable in Advance Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-checkbox[enable-advsrch][]">
                        <option <?php if ($checkbox_field_enable_advsrch == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($checkbox_field_enable_advsrch == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Multi-Select in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-checkbox[multi][]">
                        <option <?php if ($checkbox_field_multi == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($checkbox_field_multi == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Multi-Select in Forms', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-checkbox[post-multi][]">
                        <option <?php if ($checkbox_field_post_multi == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($checkbox_field_post_multi == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Collapse in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-checkbox[collapse-search][]">
                        <option <?php if ($checkbox_field_collapse_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($checkbox_field_collapse_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);

                    //echo jobsearch_icon_picker($checkbox_field_icon, $icon_id, 'jobsearch-custom-fields-checkbox[icon][]');
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($checkbox_field_icon, $icon_id, 'jobsearch-custom-fields-checkbox[icon][]', $checkbox_field_icon_group, 'jobsearch-custom-fields-checkbox[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($checkbox_field_icon, $icon_id, 'jobsearch-custom-fields-checkbox[icon][]');
                    }
                    ?>
                </div>

                <label>
                    <?php echo esc_html__('Options', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    if (isset($checkbox_field_options['value'])) {
                        $opt_counter = 0;
                        $radio_counter = 1;
                        foreach ($checkbox_field_options['value'] as $option_val) {
                            $option_label = $checkbox_field_options['label'][$opt_counter];
                            ?>
                            <div class="field-options-list">
                                <input name="jobsearch-custom-fields-checkbox[options][label][<?php echo esc_html($field_counter); ?>][]"
                                       value="<?php echo esc_html(stripslashes($option_label)); ?>"
                                       placeholder="<?php echo esc_html__('Text', 'wp-jobsearch'); ?>"/> - <input
                                        name="jobsearch-custom-fields-checkbox[options][value][<?php echo esc_html($field_counter); ?>][]"
                                        value="<?php echo esc_html($option_val); ?>"
                                        placeholder="<?php echo esc_html__('Value', 'wp-jobsearch'); ?>"/>
                                <a href="javascript:void(0);" class="option-field-add-btn"><i
                                            class="dashicons dashicons-plus"></i></a>
                                <a href="javascript:void(0);" class="option-field-remove"><i
                                            class="dashicons dashicons-no-alt"></i></a>
                            </div>
                            <?php
                            $opt_counter++;
                        }
                    } else { ?>
                        <div class="field-options-list">
                            <input name="jobsearch-custom-fields-checkbox[options][label][<?php echo esc_html($field_counter); ?>][]"
                                   value="" placeholder="<?php echo esc_html__('Text', 'wp-jobsearch'); ?>"/> - <input
                                    name="jobsearch-custom-fields-checkbox[options][value][<?php echo esc_html($field_counter); ?>][]"
                                    value="" placeholder="<?php echo esc_html__('Value', 'wp-jobsearch'); ?>"/>
                            <a href="javascript:void(0);" class="option-field-add-btn"><i
                                        class="dashicons dashicons-plus"></i></a>
                            <a href="javascript:void(0);" class="option-field-remove"><i
                                        class="dashicons dashicons-no-alt"></i></a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.checkbox-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#checkbox-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    static function jobsearch_custom_field_dropdown_html_callback($html, $global_custom_field_counter, $field_data)
    {
        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $dropdown_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $dropdown_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $dropdown_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $dropdown_field_label = stripslashes($dropdown_field_label);
        $dropdown_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $dropdown_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $dropdown_field_enable_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $dropdown_field_enable_advsrch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        $dropdown_field_multi = isset($field_data['multi']) ? $field_data['multi'] : '';
        $dropdown_field_post_multi = isset($field_data['post-multi']) ? $field_data['post-multi'] : '';
        $dropdown_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $dropdown_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        $dropdown_field_collapse_search = isset($field_data['collapse-search']) ? $field_data['collapse-search'] : '';
        $dropdown_field_options = isset($field_data['options']) ? $field_data['options'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-dropdown-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-arrow-down-alt" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $dropdown_field_label != '' ? '<b>(' . $dropdown_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="dropdown-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Dropdown Field %s', 'wp-jobsearch'), stripslashes($field_dyn_name)), array('b' => array())); ?></a>
            </div>
            <div class="field-data" id="dropdown-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="dropdown"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dropdown[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_dropdown_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-dropdown') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-dropdown[label][]"
                           value="<?php echo esc_html(stripslashes($dropdown_field_label)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-dropdown[name][]"
                           value="<?php echo esc_html($dropdown_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-dropdown[placeholder][]"
                           value="<?php echo esc_html(stripslashes($dropdown_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-dropdown[classes][]"
                           value="<?php echo esc_html($dropdown_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dropdown[required][]">
                        <option <?php if ($dropdown_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($dropdown_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Enable in filter Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dropdown[enable-search][]">
                        <option <?php if ($dropdown_field_enable_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($dropdown_field_enable_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Enable in Advance Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dropdown[enable-advsrch][]">
                        <option <?php if ($dropdown_field_enable_advsrch == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($dropdown_field_enable_advsrch == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Multi-Select in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dropdown[multi][]">
                        <option <?php if ($dropdown_field_multi == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($dropdown_field_multi == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Multi-Select in Forms', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dropdown[post-multi][]">
                        <option <?php if ($dropdown_field_post_multi == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($dropdown_field_post_multi == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Collapse in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dropdown[collapse-search][]">
                        <option <?php if ($dropdown_field_collapse_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($dropdown_field_collapse_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);

                    //echo jobsearch_icon_picker($dropdown_field_icon, $icon_id, 'jobsearch-custom-fields-dropdown[icon][]');
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($dropdown_field_icon, $icon_id, 'jobsearch-custom-fields-dropdown[icon][]', $dropdown_field_icon_group, 'jobsearch-custom-fields-dropdown[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($dropdown_field_icon, $icon_id, 'jobsearch-custom-fields-dropdown[icon][]');
                    }
                    ?>
                </div>

                <label>
                    <?php echo esc_html__('Options', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    if (isset($dropdown_field_options['value'])) {
                        $opt_counter = 0;
                        $radio_counter = 1;
                        foreach ($dropdown_field_options['value'] as $option_val) {
                            $option_label = $dropdown_field_options['label'][$opt_counter];
                            ?>
                            <div class="field-options-list">
                                <input name="jobsearch-custom-fields-dropdown[options][label][<?php echo esc_html($field_counter); ?>][]"
                                       value="<?php echo esc_html(stripslashes($option_label)); ?>"
                                       placeholder="<?php echo esc_html__('Text', 'wp-jobsearch'); ?>"/> - <input
                                        name="jobsearch-custom-fields-dropdown[options][value][<?php echo esc_html($field_counter); ?>][]"
                                        value="<?php echo esc_html($option_val); ?>"
                                        placeholder="<?php echo esc_html__('Value', 'wp-jobsearch'); ?>"/>
                                <a href="javascript:void(0);" class="option-field-add-btn"><i
                                            class="dashicons dashicons-plus"></i></a>
                                <a href="javascript:void(0);" class="option-field-remove"><i
                                            class="dashicons dashicons-no-alt"></i></a>
                            </div>
                            <?php
                            $opt_counter++;
                        }
                    } else {
                        ?>
                        <div class="field-options-list">
                            <input name="jobsearch-custom-fields-dropdown[options][label][<?php echo esc_html($field_counter); ?>][]"
                                   value="" placeholder="<?php echo esc_html__('Text', 'wp-jobsearch'); ?>"/> - <input
                                    name="jobsearch-custom-fields-dropdown[options][value][<?php echo esc_html($field_counter); ?>][]"
                                    value="" placeholder="<?php echo esc_html__('Value', 'wp-jobsearch'); ?>"/>
                            <a href="javascript:void(0);" class="option-field-add-btn"><i
                                        class="dashicons dashicons-plus"></i></a>
                            <a href="javascript:void(0);" class="option-field-remove"><i
                                        class="dashicons dashicons-no-alt"></i></a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.dropdown-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#dropdown-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    public static function dependent_dropdown_list_html($dropdown_field_options, $field_counter, $parent_id = '0', $lvel_countr = 1)
    {

        if (isset($dropdown_field_options[$parent_id]['label']) && !empty($dropdown_field_options[$parent_id]['label']) && is_array($dropdown_field_options[$parent_id]['label']) && sizeof($dropdown_field_options[$parent_id]['label']) > 0) {
            $field_group_label = isset($dropdown_field_options[$parent_id]['group_label']) ? $dropdown_field_options[$parent_id]['group_label'] : '';
            $field_vals = isset($dropdown_field_options[$parent_id]['value']) ? $dropdown_field_options[$parent_id]['value'] : '';
            $field_ids = isset($dropdown_field_options[$parent_id]['id']) ? $dropdown_field_options[$parent_id]['id'] : '';
            $field_par_ids = isset($dropdown_field_options[$parent_id]['par_id']) ? $dropdown_field_options[$parent_id]['par_id'] : '';

            ob_start();
            $field_count = 0;
            foreach ($dropdown_field_options[$parent_id]['label'] as $opt_field_label) {
                $field_val = isset($field_vals[$field_count]) ? $field_vals[$field_count] : '';
                $field_id = isset($field_ids[$field_count]) ? $field_ids[$field_count] : '';
                $field_par_id = isset($field_par_ids[$field_count]) ? $field_par_ids[$field_count] : '';
                $optcon_rand = $field_id;

                if ($field_count == 0 && $lvel_countr > 1) { ?>
                    <input type="text"
                           name="jobsearch-custom-fields-dependent_dropdown[options][<?php echo esc_html($field_counter); ?>][<?php echo($parent_id) ?>][group_label]"
                           placeholder="<?php esc_html_e('Field Label', 'wp-jobsearch'); ?>"
                           value="<?php echo($field_group_label) ?>">
                <?php } ?>
                <div class="dep-drpdwns-fopt levl<?php echo($lvel_countr) ?>">
                    <a href="javascript:void(0);" class="drp-sort-handle"><i
                                class="dashicons dashicons-image-flip-vertical"></i></a>
                    <input type="text"
                           name="jobsearch-custom-fields-dependent_dropdown[options][<?php echo esc_html($field_counter); ?>][<?php echo($parent_id) ?>][label][]"
                           placeholder="<?php esc_html_e('Option Label', 'wp-jobsearch'); ?>"
                           value="<?php echo($opt_field_label) ?>"> - <input type="text"
                                                                             name="jobsearch-custom-fields-dependent_dropdown[options][<?php echo esc_html($field_counter); ?>][<?php echo($parent_id) ?>][value][]"
                                                                             placeholder="<?php esc_html_e('Option Value', 'wp-jobsearch'); ?>"
                                                                             value="<?php echo($field_val) ?>">
                    <input type="hidden"
                           name="jobsearch-custom-fields-dependent_dropdown[options][<?php echo esc_html($field_counter); ?>][<?php echo($parent_id) ?>][id][]"
                           value="<?php echo($optcon_rand) ?>">
                    <input type="hidden"
                           name="jobsearch-custom-fields-dependent_dropdown[options][<?php echo esc_html($field_counter); ?>][<?php echo($parent_id) ?>][par_id][]"
                           value="<?php echo($field_par_id) ?>">
                    <div id="depnchilds-sec-con-<?php echo($optcon_rand) ?>">
                        <?php
                        self::dependent_dropdown_list_html($dropdown_field_options, $field_counter, $optcon_rand, ($lvel_countr + 1));
                        ?>
                    </div>
                    <div class="drpdwns-fopt-acts">
                        <?php
                        if ($lvel_countr < 4) {
                            ?>
                            <a href="javascript:void(0);"
                               style="display: <?php echo(!isset($dropdown_field_options[$optcon_rand]) ? 'inline-block' : 'none') ?>;"
                               class="add-depnchild-fieldsec"
                               data-rid="<?php echo($optcon_rand) ?>"><?php esc_html_e('Add Dependent Fields', 'wp-jobsearch'); ?></a>
                            <?php
                        }
                        ?>
                        <a href="javascript:void(0);" class="deldep-drpdwns-fopt"
                           data-rid="<?php echo($optcon_rand) ?>"><i class="dashicons dashicons-trash"></i></a>
                    </div>
                </div>
                <?php
                $field_count++;
            }
            $all_lvlopts_html = ob_get_clean();

            $field_count = 0;
            $field_val = isset($field_vals[$field_count]) ? $field_vals[$field_count] : '';
            $field_id = isset($field_ids[$field_count]) ? $field_ids[$field_count] : '';
            $field_par_id = isset($field_par_ids[$field_count]) ? $field_par_ids[$field_count] : '';
            $optcon_rand = $field_id;

            echo '<div class="dep-drpdwns-foptss foptslevl' . $lvel_countr . ' dep-drpdwns-optscon-' . $optcon_rand . '">';
            echo($all_lvlopts_html);
            ?>
            <a href="javascript:void(0);" class="add-mor-fieldbtn" data-rid="<?php echo($optcon_rand) ?>"
               data-parid="<?php echo($field_par_id) ?>"><?php esc_html_e('Add More', 'wp-jobsearch'); ?></a>
            <?php
            echo '</div>';
        }
    }

    static function jobsearch_custom_field_dependent_dropdown_html_callback($html, $global_custom_field_counter, $field_data)
    {
        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $dropdown_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $dropdown_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $dropdown_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $dropdown_field_label = stripslashes($dropdown_field_label);
        $dropdown_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $dropdown_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $dropdown_field_enable_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $dropdown_field_enable_advsrch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        $dropdown_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $dropdown_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        $dropdown_field_collapse_search = isset($field_data['collapse-search']) ? $field_data['collapse-search'] : '';
        $dropdown_field_options = isset($field_data['options_list']) ? $field_data['options_list'] : '';
        $dropdown_cont_optsid = isset($field_data['options_list_id']) && $field_data['options_list_id'] > 0 ? $field_data['options_list_id'] : rand(100000000, 999999999);
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-dependent_dropdown-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-networking" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $dropdown_field_label != '' ? '<b>(' . $dropdown_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="dependent_dropdown-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Dependent Dropdown Field %s', 'wp-jobsearch'), stripslashes($field_dyn_name)), array('b' => array())); ?></a>
            </div>
            <div class="field-data" id="dependent_dropdown-field-wraper<?php echo esc_html($rand); ?>"
                 style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="dependent_dropdown"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dependent_dropdown[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_dependent_dropdown_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-dependent_dropdown') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-dependent_dropdown[label][]"
                           value="<?php echo esc_html(stripslashes($dropdown_field_label)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-dependent_dropdown[name][]"
                           value="<?php echo esc_html($dropdown_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-dependent_dropdown[placeholder][]"
                           value="<?php echo esc_html(stripslashes($dropdown_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-dependent_dropdown[classes][]"
                           value="<?php echo esc_html($dropdown_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dependent_dropdown[required][]">
                        <option <?php if ($dropdown_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($dropdown_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Enable in filter Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dependent_dropdown[enable-search][]">
                        <option <?php if ($dropdown_field_enable_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($dropdown_field_enable_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Enable in Advance Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dependent_dropdown[enable-advsrch][]">
                        <option <?php if ($dropdown_field_enable_advsrch == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($dropdown_field_enable_advsrch == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Collapse in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dependent_dropdown[collapse-search][]">
                        <option <?php if ($dropdown_field_collapse_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($dropdown_field_collapse_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);
                    //echo jobsearch_icon_picker($dropdown_field_icon, $icon_id, 'jobsearch-custom-fields-dependent_dropdown[icon][]');
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($dropdown_field_icon, $icon_id, 'jobsearch-custom-fields-dependent_dropdown[icon][]', $dropdown_field_icon_group, 'jobsearch-custom-fields-dependent_dropdown[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($dropdown_field_icon, $icon_id, 'jobsearch-custom-fields-dependent_dropdown[icon][]');
                    }
                    ?>
                </div>

                <label>
                    <?php esc_html_e('Options', 'wp-jobsearch'); ?>:
                </label>
                <div class="dep-drpdwns-optscon" data-opid="<?php echo($dropdown_cont_optsid) ?>">
                    <input type="hidden" name="jobsearch-custom-fields-dependent_dropdown[options_field_counter_id][]"
                           value="<?php echo($dropdown_cont_optsid) ?>">
                    <?php
                    if (isset($dropdown_field_options[0]['label']) && !empty($dropdown_field_options[0]['label']) && is_array($dropdown_field_options[0]['label']) && sizeof($dropdown_field_options[0]['label']) > 0) {
                        //
                        self::dependent_dropdown_list_html($dropdown_field_options, $dropdown_cont_optsid);
                    } else {
                        $optcon_rand = rand(10000000, 99999999);
                        ?>
                        <div class="dep-drpdwns-foptss foptslevl1 dep-drpdwns-optscon-<?php echo($optcon_rand) ?>">
                            <div class="dep-drpdwns-fopt levl1">
                                <a href="javascript:void(0);" class="drp-sort-handle"><i
                                            class="dashicons dashicons-image-flip-vertical"></i></a>
                                <input type="text"
                                       name="jobsearch-custom-fields-dependent_dropdown[options][<?php echo esc_html($dropdown_cont_optsid); ?>][0][label][]"
                                       placeholder="<?php esc_html_e('Option Label', 'wp-jobsearch'); ?>" value=""> -
                                <input type="text"
                                       name="jobsearch-custom-fields-dependent_dropdown[options][0][value][<?php echo esc_html($dropdown_cont_optsid); ?>][]"
                                       placeholder="<?php esc_html_e('Option Value', 'wp-jobsearch'); ?>" value="">
                                <input type="hidden"
                                       name="jobsearch-custom-fields-dependent_dropdown[options][<?php echo esc_html($dropdown_cont_optsid); ?>][0][id][]"
                                       value="<?php echo($optcon_rand) ?>">
                                <input type="hidden"
                                       name="jobsearch-custom-fields-dependent_dropdown[options][<?php echo esc_html($dropdown_cont_optsid); ?>][0][par_id][]"
                                       value="0">
                                <div id="depnchilds-sec-con-<?php echo($optcon_rand) ?>"></div>
                                <div class="drpdwns-fopt-acts">
                                    <a href="javascript:void(0);" class="add-depnchild-fieldsec"
                                       data-rid="<?php echo($optcon_rand) ?>"><?php esc_html_e('Add Dependent Fields', 'wp-jobsearch'); ?></a>
                                </div>
                            </div>
                            <a href="javascript:void(0);" class="add-mor-fieldbtn"
                               data-rid="<?php echo($optcon_rand) ?>"
                               data-parid="0"><?php esc_html_e('Add More', 'wp-jobsearch'); ?></a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.dependent_dropdown-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#dependent_dropdown-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                    jQuery(".dep-drpdwns-foptss").sortable({handle: '.drp-sort-handle'});
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }
    
    static function jobsearch_custom_field_dependent_fields_html_callback($html, $global_custom_field_counter, $field_data)
    {
        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $rand = $field_counter;
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $depfield_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $depfield_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $depfield_field_label = stripslashes($depfield_field_label);
        $depfield_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $depfield_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $depfield_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $depfield_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-dependent_fields-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-admin-multisite" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $depfield_field_label != '' ? '<b>(' . $depfield_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="dependent_fields-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Dependent Fields %s', 'wp-jobsearch'), stripslashes($field_dyn_name)), array('b' => array())); ?></a>
            </div>
            <div class="field-data" id="dependent_fields-field-wraper<?php echo esc_html($rand); ?>"
                 style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="dependent_fields"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-dependent_fields[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_dependent_fields_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-dependent_fields') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-dependent_fields[label][]"
                           value="<?php echo esc_html(stripslashes($depfield_field_label)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-dependent_fields[name][]"
                           value="<?php echo esc_html($depfield_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-dependent_fields[placeholder][]"
                           value="<?php echo esc_html(stripslashes($depfield_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-dependent_fields[classes][]"
                           value="<?php echo esc_html($depfield_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($depfield_field_icon, $icon_id, 'jobsearch-custom-fields-dependent_fields[icon][]', $depfield_field_icon_group, 'jobsearch-custom-fields-dependent_fields[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($depfield_field_icon, $icon_id, 'jobsearch-custom-fields-dependent_fields[icon][]');
                    }
                    ?>
                </div>
                
                <?php
                do_action('jobsearch_cusfields_bk_depdfields_structure', $field_counter, $field_data);
                ?>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.dependent_fields-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#dependent_fields-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                    jQuery(".childfield-depn-con .childfield-holdr-con").sortable({handle: '.depnd-cuschfield-updte'});
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    public function admin_footer_common_js_script()
    {
        $flag = false;
        if ((isset($_GET['page']) && $_GET['page'] == 'jobsearch-candidate-fields')) {
            $flag = true;

        }

        if ((isset($_GET['page']) && $_GET['page'] == 'jobsearch-job-fields')) {

            $flag = true;
        }

        if ((isset($_GET['page']) && $_GET['page'] == 'jobsearch-employer-fields')) {

            $flag = true;
        }

        if ($flag != true) {
            return false;
        }
        ?>
        <script type="text/javascript">
            jQuery(document).on('click', '.add-mor-fieldbtn', function () {
                var _thisbtn = jQuery(this);
                var $dropdown_cont_optsid = _thisbtn.parents('.dep-drpdwns-optscon').attr('data-opid');
                var thisrid = _thisbtn.attr('data-rid');
                var thisparid = _thisbtn.attr('data-parid');
                var _math_rand = Math.floor((Math.random() * 100000000) + 1);
                var thsapender = jQuery('.dep-drpdwns-optscon-' + thisrid);

                var add_depfield_btn = '<a href="javascript:void(0);" class="add-depnchild-fieldsec" data-rid="' + _math_rand + '"><?php esc_html_e('Add Dependent Fields', 'wp-jobsearch'); ?></a>';

                var thisPar_class = 'levl1';
                var thisParFop_con = jQuery('.dep-drpdwns-optscon-' + thisrid);
                if (thisParFop_con.hasClass('foptslevl2')) {
                    thisPar_class = 'levl2';
                } else if (thisParFop_con.hasClass('foptslevl3')) {
                    thisPar_class = 'levl3';
                } else if (thisParFop_con.hasClass('foptslevl4')) {
                    add_depfield_btn = '';
                    thisPar_class = 'levl4';
                }

                var this_html = '\
                <div class="dep-drpdwns-fopt ' + thisPar_class + '">\
                    <a href="javascript:void(0);" class="drp-sort-handle"><i class="dashicons dashicons-image-flip-vertical"></i></a>\
                    <input type="text" name="jobsearch-custom-fields-dependent_dropdown[options][' + ($dropdown_cont_optsid) + '][' + (thisparid) + '][label][]" placeholder="<?php esc_html_e('Option Label', 'wp-jobsearch'); ?>"> - <input type="text" name="jobsearch-custom-fields-dependent_dropdown[options][' + ($dropdown_cont_optsid) + '][' + (thisparid) + '][value][]" placeholder="<?php esc_html_e('Option Value', 'wp-jobsearch'); ?>">\
                    <input type="hidden" name="jobsearch-custom-fields-dependent_dropdown[options][' + ($dropdown_cont_optsid) + '][' + (thisparid) + '][id][]" value="' + _math_rand + '">\
                    <input type="hidden" name="jobsearch-custom-fields-dependent_dropdown[options][' + ($dropdown_cont_optsid) + '][' + (thisparid) + '][par_id][]" value="' + (thisPar_class == 'levl1' ? '0' : thisparid) + '">\
                    <div id="depnchilds-sec-con-' + _math_rand + '"></div>\
                    <div class="drpdwns-fopt-acts">\
                        ' + add_depfield_btn + '\
                        <a href="javascript:void(0);" class="deldep-drpdwns-fopt" data-rid="' + _math_rand + '"><i class="dashicons dashicons-trash"></i></a>\
                    </div>\
                </div>';
                _thisbtn.before(this_html);
                jQuery(".dep-drpdwns-foptss").sortable({handle: '.drp-sort-handle'});
            });
            jQuery(document).on('click', '.deldep-drpdwns-fopt', function () {
                var _thisbtn = jQuery(this);
                var thisrid = _thisbtn.attr('data-rid');
                var delconf = confirm('<?php esc_html_e('Are you sure you want to remove it?', 'wp-jobsearch'); ?>');
                if (delconf) {
                    jQuery('#depnchilds-sec-con-' + thisrid).parent('.dep-drpdwns-fopt').remove();
                }
            });
            jQuery(document).on('click', '.add-depnchild-fieldsec', function () {
                var _thisbtn = jQuery(this);
                var $dropdown_cont_optsid = _thisbtn.parents('.dep-drpdwns-optscon').attr('data-opid');
                var _math_rand = Math.floor((Math.random() * 100000000) + 1);
                var thisrid = _thisbtn.attr('data-rid');
                var apendr_con = jQuery('#depnchilds-sec-con-' + thisrid);
                var thisPar_class = _thisbtn.parents('.dep-drpdwns-fopt').attr('class');
                thisPar_class = thisPar_class.replace('dep-drpdwns-fopt ', '');

                //
                var add_depfield_btn = '<a href="javascript:void(0);" class="add-depnchild-fieldsec" data-rid="' + _math_rand + '"><?php esc_html_e('Add Dependent Fields', 'wp-jobsearch'); ?></a>';
                var trash_depfield_btn = '<a href="javascript:void(0);" class="deldep-drpdwns-fopt" data-rid="' + _math_rand + '"><i class="dashicons dashicons-trash"></i></a>';
                if (apendr_con == '') {
                    trash_depfield_btn = '';
                }

                var Levl_Class = 'levl1';
                var Plevl_Class = 'foptslevl1';
                if (thisPar_class == 'levl1') {
                    Levl_Class = 'levl2';
                    Plevl_Class = 'foptslevl2';
                } else if (thisPar_class == 'levl2') {
                    Levl_Class = 'levl3';
                    Plevl_Class = 'foptslevl3';
                } else if (thisPar_class == 'levl3') {
                    add_depfield_btn = '';
                    Levl_Class = 'levl4';
                    Plevl_Class = 'foptslevl4';
                }
                var this_html = '\
                <div class="dep-drpdwns-foptss ' + Plevl_Class + ' dep-drpdwns-optscon-' + _math_rand + '">\
                    <input type="text" name="jobsearch-custom-fields-dependent_dropdown[options][' + ($dropdown_cont_optsid) + '][' + (thisrid) + '][group_label]" placeholder="<?php esc_html_e('Field Label', 'wp-jobsearch'); ?>">\
                    <div class="dep-drpdwns-fopt ' + Levl_Class + '">\
                        <a href="javascript:void(0);" class="drp-sort-handle"><i class="dashicons dashicons-image-flip-vertical"></i></a>\
                        <input type="text" name="jobsearch-custom-fields-dependent_dropdown[options][' + ($dropdown_cont_optsid) + '][' + (thisrid) + '][label][]" placeholder="<?php esc_html_e('Option Label', 'wp-jobsearch'); ?>"> - <input type="text" name="jobsearch-custom-fields-dependent_dropdown[options][' + ($dropdown_cont_optsid) + '][' + (thisrid) + '][value][]" placeholder="<?php esc_html_e('Option Value', 'wp-jobsearch'); ?>">\
                        <input type="hidden" name="jobsearch-custom-fields-dependent_dropdown[options][' + ($dropdown_cont_optsid) + '][' + (thisrid) + '][id][]" value="' + _math_rand + '">\
                        <input type="hidden" name="jobsearch-custom-fields-dependent_dropdown[options][' + ($dropdown_cont_optsid) + '][' + (thisrid) + '][par_id][]" value="' + (Levl_Class == 'levl1' ? '0' : thisrid) + '">\
                        <div id="depnchilds-sec-con-' + _math_rand + '"></div>\
                        <div class="drpdwns-fopt-acts">\
                            ' + add_depfield_btn + '\
                            ' + trash_depfield_btn + '\
                        </div>\
                    </div>\
                    <a href="javascript:void(0);" class="add-mor-fieldbtn" data-rid="' + _math_rand + '" data-parid="' + thisrid + '"><?php esc_html_e('Add More', 'wp-jobsearch'); ?></a>\
                </div>';
                apendr_con.append(this_html);
                _thisbtn.hide();
                jQuery(".dep-drpdwns-foptss").sortable({handle: '.drp-sort-handle'});
            });
        </script>
        <?php
    }

    static function jobsearch_custom_field_textarea_html_callback($html, $global_custom_field_counter, $field_data)
    {
        global $careerfy_icons_fields;
        $field_counter = $global_custom_field_counter;
        ob_start();
        $field_for_non_reg_user = isset($field_data['non_reg_user']) ? $field_data['non_reg_user'] : '';
        $rand = $field_counter;
        $textarea_field_name = isset($field_data['name']) ? $field_data['name'] : '';
        $textarea_field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $textarea_field_rich_editor = isset($field_data['rich_editor']) ? $field_data['rich_editor'] : '';
        $textarea_field_media_btns = isset($field_data['media_buttons']) ? $field_data['media_buttons'] : '';
        $textarea_field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $textarea_field_label = stripslashes($textarea_field_label);
        $textarea_field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $textarea_field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $textarea_field_enable_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $textarea_field_is_editor = isset($field_data['public_visible']) ? $field_data['public_visible'] : '';
        $textarea_field_enable_advsrch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        $textarea_field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $textarea_field_icon_group = isset($field_data['icon-group']) ? $field_data['icon-group'] : '';
        $textarea_field_collapse_search = isset($field_data['collapse-search']) ? $field_data['collapse-search'] : '';
        ?>
        <div class="jobsearch-custom-filed-container jobsearch-custom-filed-textarea-container">
            <div class="field-intro field-msort-handle">
                <span class="drag-handle"><i class="dashicons dashicons-editor-alignleft" aria-hidden="true"></i></span>
                <?php $field_dyn_name = $textarea_field_label != '' ? '<b>(' . $textarea_field_label . ')</b>' : '' ?>
                <a href="javascript:void(0);"
                   class="textarea-field<?php echo esc_html($rand); ?>"><?php echo wp_kses(sprintf(__('Textarea Field %s', 'wp-jobsearch'), $field_dyn_name), array('b' => array())); ?></a>
            </div>
            <div class="field-data" id="textarea-field-wraper<?php echo esc_html($rand); ?>" style="display:none;">
                <input type="hidden" name="jobsearch-custom-fields-type[]" value="textarea"/>
                <input type="hidden" name="jobsearch-custom-fields-id[]"
                       value="<?php echo esc_html($field_counter); ?>"/>

                <label>
                    <?php echo esc_html__('Field Type', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-textarea[non_reg_user][]">
                        <option <?php if ($field_for_non_reg_user == 'default') echo('selected="selected"'); ?>
                                value="default"><?php echo esc_html__('Default', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'for_reg') echo('selected="selected"'); ?>
                                value="for_reg"><?php echo esc_html__('After Register User Only', 'wp-jobsearch'); ?></option>
                        <option <?php if ($field_for_non_reg_user == 'admin_view_only') echo('selected="selected"'); ?>
                                value="admin_view_only"><?php echo esc_html__('For Admin View Only', 'wp-jobsearch'); ?></option>
                    </select>
                    <?php echo self::field_types_hint_text() ?>
                </div>
                <?php do_action('jobsearch_custom_fields_textarea_plus_1', $field_counter, $field_data, 'jobsearch-custom-fields-textarea') ?>
                <label>
                    <?php echo esc_html__('Field Name', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-textarea[label][]"
                           value="<?php echo esc_html($textarea_field_label); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Slug *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input class="check-name-availability" name="jobsearch-custom-fields-textarea[name][]"
                           value="<?php echo esc_html($textarea_field_name); ?>"/>
                    <span class="available-msg"><i class="dashicons dashicons-dismiss"></i></span>
                </div>

                <label>
                    <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-textarea[placeholder][]"
                           value="<?php echo esc_html(stripslashes($textarea_field_placeholder)); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="jobsearch-custom-fields-textarea[classes][]"
                           value="<?php echo esc_html($textarea_field_classes); ?>"/>
                </div>

                <label>
                    <?php echo esc_html__('Rich Editor', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-textarea[rich_editor][]">
                        <option <?php if ($textarea_field_rich_editor == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                        <option <?php if ($textarea_field_rich_editor == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Media Buttons (works with rich editor only)', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-textarea[media_buttons][]">
                        <option <?php if ($textarea_field_media_btns == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                        <option <?php if ($textarea_field_media_btns == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Required *', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-textarea[required][]">
                        <option <?php if ($textarea_field_required == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($textarea_field_required == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Enable in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-textarea[enable-search][]">
                        <option <?php if ($textarea_field_enable_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($textarea_field_enable_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>
                <label>
                    <?php echo esc_html__('Enable in Advance Search', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-textarea[enable-advsrch][]">
                        <option <?php if ($textarea_field_enable_advsrch == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($textarea_field_enable_advsrch == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Collapse in Filters', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <select name="jobsearch-custom-fields-textarea[collapse-search][]">
                        <option <?php if ($textarea_field_collapse_search == 'no') echo esc_html('selected'); ?>
                                value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        <option <?php if ($textarea_field_collapse_search == 'yes') echo esc_html('selected'); ?>
                                value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                    </select>
                </div>

                <label>
                    <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <?php
                    $icon_id = rand(1000000, 99999999);
                    if (is_object($careerfy_icons_fields)) {
                        echo $careerfy_icons_fields->careerfy_icons_fields_callback($textarea_field_icon, $icon_id, 'jobsearch-custom-fields-textarea[icon][]', $textarea_field_icon_group, 'jobsearch-custom-fields-textarea[icon-group][]');
                    } else {
                        echo jobsearch_icon_picker($textarea_field_icon, $icon_id, 'jobsearch-custom-fields-textarea[icon][]');
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.textarea-field<?php echo esc_html($rand); ?>', function () {
                        jQuery('#textarea-field-wraper<?php echo esc_html($rand); ?>').slideToggle("slow");
                    });
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    static function jobsearch_custom_field_actions_html_callback($li_rand, $rand, $field_type)
    {
        $html = '';
        ob_start();
        ?>
        <div class="actions">
            <a href="javascript:void(0);"
               class="custom-fields-edit <?php echo esc_html($field_type); ?>-field<?php echo esc_html($rand); ?>"><i
                        class="dashicons dashicons-edit" aria-hidden="true"></i></a>
            <a href="javascript:void(0);" class="custom-fields-remove" data-randid="<?php echo esc_html($li_rand) ?>"><i
                        class="dashicons dashicons-trash" aria-hidden="true"></i></a>
        </div>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

}

// class Jobsearch_CustomFieldHTML 
$Jobsearch_CustomFieldHTML_obj = new Jobsearch_CustomFieldHTML();
global $Jobsearch_CustomFieldHTML_obj;
