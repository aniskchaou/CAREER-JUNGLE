<?php

if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_CustomField_DepFields {

    // hook things up
    public function __construct() {
        add_action('admin_footer', array($this, 'depdfields_admfooter_content'), 55);
        add_action('jobsearch_cusfields_bk_depdfields_structure', array($this, 'bk_depdfields_structure'), 10, 2);
        
        add_action('wp_ajax_jobsearch_addin_cus_depnfield_child', array($this, 'add_cus_depnfield_child'));
    }
    
    public static function fields_btns_html($field_counter, $opt_counter) {
        ?>
        <ul>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="heading" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('Heading', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-editor-textcolor"></i> <?php esc_html_e('Heading', 'wp-jobsearch'); ?></a>
            </li>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="text" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('Text', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-media-text"></i> <?php esc_html_e('Text', 'wp-jobsearch'); ?></a>
            </li>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="video" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('Video', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-media-video"></i> <?php esc_html_e('Video', 'wp-jobsearch'); ?></a>
            </li>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="url" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('URL', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-admin-links"></i> <?php esc_html_e('URL', 'wp-jobsearch'); ?></a>
            </li>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="checkbox" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('Checkbox', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-yes"></i> <?php esc_html_e('Checkbox', 'wp-jobsearch'); ?></a>
            </li>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="dropdown" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('Dropdown', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-arrow-down-alt"></i> <?php esc_html_e('Dropdown', 'wp-jobsearch'); ?></a>
            </li>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="email" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('Email', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-email-alt"></i> <?php esc_html_e('Email', 'wp-jobsearch'); ?></a>
            </li>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="number" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('Number', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-editor-ol"></i> <?php esc_html_e('Number', 'wp-jobsearch'); ?></a>
            </li>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="date" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('Date', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-calendar-alt"></i> <?php esc_html_e('Date', 'wp-jobsearch'); ?></a>
            </li>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="textarea" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('Textarea', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-editor-alignleft"></i> <?php esc_html_e('Textarea', 'wp-jobsearch'); ?></a>
            </li>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="range" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('Range', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-image-flip-horizontal"></i> <?php esc_html_e('Range', 'wp-jobsearch'); ?></a>
            </li>
            <li>
                <a class="addchild-depnd-bxbtn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_counter); ?>" 
                   data-field="upload" 
                   href="javascript:void(0);" data-fieldlabel="<?php esc_html_e('Upload', 'wp-jobsearch'); ?>"><i class="dashicons dashicons-admin-media"></i> <?php esc_html_e('Upload', 'wp-jobsearch'); ?></a>
            </li>
        </ul>
        <?php
    }

    public function bk_depdfields_structure($field_counter, $field_data) {
        $rand_num = rand(100000000, 999999999);
        $field_type = isset($field_data['field_type']) ? $field_data['field_type'] : '';
        $field_type = $field_type == '' ? 'dropdown' : $field_type;
        $field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $multi_select = isset($field_data['multi']) ? $field_data['multi'] : '';
        $enable_in_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $enable_in_advsearch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        $field_options = isset($field_data['options']) ? $field_data['options'] : '';
//        echo '<pre>';
//        var_dump($field_options);
//        echo '</pre>';
        ?>
        <label>
            <?php echo esc_html__('Select Field Type', 'wp-jobsearch'); ?>:
        </label>
        <div class="input-field">
            <div class="radiochk-selectbtns-field">
                <input type="hidden" id="field-radiochk-val-<?php echo ($rand_num) ?>" name="jobsearch-custom-fields-dependent_fields[field_type][]" value="<?php echo ($field_type) ?>">
                <div class="jobsearch-field-radocheck">
                    <input id="opt-field-typedrpdwn-<?php echo ($rand_num) ?>" type="radio" name="dependent_fields[<?php echo ($rand_num) ?>][field_type]" <?php echo ($field_type == 'dropdown' ? 'checked="checked"' : ''); ?> value="dropdown">
                    <label for="opt-field-typedrpdwn-<?php echo ($rand_num) ?>">
                        <span><?php esc_html_e('Dropdown', 'wp-jobsearch'); ?></span>
                    </label>
                </div>
                <div class="jobsearch-field-radocheck">
                    <input id="opt-field-typeradio-<?php echo ($rand_num) ?>" type="radio" name="dependent_fields[<?php echo ($rand_num) ?>][field_type]" <?php echo ($field_type == 'radio' ? 'checked="checked"' : ''); ?> value="radio">
                    <label for="opt-field-typeradio-<?php echo ($rand_num) ?>">
                        <span><?php esc_html_e('Radio/Checkbox', 'wp-jobsearch'); ?></span>
                    </label>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="depfield-mega-con">
            <div class="depfield-item-holder">
                <div class="depfield-setins-con">
                    <div class="chekunchk-opt-box">
                        <div class="chekunchk-opt-boxiner">
                            <input type="hidden" name="jobsearch-custom-fields-dependent_fields[multi][]" value="<?php echo ($multi_select) ?>">
                            <input id="multi-opt-<?php echo ($rand_num) ?>" type="checkbox" name="[<?php echo ($rand_num) ?>][multi]"<?php echo ($multi_select ? ' checked' : '') ?> class="depfield-opt-chkunchk">
                            <label for="multi-opt-<?php echo ($rand_num) ?>">
                                <span class="chkunchk-onoffswitch-inner"></span>
                                <span class="chkunchk-onoffswitch-switch"></span>
                            </label>
                        </div>
                        <span class="chk-onoffswitch-title"><?php echo esc_html__('Multi Select', 'wp-jobsearch'); ?></span>
                    </div>
                    <div class="chekunchk-opt-box">
                        <div class="chekunchk-opt-boxiner">
                            <input type="hidden" name="jobsearch-custom-fields-dependent_fields[required][]" value="<?php echo ($field_required) ?>">
                            <input id="required-opt-<?php echo ($rand_num) ?>" type="checkbox" name="[<?php echo ($rand_num) ?>][required]"<?php echo ($field_required ? ' checked' : '') ?> class="depfield-opt-chkunchk">
                            <label for="required-opt-<?php echo ($rand_num) ?>">
                                <span class="chkunchk-onoffswitch-inner"></span>
                                <span class="chkunchk-onoffswitch-switch"></span>
                            </label>
                        </div>
                        <span class="chk-onoffswitch-title"><?php echo esc_html__('Required', 'wp-jobsearch'); ?></span>
                    </div>
                    <div class="chekunchk-opt-box">
                        <div class="chekunchk-opt-boxiner">
                            <input type="hidden" name="jobsearch-custom-fields-dependent_fields[enable-search][]" value="<?php echo ($enable_in_search) ?>">
                            <input id="enable-search-opt-<?php echo ($rand_num) ?>" type="checkbox" name="[<?php echo ($rand_num) ?>][enable-search]"<?php echo ($enable_in_search ? ' checked' : '') ?> class="depfield-opt-chkunchk">
                            <label for="enable-search-opt-<?php echo ($rand_num) ?>">
                                <span class="chkunchk-onoffswitch-inner"></span>
                                <span class="chkunchk-onoffswitch-switch"></span>
                            </label>
                        </div>
                        <span class="chk-onoffswitch-title"><?php echo esc_html__('Filter search', 'wp-jobsearch'); ?></span>
                    </div>
                    <div class="chekunchk-opt-box">
                        <div class="chekunchk-opt-boxiner">
                            <input type="hidden" name="jobsearch-custom-fields-dependent_fields[enable-advsrch][]" value="<?php echo ($enable_in_advsearch) ?>">
                            <input id="enable-advsrch-opt-<?php echo ($rand_num) ?>" type="checkbox" name="[<?php echo ($rand_num) ?>][enable-advsrch]"<?php echo ($enable_in_advsearch ? ' checked' : '') ?> class="depfield-opt-chkunchk">
                            <label for="enable-advsrch-opt-<?php echo ($rand_num) ?>">
                                <span class="chkunchk-onoffswitch-inner"></span>
                                <span class="chkunchk-onoffswitch-switch"></span>
                            </label>
                        </div>
                        <span class="chk-onoffswitch-title"><?php echo esc_html__('Advance search', 'wp-jobsearch'); ?></span>
                    </div>
                </div>
                <div class="depfield-options-con">
                    <label>
                        <?php echo esc_html__('Options', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <?php
                        if (!empty($field_options)) {
                            foreach ($field_options as $opt_rkey => $opt_obar) {
                                $opt_label = isset($opt_obar['label']) ? $opt_obar['label'] : '';
                                $opt_dependts = isset($opt_obar['depend']) ? $opt_obar['depend'] : '';
                                
                                $have_depn_fields = false;
                                if (!empty($opt_dependts) && sizeof($opt_dependts) > 0) {
                                    $have_depn_fields = true;
                                }
                                ?>
                                <div id="field-options-list-<?php echo ($opt_rkey) ?>" class="field-options-list" data-optid="<?php echo ($opt_rkey) ?>">
                                    <input name="jobsearch-custom-fields-dependent_fields[options][<?php echo esc_html($field_counter); ?>][<?php echo ($opt_rkey) ?>][label]" value="<?php echo ($opt_label) ?>" placeholder="<?php esc_html_e('Label', 'wp-jobsearch'); ?>"/>
                                    <a href="javascript:void(0);" class="depfield-add-depnfield-btn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_rkey); ?>"<?php echo ($have_depn_fields ? ' style="display: none;"' : '') ?>><?php esc_html_e('Add Dependent', 'wp-jobsearch'); ?></a>
                                    <a href="javascript:void(0);" class="depfield-option-add-btn" data-kname="jobsearch-custom-fields-dependent_fields[options][<?php echo esc_html($field_counter); ?>]" data-id="<?php echo esc_html($field_counter); ?>"><i class="dashicons dashicons-plus"></i></a>
                                    <a href="javascript:void(0);" class="depfield-option-remove" data-optid="<?php echo esc_html($opt_rkey); ?>"><i class="dashicons dashicons-no-alt"></i></a>
                                    <div class="thisfild-depnf-con"<?php echo (!$have_depn_fields ? ' style="display: none;"' : '') ?>>
                                        <?php
                                        if ($have_depn_fields) {
                                            ?>
                                            <div class="childfield-depn-con">
                                                <div class="childfield-alltypes-con">
                                                    <?php self::fields_btns_html($field_counter, $opt_rkey) ?>
                                                    <p><?php esc_html_e('Click one of the above dependent field.', 'wp-jobsearch'); ?></p>
                                                    <div class="childfield-loder-con"></div>
                                                </div>
                                                <div class="childfield-holdr-con">
                                                    <?php
                                                    foreach ($opt_dependts as $dep_key => $dep_vbar) {
                                                        $dep_ftype = isset($dep_vbar['type']) ? $dep_vbar['type'] : '';
                                                        $dep_vbar['opt_rkey'] = $opt_rkey;
                                                        $dep_vbar['field_counter'] = $dep_key;
                                                        $dep_vbar['input_name'] = 'jobsearch-custom-fields-dependent_fields[options][' . $field_counter . '][' . $opt_rkey . ']';
                                                        self::child_field_sett($field_counter, $dep_vbar);
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            $opt_rkey = rand(100000000, 999999999);
                            ?>
                            <div id="field-options-list-<?php echo ($opt_rkey) ?>" class="field-options-list" data-optid="<?php echo ($opt_rkey) ?>">
                                <input name="jobsearch-custom-fields-dependent_fields[options][<?php echo esc_html($field_counter); ?>][<?php echo ($opt_rkey) ?>][label]" value="" placeholder="<?php esc_html_e('Label', 'wp-jobsearch'); ?>"/>
                                <a href="javascript:void(0);" class="depfield-add-depnfield-btn" data-id="<?php echo esc_html($field_counter); ?>" data-optid="<?php echo esc_html($opt_rkey); ?>"><?php esc_html_e('Add Dependent', 'wp-jobsearch'); ?></a>
                                <a href="javascript:void(0);" class="depfield-option-add-btn" data-kname="jobsearch-custom-fields-dependent_fields[options][<?php echo esc_html($field_counter); ?>]" data-id="<?php echo esc_html($field_counter); ?>"><i class="dashicons dashicons-plus"></i></a>
                                <a href="javascript:void(0);" class="depfield-option-remove" data-optid="<?php echo esc_html($opt_rkey); ?>"><i class="dashicons dashicons-no-alt"></i></a>
                                <div class="thisfild-depnf-con" style="display: none;"></div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function depdfields_admfooter_content() {
        $admin_page = isset($_GET['page']) ? $_GET['page'] : '';
        if ($admin_page == 'jobsearch-job-fields' || $admin_page == 'jobsearch-employer-fields' || $admin_page == 'jobsearch-candidate-fields') {
            ?>
            <script type="text/javascript">
                jQuery(document).on('click', '.depfield-opt-chkunchk', function () {
                    var _this = jQuery(this);
                    var hiden_field = _this.parents('.chekunchk-opt-box').find('input[type=hidden]');
                    if (_this.is(':checked')) {
                        hiden_field.val('on');
                    } else {
                        hiden_field.val('');
                    }
                });
                jQuery(document).on("click", ".depfield-option-add-btn", function (e) {
                    "use strict";
                    e.preventDefault();
                    var _this = jQuery(this);
                    var this_id = _this.attr('data-id');
                    var field_name = _this.attr('data-kname');
                    var rand_num = Math.floor(Math.random() * 999999999) + 1;
                    var html = '<div id="field-options-list-' + rand_num + '" class="field-options-list" data-optid="' + rand_num + '">\
                        <input name="' + field_name + '[' + rand_num + '][label]" value="" placeholder="<?php echo esc_js(esc_html__('Label', 'wp-jobsearch')); ?>"/>\
                        <a href="javascript:void(0);" class="depfield-add-depnfield-btn" data-id="' + this_id + '" data-optid="' + rand_num + '"><?php echo esc_js(esc_html__('Add Dependent', 'wp-jobsearch')); ?></a>\
                        <a href="javascript:void(0);" class="depfield-option-add-btn" data-kname="' + field_name + '" data-id="' + this_id + '"><i class="dashicons dashicons-plus"></i></a>\
                        <a href="javascript:void(0);" class="depfield-option-remove" data-optid="' + rand_num + '"><i class="dashicons dashicons-no-alt"></i></a>\
                        <div class="thisfild-depnf-con" style="display: none;"></div>\
                    </div>';
                    _this.parent('.field-options-list').after(html);
                });
                jQuery(document).on("click", ".depfield-option-remove", function (e) {
                    "use strict";
                    e.preventDefault();
                    var _this = jQuery(this);
                    var this_id = _this.attr('data-optid');
                    _this.parents('#field-options-list-' + this_id).remove();
                });
                jQuery(document).on("click", ".depfield-add-depnfield-btn", function (e) {
                    "use strict";
                    e.preventDefault();
                    var _this = jQuery(this);
                    var this_id = _this.attr('data-id');
                    var opt_id = _this.attr('data-optid');
                    var html = '<div class="childfield-depn-con">\
                        <div class="childfield-alltypes-con">\
                            <ul>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="heading" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('Heading', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-editor-textcolor"></i> <?php echo esc_js(esc_html__('Heading', 'wp-jobsearch')); ?></a>\
                                </li>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="text" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('Text', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-media-text"></i> <?php echo esc_js(esc_html__('Text', 'wp-jobsearch')); ?></a>\
                                </li>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="video" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('Video', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-media-video"></i> <?php echo esc_js(esc_html__('Video', 'wp-jobsearch')); ?></a>\
                                </li>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="url" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('URL', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-admin-links"></i> <?php echo esc_js(esc_html__('URL', 'wp-jobsearch')); ?></a>\
                                </li>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="checkbox" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('Checkbox', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-yes"></i> <?php echo esc_js(esc_html__('Checkbox', 'wp-jobsearch')); ?></a>\
                                </li>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="dropdown" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('Dropdown', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-arrow-down-alt"></i> <?php echo esc_js(esc_html__('Dropdown', 'wp-jobsearch')); ?></a>\
                                </li>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="email" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('Email', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-email-alt"></i> <?php echo esc_js(esc_html__('Textarea', 'wp-jobsearch')); ?></a>\
                                </li>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="number" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('Number', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-editor-ol"></i> <?php echo esc_js(esc_html__('Number', 'wp-jobsearch')); ?></a>\
                                </li>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="date" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('Date', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-calendar-alt"></i> <?php echo esc_js(esc_html__('Date', 'wp-jobsearch')); ?></a>\
                                </li>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="textarea" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('Textarea', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-editor-alignleft"></i> <?php echo esc_js(esc_html__('Textarea', 'wp-jobsearch')); ?></a>\
                                </li>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="range" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('Range', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-image-flip-horizontal"></i> <?php echo esc_js(esc_html__('Range', 'wp-jobsearch')); ?></a>\
                                </li>\
                                <li>\
                                    <a class="addchild-depnd-bxbtn" data-id="' + this_id + '" data-optid="' + opt_id + '" \
                                       data-field="upload" \
                                       href="javascript:void(0);" data-fieldlabel="<?php echo esc_js(esc_html__('Upload', 'wp-jobsearch')); ?>"><i class="dashicons dashicons-admin-media"></i> <?php echo esc_js(esc_html__('Upload', 'wp-jobsearch')); ?></a>\
                                </li>\
                            </ul>\
                            <p><?php echo esc_js(esc_html__('Click one of the above dependent field.', 'wp-jobsearch')); ?></p>\
                            <div class="childfield-loder-con"></div>\
                        </div>\
                        <div class="childfield-holdr-con"></div>\
                    </div>';
                    _this.parent('.field-options-list').find('.thisfild-depnf-con').removeAttr('style').append(html);
                    _this.hide();
                });
                jQuery(document).on("click", ".addchild-depnd-bxbtn", function () {
                    var _this = jQuery(this);
                    var opt_rkey = _this.attr('data-optid');
                    var _thism_parnt = jQuery('#field-options-list-' + opt_rkey + ' > .thisfild-depnf-con > .childfield-depn-con');
                    var field_name = jQuery('#field-options-list-' + opt_rkey + ' > a.depfield-option-add-btn').attr('data-kname');
                    var field_id = _this.attr('data-id');
                    var field_type = _this.attr('data-field');
                    var field_label = _this.attr('data-fieldlabel');
                    var this_loader = _thism_parnt.find('> .childfield-alltypes-con').find('.childfield-loder-con');
                    var this_apender = _thism_parnt.find('> .childfield-holdr-con');
                    this_loader.html('<span class="spinner is-active"></span>');
                    
                    if (!_thism_parnt.hasClass('childfield-addin')) {
                        _thism_parnt.addClass('childfield-addin');
                        var request = jQuery.ajax({
                            url: ajaxurl,
                            method: "POST",
                            data: {
                                field_id: field_id,
                                opt_rkey: opt_rkey,
                                field_name: field_name,
                                field_type: field_type,
                                field_label: field_label,
                                action: 'jobsearch_addin_cus_depnfield_child',
                            },
                            dataType: "json"
                        });
                        request.done(function (response) {
                            if (typeof response.html !== 'undefined') {
                                this_apender.append(response.html);
                            }
                        });
                        request.complete(function () {
                            this_loader.html('');
                            _thism_parnt.removeClass('childfield-addin');
                        });
                    }
                });
                jQuery(document).on("click", ".depnd-field-hder", function () {
                    var _this = jQuery(this);
                    var this_id = _this.attr('data-id');
                    var _thism_parnt = _this.parents('#cusdepnf-itemcon-' + this_id);
                    _thism_parnt.find('>.depnd-field-wraper').slideToggle();
                });
                jQuery(document).on("click", ".depnd-cuschfield-trash", function () {
                    var _this = jQuery(this);
                    var this_id = _this.attr('data-id');
                    var _thism_parnt = _this.parents('#cusdepnf-itemcon-' + this_id);
                    _thism_parnt.remove();
                });
                jQuery(document).ready(function() {
                    if (jQuery('.customfield-selectize').length > 0) {
                        jQuery('.customfield-selectize').selectize({
                            plugins: ['remove_button'],
                        });
                    }
                });
                jQuery(document).on("change", ".radiochk-selectbtns-field input[type=checkbox], .radiochk-selectbtns-field input[type=radio]", function () {
                    var _this = jQuery(this);
                    var hiden_input = _this.parents('.radiochk-selectbtns-field').find('input[type=hidden]');
                    hiden_input.val(_this.val());
                });
            </script>
            <?php
        }
    }
    
    public function add_cus_depnfield_child() {
        $field_type = $_POST['field_type'];
        $field_id = $_POST['field_id'];
        $opt_rkey = $_POST['opt_rkey'];
        $field_name = $_POST['field_name'];
        
        $field_details = array(
            'opt_rkey' => $opt_rkey,
            'type' => $field_type,
            'input_name' => $field_name . '[' . $opt_rkey . ']',
        );
        
        ob_start();
        self::child_field_sett($field_id, $field_details);
        $html = ob_get_clean();
        
        wp_send_json(array('html' => $html));
    }
    
    private static function child_field_sett($field_id, $field_data = array()) {
        global $careerfy_icons_fields;
        
        $child_field_counter = rand(100000000, 999999999);
        if (isset($field_data['field_counter'])) {
            $child_field_counter = $field_data['field_counter'];
        }
        $field_type = isset($field_data['type']) ? $field_data['type'] : '';
        
        $field_lbl_str = esc_html__('Text', 'wp-jobsearch');
        if ($field_type == 'heading') {
            $field_lbl_str = esc_html__('Heading', 'wp-jobsearch');
        } else if ($field_type == 'video') {
            $field_lbl_str = esc_html__('Video', 'wp-jobsearch');
        } else if ($field_type == 'url') {
            $field_lbl_str = esc_html__('URL', 'wp-jobsearch');
        } else if ($field_type == 'checkbox') {
            $field_lbl_str = esc_html__('Checkbox', 'wp-jobsearch');
        } else if ($field_type == 'dropdown') {
            $field_lbl_str = esc_html__('Dropdown', 'wp-jobsearch');
        } else if ($field_type == 'email') {
            $field_lbl_str = esc_html__('Email', 'wp-jobsearch');
        } else if ($field_type == 'number') {
            $field_lbl_str = esc_html__('Number', 'wp-jobsearch');
        } else if ($field_type == 'date') {
            $field_lbl_str = esc_html__('Date', 'wp-jobsearch');
        } else if ($field_type == 'textarea') {
            $field_lbl_str = esc_html__('Textarea', 'wp-jobsearch');
        } else if ($field_type == 'range') {
            $field_lbl_str = esc_html__('Range', 'wp-jobsearch');
        } else if ($field_type == 'upload') {
            $field_lbl_str = esc_html__('Upload', 'wp-jobsearch');
        }
        
        $opt_rkey = isset($field_data['opt_rkey']) ? $field_data['opt_rkey'] : '';
        
        $rand_num = $opt_rkey . '-' . $child_field_counter;
        
        $field_name_strt = $field_data['input_name'] . '[depend][' . $child_field_counter . ']';
        
        $field_label = isset($field_data['label']) ? $field_data['label'] : '';
        $field_label = stripslashes($field_label);
        $field_placeholder = isset($field_data['placeholder']) ? $field_data['placeholder'] : '';
        $field_classes = isset($field_data['classes']) ? $field_data['classes'] : '';
        $field_required = isset($field_data['required']) ? $field_data['required'] : '';
        $enable_in_search = isset($field_data['enable-search']) ? $field_data['enable-search'] : '';
        $enable_in_advsearch = isset($field_data['enable-advsrch']) ? $field_data['enable-advsrch'] : '';
        //
        $url_field_target = isset($field_data['link_target']) ? $field_data['link_target'] : '';
        //
        $upload_field_multifiles = isset($field_data['multi_files']) ? $field_data['multi_files'] : '';
        $upload_field_numof_files = isset($field_data['numof_files']) ? $field_data['numof_files'] : '';
        $upload_field_numof_files = $upload_field_numof_files > 0 ? $upload_field_numof_files : 5;
        $upload_field_allow_types = isset($field_data['allow_types']) ? $field_data['allow_types'] : '';
        $upload_field_allow_types = !empty($upload_field_allow_types) ? $upload_field_allow_types : array();
        $upload_field_file_size = isset($field_data['file_size']) ? $field_data['file_size'] : '';
        $upload_field_file_size = $upload_field_file_size == '' ? '5MB' : $upload_field_file_size;
        //
        $date_field_date_format = isset($field_data['date-format']) ? $field_data['date-format'] : '';
        //
        $range_field_field_style = isset($field_data['field-style']) ? $field_data['field-style'] : '';
        $range_field_min = isset($field_data['min']) ? $field_data['min'] : '';
        $range_field_laps = isset($field_data['laps']) ? $field_data['laps'] : '';
        $range_field_interval = isset($field_data['interval']) ? $field_data['interval'] : '';
        //
        $textarea_field_rich_editor = isset($field_data['rich_editor']) ? $field_data['rich_editor'] : '';
        $textarea_field_media_btns = isset($field_data['media_buttons']) ? $field_data['media_buttons'] : '';
        //
        $field_is_multi = isset($field_data['multi']) ? $field_data['multi'] : '';
        //
        $field_icon = isset($field_data['icon']) ? $field_data['icon'] : '';
        $field_icon_group = isset($field_data['icon_group']) ? $field_data['icon_group'] : '';
        ?>
        <div id="cusdepnf-itemcon-<?php echo ($child_field_counter) ?>" class="jobsearch-cusdepnf-itemcon">
            <div class="field-intro">
                <?php $field_dyn_name = $field_label != '' ? $field_lbl_str . ' <strong>(' . $field_label . ')</strong>' : $field_lbl_str ?>
                <a href="javascript:void(0);" class="depnd-field-hder" data-id="<?php echo ($child_field_counter) ?>"><?php echo ($field_dyn_name); ?></a>
                <div class="cusdepnf-itemhdr-btnscon">
                    <a href="javascript:void(0);" class="depnd-cuschfield-updte" data-id="<?php echo ($child_field_counter) ?>"><i class="dashicons dashicons-image-flip-vertical"></i></a>
                    <a href="javascript:void(0);" class="depnd-cuschfield-trash" data-id="<?php echo ($child_field_counter) ?>"><i class="dashicons dashicons-trash"></i></a>
                </div>
            </div>
            <div class="field-data depnd-field-wraper" style="display: none;">
                <input type="hidden" name="<?php echo ($field_name_strt) ?>[type]" value="<?php echo ($field_type) ?>">

                <?php
                if ($field_type != 'heading') {
                    ?>
                    <div class="depfield-setins-con">
                        <div class="chekunchk-opt-box">
                            <div class="chekunchk-opt-boxiner">
                                <input type="hidden" name="<?php echo ($field_name_strt) ?>[required]" value="<?php echo ($field_required) ?>">
                                <input id="required-opt-<?php echo ($rand_num) ?>" type="checkbox" name="[<?php echo ($rand_num) ?>][required]"<?php echo ($field_required ? ' checked' : '') ?> class="depfield-opt-chkunchk">
                                <label for="required-opt-<?php echo ($rand_num) ?>">
                                    <span class="chkunchk-onoffswitch-inner"></span>
                                    <span class="chkunchk-onoffswitch-switch"></span>
                                </label>
                            </div>
                            <span class="chk-onoffswitch-title"><?php echo esc_html__('Required', 'wp-jobsearch'); ?></span>
                        </div>
                        <?php
                        if ($field_type == 'checkbox' || $field_type == 'dropdown') {
                            ?>
                            <div class="chekunchk-opt-box">
                                <div class="chekunchk-opt-boxiner">
                                    <input type="hidden" name="<?php echo ($field_name_strt) ?>[multi]" value="<?php echo ($field_is_multi) ?>">
                                    <input id="is-multi-opt-<?php echo ($rand_num) ?>" type="checkbox" name="[<?php echo ($rand_num) ?>][multi]"<?php echo ($field_is_multi ? ' checked' : '') ?> class="depfield-opt-chkunchk">
                                    <label for="is-multi-opt-<?php echo ($rand_num) ?>">
                                        <span class="chkunchk-onoffswitch-inner"></span>
                                        <span class="chkunchk-onoffswitch-switch"></span>
                                    </label>
                                </div>
                                <span class="chk-onoffswitch-title"><?php echo esc_html__('Multi-Select', 'wp-jobsearch'); ?></span>
                            </div>
                            <?php
                        }
                        if ($field_type != 'upload') {
                            ?>
                            <div class="chekunchk-opt-box">
                                <div class="chekunchk-opt-boxiner">
                                    <input type="hidden" name="<?php echo ($field_name_strt) ?>[enable-search]" value="<?php echo ($enable_in_search) ?>">
                                    <input id="enable-search-opt-<?php echo ($rand_num) ?>" type="checkbox" name="[<?php echo ($rand_num) ?>][enable-search]"<?php echo ($enable_in_search ? ' checked' : '') ?> class="depfield-opt-chkunchk">
                                    <label for="enable-search-opt-<?php echo ($rand_num) ?>">
                                        <span class="chkunchk-onoffswitch-inner"></span>
                                        <span class="chkunchk-onoffswitch-switch"></span>
                                    </label>
                                </div>
                                <span class="chk-onoffswitch-title"><?php echo esc_html__('Filter search', 'wp-jobsearch'); ?></span>
                            </div>
                            <div class="chekunchk-opt-box">
                                <div class="chekunchk-opt-boxiner">
                                    <input type="hidden" name="<?php echo ($field_name_strt) ?>[enable-advsrch]" value="<?php echo ($enable_in_advsearch) ?>">
                                    <input id="enable-advsrch-opt-<?php echo ($rand_num) ?>" type="checkbox" name="[<?php echo ($rand_num) ?>][enable-advsrch]"<?php echo ($enable_in_advsearch ? ' checked' : '') ?> class="depfield-opt-chkunchk">
                                    <label for="enable-advsrch-opt-<?php echo ($rand_num) ?>">
                                        <span class="chkunchk-onoffswitch-inner"></span>
                                        <span class="chkunchk-onoffswitch-switch"></span>
                                    </label>
                                </div>
                                <span class="chk-onoffswitch-title"><?php echo esc_html__('Advance search', 'wp-jobsearch'); ?></span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
                <label>
                    <?php echo esc_html__('Field Label', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="<?php echo ($field_name_strt) ?>[label]"
                           value="<?php echo esc_html($field_label); ?>"/>
                </div>

                <?php
                if ($field_type != 'heading') {
                    ?>
                    <label>
                        <?php echo esc_html__('Placeholder', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <input name="<?php echo ($field_name_strt) ?>[placeholder]"
                               value="<?php echo esc_html(stripslashes($field_placeholder)); ?>"/>
                    </div>
                    <?php
                }
                if ($field_type == 'url') {
                    ?>
                    <label>
                        <?php echo esc_html__('Target', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <select name="<?php echo ($field_name_strt) ?>[link_target]">
                            <option <?php if ($url_field_target == '_self') echo esc_html('selected'); ?>
                                    value="_self"><?php echo esc_html__('Self', 'wp-jobsearch'); ?></option>
                            <option <?php if ($url_field_target == '_blank') echo esc_html('selected'); ?>
                                    value="_blank"><?php echo esc_html__('Blank', 'wp-jobsearch'); ?></option>
                        </select>
                    </div>
                    <?php
                }
                if ($field_type == 'date') {
                    ?>
                    <label>
                        <?php echo esc_html__('Date Format', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <input type="text" name="<?php echo ($field_name_strt) ?>[date-format]"
                               value="<?php echo esc_html($date_field_date_format); ?>"/>
                        <div class="hint-for-field">
                            <p style="margin: 12px 0 0 0; line-height: 22px;">
                                <strong><?php echo esc_html__('Hint::', 'wp-jobsearch'); ?></strong> <?php echo esc_html__('Put date format like this "d-m-Y".', 'wp-jobsearch'); ?>
                            </p>
                        </div>
                    </div>
                    <?php
                }
                if ($field_type == 'textarea') {
                    ?>
                    <label>
                        <?php echo esc_html__('Rich Editor', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <select name="<?php echo ($field_name_strt) ?>[rich_editor]">
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
                        <select name="<?php echo ($field_name_strt) ?>[media_buttons]">
                            <option <?php if ($textarea_field_media_btns == 'yes') echo esc_html('selected'); ?>
                                    value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                            <option <?php if ($textarea_field_media_btns == 'no') echo esc_html('selected'); ?>
                                    value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                        </select>
                    </div>
                    <?php
                }
                if ($field_type == 'range') {
                    ?>
                    <label>
                        <?php echo esc_html__('Min', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <input name="<?php echo ($field_name_strt) ?>[min]" type="number"
                               value="<?php echo esc_html($range_field_min); ?>"/>
                    </div>

                    <label>
                        <?php echo esc_html__('Total Laps', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <input name="<?php echo ($field_name_strt) ?>[laps]" type="number" min="1" max="100"
                               value="<?php echo esc_html($range_field_laps); ?>"/>
                        <em><?php esc_html_e('Minimum value is 1 and maximum is 100.', 'wp-jobsearch'); ?></em>
                    </div>

                    <label>
                        <?php echo esc_html__('Interval', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <input name="<?php echo ($field_name_strt) ?>[interval]" type="number"
                               value="<?php echo esc_html($range_field_interval); ?>"/>
                    </div>

                    <label>
                        <?php echo esc_html__('Style', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <select name="<?php echo ($field_name_strt) ?>[field-style]">
                            <option <?php if ($range_field_field_style == 'simple') echo esc_html('selected'); ?>
                                    value="simple"><?php echo esc_html__('Simple', 'wp-jobsearch'); ?></option>
                            <option <?php if ($range_field_field_style == 'slider') echo esc_html('selected'); ?>
                                    value="slider"><?php echo esc_html__('Slider', 'wp-jobsearch'); ?></option>
                        </select>
                    </div>
                    <?php
                }
                if ($field_type == 'upload') {
                    ?>
                    <label>
                        <?php echo esc_html__('Multi Files', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <select id="multi-cfield-selct-<?php echo absint($rand_num); ?>"
                                name="<?php echo ($field_name_strt) ?>[multi_files]">
                            <option <?php if ($upload_field_multifiles == 'no') echo esc_html('selected'); ?>
                                    value="no"><?php echo esc_html__('No', 'wp-jobsearch'); ?></option>
                            <option <?php if ($upload_field_multifiles == 'yes') echo esc_html('selected'); ?>
                                    value="yes"><?php echo esc_html__('Yes', 'wp-jobsearch'); ?></option>
                        </select>
                    </div>

                    <div id="num-fields-holdr-<?php echo absint($rand_num); ?>" class="num_fields_holdr"
                         style="margin-bottom: 15px; display: <?php echo($upload_field_multifiles == 'yes' ? 'block' : 'none') ?>;">
                        <label>
                            <?php echo esc_html__('Number of Files', 'wp-jobsearch'); ?>:
                        </label>
                        <div class="input-field">
                            <input name="<?php echo ($field_name_strt) ?>[numof_files]"
                                   value="<?php echo absint($upload_field_numof_files); ?>"/>
                        </div>
                    </div>

                    <label>
                        <?php echo esc_html__('Allow File Types', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <select name="<?php echo ($field_name_strt) ?>[allow_types][]"
                                multiple="multiple" class="customfield-selectize"
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
                        <input name="<?php echo ($field_name_strt) ?>[file_size]"
                               value="<?php echo esc_html($upload_field_file_size); ?>"/>
                        <em><?php esc_html_e('Insert File size here only in MB i.e. 1MB, 2MB, 5MB, 50MB, 100MB.', 'wp-jobsearch'); ?></em>
                    </div>
                    <script>
                        jQuery(document).on('change', '#multi-cfield-selct-<?php echo absint($rand_num); ?>', function () {
                            if (jQuery(this).val() == 'yes') {
                                jQuery('#num-fields-holdr-<?php echo absint($rand_num); ?>').removeAttr('style');
                            } else {
                                jQuery('#num-fields-holdr-<?php echo absint($rand_num); ?>').hide();
                            }
                        });
                    </script>
                    <?php
                }
                ?>

                <label>
                    <?php echo esc_html__('Custom CSS Class', 'wp-jobsearch'); ?>:
                </label>
                <div class="input-field">
                    <input name="<?php echo ($field_name_strt) ?>[classes]" value="<?php echo esc_html($field_classes); ?>">
                </div>
                
                <?php
                if ($field_type != 'heading') {
                    ?>

                    <label>
                        <?php echo esc_html__('Icon', 'wp-jobsearch'); ?>:
                    </label>
                    <div class="input-field">
                        <?php
                        $icon_id = rand(1000000, 99999999);
                        if (is_object($careerfy_icons_fields)) {
                            echo $careerfy_icons_fields->careerfy_icons_fields_callback($field_icon, $icon_id, $field_name_strt . '[icon]', $field_icon_group, $field_name_strt . '[icon-group]');
                        } else {
                            echo jobsearch_icon_picker($field_icon, $icon_id, $field_name_strt . '[icon]');
                        }
                        ?>
                    </div>

                    <?php
                }
                //
                if ($field_type == 'dropdown' || $field_type == 'checkbox') {
                    $field_options = isset($field_data['options']) ? $field_data['options'] : '';
                    ?>
                    <div class="depfield-options-con">
                        <label>
                            <?php echo esc_html__('Options', 'wp-jobsearch'); ?>:
                        </label>
                        <div class="input-field">
                            <?php
                            if (!empty($field_options)) {
                                foreach ($field_options as $opt_rkey => $opt_obar) {
                                    $opt_label = isset($opt_obar['label']) ? $opt_obar['label'] : '';
                                    $opt_dependts = isset($opt_obar['depend']) ? $opt_obar['depend'] : '';

                                    $have_depn_fields = false;
                                    if (!empty($opt_dependts) && sizeof($opt_dependts) > 0) {
                                        $have_depn_fields = true;
                                    }
                                    ?>
                                    <div id="field-options-list-<?php echo ($opt_rkey) ?>" class="field-options-list" data-optid="<?php echo ($opt_rkey) ?>">
                                        <input name="<?php echo ($field_name_strt) ?>[options][<?php echo ($opt_rkey) ?>][label]" value="<?php echo ($opt_label) ?>" placeholder="<?php esc_html_e('Label', 'wp-jobsearch'); ?>"/>
                                        <a href="javascript:void(0);" class="depfield-add-depnfield-btn" data-id="<?php echo esc_html($field_id); ?>" data-optid="<?php echo esc_html($opt_rkey); ?>"<?php echo ($have_depn_fields ? ' style="display: none;"' : '') ?>><?php esc_html_e('Add Dependent', 'wp-jobsearch'); ?></a>
                                        <a href="javascript:void(0);" class="depfield-option-add-btn" data-kname="<?php echo ($field_name_strt) ?>[options]" data-id="<?php echo esc_html($field_id); ?>"><i class="dashicons dashicons-plus"></i></a>
                                        <a href="javascript:void(0);" class="depfield-option-remove" data-optid="<?php echo esc_html($opt_rkey); ?>"><i class="dashicons dashicons-no-alt"></i></a>
                                        <div class="thisfild-depnf-con"<?php echo (!$have_depn_fields ? ' style="display: none;"' : '') ?>>
                                            <?php
                                            if ($have_depn_fields) {
                                                ?>
                                                <div class="childfield-depn-con">
                                                    <div class="childfield-alltypes-con">
                                                        <?php self::fields_btns_html($field_id, $opt_rkey) ?>
                                                        <p><?php esc_html_e('Click one of the above dependent field.', 'wp-jobsearch'); ?></p>
                                                        <div class="childfield-loder-con"></div>
                                                    </div>
                                                    <div class="childfield-holdr-con">
                                                        <?php
                                                        foreach ($opt_dependts as $dep_key => $dep_vbar) {
                                                            $dep_ftype = isset($dep_vbar['type']) ? $dep_vbar['type'] : '';
                                                            $dep_vbar['opt_rkey'] = $opt_rkey;
                                                            $dep_vbar['field_counter'] = $dep_key;
                                                            $dep_vbar['input_name'] = $field_name_strt . '[options][' .$opt_rkey . ']';
                                                            self::child_field_sett($field_id, $dep_vbar);
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                $opt_rkey = rand(100000000, 999999999);
                                ?>
                                <div id="field-options-list-<?php echo ($opt_rkey) ?>" class="field-options-list" data-optid="<?php echo ($opt_rkey) ?>">
                                    <input name="<?php echo ($field_name_strt) ?>[options][<?php echo ($opt_rkey) ?>][label]" value="" placeholder="<?php esc_html_e('Label', 'wp-jobsearch'); ?>"/>
                                    <a href="javascript:void(0);" class="depfield-add-depnfield-btn" data-id="<?php echo esc_html($field_id); ?>" data-optid="<?php echo esc_html($opt_rkey); ?>"><?php esc_html_e('Add Dependent', 'wp-jobsearch'); ?></a>
                                    <a href="javascript:void(0);" class="depfield-option-add-btn" data-kname="<?php echo ($field_name_strt) ?>[options]" data-id="<?php echo esc_html($field_id); ?>"><i class="dashicons dashicons-plus"></i></a>
                                    <a href="javascript:void(0);" class="depfield-option-remove" data-optid="<?php echo esc_html($opt_rkey); ?>"><i class="dashicons dashicons-no-alt"></i></a>
                                    <div class="thisfild-depnf-con" style="display: none;"></div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }
}

global $jobsearch_cusfdepfields_structre;
$jobsearch_cusfdepfields_structre = new Jobsearch_CustomField_DepFields();
