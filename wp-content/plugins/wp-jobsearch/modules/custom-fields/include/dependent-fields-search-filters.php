<?php

if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_CustomField_DepFields_Filters {

    // hook things up
    public function __construct() {
        add_filter('jobsearch_cusfields_left_filters_before_dropdwn', array($this, 'cusfields_left_filters'), 10, 2);
        
        add_filter('jobsearch_cusfields_top_filters_before_dropdwn', array($this, 'cusfields_top_filters'), 10, 2);
        
        add_filter('jobsearch_listing_filters_cusf_query_arr', array($this, 'filters_cusf_query_arr'), 10, 2);
        
        add_filter('jobsearch_listing_url_query_innervar_byfiltr', array($this, 'url_query_innervar_byfiltr'), 10, 2);
    }
    
    public function cusfields_left_filters($html, $args) {
        global $jobsearch_plugin_options, $sitepress;
        
        extract($args);
        
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $submit_js_function_str = '';
        if ($submit_js_function != '') {
            $submit_js_function_str = $submit_js_function . '(' . $global_rand_id . ')';
        }
            
        $query_str_var_name = isset($cus_field['name']) ? $cus_field['name'] : '';
        
        ob_start();
        if ($cus_field['type'] == 'dependent_fields') {
            
            $dep_fields_html = '';
            //self::left_filter_before($cus_field);
            $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
            $request_val_arr = explode(",", $request_val);
            ?>
            <input type="hidden" value="<?php echo jobsearch_esc_html($request_val); ?>"
               name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
               id="hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
               class="<?php echo jobsearch_esc_html($query_str_var_name); ?>"/>
            <?php
            if ($query_str_var_name != '') {
            ?>
            <script type="text/javascript">
                jQuery(function () {
                    'use strict'
                    var $checkboxes = jQuery("input[type=checkbox].<?php echo jobsearch_esc_html($query_str_var_name); ?>");
                    $checkboxes.on('change', function () {
                        var ids = $checkboxes.filter(':checked').map(function () {
                            return this.value;
                        }).get().join(',');
                        jQuery('#hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>').val(ids);
                        <?php echo($submit_js_function_str); ?>
                    });
                });
            </script>
            <?php
            }
            ?>
            <ul class="jobsearch-checkbox">
                <?php
                $number_option_flag = 1;
                $cut_field_flag = 0;
                $field_options = isset($cus_field['options']) ? $cus_field['options'] : '';
                if (!empty($field_options)) {
                    foreach ($field_options as $opt_rkey => $opt_obj) {

                        $opt_label = isset($opt_obj['label']) ? $opt_obj['label'] : '';
                        $opt_depend = isset($opt_obj['depend']) ? $opt_obj['depend'] : '';

                        $field_options_value = $opt_label;

                        if ($field_options_value == '') {
                            $cut_field_flag++;
                            continue;
                        }
                        $has_depend = 'false';
                        // get count of each item
                        // extra condidation
                        if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {

                            $dropdown_count_arr = array(
                                array(
                                    'key' => $query_str_var_name,
                                    'value' => ($field_options_value),
                                    'compare' => 'Like',
                                )
                            );
                        } else {
                            $dropdown_count_arr = array(
                                array(
                                    'key' => $query_str_var_name,
                                    'value' => $field_options_value,
                                    'compare' => '=',
                                )
                            );
                        }
                        // main query array $args_count

                        if ($field_options_value != '') {
                            if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {
                                $checked = '';
                                if (!empty($request_val_arr) && in_array($field_options_value, $request_val_arr)) {
                                    $checked = ' checked="checked"';
                                    if (!empty($opt_depend)) {
                                        $has_depend = 'true';
                                    }
                                }
                                ?>
                                <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">

                                    <input type="checkbox"
                                           id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                           value="<?php echo jobsearch_esc_html($field_options_value); ?>"
                                           class="<?php echo jobsearch_esc_html($query_str_var_name); ?>" <?php echo ($checked); ?> />
                                    <label for="<?php echo force_balance_tags($query_str_var_name . '_' . $number_option_flag) ?>">
                                        <span></span><?php echo ($opt_label); ?>
                                    </label>
                                    <?php if ($left_filter_count_switch == 'yes') {
                                        $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                        ?>
                                        <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                    <?php } ?>
                                </li>
                                <?php
                                //
                            } else {
                                $custom_dropdown_selected = '';
                                if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == $field_options_value) {
                                    $custom_dropdown_selected = ' checked="checked"';
                                    if (!empty($opt_depend)) {
                                        $has_depend = 'true';
                                    }
                                }
                                ?>
                                <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                    <input type="radio"
                                           name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                           value="<?php echo jobsearch_esc_html($field_options_value); ?>" <?php echo ($custom_dropdown_selected); ?>
                                           onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                    <label for="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>">
                                        <span></span><?php echo ($opt_label); ?>
                                    </label>
                                    <?php if ($left_filter_count_switch == 'yes') {
                                        $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                        ?>
                                        <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                        <?php
                                    }
                                    ?>
                                </li>
                                <?php
                            }
                        }
                        $number_option_flag++;
                        $cut_field_flag++;
                        
                        if ($has_depend == 'true') {
                            ob_start();
                            $this->dep_fields_filter($query_str_var_name, $opt_depend, $args);
                            $dep_fields_html .= ob_get_clean();
                        }
                    }
                }
                ?>
            </ul>
            <?php
            if ($number_option_flag > 6) {
                echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
            }
            
            //self::left_filter_after($cus_field);
            
            if ($dep_fields_html != '') {
                echo ($dep_fields_html);
            }
        }
        $html .= ob_get_clean();
        return $html;
    }
    
    public function dep_fields_filter($field_name, $opt_depend, $args) {
        global $jobsearch_plugin_options, $sitepress;
        
        extract($args);
        
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $submit_js_function_str = '';
        if ($submit_js_function != '') {
            $submit_js_function_str = $submit_js_function . '(' . $global_rand_id . ')';
        }
        
        foreach ($opt_depend as $dep_fkey => $dep_fobj) {
            $cus_field = $dep_fobj;
            
            if (isset($cus_field['enable-search']) && $cus_field['enable-search'] == 'on') {
                $cus_field['name'] = $field_name . '_' . $dep_fkey;
                self::left_filter_before($cus_field);

                $dep_fields_html = '';
            
                $query_str_var_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                if ($cus_field['type'] == 'dropdown' || $cus_field['type'] == 'checkbox') {

                    $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                    $request_val_arr = explode(",", $request_val);
                    ?>
                    <input type="hidden" value="<?php echo jobsearch_esc_html($request_val); ?>"
                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                       id="hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                       class="<?php echo jobsearch_esc_html($query_str_var_name); ?>"/>
                    <?php
                    if ($query_str_var_name != '') {
                        ?>
                        <script type="text/javascript">
                            jQuery(function () {
                                'use strict'
                                var $checkboxes = jQuery("input[type=checkbox].<?php echo jobsearch_esc_html($query_str_var_name); ?>");
                                $checkboxes.on('change', function () {
                                    var ids = $checkboxes.filter(':checked').map(function () {
                                        return this.value;
                                    }).get().join(',');
                                    jQuery('#hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>').val(ids);
                                    <?php echo($submit_js_function_str); ?>
                                });
                            });
                        </script>
                        <?php
                    }
                    ?>
                    <ul class="jobsearch-checkbox">
                        <?php
                        $number_option_flag = 1;
                        $cut_field_flag = 0;
                        $field_options = isset($cus_field['options']) ? $cus_field['options'] : '';
                        if (!empty($field_options)) {
                            foreach ($field_options as $opt_rkey => $opt_obj) {

                                $opt_label = isset($opt_obj['label']) ? $opt_obj['label'] : '';
                                $opt_depend = isset($opt_obj['depend']) ? $opt_obj['depend'] : '';

                                $field_options_value = $opt_label;

                                if ($field_options_value == '') {
                                    $cut_field_flag++;
                                    continue;
                                }
                                $has_depend = 'false';
                                // get count of each item
                                // extra condidation
                                if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {

                                    $dropdown_count_arr = array(
                                        array(
                                            'key' => $query_str_var_name,
                                            'value' => ($field_options_value),
                                            'compare' => 'Like',
                                        )
                                    );
                                } else {
                                    $dropdown_count_arr = array(
                                        array(
                                            'key' => $query_str_var_name,
                                            'value' => $field_options_value,
                                            'compare' => '=',
                                        )
                                    );
                                }
                                // main query array $args_count

                                if ($field_options_value != '') {
                                    if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {
                                        $checked = '';
                                        if (!empty($request_val_arr) && in_array($field_options_value, $request_val_arr)) {
                                            $checked = ' checked="checked"';
                                            if (!empty($opt_depend)) {
                                                $has_depend = 'true';
                                            }
                                        }
                                        ?>
                                        <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">

                                            <input type="checkbox"
                                                   id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                   value="<?php echo jobsearch_esc_html($field_options_value); ?>"
                                                   class="<?php echo jobsearch_esc_html($query_str_var_name); ?>" <?php echo ($checked); ?> />
                                            <label for="<?php echo force_balance_tags($query_str_var_name . '_' . $number_option_flag) ?>">
                                                <span></span><?php echo ($opt_label); ?>
                                            </label>
                                            <?php if ($left_filter_count_switch == 'yes') {
                                                $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                ?>
                                                <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                            <?php } ?>
                                        </li>
                                        <?php
                                        //
                                    } else {
                                        $custom_dropdown_selected = '';
                                        if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == $field_options_value) {
                                            $custom_dropdown_selected = ' checked="checked"';
                                            if (!empty($opt_depend)) {
                                                $has_depend = 'true';
                                            }
                                        }
                                        ?>
                                        <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                            <input type="radio"
                                                   name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                   id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                   value="<?php echo jobsearch_esc_html($field_options_value); ?>" <?php echo ($custom_dropdown_selected); ?>
                                                   onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                            <label for="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>">
                                                <span></span><?php echo ($opt_label); ?>
                                            </label>
                                            <?php if ($left_filter_count_switch == 'yes') {
                                                $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                ?>
                                                <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                <?php
                                            }
                                            ?>
                                        </li>
                                        <?php
                                    }
                                }
                                $number_option_flag++;
                                $cut_field_flag++;

                                if ($has_depend == 'true') {
                                    ob_start();
                                    $this->dep_fields_filter($field_name, $opt_depend, $args);
                                    $dep_fields_html .= ob_get_clean();
                                }
                            }
                        }
                        ?>
                    </ul>
                    <?php
                    if ($number_option_flag > 6) {
                        echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                    }
                } else if ($cus_field['type'] == 'range') {
                    $range_min = $cus_field['min'];
                    $range_laps = $cus_field['laps'];
                    $range_laps = $range_laps > 100 ? 100 : $range_laps;
                    $range_interval = $cus_field['interval'];
                    $range_field_type = isset($cus_field['field-style']) ? $cus_field['field-style'] : 'simple'; //input, slider, input_slider

                    if (strpos($range_field_type, '-') !== FALSE) {
                        $range_field_type_arr = explode("_", $range_field_type);
                    } else {
                        $range_field_type_arr[0] = $range_field_type;
                    }
                    $range_flag = 0;
                    while (count($range_field_type_arr) > $range_flag) {
                        if ($range_field_type_arr[$range_flag] == 'simple') { // if input style
                            $filter_more_counter = 1;
                            ?>
                            <ul class="jobsearch-checkbox">
                                <?php
                                $loop_flag = 1;
                                while ($loop_flag <= $range_laps) {
                                    ?>
                                <li class="<?php echo($filter_more_counter > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                    <?php
                                    // main query array $args_count
                                    $range_first = $range_min + 1;
                                    $range_seond = $range_min + $range_interval;
                                    $range_count_arr = array(
                                        array(
                                            'key' => $query_str_var_name,
                                            'value' => $range_first,
                                            'compare' => '>=',
                                            'type' => 'numeric'
                                        ),
                                        array(
                                            'key' => $query_str_var_name,
                                            'value' => $range_seond,
                                            'compare' => '<=',
                                            'type' => 'numeric'
                                        )
                                    );
                                    $range_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $range_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                    $custom_slider_selected = '';
                                    if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == (($range_min + 1) . "-" . ($range_min + $range_interval))) {
                                        $custom_slider_selected = ' checked="checked"';
                                    }
                                    ?>
                                    <input type="radio" name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           id="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"
                                           value="<?php echo jobsearch_esc_html((($range_min + 1) . "-" . ($range_min + $range_interval))); ?>" <?php echo ($custom_slider_selected); ?>
                                           onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                    <label for="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"><span></span><?php echo force_balance_tags((($range_min + 1) . " - " . ($range_min + $range_interval))); ?>
                                    </label>
                                    <?php if ($left_filter_count_switch == 'yes') { ?>
                                        <span class="filter-post-count"><?php echo absint($range_totnum); ?></span>
                                    <?php } ?>
                                    </li><?php
                                    $range_min = $range_min + $range_interval;
                                    $loop_flag++;
                                    $filter_more_counter++;
                                }
                                ?>
                            </ul>
                            <?php
                            if ($filter_more_counter > 6) {
                                echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                            }
                        } else if ($range_field_type_arr[$range_flag] == 'slider') { // if slider style
                            wp_enqueue_style('jquery-ui');
                            wp_enqueue_script('jquery-ui');
                            $rand_id = rand(123, 1231231);
                            $range_field_max = $range_min;
                            $i = 0;
                            while ($range_laps > $i) {
                                $range_field_max = $range_field_max + $range_interval;
                                $i++;
                            }
                            $range_complete_str_first = "";
                            $range_complete_str_second = "";
                            $range_complete_str = '';
                            $range_complete_str_first = $range_min;
                            $range_complete_str_second = $range_field_max;
                            if (isset($_REQUEST[$query_str_var_name])) {
                                $range_complete_str = $_REQUEST[$query_str_var_name];
                                $range_complete_str_arr = explode("-", $range_complete_str);
                                $range_complete_str_first = isset($range_complete_str_arr[0]) ? $range_complete_str_arr[0] : '';
                                $range_complete_str_second = isset($range_complete_str_arr[1]) ? $range_complete_str_arr[1] : '';
                            }
                            ?>
                            <ul class="jobsearch-checkbox">
                                <li>
                                    <input type="text" name="<?php echo jobsearch_esc_html($query_str_var_name) ?>"
                                           id="<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"
                                           value="<?php echo jobsearch_esc_html($range_complete_str); ?>" readonly
                                           style="border:0; color:#f6931f; font-weight:bold;"/>
                                    <div id="slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"></div>
                                    <script type="text/javascript">
                                        jQuery(document).ready(function () {


                                            jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider({
                                                range: true,
                                                min: <?php echo absint($range_min); ?>,
                                                max: <?php echo absint($range_field_max); ?>,
                                                values: [<?php echo absint($range_complete_str_first); ?>, <?php echo absint($range_complete_str_second); ?>],
                                                slide: function (event, ui) {
                                                    jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(ui.values[0] + "-" + ui.values[1]);
                                                },
                                                stop: function (event, ui) {
                                                    <?php echo force_balance_tags($submit_js_function_str); ?>;
                                                }
                                            });
                                            jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 0) +
                                                "-" + jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 1));
                                        });
                                    </script>
                                </li>
                            </ul>
                            <?php
                        }
                        $range_flag++;
                    }
                } else {
                    $text_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                    ?>
                    <ul class="jobsearch-checkbox">
                        <li>
                            <input type="text" name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                   id="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                   value="<?php echo jobsearch_esc_html($text_field_req_val); ?>"
                                   onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                        </li>
                    </ul>
                    <?php
                }

                self::left_filter_after($cus_field);
                
                if ($dep_fields_html != '') {
                    echo ($dep_fields_html);
                }
            }
        }
    }


    public static function left_filter_before($cus_field) {
        $filter_collapse_cval = 'open';
        $collapse_condition = 'no';

        $filter_collapse_cname = isset($cus_field['name']) ? sanitize_title($cus_field['name']) . '_csec_collapse' : '';
        if ($filter_collapse_cname != '' && isset($_COOKIE[$filter_collapse_cname]) && $_COOKIE[$filter_collapse_cname] != '') {
            $filter_collapse_cval = $_COOKIE[$filter_collapse_cname];
            if ($_COOKIE[$filter_collapse_cname] == 'open') {
                $collapse_condition = 'no';
            } else {
                $collapse_condition = 'yes';
            }
        }
        
        $field_label_arr = isset($cus_field['label']) ? $cus_field['label'] : '';
        
        $html = '
        <div class="jobsearch-filter-responsive-wrap">
        <div class="jobsearch-search-filter-wrap ' . ($collapse_condition == 'yes' ? 'jobsearch-search-filter-toggle jobsearch-remove-padding' : 'jobsearch-search-filter-toggle') . '">
        <div class="jobsearch-fltbox-title">
            <a href="javascript:void(0);" data-cname="' . ($filter_collapse_cname) . '"
               data-cval="' . ($filter_collapse_cval) . '" class="jobsearch-click-btn">
               ' . jobsearch_esc_html(stripslashes($field_label_arr)) . '
            </a>
        </div>
        <div class="jobsearch-checkbox-toggle" ' . ($collapse_condition == 'yes' ? 'style="display: none;"' : '') . '>';
        
        echo ($html);
    }
    
    public static function left_filter_after($cus_field) {
        $html = '</div></div></div>';
        
        echo ($html);
    }
    
    public function cusfields_top_filters($html, $args) {
        global $jobsearch_plugin_options, $sitepress;
        
        extract($args);
        
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        ob_start();
        if ($cus_field['type'] == 'dependent_fields') {
            
            $dep_fields_html = '';
            
            $query_str_var_name = isset($cus_field['name']) ? $cus_field['name'] : '';
            
            //
            $cus_field_label = isset($cus_field['label']) ? $cus_field['label'] : '';
            $dropdown_main_class = 'jobsearch-select-style';
            $field_label_html = '';
            if ($allow_type == 'enable_search') {
                $dropdown_main_class = 'jobsearch-profile-select';
                $field_label_html = '<label>' . $cus_field_label . '</label>';
            }
            $field_label_placeholder = isset($cus_field['placeholder']) ? $cus_field['placeholder'] : '';

            $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
            $request_val_arr = explode(",", $request_val);

            $number_option_flag = 1;
            $cut_field_flag = 0;
            if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {
                $select_param = 'multiple="multiple"';
                $select_class = 'jobsearch-select-multi';
            } else {
                $select_param = '';
                $select_class = '';
            }
            ?>
            <li>
                <?php echo ($field_label_html) ?>
                <div class="<?php echo ($dropdown_main_class) ?> <?php echo $select_class ?>">
                    <select name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                            class="selectize-select"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_dropdwn_exatts', '', $global_rand_id, array(), $custom_field_entity) ?> <?php echo($select_param) ?>
                            placeholder="<?php echo($field_label_placeholder); ?>">
                        <?php

                        $cutsf_field_flag = 1;
                        $field_options = isset($cus_field['options']) ? $cus_field['options'] : '';
                        if (!empty($field_options)) {
                            foreach ($field_options as $opt_rkey => $opt_obj) {

                                $opt_label = isset($opt_obj['label']) ? $opt_obj['label'] : '';
                                $opt_depend = isset($opt_obj['depend']) ? $opt_obj['depend'] : '';

                                $field_options_value = $opt_label;
                                if ($field_options_value == '') {
                                    $cut_field_flag++;
                                    continue;
                                }
                                $has_depend = 'false';

                                if ($field_options_value != '') {
                                    if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {
                                        $checked = '';

                                        if (!empty($request_val_arr) && in_array($field_options_value, $request_val_arr)) {
                                            $checked = 'selected="selected"';
                                            if (!empty($opt_depend)) {
                                                $has_depend = 'true';
                                            }
                                        }
                                        ?>
                                        <option value="<?php echo jobsearch_esc_html($field_options_value); ?>" <?php echo($checked) ?>><?php echo ($opt_label); ?></option>
                                        <?php
                                    } else {
                                        if ($cutsf_field_flag == 1) { ?>
                                            <option value=""><?php echo($field_label_placeholder); ?></option>
                                            <?php
                                        }
                                        $custom_dropdown_selected = '';
                                        if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == $field_options_value) {
                                            $custom_dropdown_selected = ' selected="selected"';
                                            if (!empty($opt_depend)) {
                                                $has_depend = 'true';
                                            }
                                        }
                                        ?>
                                        <option value="<?php echo jobsearch_esc_html($field_options_value); ?>" <?php echo($custom_dropdown_selected) ?>><?php echo ($opt_label); ?></option>
                                        <?php
                                        $cutsf_field_flag++;
                                    }
                                }
                                $number_option_flag++;
                                $cut_field_flag++;

                                if ($has_depend == 'true') {
                                    ob_start();
                                    $this->dep_fields_top_filter($query_str_var_name, $opt_depend, $args);
                                    $dep_fields_html .= ob_get_clean();
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </li>
            <?php
            if ($dep_fields_html != '') {
                echo ($dep_fields_html);
            }
        }

        $html .= ob_get_clean();
        return $html;
    }
    
    public function dep_fields_top_filter($field_name, $opt_depend, $args) {
        global $jobsearch_plugin_options, $sitepress;
        
        extract($args);
        
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        
        foreach ($opt_depend as $dep_fkey => $dep_fobj) {
            $cus_field = $dep_fobj;
            
            if (isset($cus_field['enable-search']) && $cus_field['enable-search'] == 'on') {
                
                $dep_fields_html = '';
                
                $cus_field['name'] = $field_name . '_' . $dep_fkey;
                
                $query_str_var_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                
                //
                $cus_field_label = isset($cus_field['label']) ? $cus_field['label'] : '';
                $dropdown_main_class = 'jobsearch-select-style';
                $field_label_html = '';
                if ($allow_type == 'enable_search') {
                    $dropdown_main_class = 'jobsearch-profile-select';
                    $field_label_html = '<label>' . $cus_field_label . '</label>';
                }
                $field_label_placeholder = isset($cus_field['placeholder']) ? $cus_field['placeholder'] : '';

                if ($cus_field['type'] == 'dropdown' || $cus_field['type'] == 'checkbox') {
                    $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                    $request_val_arr = explode(",", $request_val);

                    $number_option_flag = 1;
                    $cut_field_flag = 0;
                    if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {
                        $select_param = 'multiple="multiple"';
                        $select_class = 'jobsearch-select-multi';
                    } else {
                        $select_param = '';
                        $select_class = '';
                    }
                    ?>
                    <li>
                        <?php echo ($field_label_html) ?>
                        <div class="<?php echo ($dropdown_main_class) ?> <?php echo $select_class ?>">
                            <select name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                    class="selectize-select"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_dropdwn_exatts', '', $global_rand_id, array(), $custom_field_entity) ?> <?php echo($select_param) ?>
                                    placeholder="<?php echo($field_label_placeholder); ?>">
                                <?php

                                $cutsf_field_flag = 1;
                                $field_options = isset($cus_field['options']) ? $cus_field['options'] : '';
                                if (!empty($field_options)) {
                                    foreach ($field_options as $opt_rkey => $opt_obj) {

                                        $opt_label = isset($opt_obj['label']) ? $opt_obj['label'] : '';
                                        $opt_depend = isset($opt_obj['depend']) ? $opt_obj['depend'] : '';

                                        $field_options_value = $opt_label;
                                        if ($field_options_value == '') {
                                            $cut_field_flag++;
                                            continue;
                                        }
                                        $has_depend = 'false';

                                        if ($field_options_value != '') {
                                            if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {
                                                $checked = '';

                                                if (!empty($request_val_arr) && in_array($field_options_value, $request_val_arr)) {
                                                    $checked = 'selected="selected"';
                                                    if (!empty($opt_depend)) {
                                                        $has_depend = 'true';
                                                    }
                                                }
                                                ?>
                                                <option value="<?php echo jobsearch_esc_html($field_options_value); ?>" <?php echo($checked) ?>><?php echo ($opt_label); ?></option>
                                                <?php
                                            } else {
                                                if ($cutsf_field_flag == 1) { ?>
                                                    <option value=""><?php echo($field_label_placeholder); ?></option>
                                                    <?php
                                                }
                                                $custom_dropdown_selected = '';
                                                if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == $field_options_value) {
                                                    $custom_dropdown_selected = ' selected="selected"';
                                                    if (!empty($opt_depend)) {
                                                        $has_depend = 'true';
                                                    }
                                                }
                                                ?>
                                                <option value="<?php echo jobsearch_esc_html($field_options_value); ?>" <?php echo($custom_dropdown_selected) ?>><?php echo ($opt_label); ?></option>
                                                <?php
                                                $cutsf_field_flag++;
                                            }
                                        }
                                        $number_option_flag++;
                                        $cut_field_flag++;

                                        if ($has_depend == 'true') {
                                            ob_start();
                                            $this->dep_fields_top_filter($field_name, $opt_depend, $args);
                                            $dep_fields_html .= ob_get_clean();
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </li>
                    <?php
                } else if ($cus_field['type'] == 'range') {
                    $range_min = $cus_field['min'];
                    $range_laps = $cus_field['laps'];
                    $range_interval = $cus_field['interval'];
                    $range_field_type = isset($cus_field['field-style']) ? $cus_field['field-style'] : 'simple'; //input, slider, input_slider

                    if (strpos($range_field_type, '-') !== FALSE) {
                        $range_field_type_arr = explode("_", $range_field_type);
                    } else {
                        $range_field_type_arr[0] = $range_field_type;
                    }
                    $range_flag = 0;
                    while (count($range_field_type_arr) > $range_flag) {
                        if ($range_field_type_arr[$range_flag] == 'simple') { // if input style
                            $filter_more_counter = 1;
                            $loop_flag = 1;
                            ?>
                            <li>
                                <?php echo($field_label_html) ?>
                                <div class="<?php echo($dropdown_main_class) ?>">
                                    <select name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_range_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                            class="selectize-select"
                                            placeholder="<?php echo($field_label_placeholder); ?>">
                                        <?php
                                        while ($loop_flag <= $range_laps) {

                                            // main query array $args_count
                                            $range_first = $range_min + 1;
                                            $range_seond = $range_min + $range_interval;

                                            $custom_slider_selected = '';
                                            if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == (($range_min + 1) . "-" . ($range_min + $range_interval))) {
                                                $custom_slider_selected = ' selected="selected"';
                                            }
                                            if ($loop_flag == 1) {
                                                ?>
                                                <option value=""><?php echo($field_label_placeholder); ?></option>
                                                <?php
                                            }
                                            ?>
                                            <option value="<?php echo jobsearch_esc_html((($range_min + 1) . "-" . ($range_min + $range_interval))); ?>" <?php echo($custom_slider_selected) ?>><?php echo force_balance_tags((($range_min + 1) . " - " . ($range_min + $range_interval))); ?></option>
                                            <?php
                                            $range_min = $range_min + $range_interval;
                                            $loop_flag++;
                                            $filter_more_counter++;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </li>
                            <?php
                            if ($filter_more_counter > 6) {
                                //echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                            }
                        } elseif ($range_field_type_arr[$range_flag] == 'slider') { // if slider style 
                            wp_enqueue_style('jquery-ui');
                            wp_enqueue_script('jquery-ui');
                            $rand_id = rand(123, 1231231);
                            $range_field_max = $range_min;
                            $i = 0;
                            while ($range_laps > $i) {
                                $range_field_max = $range_field_max + $range_interval;
                                $i++;
                            }
                            $range_complete_str_first = "";
                            $range_complete_str_second = "";
                            $range_complete_str = '';
                            $range_complete_str_first = $range_min;
                            $range_complete_str_second = $range_field_max;
                            if (isset($_REQUEST[$query_str_var_name])) {
                                $range_complete_str = $_REQUEST[$query_str_var_name];
                                $range_complete_str_arr = explode("-", $range_complete_str);
                                $range_complete_str_first = isset($range_complete_str_arr[0]) ? $range_complete_str_arr[0] : '';
                                $range_complete_str_second = isset($range_complete_str_arr[1]) ? $range_complete_str_arr[1] : '';
                            }
                            ?>
                            <li>
                                <?php echo($field_label_html) ?>
                                <input type="text" name="<?php echo jobsearch_esc_html($query_str_var_name) ?>"
                                       id="<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_range_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                       value="<?php echo jobsearch_esc_html($range_complete_str); ?>" readonly
                                       style="border:0; color:#f6931f; font-weight:bold;"/>
                                <div id="slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"></div>
                                <script type="text/javascript">
                                    jQuery(document).ready(function () {
                                        jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider({
                                            range: true,
                                            min: <?php echo absint($range_min); ?>,
                                            max: <?php echo absint($range_field_max); ?>,
                                            values: [<?php echo absint($range_complete_str_first); ?>, <?php echo absint($range_complete_str_second); ?>],
                                            slide: function (event, ui) {
                                                jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(ui.values[0] + "-" + ui.values[1]);
                                            },
                                            stop: function (event, ui) {
                                                <?php //echo force_balance_tags($submit_js_function_str); ?>;
                                            }
                                        });
                                        jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 0) +
                                            "-" + jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 1));
                                    });
                                </script>
                            </li>
                            <?php
                        }
                        $range_flag++;
                    }
                } else {
                    $text_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                    ?>
                    <li>
                        <?php echo($field_label_html) ?>
                        <input type="text" name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                               id="<?php echo jobsearch_esc_html($query_str_var_name); ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_txt_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                               placeholder="<?php echo($field_label_placeholder) ?>"
                               value="<?php echo jobsearch_esc_html($text_field_req_val); ?>"/>
                    </li>
                    <?php
                }

                if ($dep_fields_html != '') {
                    echo ($dep_fields_html);
                }
            }
        }
    }
    
    public function filters_cusf_query_arr($filter_arr, $args) {
        extract($args);
        
        $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;
        
        if (!empty($cust_request_arr)) {
            $_request_arr = $cust_request_arr;
        } else {
            $_request_arr = $_REQUEST;
        }

        $jobsearch_post_cus_fields = get_option($field_db_slug);
        if (is_array($jobsearch_post_cus_fields) && sizeof($jobsearch_post_cus_fields) > 0) {
            $custom_field_flag = 1;
            foreach ($jobsearch_post_cus_fields as $cus_fieldvar => $cus_field) {
                
                if ((isset($cus_field['enable-search']) && $cus_field['enable-search'] == 'on') || (isset($cus_field['enable-advsrch']) && $cus_field['enable-advsrch'] == 'on')) {
                    if ($cus_field['type'] == 'dependent_fields') {
                        $f_custf_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                        $query_str_var_name = trim(str_replace(' ', '', $f_custf_name));
                        if (isset($_request_arr[$query_str_var_name])) {
                            if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {
                                $dropdown_query_str_var_name = explode(",", $_request_arr[$query_str_var_name]);
                                if (isset($dropdown_query_str_var_name[0]) && $dropdown_query_str_var_name[0] !== 'null') {
                                    $filter_multi_arr = array();
                                    $filter_multi_arr ['relation'] = 'OR';
                                    //var_dump($dropdown_query_str_var_name);
                                    foreach ($dropdown_query_str_var_name as $query_str_var_name_key) {
                                        if ($query_str_var_name_key == 'null') {
                                            $query_str_var_name_key = '';
                                        }
                                        if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {
                                            $filter_multi_arr[] = array(
                                                'key' => $query_str_var_name,
                                                'value' => ($query_str_var_name_key),
                                                'compare' => 'LIKE',
                                            );
                                        } else {
                                            $filter_multi_arr[] = array(
                                                'key' => $query_str_var_name,
                                                'value' => $query_str_var_name_key,
                                                'compare' => 'LIKE',
                                            );
                                        }
                                    }
                                    $filter_arr[] = array(
                                        $filter_multi_arr
                                    );
                                }
                            } else {
                                if (isset($_request_arr[$query_str_var_name]) && $_request_arr[$query_str_var_name] == 'null') {
                                    $_request_arr[$query_str_var_name] = '';
                                }
                                if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {

                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => ($_request_arr[$query_str_var_name]),
                                        'compare' => 'LIKE',
                                    );
                                } else {
                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => $_request_arr[$query_str_var_name],
                                        'compare' => '=',
                                    );
                                }
                            }

                            if (isset($_request_arr[$query_str_var_name]) && !empty($_request_arr[$query_str_var_name])) {
                                $opti_vals = explode(",", $_request_arr[$query_str_var_name]);

                                $main_options = isset($cus_field['options']) ? $cus_field['options'] : '';
                                if (!empty($main_options) && is_array($main_options)) {
                                    $optids = array();
                                    foreach ($main_options as $cusfiled_arkey => $cusfiled_aror) {
                                        if (isset($cusfiled_aror['depend']) && !empty($cusfiled_aror['depend']) && isset($cusfiled_aror['label']) && in_array($cusfiled_aror['label'], $opti_vals)) {
                                            $optids[] = $cusfiled_arkey;
                                        }
                                    }

                                    $depend_query = self::inner_filters_cusf_query($query_str_var_name, $optids, $main_options);
                                    if (!empty($depend_query)) {
                                        foreach ($depend_query as $depend_query_ar) {
                                            $filter_arr[] = $depend_query_ar;
                                        }
                                    }
                                }
                                //
                            }
                            //
                        }
                    }
                }
            }
        }
        //echo '<pre>';
        //var_dump($filter_arr);
        //echo '</pre>';
        return $filter_arr;
    }
    
    public static function inner_filters_cusf_query($field_name, $optids, $main_options) {
        if (!empty($optids)) {
            $_request_arr = $_REQUEST;
            $filter_arr = array();
            //var_dump($optids);
            foreach ($optids as $optid) {
                $main_dependent = $main_options[$optid]['depend'];
                
                if (!empty($main_dependent)) {
                    
                    foreach ($main_dependent as $cus_fieldvar => $cus_field) {
                        $cus_field['name'] = $field_name . '_' . $cus_fieldvar;
                        $f_custf_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                        $query_str_var_name = trim(str_replace(' ', '', $f_custf_name));
                        
                        if ((isset($cus_field['enable-search']) && $cus_field['enable-search'] == 'on') || (isset($cus_field['enable-advsrch']) && $cus_field['enable-advsrch'] == 'on')) {
                            
                            //echo '<pre>';
                            //var_dump($cus_field);
                            //echo '</pre>';
                            if ($cus_field['type'] == 'dropdown' || $cus_field['type'] == 'checkbox') {
                                
                                if (isset($_request_arr[$query_str_var_name]) && $_request_arr[$query_str_var_name] != '') {
                                    if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {
                                        $dropdown_query_str_var_name = explode(",", $_request_arr[$query_str_var_name]);
                                        if (isset($dropdown_query_str_var_name[0]) && $dropdown_query_str_var_name[0] !== 'null') {
                                            $filter_multi_arr = array();
                                            $filter_multi_arr ['relation'] = 'OR';
                                            //var_dump($dropdown_query_str_var_name);
                                            foreach ($dropdown_query_str_var_name as $query_str_var_name_key) {
                                                if ($query_str_var_name_key == 'null') {
                                                    $query_str_var_name_key = '';
                                                }
                                                if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {
                                                    $filter_multi_arr[] = array(
                                                        'key' => $query_str_var_name,
                                                        'value' => ($query_str_var_name_key),
                                                        'compare' => 'LIKE',
                                                    );
                                                } else {
                                                    $filter_multi_arr[] = array(
                                                        'key' => $query_str_var_name,
                                                        'value' => $query_str_var_name_key,
                                                        'compare' => 'LIKE',
                                                    );
                                                }
                                            }
                                            $filter_arr[] = array(
                                                $filter_multi_arr
                                            );
                                        }
                                    } else {
                                        if (isset($_request_arr[$query_str_var_name]) && $_request_arr[$query_str_var_name] == 'null') {
                                            $_request_arr[$query_str_var_name] = '';
                                        }
                                        if (isset($cus_field['multi']) && $cus_field['multi'] == 'on') {

                                            $filter_arr[] = array(
                                                'key' => $query_str_var_name,
                                                'value' => ($_request_arr[$query_str_var_name]),
                                                'compare' => 'LIKE',
                                            );
                                        } else {
                                            $filter_arr[] = array(
                                                'key' => $query_str_var_name,
                                                'value' => $_request_arr[$query_str_var_name],
                                                'compare' => '=',
                                            );
                                        }
                                    }
                                    
                                    $opti_vals = explode(",", $_request_arr[$query_str_var_name]);

                                    $main_options = isset($cus_field['options']) ? $cus_field['options'] : '';
                                    if (!empty($main_options) && is_array($main_options)) {
                                        $optids = array();
                                        foreach ($main_options as $cusfiled_arkey => $cusfiled_aror) {
                                            if (isset($cusfiled_aror['depend']) && !empty($cusfiled_aror['depend']) && isset($cusfiled_aror['label']) && in_array($cusfiled_aror['label'], $opti_vals)) {
                                                $optids[] = $cusfiled_arkey;
                                            }
                                        }

                                        $depend_query = self::inner_filters_cusf_query($field_name, $optids, $main_options);
                                        if (!empty($depend_query)) {
                                            foreach ($depend_query as $depend_query_ar) {
                                                $filter_arr[] = $depend_query_ar;
                                            }
                                        }
                                    }
                                    //
                                }
                                //
                            } else if ($cus_field['type'] == 'range') {
                                if (isset($_request_arr[$query_str_var_name]) && $_request_arr[$query_str_var_name] != '') {
                                    $ranges_str_arr = explode("-", $_request_arr[$query_str_var_name]);
                                    if (!isset($ranges_str_arr[1])) {
                                        $ranges_str_arr = explode("-", $ranges_str_arr[0]);
                                    }
                                    $range_first = $ranges_str_arr[0];
                                    $range_seond = $ranges_str_arr[1];
                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => $range_first,
                                        'compare' => '>=',
                                        'type' => 'numeric'
                                    );
                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => $range_seond,
                                        'compare' => '<=',
                                        'type' => 'numeric'
                                    );
                                }
                            } else {
                                if (isset($_request_arr[$query_str_var_name]) && $_request_arr[$query_str_var_name] != '') {
                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => $_request_arr[$query_str_var_name],
                                        'compare' => 'LIKE',
                                    );
                                }
                            }
                        }
                    }
                    //
                }
                //
            }
            
            return $filter_arr;
        }
    }
    
    public function url_query_innervar_byfiltr($qry_var, $entity_type) {
        global $sitepress;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $field_db_slug = "jobsearch_custom_field_" . $entity_type;
        $custom_all_fields_saved_data = get_option($field_db_slug);
        $all_fields_name_str = '';
        if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
            $field_names_counter = 0;
            foreach ($custom_all_fields_saved_data as $cusf_key => $custom_field_saved_data) {
                $cusf_type = isset($custom_field_saved_data['type']) ? $custom_field_saved_data['type'] : '';
                $cusf_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
                if ($cusf_type == 'dependent_fields') {
                    $cusf_options = isset($custom_field_saved_data['options']) ? $custom_field_saved_data['options'] : '';
                    if (!empty($cusf_options)) {
                        foreach ($cusf_options as $cusf_opt_key => $cusf_opt_obj) {
                            if (isset($cusf_opt_obj['depend']) && !empty($cusf_opt_obj['depend'])) {
                                foreach ($cusf_opt_obj['depend'] as $dep_field_key => $dep_field_val) {
                                    $indep_field_name = $cusf_name . '_' . $dep_field_key;
                                    if ($qry_var == $indep_field_name) {
                                        $cusf_labl_str = isset($dep_field_val['label']) ? $dep_field_val['label'] : '';
                                        $qry_var = $cusf_labl_str;
                                        break;
                                    } else if (isset($dep_field_val['type']) && ($dep_field_val['type'] == 'dropdown' || $dep_field_val['type'] == 'checkbox')) {
                                        $inner_query_var = $this->inner_query_var_string($cusf_name, $dep_field_val, $qry_var);
                                        if (isset($inner_query_var[0]) && $qry_var == $inner_query_var[0]) {
                                            $qry_var = $inner_query_var[1];
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        //
                    }
                }
            }
        }
        
        return $qry_var;
    }
    
    public function inner_query_var_string($cusf_name, $custom_field_saved_data, $qry_var) {
        $cusf_options = isset($custom_field_saved_data['options']) ? $custom_field_saved_data['options'] : '';
        if (!empty($cusf_options)) {
            $qry_var_iner = '';
            foreach ($cusf_options as $cusf_opt_key => $cusf_opt_obj) {
                
                if (isset($cusf_opt_obj['depend']) && !empty($cusf_opt_obj['depend'])) {
                    foreach ($cusf_opt_obj['depend'] as $dep_field_key => $dep_field_val) {
                        $indep_field_name = $cusf_name . '_' . $dep_field_key;
                        if ($qry_var == $indep_field_name) {
                            $cusf_labl_str = isset($dep_field_val['label']) ? $dep_field_val['label'] : '';
                            $qry_var_iner = $cusf_labl_str;
                            return array($qry_var, $qry_var_iner);
                            break;
                        } else if (isset($dep_field_val['type']) && ($dep_field_val['type'] == 'dropdown' || $dep_field_val['type'] == 'checkbox')) {
                            $inner_query_var = $this->inner_query_var_string($cusf_name, $dep_field_val, $qry_var);
                            if (isset($inner_query_var[0]) && $qry_var == $inner_query_var[0]) {
                                return $inner_query_var;
                            }
                        }
                    }
                }
            }
            //
        }
    }
}

new Jobsearch_CustomField_DepFields_Filters();
