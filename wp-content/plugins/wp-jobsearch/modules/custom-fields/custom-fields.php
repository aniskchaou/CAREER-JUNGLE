<?php
/*
  Class : CustomField
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_CustomField {

// hook things up
    public function __construct() {

        add_action('admin_enqueue_scripts', array($this, 'customfield_admin_enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'customfield_admin_enqueue_scripts'), 51);
        add_action('jobsearch_load_custom_fields', array($this, 'jobsearch_load_custom_fields_callback'), 1);
        add_action('admin_footer', array($this, 'admin_custom_script'));
        Jobsearch_CustomField::load_files();
    }

    public function admin_custom_script() {

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

        if($flag != true){
            return false;
        }
        ?>
        <script>
            var adminmenu = jQuery('#adminmenu');
            var _parent_menu = adminmenu.find('.toplevel_page_jobsearch-job-fields');
            _parent_menu.find('>ul>li').eq(1).find('a').html('<?php esc_html_e('Job Custom Fields', 'wp-jobsearch'); ?>');
        </script>
        <?php
    }

    static function load_files() {
        include plugin_dir_path(dirname(__FILE__)) . 'custom-fields/include/dependent-fields-structure.php';
        include plugin_dir_path(dirname(__FILE__)) . 'custom-fields/include/dependent-fields-rendering.php';
        include plugin_dir_path(dirname(__FILE__)) . 'custom-fields/include/dependent-fields-search-filters.php';
        include plugin_dir_path(dirname(__FILE__)) . 'custom-fields/include/custom-fields-ajax.php';
        include plugin_dir_path(dirname(__FILE__)) . 'custom-fields/include/custom-fields-html.php';
        include plugin_dir_path(dirname(__FILE__)) . 'custom-fields/include/custom-fields-load.php';
    }

    public function customfield_admin_enqueue_scripts() {
        wp_register_style('jobsearch-custom-field-sortable-app-css', jobsearch_plugin_get_url('modules/custom-fields/css/custom-field.css'), array(), '');
        wp_register_script('jobsearch-custom-field', jobsearch_plugin_get_url('modules/custom-fields/js/custom-field-functions.js'), array('jquery'), '', true);
        // Localize the script
        $jobsearch_customfield_common_arr = array(
            'plugin_url' => jobsearch_plugin_get_url(),
            'ajax_url' => admin_url('admin-ajax.php'),
        );
        wp_localize_script('jobsearch-custom-field', 'jobsearch_customfield_common_vars', $jobsearch_customfield_common_arr);

        wp_register_script('jobsearch-custom-field-sortable-sortable', jobsearch_plugin_get_url('modules/custom-fields/js/sortable.js'), array('jquery'), '', true);
        wp_register_script('jobsearch-custom-field-sortable-app', jobsearch_plugin_get_url('modules/custom-fields/js/app.js'), array('jquery'), '', true);

        // for range slider
        wp_register_script('jquery-ui', jobsearch_plugin_get_url('admin/js/jquery-ui.js'), array(), JobSearch_plugin::get_version(), false);
        wp_enqueue_style('jquery-ui', jobsearch_plugin_get_url('admin/css/jquery-ui.css'));
    }

    public function jobsearch_load_custom_fields_callback($custom_field_entity) {
        global $pagenow;
        $get_cfields_page = isset($_GET['page']) ? $_GET['page'] : '';
        wp_enqueue_script('jobsearch-custom-field');
        wp_enqueue_style('jobsearch-custom-field-sortable-app-css');
        wp_enqueue_script('jobsearch-custom-field-sortable-sortable');
        wp_enqueue_script('jobsearch-custom-field-sortable-app');
        
        wp_enqueue_script('jobsearch-selectize');
        
        $rand_id = rand(123, 8787987);
        // load all saved fields
        $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;
        $custom_all_fields_saved_data = get_option($field_db_slug);
        ?>
        <div class="container">
            <div data-force="30" class="layer block" >
                <div class="jobsearch-form-field-list">
                    <label><?php esc_html_e('Field List:', 'wp-jobsearch'); ?></label>
                    <ul>
                        <li><i class="dashicons dashicons-editor-textcolor" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="heading" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Heading', 'wp-jobsearch'); ?>"><?php echo esc_html('Heading', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-media-text" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="text" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Text', 'wp-jobsearch'); ?>"><?php echo esc_html('Text', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-admin-media" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="upload_file" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Upload File', 'wp-jobsearch'); ?>"><?php echo esc_html('Upload File', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-media-video" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="video" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Video', 'wp-jobsearch'); ?>"><?php echo esc_html('Video', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-admin-links" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="linkurl" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('URL', 'wp-jobsearch'); ?>"><?php echo esc_html('URL', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-yes" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="checkbox" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Checkbox', 'wp-jobsearch'); ?>"><?php echo esc_html('Checkbox', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-arrow-down-alt" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="dropdown" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Dropdown', 'wp-jobsearch'); ?>"><?php echo esc_html('Dropdown', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-networking" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="dependent_dropdown" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Dependent Dropdown', 'wp-jobsearch'); ?>"><?php echo esc_html('Dependent Dropdown', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-admin-multisite" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="dependent_fields" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Dependent Fields', 'wp-jobsearch'); ?>"><?php echo esc_html('Dependent Fields', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-editor-alignleft" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="textarea" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Textarea', 'wp-jobsearch'); ?>"><?php echo esc_html('Textarea', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-email-alt" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="email" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Email', 'wp-jobsearch'); ?>"><?php echo esc_html('Email', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-editor-ol" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="number" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Number', 'wp-jobsearch'); ?>"><?php echo esc_html('Number', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-calendar-alt" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="date" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Date', 'wp-jobsearch'); ?>"><?php echo esc_html('Date', 'wp-jobsearch'); ?></a></li>
                        <li><i class="dashicons dashicons-image-flip-horizontal" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="range" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Range', 'wp-jobsearch'); ?>"><?php echo esc_html('Range', 'wp-jobsearch'); ?></a></li>
                        <?php if ($get_cfields_page == 'jobsearch-job-fields' || $get_cfields_page == 'jobsearch-candidate-fields') { ?>
                            <li><i class="dashicons dashicons-vault" aria-hidden="true"></i> <a class="jobsearch-custom-field-add-field" data-fieldtype="salary" data-randid="<?php echo absint($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_html('Salary (For Search)', 'wp-jobsearch'); ?>"><?php echo esc_html('Salary (For Search)', 'wp-jobsearch'); ?></a></li>
                            <?php
                        }
                        
                        echo apply_filters('jobsearch_bk_custom_fields_itms_list_after', '', $rand_id);
                        ?>
                    </ul>
                </div>

                <div class="jobsearch-custom-field-form">
                    <form id="jobsearch-custom-field-form-<?php echo absint($rand_id); ?>" action="" method="post">
                        <div class="layer custom-field-title"><?php echo esc_html('List of Fields', 'wp-jobsearch'); ?></div>
                        <ul id="foo<?php echo absint($rand_id); ?>" class="block__list block__list_words"> 
                            <?php
                            $count_node = time();
                            $all_fields_name_str = '';
                            $all_fields_name_count = 0;
                            $empty_container_display = '';
                            if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
                                $field_names_counter = 0;
                                $output = '';
                                foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {
                                    $all_fields_name_count++;
                                    if (isset($custom_field_saved_data['name']) && $custom_field_saved_data['name'] != '') {
                                        if ($field_names_counter > 0) {
                                            $all_fields_name_str .= ',';
                                        }
                                        $all_fields_name_str .= isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
                                        $field_names_counter++;
                                    }
                                    $li_rand_id = rand(454, 999999);
                                    $output .= '<li class="custom-field-class-' . $li_rand_id . '">';
                                    if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "text") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_text_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "video") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_video_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "linkurl") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_linkurl_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "upload_file") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_upload_file_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "email") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_email_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "textarea") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_textarea_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "date") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_date_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "range") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_range_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "salary") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_salary_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "number") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_number_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "checkbox") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_checkbox_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dropdown") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_dropdown_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dependent_dropdown") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_dependent_dropdown_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dependent_fields") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_dependent_fields_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "heading") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_heading_html', '', $count_node, $custom_field_saved_data);
                                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "company_benefits") {

                                        $count_node ++;
                                        $output .= apply_filters('jobsearch_custom_field_company_benefits_html', '', $count_node, $custom_field_saved_data);
                                    }
                                    
                                    $output .= apply_filters('jobsearch_custom_field_actions_html', $li_rand_id, $count_node, $custom_field_saved_data['type']);
                                    $output .= '</li>';
                                }
                                echo force_balance_tags($output);
                                $empty_container_display = 'style="display:none"';
                            }
                            ?>
                            <li class="custom-field-empty-msg" <?php echo force_balance_tags($empty_container_display); ?>><span><?php echo esc_html__('Click to the list on the right side to create new fields set.', 'wp-jobsearch'); ?></span></li>
                        </ul>
                        <input type="hidden" name="custom-fields-all-names" value="<?php echo esc_html($all_fields_name_str); ?>" />
                        <input type="hidden" name="custom-fields-all-names-count" value="<?php echo esc_html($all_fields_name_count); ?>" />
                        <div class="jobsearch-custom-fields-submit">                        
                            <button class="custom-fields-submit" data-randid="<?php echo absint($rand_id); ?>" data-entitytype="<?php echo esc_html($custom_field_entity); ?>" data-btntext="<?php echo esc_html('Save Fields', 'wp-jobsearch'); ?>"><?php echo esc_html('Save Fields', 'wp-jobsearch'); ?></button>
                            <div class="custom-field-msg custom-field-msg-<?php echo absint($rand_id); ?>" style="display: none;"></div>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
        <script>
            var global_custom_field_counter = 0;
            jQuery(document).ready(function () {

                var byId = function (id) {
                    return document.getElementById(id);
                },
                        loadScripts = function (desc, callback) {
                            var deps = [], key, idx = 0;

                            for (key in desc) {
                                deps.push(key);
                            }

                            (function _next() {
                                var pid,
                                        name = deps[idx],
                                        script = document.createElement('script');

                                script.type = 'text/javascript';
                                script.src = desc[deps[idx]];

                                pid = setInterval(function () {
                                    if (window[name]) {
                                        clearTimeout(pid);

                                        deps[idx++] = window[name];

                                        if (deps[idx]) {
                                            _next();
                                        } else {
                                            callback.apply(null, deps);
                                        }
                                    }
                                }, 30);

                                document.getElementsByTagName('head')[0].appendChild(script);
                            })()
                        },
                        console = window.console;

                if (!console.log) {
                    console.log = function () {
                        alert([].join.apply(arguments, ' '));
                    };
                }
                Sortable.create(byId('foo<?php echo absint($rand_id); ?>'), {
                    group: "words",
                    handle: ".field-msort-handle",
                    animation: 150,
                    store: {
                        get: function (sortable) {
                            var order = localStorage.getItem(sortable.options.group);
                            return order ? order.split('|') : [];
                        },
                        set: function (sortable) {
                            var order = sortable.toArray();
                            localStorage.setItem(sortable.options.group, order.join('|'));
                        }
                    },
                    onAdd: function (evt) {
                        console.log('onAdd.foo<?php echo absint($rand_id); ?>:', [evt.item, evt.from]);
                    },
                    onUpdate: function (evt) {
                        console.log('onUpdate.foo<?php echo absint($rand_id); ?>:', [evt.item, evt.from]);
                    },
                    onRemove: function (evt) {
                        console.log('onRemove.foo<?php echo absint($rand_id); ?>:', [evt.item, evt.from]);
                    },
                    onStart: function (evt) {
                        console.log('onStart.foo<?php echo absint($rand_id); ?>:', [evt.item, evt.from]);
                    },
                    onSort: function (evt) {
                        console.log('onStart.foo<?php echo absint($rand_id); ?>:', [evt.item, evt.from]);
                    },
                    onEnd: function (evt) {
                        console.log('onEnd.foo<?php echo absint($rand_id); ?>:', [evt.item, evt.from]);
                    }
                });
            });
        </script>
        <?php
    }

}

// class Jobsearch_CustomField 
$Jobsearch_CustomField_obj = new Jobsearch_CustomField();
global $Jobsearch_CustomField_obj;
