<?php

use WP_Jobsearch\Package_Limits;

if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_CustomField_DepFields_Render {

    // hook things up
    public function __construct() {
        add_filter('jobsearch_dashboard_custom_field_dependent_fields_load', array($this, 'depdfields_in_dashbord'), 10, 6);
        add_filter('jobsearch_custom_field_dependent_fields_load', array($this, 'depdfields_in_dashbord'), 10, 6);
        add_filter('jobsearch_form_custom_field_dependent_fields_load', array($this, 'depdfields_in_dashbord'), 10, 9);
        
        add_action('wp_ajax_jobsearch_get_depend_fields_infront', array($this, 'depend_fields_infront_call'));
        add_action('wp_ajax_nopriv_jobsearch_get_depend_fields_infront', array($this, 'depend_fields_infront_call'));
    }
    
    public function depdfields_in_dashbord($html, $post_id, $field_saved_data, $fields_prefix, $field_key, $field_place = 'dash', $self_vals = array(), $con_class = '', $f_display = '') {
        global $custom_field_posttype;
        
        $user_pkg_limits = new Package_Limits;
        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);
        
        $field_for_wuser = isset($field_saved_data['non_reg_user']) ? $field_saved_data['non_reg_user'] : '';
        $field_label = isset($field_saved_data['label']) ? $field_saved_data['label'] : '';
        $field_name = isset($field_saved_data['name']) ? $fields_prefix . $field_saved_data['name'] : '';
        $field_classes = isset($field_saved_data['classes']) ? $field_saved_data['classes'] : '';
        $field_placeholder = isset($field_saved_data['placeholder']) ? $field_saved_data['placeholder'] : '';
        $field_required = isset($field_saved_data['required']) ? $field_saved_data['required'] : '';
        $field_type = isset($field_saved_data['field_type']) ? $field_saved_data['field_type'] : '';
        $field_is_multi = isset($field_saved_data['multi']) ? $field_saved_data['multi'] : '';
        $field_options = isset($field_saved_data['options']) ? $field_saved_data['options'] : '';
        
        $field_db_val = get_post_meta($post_id, $field_name, true);
        
        $field_required_str = '';
        if ($field_required == 'on') {
            $field_required_str = 'required="required"';
            $field_label = $field_label . ' *';
        }
        $admin_field_start = $admin_field_mid = $admin_field_end = '';
        if ($field_place == 'admin') {
            $main_con_tag = 'div';
            $admin_field_start = '<div class="elem-label">';
            $admin_field_mid = '</div><div class="elem-field">';
            $admin_field_end = '</div>';
        } else {
            $main_con_tag = 'li';
        }
        
        $main_con_class = '';
        $con_f_style = '';
        if ($field_place == 'admin') {
            $main_con_class = 'jobsearch-element-field';
        } else if ($field_place == 'simp_form' || $field_place == 'signup') {
            if ($field_type == 'radio') {
                $main_con_class = 'jobsearch-user-form-coltwo-full';
            }
            if ($con_class != '') {
                $main_con_class .= ' ' . $con_class;
            }
            if ($f_display != '') {
                $con_f_style = 'style="display: ' . $f_display . ';"';
            }
        } else {
            $main_con_class = 'jobsearch-column-' . ($field_type == 'radio' ? '12' : '6');
        }
        
        $check_requr_class = '';
        if ($field_type == 'radio') {
            if ($field_place == 'dash') {
                $check_requr_class = ' cusfield-checkbox-required';
            }
            if ($field_place == 'form') {
                $check_requr_class = ' required-cussel-field';
            }
        }
        
        ob_start();
        //echo '<pre>';
        //var_dump($field_saved_data);
        //echo '</pre>';
        ?>
        <<?php echo ($main_con_tag) ?> class="<?php echo ($main_con_class) ?><?php echo ($check_requr_class) ?> <?php echo ($field_type == 'radio' && $field_is_multi == 'on' ? 'dep-multi-checks' : '') ?> jobsearch-depndetfield-con" <?php echo ($con_f_style) ?> data-thid="<?php echo ($field_key) ?>" data-plc="<?php echo ($field_place) ?>" data-mid="<?php echo ($field_key) ?>" data-ftype="<?php echo ($custom_field_posttype) ?>">
            <?php echo ($admin_field_start) ?>
            <label><?php echo ($field_label) ?></label>
            <?php echo ($admin_field_mid) ?>
            <?php
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $field_name) && $field_place != 'admin') {
                echo ($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $field_name) && $field_place != 'admin') {
                echo ($user_pkg_limits::emp_gen_locked_html());
            } else {
                if (!empty($field_options) && count($field_options) > 0) {
                    if ($field_type == 'radio') {
                        foreach ($field_options as $opt_rkey => $opt_obj) {
                            $opt_label = isset($opt_obj['label']) ? $opt_obj['label'] : '';
                            $opt_depend = isset($opt_obj['depend']) ? $opt_obj['depend'] : '';
                            
                            $has_depend = 'false';
                            if (!empty($opt_depend)) {
                                $has_depend = 'true';
                            }

                            $selected_str = '';
                            if (!empty($field_db_val)) {
                                if ($field_is_multi == 'on') {
                                    if (in_array($opt_label, $field_db_val)) {
                                        $selected_str = ' checked="checked"';
                                    }
                                } else {
                                    if ($opt_label == $field_db_val) {
                                        $selected_str = ' checked="checked"';
                                    }
                                }
                            }
                            ?>
                            <div class="cusfield-checkbox-radioitm jobsearch-checkbox <?php echo jobsearch_esc_html($field_classes) ?>">
                                <input id="opt-<?php echo ($field_name . '-' . $opt_rkey) ?>"
                                       type="<?php echo ($field_is_multi == 'on' ? 'checkbox' : 'radio') ?>" name="<?php echo ($field_name) ?><?php echo ($field_is_multi == 'on' ? '[]' : '') ?>"
                                       value="<?php echo ($opt_label) ?>" <?php echo ($selected_str) ?> class="jobsearch-depndfield-rchchange" data-optid="<?php echo ($opt_rkey) ?>" data-depend="<?php echo ($has_depend) ?>">
                                <label for="opt-<?php echo ($field_name . '-' . $opt_rkey) ?>">
                                    <span></span> <?php echo ($opt_label) ?>
                                </label>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="jobsearch-profile-select">
                            <select
                                <?php echo($field_is_multi == 'on' ? 'multiple="multiple" ' : '') ?>name="<?php echo jobsearch_esc_html($field_name) ?><?php echo ($field_is_multi == 'on' ? '[]' : '') ?>"
                                placeholder="<?php echo ($field_placeholder) ?>"
                                class="<?php echo jobsearch_esc_html($field_classes) ?> depndfield-selectize jobsearch-depndfield-srchange" <?php echo ($field_required_str) ?>>
                                <?php
                                if ($field_placeholder != '') {
                                    ?>
                                    <option value="" data-depend="false" data-id="0"><?php echo ($field_placeholder) ?></option>
                                    <?php
                                }
                                foreach ($field_options as $opt_rkey => $opt_obj) {
                                    $opt_label = isset($opt_obj['label']) ? $opt_obj['label'] : '';
                                    $opt_depend = isset($opt_obj['depend']) ? $opt_obj['depend'] : '';
                                    
                                    $selected_str = '';
                                    if (!empty($field_db_val)) {
                                        if ($field_is_multi == 'on') {
                                            if (in_array($opt_label, $field_db_val)) {
                                                $selected_str = ' selected="selected"';
                                            }
                                        } else {
                                            if ($opt_label == $field_db_val) {
                                                $selected_str = ' selected="selected"';
                                            }
                                        }
                                    }
                                    
                                    $has_depend = 'false';
                                    if (!empty($opt_depend)) {
                                        $has_depend = 'true';
                                    }
                                    ?>
                                    <option value="<?php echo ($opt_label) ?>"<?php echo ($selected_str) ?> data-data='{"depend": "<?php echo ($has_depend) ?>","optid": "<?php echo ($opt_rkey) ?>"}'><?php echo esc_js($opt_label) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <span><?php echo jobsearch_esc_html('Field did not configure properly', 'wp-jobsearch'); ?></span>
                    <?php
                }
            }
            ?>
            <?php echo ($admin_field_end) ?>
        </<?php echo ($main_con_tag) ?>>
        <?php
        if (!empty($field_db_val)) {
            $opti_vals = $field_db_val;
            if (!is_array($field_db_val)) {
                $opti_vals = array($field_db_val);
            }
            $cusfiled_arr = get_option('jobsearch_custom_field_' . $custom_field_posttype);
            $cusfiled_arobj = isset($cusfiled_arr[$field_key]) ? $cusfiled_arr[$field_key] : '';
            if (isset($cusfiled_arobj['options']) && is_array($cusfiled_arobj['options'])) {
                $optids = array();
                foreach ($cusfiled_arobj['options'] as $cusfiled_arkey => $cusfiled_aror) {
                    if (isset($cusfiled_aror['label']) && in_array($cusfiled_aror['label'], $opti_vals)) {
                        $optids[] = $cusfiled_arkey;
                    }
                }

                echo self::depend_fields_html($optids, $field_key, $cusfiled_arobj, $custom_field_posttype, false, $post_id, $field_place);
            }
        }
        $html = ob_get_clean();
        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }
        return $html;
    }
    
    public static function depend_fields_html($optids, $field_mid, $cusfiled_arobj, $entity_type, $is_ajax = false, $post_id = 0, $field_place = 'dash') {
        if (isset($cusfiled_arobj['options'])) {
            $field_mainame = isset($cusfiled_arobj['name']) ? $cusfiled_arobj['name'] : '';
            
            $html = '';
            if (!empty($optids)) {
                
                $admin_field_start = $admin_field_mid = $admin_field_end = '';
                if ($field_place == 'admin') {
                    $main_con_tag = 'div';
                    $admin_field_start = '<div class="elem-label">';
                    $admin_field_mid = '</div><div class="elem-field">';
                    $admin_field_end = '</div>';
                } else {
                    $main_con_tag = 'li';
                }
                foreach ($optids as $optid) {
                    
                    $cusfiled_plod = explode('|', $field_mid);
                    $fields_major_key = isset($cusfiled_plod[0]) ? $cusfiled_plod[0] : '';
                    if (sizeof($cusfiled_plod) > 1) {
                        $field_kdepend_arr = $cusfiled_arobj['options'];
                        $mid_keys_counter = 0;
                        foreach ($cusfiled_plod as $cusfiled_keyid) {
                            if ($mid_keys_counter == 0) {
                                $mid_keys_counter++;
                                continue;
                            }
                            $each_dep_key = 'depend';
                            if (fmod($mid_keys_counter, 2) == 0) {
                                $each_dep_key = 'options';
                            }
                            $field_kdepend_arr = isset($field_kdepend_arr[$cusfiled_keyid][$each_dep_key]) ? $field_kdepend_arr[$cusfiled_keyid][$each_dep_key] : '';
                            $mid_keys_counter++;
                        }
                        $field_kdepend_arr = isset($field_kdepend_arr[$optid]['depend']) ? $field_kdepend_arr[$optid]['depend'] : '';
                    } else {
                        $field_kdepend_arr = isset($cusfiled_arobj['options'][$optid]['depend']) ? $cusfiled_arobj['options'][$optid]['depend'] : '';
                    }
                    
                    if (!empty($field_kdepend_arr)) {
                        
                        $dynm_deparent_classes = self::dynm_deparent_classes($field_mid);

                        ob_start();
                        foreach ($field_kdepend_arr as $kdepnd_key => $kdepnd_arr) {
                            $field_type = isset($kdepnd_arr['type']) ? $kdepnd_arr['type'] : '';

                            $main_con_class = '';
                            $full_field_types = array('heading', 'upload', 'textarea', 'checkbox');
                            if ($field_place == 'admin') {
                                $main_con_class = 'jobsearch-element-field';
                            } else if ($field_place == 'simp_form' || $field_place == 'signup') {
                                if (in_array($field_type, $full_field_types)) {
                                    $main_con_class = 'jobsearch-user-form-coltwo-full';
                                }
                                if ($field_place == 'signup') {
                                    if ($entity_type == 'employer') {
                                        $main_con_class .= ' employer-cus-field';
                                    } else if ($entity_type == 'candidate') {
                                        $main_con_class .= ' candidate-cus-field';
                                    }
                                }
                            } else {
                                $main_con_class = 'jobsearch-column-' . (in_array($field_type, $full_field_types) ? '12' : '6');
                            }

                            $field_name = $field_mainame . '_' . $kdepnd_key;

                            $field_label = isset($kdepnd_arr['label']) ? $kdepnd_arr['label'] : '';
                            $field_required = isset($kdepnd_arr['required']) ? $kdepnd_arr['required'] : '';
                            $field_required_str = '';
                            if ($field_required == 'on') {
                                $field_required_str = 'required="required"';
                                $field_label = $field_label . ' *';
                            }
                            $field_classes = isset($kdepnd_arr['class']) ? $kdepnd_arr['class'] : '';
                            $field_placeholder = isset($kdepnd_arr['placeholder']) ? $kdepnd_arr['placeholder'] : '';

                            $field_db_val = get_post_meta($post_id, $field_name, true);

                            if ($field_type == 'upload') {
                                $rand_num = rand(1000000, 9999999);
                                $upload_field_multifiles = isset($kdepnd_arr['multi_files']) ? $kdepnd_arr['multi_files'] : '';
                                $upload_field_numof_files = isset($kdepnd_arr['numof_files']) ? $kdepnd_arr['numof_files'] : '';
                                $upload_field_numof_files = $upload_field_numof_files > 0 ? $upload_field_numof_files : 5;
                                $upload_field_allow_types = isset($kdepnd_arr['allow_types']) ? $kdepnd_arr['allow_types'] : '';
                                $upload_field_allow_types = !empty($upload_field_allow_types) ? $upload_field_allow_types : array();
                                $upload_field_file_size = isset($kdepnd_arr['file_size']) ? $kdepnd_arr['file_size'] : '';
                                $upload_field_file_size = $upload_field_file_size == '' ? '5MB' : $upload_field_file_size;
                                // get db value if saved
                                $post_files_name = 'jobsearch_cfupfiles_' . $field_name;

                                $all_attach_files = get_post_meta($post_id, $post_files_name, true);

                                if ($upload_field_multifiles != 'on') {
                                    $upload_field_numof_files = 1;
                                }

                                $uplod_file_size_num = abs((int)filter_var($upload_field_file_size, FILTER_SANITIZE_NUMBER_INT));
                                $uplod_file_size_num = $uplod_file_size_num > 0 ? $uplod_file_size_num : 5;
                                $uplod_file_size = $uplod_file_size_num * 1024;
                                ?>
                                <<?php echo ($main_con_tag) ?> class="<?php echo ($main_con_class) ?> jobsearch-deparent-field <?php echo ($dynm_deparent_classes) ?>">
                                    <?php echo ($admin_field_start) ?>
                                    <label><?php echo ($field_label) ?></label>
                                    <?php echo ($admin_field_mid) ?>
                                    <div class="jobsearch-fileUpload">
                                        <span><i class="jobsearch-icon jobsearch-upload"></i> <?php echo($upload_field_numof_files > 1 ? esc_html__('Upload Files', 'wp-jobsearch') : esc_html__('Upload File', 'wp-jobsearch')); ?></span>
                                        <input name="<?php echo jobsearch_esc_html($field_name) ?>[]" type="file"
                                               class="upload jobsearch-upload jobsearch-uploadfile-field <?php echo($field_required == 'on' && empty($all_attach_files) ? 'jobsearch-cusfieldatt-req' : '') ?>"
                                               multiple="multiple"
                                               onchange="jobsearch_job_attach_files_url_<?php echo($rand_num) ?>(event)"/>
                                        <?php
                                        if ($upload_field_numof_files > 1) {
                                            ?>
                                            <div class="jobsearch-fileUpload-info">
                                                <p><?php printf(esc_html__('You can upload up to %s files.', 'wp-jobsearch'), $upload_field_numof_files); ?></p>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div id="field-files-holder-<?php echo($rand_num) ?>" class="uplodfield-files-holder">
                                        <?php
                                        //var_dump($all_attach_files);
                                        if (!empty($all_attach_files)) {
                                            ?>
                                            <ul>
                                                <?php
                                                foreach ($all_attach_files as $_attach_file) {
                                                    $_attach_id = jobsearch_get_attachment_id_from_url($_attach_file);
                                                    if ($_attach_id > 0) {
                                                        $_attach_post = get_post($_attach_id);
                                                        $_attach_mime = isset($_attach_post->post_mime_type) ? $_attach_post->post_mime_type : '';
                                                        $_attach_guide = isset($_attach_post->guid) ? $_attach_post->guid : '';
                                                        $attach_name = basename($_attach_guide);
                                                        $file_icon = 'fa fa-file-text-o';
                                                        if ($_attach_mime == 'image/png' || $_attach_mime == 'image/jpeg') {
                                                            $file_icon = 'fa fa-file-image-o';
                                                        } else if ($_attach_mime == 'application/msword' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                                            $file_icon = 'fa fa-file-word-o';
                                                        } else if ($_attach_mime == 'application/vnd.ms-excel' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                                            $file_icon = 'fa fa-file-excel-o';
                                                        } else if ($_attach_mime == 'application/pdf') {
                                                            $file_icon = 'fa fa-file-pdf-o';
                                                        }
                                                        ?>
                                                        <li class="jobsearch-column-3">
                                                            <a href="javascript:void(0);" class="fa fa-remove el-remove"></a>
                                                            <div class="file-container">
                                                                <a href="<?php echo($_attach_file) ?>"
                                                                   oncontextmenu="javascript: return false;"
                                                                   onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                                   download="<?php echo($attach_name) ?>"><i
                                                                            class="<?php echo($file_icon) ?>"></i> <?php echo($attach_name) ?>
                                                                </a>
                                                            </div>
                                                            <input type="hidden" name="<?php echo jobsearch_esc_html($post_files_name) ?>[]"
                                                                   value="<?php echo jobsearch_esc_html($_attach_file) ?>">
                                                        </li>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </ul>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <script type="text/javascript">
                                        jQuery(document).on('click', '.uplodfield-files-holder .el-remove', function () {
                                            var e_target = jQuery(this).parent('li');
                                            e_target.fadeOut('slow', function () {
                                                e_target.remove();
                                            });
                                        });

                                        function jobsearch_job_attach_files_url_<?php echo($rand_num) ?>(event) {

                                            if (window.File && window.FileList && window.FileReader) {

                                                var file_types_str = '<?php echo implode('|', $upload_field_allow_types) ?>';
                                                if (file_types_str != '') {
                                                    var file_types_array = file_types_str.split('|');
                                                } else {
                                                    var file_types_array = [];
                                                }
                                                var file_allow_size = '<?php echo($uplod_file_size) ?>';
                                                var num_files_allow = '<?php echo($upload_field_numof_files) ?>';
                                                file_allow_size = parseInt(file_allow_size);
                                                num_files_allow = parseInt(num_files_allow);
                                                jQuery('#field-files-holder-<?php echo($rand_num) ?>').find('.adding-file').remove();
                                                var files = event.target.files;
                                                for (var i = 0; i < files.length; i++) {

                                                    var _file = files[i];
                                                    var file_type = _file.type;
                                                    var file_size = _file.size;
                                                    var file_name = _file.name;
                                                    file_size = parseFloat(file_size / 1024).toFixed(2);
                                                    if (file_size <= file_allow_size) {
                                                        if (file_types_array.indexOf(file_type) >= 0) {
                                                            var file_icon = 'fa fa-file-text-o';
                                                            if (file_type == 'image/png' || file_type == 'image/jpeg') {
                                                                file_icon = 'fa fa-file-image-o';
                                                            } else if (file_type == 'application/msword' || file_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                                                file_icon = 'fa fa-file-word-o';
                                                            } else if (file_type == 'application/vnd.ms-excel' || file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                                                file_icon = 'fa fa-file-excel-o';
                                                            } else if (file_type == 'application/pdf') {
                                                                file_icon = 'fa fa-file-pdf-o';
                                                            }

                                                            var rand_number = Math.floor((Math.random() * 99999999) + 1);
                                                            var ihtml = '\
                                                            <div class="jobsearch-column-3 adding-file">\
                                                                <div class="file-container">\
                                                                    <a><i class="' + file_icon + '"></i> ' + file_name + '</a>\
                                                                </div>\
                                                            </div>';
                                                            jQuery('#field-files-holder-<?php echo($rand_num) ?>').append(ihtml);
                                                        } else {
                                                            alert('<?php esc_html_e('This File type is not allowed.') ?>');
                                                            return false;
                                                        }
                                                    } else {
                                                        alert('<?php esc_html_e('The file size is too large.') ?>');
                                                        return false;
                                                    }

                                                    if (i == (num_files_allow - 1)) {
                                                        return false;
                                                    }
                                                }
                                            }
                                        }
                                    </script>
                                    <?php echo ($admin_field_end) ?>
                                </<?php echo ($main_con_tag) ?>>
                                <?php
                            } else if ($field_type == 'heading') {
                                if ($field_place == 'admin') {
                                    ?>
                                    <div class="jobsearch-elem-heading jobsearch-deparent-field <?php echo ($dynm_deparent_classes) ?>">
                                        <h2><?php echo ($field_label) ?></h2>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <li class="jobsearch-column-12 jobsearch-deparent-field <?php echo ($dynm_deparent_classes) ?>">
                                        <div class="jobsearch-profile-title jobsearch-dashboard-heading">
                                            <h2><?php echo ($field_label) ?></h2>
                                        </div>
                                    </li>
                                    <?php
                                }
                            } else if ($field_type == 'range') {
                                ?>
                                <<?php echo ($main_con_tag) ?> class="<?php echo ($main_con_class) ?> jobsearch-deparent-field <?php echo ($dynm_deparent_classes) ?>">
                                    <?php echo ($admin_field_start) ?>
                                    <label><?php echo ($field_label) ?></label>
                                    <?php echo ($admin_field_mid) ?>
                                    <?php
                                    $rand_id = rand(1000000, 9999999);
                                    $field_style = isset($kdepnd_arr['field-style']) ? $kdepnd_arr['field-style'] : '';
                                    $field_min = isset($kdepnd_arr['min']) ? $kdepnd_arr['min'] : '0';
                                    $field_laps = isset($kdepnd_arr['laps']) ? $kdepnd_arr['laps'] : '20';
                                    $field_interval = isset($kdepnd_arr['interval']) ? $kdepnd_arr['interval'] : '1000';
                                    if ($field_style == 'slider') {
                                        ?>
                                        <div class="range-field-container">
                                            <input type="text" id="<?php echo jobsearch_esc_html($field_name . $rand_id) ?>"
                                                   name="<?php echo jobsearch_esc_html($field_name) ?>" value="" readonly
                                                   style="border:0; color:#f6931f; font-weight:bold;"/>
                                            <div id="slider-range<?php echo jobsearch_esc_html($field_name . $rand_id) ?>"></div>
                                        </div>
                                        <script type="text/javascript">
                                            <?php
                                            if (!$is_ajax) {
                                            ?>
                                            jQuery(document).ready(function () {
                                                <?php
                                                }
                                                ?>
                                                jQuery("#slider-range<?php echo jobsearch_esc_html($field_name . $rand_id) ?>").slider({
                                                    range: "max",
                                                    min: <?php echo absint($field_min); ?>,
                                                    max: <?php echo absint($field_max); ?>,
                                                    value: <?php echo absint($field_db_val); ?>,
                                                    slide: function (event, ui) {
                                                        jQuery("#<?php echo jobsearch_esc_html($field_name . $rand_id) ?>").val(ui.value);
                                                    }
                                                });
                                                jQuery("#<?php echo jobsearch_esc_html($field_name . $rand_id) ?>").val(jQuery("#slider-range<?php echo jobsearch_esc_html($field_name . $rand_id) ?>").slider("value"));
                                            <?php
                                            if (!$is_ajax) {
                                            ?>
                                            });
                                            <?php
                                            }
                                            ?>
                                        </script>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="jobsearch-profile-select">
                                            <select name="<?php echo jobsearch_esc_html($field_name) ?>"
                                                    class="<?php echo($field_classes) ?> <?php echo ($is_ajax ? 'depndfield-selectize-' . $kdepnd_key : 'selectize-select') ?>" <?php echo ($field_placeholder != '' ? 'placeholder="' . $field_placeholder . '"' : '') ?> <?php echo ($field_required == 'on' ? 'required="required"' : '') ?>>
                                                <?php
                                                echo($field_placeholder != '' ? '<option value="">' . $field_placeholder . '</option>' : '');
                                                $i = 0;
                                                while ($field_laps > $i) {
                                                    echo '<option value="' . $field_max . '">' . $field_max . '</option>';
                                                    $field_max = $field_max + $field_interval;
                                                    $i++;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <?php
                                        if ($is_ajax) {
                                            ?>
                                            <script type="text/javascript">
                                                jQuery('.depndfield-selectize-<?php echo ($kdepnd_key) ?>').selectize({
                                                    plugins: ['remove_button'],
                                                });
                                            </script>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <?php echo ($admin_field_end) ?>
                                </<?php echo ($main_con_tag) ?>>
                                <?php
                            } else if ($field_type == 'textarea') {
                                ?>
                                <<?php echo ($main_con_tag) ?> class="<?php echo ($main_con_class) ?> jobsearch-deparent-field <?php echo ($dynm_deparent_classes) ?>">
                                    <?php echo ($admin_field_start) ?>
                                    <label><?php echo ($field_label) ?></label>
                                    <?php echo ($admin_field_mid) ?>
                                    <?php
                                    $field_media_btns = isset($kdepnd_arr['media_buttons']) ? $kdepnd_arr['media_buttons'] : '';
                                    $field_rich_editor = isset($kdepnd_arr['rich_editor']) ? $kdepnd_arr['rich_editor'] : '';
                                    if ($field_rich_editor == 'no') {
                                        ?>
                                        <textarea name="<?php echo($field_name) ?>"<?php echo ($field_required == 'on' ? ' required' : '') ?><?php echo($field_classes != '' ? ' class="' . $field_classes . '"' : '') ?><?php echo($field_placeholder != '' ? ' placeholder="' . $field_placeholder . '"' : '') ?>><?php echo($field_db_val) ?></textarea>
                                        <?php
                                    } else {
                                        $wped_settings = array(
                                            'media_buttons' => ($field_media_btns == 'on' ? true : false),
                                            'editor_class' => $field_classes,
                                            'quicktags' => array('buttons' => 'strong,em,del,ul,ol,li,close'),
                                            'tinymce' => array(
                                                'toolbar1' => 'bold,bullist,numlist,italic,underline,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                                                'toolbar2' => '',
                                                'toolbar3' => '',
                                            ),
                                        );
                                        wp_editor($field_db_val, $field_name, $wped_settings);
                                    }
                                    ?>
                                    <?php echo ($admin_field_end) ?>
                                </<?php echo ($main_con_tag) ?>>
                                <?php
                            } else if ($field_type == 'date') {
                                $field_date_format = isset($kdepnd_arr['class']) ? $kdepnd_arr['class'] : '';
                                ?>
                                <<?php echo ($main_con_tag) ?> class="<?php echo ($main_con_class) ?> jobsearch-deparent-field <?php echo ($dynm_deparent_classes) ?>">
                                    <script type="text/javascript">
                                        jQuery(document).ready(function () {
                                            jQuery('#<?php echo ($field_name) ?>').datetimepicker({
                                                format: '<?php echo jobsearch_esc_html($field_date_format) ?>'
                                            });
                                        });
                                    </script>
                                    <?php echo ($admin_field_start) ?>
                                    <label><?php echo ($field_label) ?></label>
                                    <?php echo ($admin_field_mid) ?>
                                    <input type="text" id="<?php echo jobsearch_esc_html($field_name) ?>" name="<?php echo jobsearch_esc_html($field_name) ?>"
                                        class="<?php echo jobsearch_esc_html($field_classes) ?>"
                                        placeholder="<?php echo ($field_placeholder) ?>" <?php echo force_balance_tags($field_required_str) ?>
                                        value="<?php echo jobsearch_esc_html($field_db_val) ?>"/>
                                    <?php echo ($admin_field_end) ?>
                                </<?php echo ($main_con_tag) ?>>
                                <?php
                            } else if ($field_type == 'dropdown') {
                                $field_is_multi = isset($kdepnd_arr['multi']) ? $kdepnd_arr['multi'] : '';
                                $field_options = isset($kdepnd_arr['options']) ? $kdepnd_arr['options'] : '';
                                $field_mid = $field_mid . '|' . $optid . '|' . $kdepnd_key;
                                ?>
                                <<?php echo ($main_con_tag) ?> class="<?php echo ($main_con_class) ?> jobsearch-deparent-field <?php echo ($dynm_deparent_classes) ?> jobsearch-depndetfield-con" data-plc="<?php echo ($field_place) ?>" data-thid="<?php echo ($kdepnd_key) ?>" data-mid="<?php echo ($field_mid) ?>" data-ftype="<?php echo ($entity_type) ?>">
                                    <?php echo ($admin_field_start) ?>
                                    <label><?php echo ($field_label) ?></label>
                                    <?php echo ($admin_field_mid) ?>
                                    <div class="jobsearch-profile-select">
                                        <select
                                            <?php echo ($field_is_multi == 'on' ? 'multiple="multiple" ' : '') ?>name="<?php echo jobsearch_esc_html($field_name) ?><?php echo ($field_is_multi == 'on' ? '[]' : '') ?>"
                                            placeholder="<?php echo ($field_placeholder) ?>"
                                            class="<?php echo jobsearch_esc_html($field_classes) ?> <?php echo ($is_ajax ? 'depndfield-selectize-' . $kdepnd_key : 'depndfield-selectize') ?> <?php echo ($field_is_multi == 'on' ? 'jobsearch-depndfield-mulchange' : 'jobsearch-depndfield-srchange') ?>" <?php echo ($field_required_str) ?>>
                                            <?php
                                            if ($field_placeholder != '') {
                                                ?>
                                                <option value="" data-depend="false" data-id="0"><?php echo ($field_placeholder) ?></option>
                                                <?php
                                            }
                                            if (!empty($field_options)) {
                                                foreach ($field_options as $opt_rkey => $opt_obj) {
                                                    $opt_label = isset($opt_obj['label']) ? $opt_obj['label'] : '';
                                                    $opt_depend = isset($opt_obj['depend']) ? $opt_obj['depend'] : '';
                                                    
                                                    $selected_str = '';
                                                    if (!empty($field_db_val)) {
                                                        if ($field_is_multi == 'on') {
                                                            if (in_array($opt_label, $field_db_val)) {
                                                                $selected_str = ' selected="selected"';
                                                            }
                                                        } else {
                                                            if ($opt_label == $field_db_val) {
                                                                $selected_str = ' selected="selected"';
                                                            }
                                                        }
                                                    }

                                                    $has_depend = 'false';
                                                    if (!empty($opt_depend)) {
                                                        $has_depend = 'true';
                                                    }
                                                    ?>
                                                    <option value="<?php echo ($opt_label) ?>"<?php echo ($selected_str) ?> data-data='{"depend": "<?php echo ($has_depend) ?>","optid": "<?php echo ($opt_rkey) ?>"}'><?php echo esc_js($opt_label) ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php echo ($admin_field_end) ?>
                                    <?php
                                    if ($field_is_multi == 'on') {
                                        if (!empty($field_options)) {
                                            ?>
                                            <div class="hiden-multiselc-opts" style="display: none;">
                                                <?php
                                                foreach ($field_options as $opt_rkey => $opt_obj) {
                                                    $opt_label = isset($opt_obj['label']) ? $opt_obj['label'] : '';
                                                    $opt_depend = isset($opt_obj['depend']) ? $opt_obj['depend'] : '';
                                                    $has_depend = 'false';
                                                    if (!empty($opt_depend)) {
                                                        $has_depend = 'true';
                                                    }
                                                    ?>
                                                    <div data-value="<?php echo ($opt_label) ?>" data-depend="<?php echo ($has_depend) ?>" data-optid="<?php echo ($opt_rkey) ?>"></div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                    if ($is_ajax) {
                                        ?>
                                        <script type="text/javascript">
                                            jQuery('.depndfield-selectize-<?php echo ($kdepnd_key) ?>').selectize({
                                                render: {
                                                    option: function (data, escape) {
                                                        return "<div data-depend='" + data.depend + "' data-optid='" + data.optid + "'>" + data.text + "</div>"
                                                    }
                                                },
                                                plugins: ['remove_button'],
                                            });
                                        </script>
                                        <?php
                                    }
                                    ?>
                                </<?php echo ($main_con_tag) ?>>
                                <?php
                                if (!empty($field_db_val)) {
                                    $opti_vals = $field_db_val;
                                    if (!is_array($field_db_val)) {
                                        $opti_vals = array($field_db_val);
                                    }
                                    $cusfiled_arobj = $field_options;
                                    if (!empty($cusfiled_arobj) && is_array($cusfiled_arobj)) {
                                        $optids = array();
                                        foreach ($cusfiled_arobj as $cusfiled_arkey => $cusfiled_aror) {
                                            $opt_depend = isset($cusfiled_aror['depend']) ? $cusfiled_aror['depend'] : '';
                                            $has_depend = 'false';
                                            if (!empty($opt_depend)) {
                                                $has_depend = 'true';
                                            }
                                            if (isset($cusfiled_aror['label']) && in_array($cusfiled_aror['label'], $opti_vals) && $has_depend == 'true') {
                                                $optids[] = $cusfiled_arkey;
                                            }
                                        }
                                        $cusfiled_all_arr = get_option('jobsearch_custom_field_' . $entity_type);
                                        $cusfiled_all_arobj = isset($cusfiled_all_arr[$fields_major_key]) ? $cusfiled_all_arr[$fields_major_key] : '';

                                        echo self::depend_fields_html($optids, $field_mid, $cusfiled_all_arobj, $entity_type, false, $post_id, $field_place);
                                    }
                                }
                            } else if ($field_type == 'checkbox') {
                                $field_is_multi = isset($kdepnd_arr['multi']) ? $kdepnd_arr['multi'] : '';
                                $field_options = isset($kdepnd_arr['options']) ? $kdepnd_arr['options'] : '';
                                $field_mid = $field_mid . '|' . $optid . '|' . $kdepnd_key;
                                
                                $check_requr_class = '';
                                if ($field_place == 'dash') {
                                    $check_requr_class = ' cusfield-checkbox-required';
                                }
                                if ($field_place == 'form') {
                                    $check_requr_class = ' required-cussel-field';
                                }
                                ?>
                                <<?php echo ($main_con_tag) ?> class="<?php echo ($main_con_class) ?><?php echo ($check_requr_class) ?> <?php echo ($field_is_multi == 'on' ? 'dep-multi-checks' : '') ?> jobsearch-deparent-field <?php echo ($dynm_deparent_classes) ?> jobsearch-depndetfield-con" data-plc="<?php echo ($field_place) ?>" data-thid="<?php echo ($kdepnd_key) ?>" data-mid="<?php echo ($field_mid) ?>" data-ftype="<?php echo ($entity_type) ?>">
                                    <?php echo ($admin_field_start) ?>
                                    <label><?php echo ($field_label) ?></label>
                                    <?php echo ($admin_field_mid) ?>
                                    <?php
                                    if (!empty($field_options)) {
                                        foreach ($field_options as $opt_rkey => $opt_obj) {
                                            $opt_label = isset($opt_obj['label']) ? $opt_obj['label'] : '';
                                            $opt_depend = isset($opt_obj['depend']) ? $opt_obj['depend'] : '';
                                            
                                            $has_depend = 'false';
                                            if (!empty($opt_depend)) {
                                                $has_depend = 'true';
                                            }

                                            $selected_str = '';
                                            if (!empty($field_db_val)) {
                                                if ($field_is_multi == 'on') {
                                                    if (in_array($opt_label, $field_db_val)) {
                                                        $selected_str = ' checked="checked"';
                                                    }
                                                } else {
                                                    if ($opt_label == $field_db_val) {
                                                        $selected_str = ' checked="checked"';
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="cusfield-checkbox-radioitm jobsearch-checkbox <?php echo jobsearch_esc_html($field_classes) ?>">
                                                <input id="opt-<?php echo ($field_name . '-' . $opt_rkey) ?>"
                                                       type="<?php echo ($field_is_multi == 'on' ? 'checkbox' : 'radio') ?>" name="<?php echo ($field_name) ?><?php echo ($field_is_multi == 'on' ? '[]' : '') ?>"
                                                       value="<?php echo ($opt_label) ?>" <?php echo ($selected_str) ?> class="jobsearch-depndfield-rchchange" data-optid="<?php echo ($opt_rkey) ?>" data-depend="<?php echo ($has_depend) ?>">
                                                <label for="opt-<?php echo ($field_name . '-' . $opt_rkey) ?>">
                                                    <span></span> <?php echo ($opt_label) ?>
                                                </label>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <?php echo ($admin_field_end) ?>
                                </<?php echo ($main_con_tag) ?>>
                                <?php
                                if (!empty($field_db_val)) {
                                    $opti_vals = $field_db_val;
                                    if (!is_array($field_db_val)) {
                                        $opti_vals = array($field_db_val);
                                    }
                                    $cusfiled_arobj = $field_options;
                                    if (!empty($cusfiled_arobj) && is_array($cusfiled_arobj)) {
                                        $optids = array();
                                        foreach ($cusfiled_arobj as $cusfiled_arkey => $cusfiled_aror) {
                                            $opt_depend = isset($cusfiled_aror['depend']) ? $cusfiled_aror['depend'] : '';
                                            $has_depend = 'false';
                                            if (!empty($opt_depend)) {
                                                $has_depend = 'true';
                                            }
                                            if (isset($cusfiled_aror['label']) && in_array($cusfiled_aror['label'], $opti_vals) && $has_depend == 'true') {
                                                $optids[] = $cusfiled_arkey;
                                            }
                                        }
                                        $cusfiled_all_arr = get_option('jobsearch_custom_field_' . $entity_type);
                                        $cusfiled_all_arobj = isset($cusfiled_all_arr[$fields_major_key]) ? $cusfiled_all_arr[$fields_major_key] : '';

                                        echo self::depend_fields_html($optids, $field_mid, $cusfiled_all_arobj, $entity_type, false, $post_id, $field_place);
                                    }
                                }
                            } else {
                                ?>
                                <<?php echo ($main_con_tag) ?> class="<?php echo ($main_con_class) ?> jobsearch-deparent-field <?php echo ($dynm_deparent_classes) ?>">
                                    <?php echo ($admin_field_start) ?>
                                    <label><?php echo ($field_label) ?></label>
                                    <?php echo ($admin_field_mid) ?>
                                        <input type="text" name="<?php echo jobsearch_esc_html($field_name) ?>"
                                               class="<?php echo jobsearch_esc_html($field_classes) ?>"
                                               placeholder="<?php echo ($field_placeholder) ?>" <?php echo force_balance_tags($field_required_str) ?>
                                               value="<?php echo jobsearch_esc_html($field_db_val) ?>"/>
                                    <?php echo ($admin_field_end) ?>
                                </<?php echo ($main_con_tag) ?>>
                                <?php
                            }
                        }
                        $html .= ob_get_clean();
                    }
                }
            }
            return $html;
        }
    }
    
    public function depend_fields_infront_call() {
        $optid = $_POST['opt_id'];
        $field_mid = $_POST['field_mid'];
        $field_type = $_POST['field_type'];
        $field_place = $_POST['field_plc'];
        
        $optids = explode(',', $optid);
        
        $cusfiled_plod = explode('|', $field_mid);
        $cusfiled_id = isset($cusfiled_plod[0]) ? $cusfiled_plod[0] : '';
        $cusfiled_arr = get_option('jobsearch_custom_field_' . $field_type);
        $cusfiled_arobj = isset($cusfiled_arr[$cusfiled_id]) ? $cusfiled_arr[$cusfiled_id] : '';
        if (!empty($cusfiled_arobj) && is_array($cusfiled_arobj)) {
            $html = self::depend_fields_html($optids, $field_mid, $cusfiled_arobj, $field_type, true, 0, $field_place);
            $error = '0';
        } else {
            $html = '';
            $error = '1';
        }
        
        $json = array('html' => $html, 'error' => $error);
        wp_send_json($json);
    }
    
    private static function dynm_deparent_classes($field_mid) {
        
        $classes_arr = explode('|', $field_mid);
        if (!empty($classes_arr) && sizeof($classes_arr) >= 3) {
            $to_count = sizeof($classes_arr);
            $classes = array();
            $ids_counter = 1;
            foreach ($classes_arr as $class_id) {
                if (fmod($ids_counter, 2) != 0) {
                    $classes[] = 'deparent-' . $class_id;
                }
                if ($ids_counter == $to_count) {
                    break;
                }
                $ids_counter++;
            }
        } else {
            $field_mid = 'deparent-' . $field_mid;
            $classes = array($field_mid);
        }
        
        return implode(' ', $classes);
    }

    public function dependent_fields_showval_html($post_id, $cus_field, $cus_fieldvar, $field_name, $cus_field_value_arr, $before_html, $after_html, $prefix) {
        global $sitepress;
        
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        
        $field_val_arr = is_array($cus_field_value_arr) ? $cus_field_value_arr : array($cus_field_value_arr);
        
        $cus_field_icon = isset($cus_field['icon']) ? $cus_field['icon'] : '';
        $cus_field_label = isset($cus_field['label']) ? $cus_field['label'] : '';
        
        echo $before_html;
        $no_icon_class = ' has-no-icon';
        if (isset($cus_field_icon) && $cus_field_icon != '') {
            $no_icon_class = '';
            ?>
            <i class="<?php echo jobsearch_esc_html($cus_field_icon) ?>"></i>
            <?php
        }
        echo '<div class="' . $prefix . '-services-text' . $no_icon_class . '">';
        
        if (isset($cus_field_label) && $cus_field_label != '') {
            echo jobsearch_esc_html($cus_field_label) . ' ';
        }
        if (is_array($cus_field_value_arr)) {

            foreach ($cus_field_value_arr as $key => $single_value) {
                $single_value = jobsearch_esc_html($single_value);
                if ($single_value != '') {
                    echo '<small>';
                    echo jobsearch_esc_html($single_value);
                    echo '</small>';
                }
            }
        } else {

            echo '<small>';
            echo jobsearch_esc_html($cus_field_value_arr);
            echo '</small>';
        }
        echo '</div>';
        
        echo $after_html;
        
        if (isset($cus_field['options']) && !empty($cus_field['options'])) {
            foreach ($cus_field['options'] as $optid => $optobj) {
                if (isset($optobj['depend']) && !empty($optobj['depend'])) {
                    
                    $this_flabel = isset($optobj['label']) ? $optobj['label'] : '';
                    if (in_array($this_flabel, $field_val_arr)) {
                        foreach ($optobj['depend'] as $dep_field_id => $dep_field_obj) {
                            $this->rendering_depfields_vals_html($post_id, $dep_field_obj, $dep_field_id, $field_name, $before_html, $after_html, $prefix);
                        }
                    }
                }
                //
                
            }
        }
    }
    
    public function rendering_depfields_vals_html($post_id, $cus_field, $cus_fieldvar, $field_main_name, $before_html, $after_html, $prefix) {
        global $sitepress;
        
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        
        $field_name = $field_main_name . '_' . $cus_fieldvar;
        
        $cus_field_value_arr = get_post_meta($post_id, $field_name, true);
        
        $field_val_arr = is_array($cus_field_value_arr) ? $cus_field_value_arr : array($cus_field_value_arr);
        
        $type = isset($cus_field['type']) ? $cus_field['type'] : '';
        
        $cus_field_icon = isset($cus_field['icon']) ? $cus_field['icon'] : '';
        $cus_field_label = isset($cus_field['label']) ? $cus_field['label'] : '';
        
        $orig_before_html = $before_html;
        
        if (($type == 'textarea' || $type == 'video' || $type == 'heading')) {
            if (strpos($before_html, $prefix . '-column-4') !== false) {
                $before_html = str_replace(array($prefix . '-column-4'), array($prefix . '-column-12'), $before_html);
            } else if (strpos($before_html, 'jobsearch-column-4') !== false) {
                $before_html = str_replace(array('jobsearch-column-4'), array('jobsearch-column-12'), $before_html);
            } else if (strpos($before_html, 'careerfy-column-4') !== false) {
                $before_html = str_replace(array('careerfy-column-4'), array('careerfy-column-12'), $before_html);
            } else if (strpos($before_html, $prefix . '-column-6') !== false) {
                $before_html = str_replace(array($prefix . '-column-6'), array($prefix . '-column-12'), $before_html);
            }
        } else {
            $before_html = $orig_before_html;
        }
        
        if ($type == 'heading') {
            echo $before_html;
            echo '<div class="' . $prefix . '-content-title"><h2>' . $cus_field_label . '</h2></div>';
            echo $after_html;
        }
        
        echo $before_html;
        $no_icon_class = ' has-no-icon';
        if (isset($cus_field_icon) && $cus_field_icon != '') {
            $no_icon_class = '';
            ?>
            <i class="<?php echo jobsearch_esc_html($cus_field_icon) ?>"></i>
            <?php
        }
        echo '<div class="' . $prefix . '-services-text' . $no_icon_class . '">';
        
        if (isset($cus_field_label) && $cus_field_label != '') {
            echo jobsearch_esc_html($cus_field_label) . ' ';
        }
        if (is_array($cus_field_value_arr)) {

            foreach ($cus_field_value_arr as $key => $single_value) {
                $single_value = jobsearch_esc_html($single_value);
                if ($single_value != '') {
                    echo '<small>';
                    echo jobsearch_esc_html($single_value);
                    echo '</small>';
                }
            }
        } else {

            echo '<small>';
            echo jobsearch_esc_html($cus_field_value_arr);
            echo '</small>';
        }
        echo '</div>';
        
        echo $after_html;
        
        if (isset($cus_field['options']) && !empty($cus_field['options'])) {
            foreach ($cus_field['options'] as $optid => $optobj) {
                if (isset($optobj['depend']) && !empty($optobj['depend'])) {
                    
                    $this_flabel = isset($optobj['label']) ? $optobj['label'] : '';
                    if (in_array($this_flabel, $field_val_arr)) {
                        foreach ($optobj['depend'] as $dep_field_id => $dep_field_obj) {
                            $this->rendering_depfields_vals_html($post_id, $dep_field_obj, $dep_field_id, $field_main_name, $orig_before_html, $after_html, $prefix);
                        }
                    }
                }
                //
                
            }
        }
    }
}

global $jobsearch_cusfdepfields_rendring;
$jobsearch_cusfdepfields_rendring = new Jobsearch_CustomField_DepFields_Render();
