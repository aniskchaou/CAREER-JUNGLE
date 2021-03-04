<?php
/**
 * File Type: Icons Fields
 */
if (!class_exists('Careerfy_Icons_Fields')) {

    class Careerfy_Icons_Fields {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_filter('careerfy_icons_fields', array($this, 'careerfy_icons_fields_callback'), 10, 5);
            add_action('wp_ajax_careerfy_library_icons_list', array($this, 'careerfy_library_icons_list_callback'));
        }

        /*
         * Render Icons Fields
         */

        public static function careerfy_icons_fields_callback($icon_value = '', $id = '', $name = '', $icons_library = 'default', $group_field_name = '') {
            global $careerfy_icons_form_fields;
            $group_name = $icons_library;
            if (empty($group_name)) {
                $group_name = 'default';
            }
            $icons_groups_array = array();
            $output = '';
            $group_field_name = ( $group_field_name != '' ) ? $group_field_name : esc_html($name) . '_group';
            ob_start();
            $icons_groups = get_option('careerfy_icons_groups');
            if (!empty($icons_groups)) {
                foreach ($icons_groups as $icon_key => $icon_obj) {
                    if (isset($icon_obj['status']) && $icon_obj['status'] == 'on') {
                        $icons_groups_array[$icon_key] = ucwords($icon_key);
                    }
                }
            }
            echo '<div class="careerfy-icon-choser" data-id="' . $id . '" data-name="' . $name . '" data-value="' . ($icon_value) . '">';

            $group_obj = $icons_groups[$group_name];

            echo '<div class="careerfy-element-field">';
            echo '<div class="elem-label">';
            echo '<div class="careerfy-library-icons-list">';
            echo self::careerfy_icomoon_icons_box($group_obj['url'], $group_name, $icon_value, $id, $name);
            echo '</div>';
            echo '</div>';
            echo '<div class="elem-field">';
            ?>
            <select id="careerfy_icons_careerfy_icon_library" name="<?php echo ($group_field_name) ?>" class="careerfy-icon-library">
                <?php
                if (!empty($icons_groups_array)) {
                    foreach ($icons_groups_array as $icons_group_key => $icons_group_val) {
                        ?>
                        <option value="<?php echo ($icons_group_key) ?>" <?php echo ($icons_group_key == $group_name ? 'selected="selected"' : '') ?>><?php echo ($icons_group_val) ?></option>
                        <?php
                    }
                }
                ?>
            </select>
            <?php
            echo '</div>';
            echo '</div>';
            echo '</div>';
            $output = ob_get_clean();

            return $output;
        }

        /*
         * Load Icons by Library
         */

        public function careerfy_library_icons_list_callback() {
            $icons_groups = get_option('careerfy_icons_groups');
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $icon_value = isset($_POST['value']) ? $_POST['value'] : '';
            $icons_library = isset($_POST['icons_library']) ? $_POST['icons_library'] : 'default';
            $group_obj = $icons_groups[$icons_library];
            echo self::careerfy_icomoon_icons_box($group_obj['url'], $icons_library, $icon_value, $id, $name);
            wp_die();
        }

        public static function careerfy_icomoon_icons_box($selection_path = '', $icons_library = 'default', $icon_value = '', $id = '', $name = '') {
            global $careerfy_icons_form_fields;
            $careerfy_icons_careerfy_var_icomoon = '';

            $org_site_url = get_option('siteurl');
            $selection_path = str_replace($org_site_url, site_url(), $selection_path);

            if (preg_match('/www/', $_SERVER['HTTP_HOST'])) {
                if (!preg_match('/www/', $selection_path)) {
                    $bits = parse_url($selection_path);
                    $newHost = substr($bits["host"], 0, 4) !== "www." ? "www." . $bits["host"] : $bits["host"];
                    $selection_path = $bits["scheme"] . "://" . $newHost . (isset($bits["port"]) ? ":" . $bits["port"] : "") . $bits["path"] . (!empty($bits["query"]) ? "?" . $bits["query"] : "");
                }
            }


            $careerfy_icons_careerfy_var_icomoon .= '
                    <script>
                    jQuery(document).ready(function ($) {
                            var e9_element = $(\'#e9_element_' . esc_html($id) . '\').fontIconPicker({
                                    theme: \'fip-bootstrap\'
                            });
                            // Add the event on the button
                            $(\'#e9_buttons_' . esc_html($id) . ' button\').on(\'click\', function (e) {
                                    e.preventDefault();
                                    // Show processing message
                                    $(this).prop(\'disabled\', true).html(\'<i class="icon-cog demo-animate-spin"></i> Please wait\');
                                    $.ajax({
                                            url: "' . $selection_path . '/selection.json",
                                            type: \'GET\',
                                            dataType: \'json\'
                                    }).done(function (response) {
                                                    // Get the class prefix
                                                    var classPrefix = response.preferences.fontPref.prefix,
                                                        icomoon_json_icons = [],
                                                        icomoon_json_search = [];
                                                        $.each(response.icons, function (i, v) {
                                                        icomoon_json_icons.push(classPrefix + v.properties.name);
                                                        if (v.icon && v.icon.tags && v.icon.tags.length) {
                                                            icomoon_json_search.push(v.properties.name + \' \' + v.icon.tags.join(\' \'));
                                                        } else {
                                                            icomoon_json_search.push(v.properties.name);
                                                        }
                                                    });
                                                    // Set new fonts on fontIconPicker
                                                    e9_element.setIcons(icomoon_json_icons, icomoon_json_search);
                                                    // Show success message and disable
                                                    $(\'#e9_buttons_' . esc_html($id) . ' button\').removeClass(\'btn-primary\').addClass(\'btn-success\').text(\'icon load\').prop(\'disabled\', true);
                                    })
                                    .fail(function () {
                                                    // Show error message and enable
                                                    $(\'#e9_buttons_' . esc_html($id) . ' button\').removeClass(\'btn-primary\').addClass(\'btn-danger\').text(\'Try Again\').prop(\'disabled\', false);
                                    });
                                    e.stopPropagation();
                            });
                            jQuery("#e9_buttons_' . esc_html($id) . ' button").click();
                    });
                    </script>';
            
            $careerfy_icons_careerfy_var_icomoon .= '
            <input type="text" id="e9_element_' . esc_html($id) . '" name="' . $name . '" value="' . ($icon_value) . '" class="careerfy-icon-chose-input">
            <span id="e9_buttons_' . esc_html($id) . '" style="display:none">
                <button autocomplete="off" type="button" class="btn btn-primary">json load</button>
            </span>';

            return $careerfy_icons_careerfy_var_icomoon;
        }

    }

    global $careerfy_icons_fields;
    $careerfy_icons_fields = new Careerfy_Icons_Fields();
}