<?php
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Careerfy_Icons_Manager')) {

    class Careerfy_Icons_Manager {

        /**
         * Start construct Functions
         */
        public function __construct() {

            // Initialize Addon

            add_action('init', array($this, 'register_activation_hook_callback'));

            add_action('admin_enqueue_scripts', array($this, 'admin_style_scripts'));
            add_action('wp_enqueue_scripts', array($this, 'front_style_scripts'));
            add_action('admin_menu', array($this, 'create_icon_manager_menu'), 10);
            add_action('import_icons', array($this, 'import_icons_handle'));
            add_action('wp_ajax_export_icons', array($this, 'export_icons_callback'));
            
            //
            add_action('wp_ajax_jobsearch_reset_all_icon_manager_pact', array($this, 'reset_icon_manager'));

            add_filter('careerfy_vc_custom_icons_list_arr', array($this, 'vc_icon_lists_add'), 10, 1);

            $this->include_files();
        }
        
        public function icons_upload_files_path($dir = '') {

            $cus_dir = 'careerfy-icons-manager';
            $dir_path = array(
                'path' => $dir['basedir'] . '/' . $cus_dir,
                'url' => $dir['baseurl'] . '/' . $cus_dir,
                'subdir' => $cus_dir,
            );
            return $dir_path + $dir;
        }

        /*
         * Include Recomended Files
         */

        public function include_files() {
            require_once plugin_dir_path(dirname(__FILE__)) . 'icons-manager/classes/class-icons-uploader.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'icons-manager/classes/class-icons-fields.php';
            require_once ABSPATH . 'wp-admin/includes/screen.php';
        }

        public function vc_icon_lists_add($icons_list) {
            global $wp_filesystem;
            require_once ABSPATH . '/wp-admin/includes/file.php';

            if (false === ($creds = request_filesystem_credentials(wp_nonce_url('post.php'), '', false, false, array()) )) {
                return true;
            }
            if (!WP_Filesystem($creds)) {
                request_filesystem_credentials(wp_nonce_url('post.php'), '', true, false, array());
                return true;
            }
            
            add_filter('upload_dir', array($this, 'icons_upload_files_path'));

            $upload_dir = wp_upload_dir();
            $up_dir_path = $upload_dir['path'] . '/';
            $icons_groups = get_option('careerfy_icons_groups');
            if (!empty($icons_groups)) {
                foreach ($icons_groups as $icon_key => $icon_obj) {
                    if (isset($icon_obj['status']) && $icon_obj['status'] == 'on') {
                        global $pagenow;
                        $post_type = !isset($_GET['post_type']) ? 'post' : $_GET['post_type'];
                        $org_site_url = get_option('siteurl');
                        $icon_obj_url = str_replace($org_site_url, site_url(), $icon_obj['url']);

                        if (preg_match('/www/', $_SERVER['HTTP_HOST'])) {
                            if (!preg_match('/www/', $icon_obj_url)) {
                                $bits = parse_url($icon_obj_url);
                                $newHost = substr($bits["host"], 0, 4) !== "www." ? "www." . $bits["host"] : $bits["host"];
                                $icon_obj_url = $bits["scheme"] . "://" . $newHost . (isset($bits["port"]) ? ":" . $bits["port"] : "") . $bits["path"] . (!empty($bits["query"]) ? "?" . $bits["query"] : "");
                            }
                        }
                        $icons_selection_file = $up_dir_path . $icon_key . '/selection.json';
                        if (is_file($icons_selection_file)) {
                            $get_json_data = $wp_filesystem->get_contents($icons_selection_file);

                            $get_json_data = json_decode($get_json_data, true);

                            $cus_icons_arr = array();
                            if (isset($get_json_data['icons']) && !empty($get_json_data['icons'])) {
                                $icons_prefix = isset($get_json_data['preferences']['fontPref']['prefix']) ? $get_json_data['preferences']['fontPref']['prefix'] : '';
                                $sd = 1;
                                foreach ($get_json_data['icons'] as $icon_data) {
                                    if (isset($icon_data['properties']['name'])) {
                                        $cus_icons_arr[] = array($icons_prefix . $icon_data['properties']['name'] => $icon_data['properties']['name']);
                                        $sd++;
                                    }
                                }
                                $icons_group_name = str_replace(array('-', '_'), array(' ', ' '), $icon_key);
                                $icons_group_name = ucwords($icons_group_name);
                                $icons_list[$icons_group_name] = $cus_icons_arr;
                            }
                        }
                    }
                }
            }
            
            remove_filter('upload_dir', array($this, 'icons_upload_files_path'));
            
            return $icons_list;
        }

        public function front_style_scripts() {

            $icons_groups = get_option('careerfy_icons_groups');
            if (!empty($icons_groups)) {
                foreach ($icons_groups as $icon_key => $icon_obj) {
                    if (isset($icon_obj['status']) && $icon_obj['status'] == 'on') {
                        global $pagenow;
                        $post_type = !isset($_GET['post_type']) ? 'post' : $_GET['post_type'];
                        $org_site_url = get_option('siteurl');
                        $icon_obj_url = str_replace($org_site_url, site_url(), $icon_obj['url']);

                        if (preg_match('/www/', $_SERVER['HTTP_HOST'])) {
                            if (!preg_match('/www/', $icon_obj_url)) {
                                $bits = parse_url($icon_obj_url);
                                $newHost = substr($bits["host"], 0, 4) !== "www." ? "www." . $bits["host"] : $bits["host"];
                                $icon_obj_url = $bits["scheme"] . "://" . $newHost . (isset($bits["port"]) ? ":" . $bits["port"] : "") . $bits["path"] . (!empty($bits["query"]) ? "?" . $bits["query"] : "");
                            }
                        }
                        wp_enqueue_style('careerfy_icons_data_css_' . $icon_key, $icon_obj_url . '/style.css', array(), Careerfy_framework::get_version());
                    }
                }
            }
        }

        /**
         * Initialize enqueue scripts
         */
        public function admin_style_scripts() {
            wp_enqueue_media();
            wp_enqueue_style('careerfy-icons-manager', careerfy_framework_get_url('icons-manager/assets/css/icons-manager.css'));

            $icons_groups = get_option('careerfy_icons_groups');
            if (!empty($icons_groups)) {
                foreach ($icons_groups as $icon_key => $icon_obj) {
                    if (isset($icon_obj['status']) && $icon_obj['status'] == 'on') {
                        global $pagenow;
                        $post_type = !isset($_GET['post_type']) ? 'post' : $_GET['post_type'];
                        $org_site_url = get_option('siteurl');
                        $icon_obj_url = str_replace($org_site_url, site_url(), $icon_obj['url']);

                        if (preg_match('/www/', $_SERVER['HTTP_HOST'])) {
                            if (!preg_match('/www/', $icon_obj_url)) {
                                $bits = parse_url($icon_obj_url);
                                $newHost = substr($bits["host"], 0, 4) !== "www." ? "www." . $bits["host"] : $bits["host"];
                                $icon_obj_url = $bits["scheme"] . "://" . $newHost . (isset($bits["port"]) ? ":" . $bits["port"] : "") . $bits["path"] . (!empty($bits["query"]) ? "?" . $bits["query"] : "");
                            }
                        }
                        wp_register_style('careerfy_icons_data_css_' . $icon_key, $icon_obj_url . '/style.css');
                        if (is_admin() || $icon_key == 'default') {
                            wp_enqueue_style('careerfy_icons_data_css_' . $icon_key);
                        }
                    }
                }
            }

            wp_enqueue_script('careerfy-icons-manager-script', careerfy_framework_get_url('icons-manager/assets/scripts/icons-manager-scripts.js', array(), Careerfy_framework::get_version(), true));
            wp_localize_script('careerfy-icons-manager-script', 'careerfy_icons_manager', array(
                'ajax_url' => esc_url(admin_url('admin-ajax.php')),
            ));
        }

        public function create_icon_manager_menu() {
            add_submenu_page('themes.php', esc_html__('Careerfy Icons Manager', 'careerfy-frame'), esc_html__('Careerfy Icons Manager', 'careerfy-frame'), 'administrator', 'careerfy-icons-manager', array($this, 'icons_manager_settings_callback'));
        }

        public function icons_manager_settings_callback() {
            echo '<div class="wrap"><h2>' . __('Icons Manager', 'careerfy-frame');
            echo '<a href="javascript:;" class="add-new-h2 careerfy-icons-uploadMedia">' . __('Upload New Icons', 'careerfy-frame') . '</a>';
            echo '</h2> <span class="icon-manager-loder"></span>';

            $this->careerfy_export_icons();
            
            ?>
            <script>
                jQuery(document).on('click', '.reset-iconsmnger-btn', function () {
                    var icob_con = confirm('<?php echo esc_js(__('Warning! It will remove all icons group and set only default icons.', 'careerfy-frame')) ?>');
                    if (icob_con) {
                        var _this = jQuery(this);
                        var loader_con = _this.parent('div').find('span');

                        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                        var request = jQuery.ajax({
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            method: "POST",
                            data: {
                                'doing': 'reset_all_icon_manager',
                                'action': 'jobsearch_reset_all_icon_manager_pact'
                            },
                            dataType: "json"
                        });
                        request.done(function (response) {
                            if (typeof response.success !== 'undefined' && response.success == '1') {
                                window.location.reload();
                            } else {
                                loader_con.html('<?php _e('There is some issue.', 'careerfy-frame') ?>');
                            }
                        });

                        request.fail(function (jqXHR, textStatus) {
                            loader_con.html('<?php _e('There is some issue.', 'careerfy-frame') ?>');
                        });
                    }
                });
            </script>
            <?php
            echo '<div class="reset-iconsmnger-con"><a href="javascript:void(0);" class="button button-primary reset-iconsmnger-btn">' . __('Reset all Icons', 'careerfy-frame') . '</a> <span></span></div>';
            
            echo '<input type="hidden" id="careerfy_icons_fonts_zip_rand" name="careerfy_icons_fonts_zip_rand">';
            echo '<div class="careerfy-icons-msg"></div>';
            echo '<div class="careerfy-icons-manager-wrapper">';
            $this->careerfy_icons_list();
            echo '</div></div>';
            do_action('careerfy_icons_fields');
        }
        
        public function reset_icon_manager() {
            if (isset($_POST['doing']) && $_POST['doing'] == 'reset_all_icon_manager') {
                update_option('careerfy_icons_groups', '');
                echo json_encode(array('success' => '1'));
            }
            die;
        }

        public function careerfy_export_icons() {
            if (isset($_REQUEST['export']) && $_REQUEST['export'] == '1') {
                ?>
                <div class="export-icons-wrapper">
                    <div class="export-btn">
                        <a class="export-icons-btn" href="javascript:void(0);"><?php echo __('Export Icons', 'careerfy-frame'); ?></a>
                        <a id="export-icons" class="export-icons" href="javascript:void(0);" style="display:none;" download=""><?php echo __('Export Icons', 'careerfy-frame'); ?></a>
                    </div>
                </div>
                <?php
            }
        }

        /*
         * Listing all Icons
         */

        public function careerfy_icons_list() {
            global $careerfy_icons_html_fields, $careerfy_icons_form_fields;
            $icons_groups = get_option('careerfy_icons_groups');
            if (!empty($icons_groups)) {
                $footr_script_priority = 15;
                foreach ($icons_groups as $icons_key => $icons_obj) {

                    $icons_key_for_id = str_replace(array('.', ','), array('-', '-'), $icons_key);
                    
                    $group_obj = $icons_groups[$icons_key];
                    $selection_path = $group_obj['url'];
                    $org_site_url = get_option('siteurl');
                    $selection_path = str_replace($org_site_url, site_url(), $selection_path);
                    if (preg_match('/www/', $_SERVER['HTTP_HOST'])) {
                        if (!preg_match('/www/', $selection_path)) {
                            $bits = parse_url($selection_path);
                            $newHost = substr($bits["host"], 0, 4) !== "www." ? "www." . $bits["host"] : $bits["host"];
                            $selection_path = $bits["scheme"] . "://" . $newHost . (isset($bits["port"]) ? ":" . $bits["port"] : "") . $bits["path"] . (!empty($bits["query"]) ? "?" . $bits["query"] : "");
                        }
                    }

                    wp_enqueue_style('careerfy_icons_css_' . $icons_key, $selection_path . '/style.css');
                    ?>
                    <div class="icon_set-Defaults metabox-holder careerfy-icons-manager-list" data-id="<?php echo esc_attr($icons_key); ?>">
                        <div class="postbox">
                            <h3 class="icon_font_name">
                                <strong><?php echo str_replace(array('-', '_'), array(' ', ' '), $icons_key); ?></strong>
                                <span class="fonts-count careerfy-count-icons" id="careerfy-count-<?php echo esc_html($icons_key_for_id); ?>">0</span>
                                <?php if ($icons_key != 'default') { ?>
                                    <span class="fonts-count careerfy-group-remove"><?php echo __('Delete', 'careerfy-frame'); ?></span>
                                    <input type="checkbox" id="enable_group<?php echo($icons_key) ?>" class="careerfy-icons-enable-group" data-group="<?php echo ($icons_key) ?>" <?php echo ($icons_obj['status'] == 'on' ? 'checked' : '') ?> value="<?php echo ($icons_obj['status']) ?>">
                                    <?php
                                }
                                ?>
                            </h3>
                            <?php
                            echo '
                            <div class="inside">
                                <div class="icon_actions"></div>
                                <div class="icon_search icons_list_' . $icons_key_for_id . ' careerfy-icons-list">
                                    <ul></ul>
                                </div>
                            </div>';
                            ?>
                        </div>
                    </div>
                    <?php
                    $footr_script_priority++;
                    $popup_args = array(
                        'selection_path' => $selection_path,
                        'icons_key' => $icons_key,
                        'icons_key_for_id' => $icons_key_for_id,
                    );
                    add_action('admin_footer', function () use ($popup_args) {

                        extract(shortcode_atts(array(
                            'selection_path' => '',
                            'icons_key' => '',
                            'icons_key_for_id' => '',
                        ), $popup_args));
                        ?>
                        <script type="text/javascript">
                            jQuery(document).ready(function ($) {
                                var html_response  = "";
                                $.ajax({
                                    url: "<?php echo ($selection_path) ?>/selection.json?ver=<?php echo rand(10000000, 999999999) ?>",
                                    type: 'GET',
                                    dataType: 'json'
                                }).done(function (response) {
                                    var classPrefix = response.preferences.fontPref.prefix;
                                    //jQuery('body').append('<?php echo ($icons_key_for_id) ?>-' + 1445789900 + ' - ' + response.icons.length);
                                    //alert(classPrefix);
                                    //alert('<?php echo ($icons_key_for_id) ?>');
                                    //alert(jQuery("#careerfy-count-<?php echo ($icons_key_for_id) ?>").html());
                                    jQuery("#careerfy-count-<?php echo ($icons_key_for_id) ?>").html(response.icons.length);
                                    $.each(response.icons, function (i, v) {
                                        var li_html = "";
                                        li_html += "<li><i class='";
                                        li_html += classPrefix+v.properties.name;
                                        li_html += "'></i></li>";
                                        html_response   += li_html;
                                    });
                                    jQuery(".icons_list_<?php echo ($icons_key_for_id) ?> ul").html(html_response);
                                });
                            });
                        </script>
                        <?php
                    }, $footr_script_priority, 1);
                }
            }
        }

        public function register_activation_hook_callback() {
            add_filter('upload_dir', array($this, 'icons_upload_files_path'));
            $upload_dir = wp_upload_dir();
            $destination_path = $upload_dir['path'] . '/';
            wp_mkdir_p($destination_path);
            $icons_groups = get_option('careerfy_icons_groups');
            if (!isset($icons_groups['default']) || empty($icons_groups['default'])) {
                $new_group['default'] = array(
                    'path' => careerfy_framework_get_path('icons-manager/assets/default'),
                    'url' => careerfy_framework_get_url('icons-manager/assets/default'),
                    'status' => 'on'
                );
                if (!empty($icons_groups)) {
                    $new_group = array_merge($new_group, $icons_groups);
                }
                update_option('careerfy_icons_groups', $new_group);
            }
            remove_filter('upload_dir', array($this, 'icons_upload_files_path'));
        }

        /*
         * Import Icons
         */

        public function import_icons_handle($obj) {
            global $wp_filesystem;
            
            add_filter('upload_dir', array($this, 'icons_upload_files_path'));
            if ($obj->icons_data_path != '') {
                $icons = $wp_filesystem->get_contents($obj->icons_data_path);
                $icons = json_decode($icons, true);
                $icons = array();
                if (!empty($icons) && is_array($icons)) {
                    foreach ($icons as $key => $val) {
                        if ($key == 'default') {
                            $icons[$key]['path'] = careerfy_framework_get_path('icons-manager/assets/default');
                            $icons[$key]['url'] = careerfy_framework_get_url('icons-manager/assets/default');
                            $icons[$key]['status'] = 'on';
                        } else {
                            $upload_dir = wp_upload_dir();
                            $icons[$key]['path'] = $upload_dir['path'] . '/' . $key;
                            $icons[$key]['url'] = $upload_dir['url'] . '/' . $key;
                            $icons[$key]['status'] = $val['status'];
                        }
                    }
                } else {
                    $icons = $icons;
                }
                update_option('careerfy_icons_groups', $icons);
                $obj->action_return = true;
            } else {
                $obj->action_return = false;
            }
            remove_filter('upload_dir', array($this, 'icons_upload_files_path'));
        }

        /*
         * Export Icons
         */

        public function export_icons_callback() {
            global $wp_filesystem;

            $icons_groups = get_option('careerfy_icons_groups');

            $icons_groups_fields = json_encode($icons_groups, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
            $upload_dir = careerfy_framework_get_path('icons-manager/assets/backups/');
            $name = 'icons.json';
            $filename = trailingslashit($upload_dir) . $name;
            $fileurl = careerfy_framework_get_url('icons-manager/assets/backups/') . $name;
            if (!$wp_filesystem->put_contents($filename, $icons_groups_fields, FS_CHMOD_FILE)) {
                echo json_encode(array('type' => 'error', 'name' => $name, 'url' => $fileurl));
            } else {
                echo json_encode(array('type' => 'success', 'name' => $name, 'url' => $fileurl));
            }
            die();
        }

        public function check_file_exists($pattern, $flags = 0) {
            $files = glob($pattern, $flags);
            foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
                $files = array_merge($files, $this->check_file_exists($dir . '/' . basename($pattern), $flags));
            }
            return $files;
        }

    }

    global $careerfy_icons_manager;
    $careerfy_icons_manager = new Careerfy_Icons_Manager();
}
