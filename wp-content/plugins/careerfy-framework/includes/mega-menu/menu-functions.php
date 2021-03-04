<?php

if (!class_exists('careerfy_mega_custom_menu')) {

    class careerfy_mega_custom_menu {
        /* --------------------------------------------*
         * Constructor
         * -------------------------------------------- */
        /**
         * Initializes the plugin by setting localization, 
         * filters, and administration functions.
         */
        function __construct() {
            // add custom menu fields to menu
            add_filter('wp_setup_nav_menu_item', array($this, 'careerfy_mega_add_custom_nav_fields'));
            // save menu custom fields
            add_action('wp_update_nav_menu_item', array($this, 'careerfy_mega_update_custom_nav_fields'), 10, 3);
            //
            add_filter('wp_nav_menu_item_custom_fields', array($this, 'menu_item_cus_fields'), 10, 4);
        }
        
        public function menu_item_cus_fields($id, $item, $depth, $args) {
            global $careerfy_form_fields;
            $item_id = $id;
            do_action('careerfy_mega_menu_cus_items_before', $item, $item_id);
            if (isset($item->menu_item_parent) && $item->menu_item_parent == '0') { ?>
                <p class="field-view description description-wide">
                    <label for="edit-menu-item-visifor-<?php echo absint($item_id); ?>">
                        <?php
                        $item_visidfor = get_post_meta($item_id, '_menu_item_visifor', true);
                        //var_dump($item);
                        ?>
                        <?php _e('Visible for', 'careerfy-frame'); ?><br/>
                        <select id="edit-menu-item-visifor-<?php echo absint($item_id); ?>"
                                class="widefat edit-menu-item-visifor"
                                name="menu-item-visifor[<?php echo absint($item_id); ?>]">
                            <option<?php echo($item->visifor == 'all' ? ' selected="selected"' : '') ?>
                                    value="all"><?php _e('For All', 'careerfy-frame'); ?></option>
                            <option<?php echo($item->visifor == 'candidate' ? ' selected="selected"' : '') ?>
                                    value="candidate"><?php _e('For Candidates', 'careerfy-frame'); ?></option>
                            <option<?php echo($item->visifor == 'employer' ? ' selected="selected"' : '') ?>
                                    value="employer"><?php _e('For Employers', 'careerfy-frame'); ?></option>
                        </select>
                    </label>
                </p>
            <?php } ?>
            <p class="field-custom description description-wide custom_onof">
                <label for="edit-menu-item-megamenu-<?php echo intval($item_id); ?>">
                    <?php _e('Active Mega Menu', 'careerfy-frame'); ?><br/>
                    <input type="checkbox" id="edit-menu-item-megamenu-<?php echo intval($item_id); ?>"
                           class="widefat code edit-menu-item-custom"
                           name="menu-item-megamenu[<?php echo intval($item_id); ?>]" <?php
                    if (esc_attr($item->megamenu) == 'on') {
                        echo 'checked="checked"';
                    }
                    ?>>
                </label>
            </p>
            <p class="field-view description description-wide">
                <label for="edit-menu-item-view-<?php echo absint($item_id); ?>">
                    <?php _e('Mega Menu View', 'careerfy-frame'); ?><br/>
                    <select id="edit-menu-item-view-<?php echo absint($item_id); ?>"
                            onchange="careerfy_menu_view_select(this.value, '<?php echo absint($item_id); ?>')"
                            class="widefat edit-menu-item-view" name="menu-item-view[<?php echo absint($item_id); ?>]">
                        <option<?php echo esc_attr($item->view) == 'image-text' ? ' selected="selected"' : '' ?>
                                value="image-text"><?php _e('Image Text', 'careerfy-frame'); ?></option>
                        <option<?php echo esc_attr($item->view) == 'video' ? ' selected="selected"' : '' ?>
                                value="video"><?php _e('Video', 'careerfy-frame'); ?></option>
                    </select>
                </label>
            </p>
            <p id="field-image-title-1-<?php echo absint($item_id); ?>"
               class="field-image-title description description-wide"
               style="display: <?php echo($item->view == 'image-text' ? 'block' : 'none') ?>;">
                <label for="edit-menu-item-image-title-<?php echo absint($item_id); ?>">
                    <?php _e('Paragraph Title', 'careerfy-frame'); ?><br/>
                    <input type="text" id="edit-menu-item-image-title-<?php echo absint($item_id); ?>"
                           class="widefat code edit-menu-item-image-title"
                           name="menu-item-image-title[<?php echo absint($item_id); ?>]"
                           value="<?php echo esc_attr($item->image_title); ?>"/>
                </label>
            </p>
            <p id="field-image-paragragh-<?php echo absint($item_id); ?>"
               class="field-image-paragragh description description-wide"
               style="display: <?php echo($item->view == 'image-text' ? 'block' : 'none') ?>;">
                <label for="edit-menu-item-image-paragragh-<?php echo absint($item_id); ?>">
                    <?php _e('Paragraph Text', 'careerfy-frame'); ?><br/>
                    <textarea id="edit-menu-item-image-paragragh-<?php echo absint($item_id); ?>"
                              class="widefat code edit-menu-item-image-paragragh"
                              name="menu-item-image-paragragh[<?php echo absint($item_id); ?>]"><?php echo esc_attr($item->image_paragragh); ?></textarea>
                </label>
            </p>
            <p id="field-image-title-2-<?php echo absint($item_id); ?>"
               class="field-image-title-2 description description-wide"
               style="display: <?php echo($item->view == 'image-text' ? 'block' : 'none') ?>;">
                <label for="edit-menu-item-image-title-2-<?php echo absint($item_id); ?>">
                    <?php _e('Image Title', 'careerfy-frame'); ?><br/>
                    <input type="text" id="edit-menu-item-image-title-2-<?php echo absint($item_id); ?>"
                           class="widefat code edit-menu-item-image-title-2"
                           name="menu-item-image-title-2[<?php echo absint($item_id); ?>]"
                           value="<?php echo esc_attr($item->image_title_2); ?>"/>
                </label>
            </p>
            <div id="field-image-img-<?php echo absint($item_id); ?>"
                 class="field-image-img description description-wide"
                 style="display: <?php echo($item->view == 'image-text' ? 'block' : 'none') ?>;">
                <label for="edit-menu-item-image-img-<?php echo absint($item_id); ?>">
                    <?php _e('Image', 'careerfy-frame'); ?><br/>
                    <?php
                    $field_params = array(
                        'id' => 'edit-menu-item-image-img-' . $item_id,
                        'cus_name' => 'menu-item-image-img[' . absint($item_id) . ']',
                        'force_std' => $item->image_img,
                    );
                    $careerfy_form_fields->image_upload_field($field_params);
                    ?>
                </label>
            </div>
            <p id="fields-video-<?php echo absint($item_id); ?>" class="field-video description description-wide"
               style="display: <?php echo($item->view == 'video' ? 'block' : 'none') ?>;">
                <label for="edit-menu-item-video-title-<?php echo absint($item_id); ?>">
                    <?php _e('Video URL', 'careerfy-frame'); ?><br/>
                    <input type="text" id="edit-menu-item-video-title-<?php echo absint($item_id); ?>"
                           class="widefat code edit-menu-item-video-title"
                           name="menu-item-video[<?php echo absint($item_id); ?>]"
                           value="<?php echo esc_url($item->video); ?>"/>
                </label>
            </p>
            <?php
            do_action('careerfy_mega_menu_cus_items_after', $item, $item_id);
        }

        /**
         * Add custom fields to $item nav object
         * in order to be used in custom Walker
         * @access      public
         * @return      void
         */
        function careerfy_mega_add_custom_nav_fields($menu_item) {
            $menu_item->megamenu = get_post_meta($menu_item->ID, '_menu_item_megamenu', true);
            $menu_item->view = get_post_meta($menu_item->ID, '_menu_item_view', true);
            $menu_item->video = get_post_meta($menu_item->ID, '_menu_item_video', true);
            $menu_item->image_title = get_post_meta($menu_item->ID, '_menu_item_image_title', true);
            $menu_item->image_paragragh = get_post_meta($menu_item->ID, '_menu_item_image_paragragh', true);
            $menu_item->image_title_2 = get_post_meta($menu_item->ID, '_menu_item_image_title_2', true);
            $menu_item->image_img = get_post_meta($menu_item->ID, '_menu_item_image_img', true);
            $menu_item->visifor = get_post_meta($menu_item->ID, '_menu_item_visifor', true);
            $menu_item = apply_filters('careerfy_mega_add_custom_nav_fields_filtr', $menu_item);
            return $menu_item;
        }

        /**
         * Save menu custom fields
         * @access      public
         * @return      void
         */
        function careerfy_mega_update_custom_nav_fields($menu_id, $menu_item_db_id, $args) {
            // Check if element is properly sent
            $megamenu_value = 'off';
            $view_value = 'image-text';

            if (isset($_POST['menu-item-megamenu'][$menu_item_db_id])) {
                $megamenu_value = $_POST['menu-item-megamenu'][$menu_item_db_id];
            } else {
                $megamenu_value = 'off';
            }

            if (isset($_POST['menu-item-view'][$menu_item_db_id])) {
                $view_value = $_POST['menu-item-view'][$menu_item_db_id];
            } else {
                $view_value = 'image-text';
            }

            if (isset($_POST['menu-item-video'][$menu_item_db_id])) {
                $video_value = $_POST['menu-item-video'][$menu_item_db_id];
            } else {
                $video_value = '';
            }

            if (isset($_POST['menu-item-image-title'][$menu_item_db_id])) {
                $image_title_value = $_POST['menu-item-image-title'][$menu_item_db_id];
            } else {
                $image_title_value = '';
            }

            if (isset($_POST['menu-item-image-paragragh'][$menu_item_db_id])) {
                $image_title_para_value = $_POST['menu-item-image-paragragh'][$menu_item_db_id];
            } else {
                $image_title_para_value = '';
            }

            if (isset($_POST['menu-item-image-title-2'][$menu_item_db_id])) {
                $image_title_2_value = $_POST['menu-item-image-title-2'][$menu_item_db_id];
            } else {
                $image_title_2_value = '';
            }

            if (isset($_POST['menu-item-image-img'][$menu_item_db_id])) {
                $image_img_value = $_POST['menu-item-image-img'][$menu_item_db_id];
            } else {
                $image_img_value = '';
            }

            if (isset($_POST['menu-item-visifor'][$menu_item_db_id])) {
                $menu_item_visifor = $_POST['menu-item-visifor'][$menu_item_db_id];
            } else {
                $menu_item_visifor = 'all';
            }

            update_post_meta($menu_item_db_id, '_menu_item_megamenu', sanitize_text_field($megamenu_value));
            update_post_meta($menu_item_db_id, '_menu_item_view', sanitize_text_field($view_value));
            update_post_meta($menu_item_db_id, '_menu_item_video', $video_value);
            update_post_meta($menu_item_db_id, '_menu_item_image_title', $image_title_value);
            update_post_meta($menu_item_db_id, '_menu_item_image_paragragh', $image_title_para_value);
            update_post_meta($menu_item_db_id, '_menu_item_image_title_2', $image_title_2_value);
            update_post_meta($menu_item_db_id, '_menu_item_image_img', $image_img_value);
            update_post_meta($menu_item_db_id, '_menu_item_visifor', $menu_item_visifor);
            do_action('careerfy_mega_menu_items_save', $menu_item_db_id);
        }
    }
}

// instantiate plugin's class
$careerfy_mega_custom_menu = new careerfy_mega_custom_menu();