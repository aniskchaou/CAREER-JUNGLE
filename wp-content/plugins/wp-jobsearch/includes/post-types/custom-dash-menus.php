<?php
/**
 * @Manage Columns
 * @return
 *
 */
if (!class_exists('jobsearch_custom_dash_menu')) {

    class jobsearch_custom_dash_menu {

        // The Constructor
        public function __construct() {
            // Adding columns
            add_filter('manage_dashb_menu_posts_columns', array($this, 'columns_add'));
            add_action('manage_dashb_menu_posts_custom_column', array($this, 'custom_column'), 10, 2);

            add_action('init', array($this, 'post_type_register'), 1); // post type register

            add_action('admin_head', array($this, 'admin_custom_styles'));

            add_action('add_meta_boxes', array($this, 'settings_meta_boxes'));
            
            add_action('save_post', array($this, 'dashb_menu_save'), 99, 3);

            //
            add_filter('jobsearch_dashboard_tab_content_ext', array($this, 'dashboard_tab_content_add'), 10, 2);
            
            //
            add_action('init', array($this, 'set_post_type_enable_vc'));
        }

        function admin_custom_styles() {
            $output_css = '<style type="text/css"> 
                .column-menu_type { min-width:200px !important; max-width:500px !important; overflow:hidden }
            </style>';
            echo $output_css;
        }

        public function post_type_register() {

            $labels = array(
                'name' => _x('Dashboard Menus', 'post type general name', 'wp-jobsearch'),
                'singular_name' => _x('Dashboard Menu', 'post type singular name', 'wp-jobsearch'),
                'menu_name' => _x('Dashboard Menus', 'admin menu', 'wp-jobsearch'),
                'name_admin_bar' => _x('Dashboard Menu', 'add new on admin bar', 'wp-jobsearch'),
                'add_new' => _x('Add New', 'dashboard menu', 'wp-jobsearch'),
                'add_new_item' => __('Add New Dashboard Menu', 'wp-jobsearch'),
                'new_item' => __('New Dashboard Menu', 'wp-jobsearch'),
                'edit_item' => __('Edit Dashboard Menu', 'wp-jobsearch'),
                'view_item' => __('View Dashboard Menu', 'wp-jobsearch'),
                'all_items' => __('All Dashboard Menus', 'wp-jobsearch'),
                'search_items' => __('Search Dashboard Menus', 'wp-jobsearch'),
                'parent_item_colon' => __('Parent Dashboard Menus:', 'wp-jobsearch'),
                'not_found' => __('No dashboard menus found.', 'wp-jobsearch'),
                'not_found_in_trash' => __('No dashboard menus found in Trash.', 'wp-jobsearch')
            );

            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'wp-jobsearch'),
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => false,
                'capability_type' => 'post',
                'has_archive' => false,
                'exclude_from_search' => true,
                'hierarchical' => false,
                'supports' => array('title', 'editor')
            );

            register_post_type('dashb_menu', apply_filters('jobsearch_reg_post_type_cust_menu_args', $args));
        }

        public function columns_add($columns) {
            global $sitepress;
            if (isset($columns['date'])) {
                unset($columns['date']);
            }
            $columns['menu_type'] = esc_html('Type', 'wp-jobsearch');
            $columns['user_type'] = esc_html('For User', 'wp-jobsearch');
            $columns['date'] = esc_html('Date', 'wp-jobsearch');
            return $columns;
        }

        public function custom_column($column) {
            global $post;
            $_post_id = $post->ID;
            switch ($column) {
                case 'menu_type' :
                    $cusmenu_type = get_post_meta($_post_id, 'jobsearch_field_menu_type', true);
                    if ($cusmenu_type == 'url') {
                        esc_html_e('External URL', 'wp-jobsearch');
                    } else {
                        esc_html_e('Inner Content', 'wp-jobsearch');
                    }
                    break;
                case 'user_type' :
                    $cusmenu_type = get_post_meta($_post_id, 'jobsearch_field_menu_user_type', true);
                    if ($cusmenu_type == 'emp') {
                        esc_html_e('For Employer', 'wp-jobsearch');
                    } else if ($cusmenu_type == 'both') {
                        esc_html_e('For both', 'wp-jobsearch');
                    } else {
                        esc_html_e('For Candidate', 'wp-jobsearch');
                    }
                    break;
            }
        }
        
        public function set_post_type_enable_vc() {
            global $wpdb, $pagenow, $post;
            
            $db_opt_name = $wpdb->prefix . 'user_roles';
            $user_roles = get_option($db_opt_name);
            $user_roles = maybe_unserialize($user_roles);
            if (!isset($user_roles['administrator']['capabilities']['vc_access_rules_post_types/dashb_menu'])
                || (isset($user_roles['administrator']['capabilities']['vc_access_rules_post_types/dashb_menu']) && $user_roles['administrator']['capabilities']['vc_access_rules_post_types/dashb_menu'] == false)) {
                $user_roles['administrator']['capabilities']['vc_access_rules_post_types/dashb_menu'] = true;
                update_option($db_opt_name, $user_roles);
            }
        }
        
        public function dashb_menu_save($post_id, $post, $update) {
            global $wpdb, $pagenow;

            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            $post_type = '';
            if ($pagenow == 'post.php') {
                $post_type = get_post_type();
            }
            if (isset($_REQUEST)) {
                if ($post_type == 'dashb_menu') {
                    
                    $jobsearch__options = get_option('jobsearch_plugin_options');
                    $cand_dashpages_arr = isset($jobsearch__options['cand_dashbord_menu']) ? $jobsearch__options['cand_dashbord_menu'] : '';
                    $cand_dashpages_arr = !empty($cand_dashpages_arr) ? $cand_dashpages_arr : array();
                    $emp_dashpages_arr = isset($jobsearch__options['emp_dashbord_menu']) ? $jobsearch__options['emp_dashbord_menu'] : '';
                    $emp_dashpages_arr = !empty($emp_dashpages_arr) ? $emp_dashpages_arr : array();
                    
                    $post_ids_query = "SELECT ID,post_name FROM $wpdb->posts AS posts";
                    $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
                    $cusmenu_post_ids = $wpdb->get_results($post_ids_query, 'ARRAY_A');
                    if (!empty($cusmenu_post_ids)) {
                        foreach ($cusmenu_post_ids as $dashbmenu_arr) {
                            $dashbmenu_id = $dashbmenu_arr['ID'];
                            $dashbmenu_slug = $dashbmenu_arr['post_name'];
                            $cusmenu_user_type = get_post_meta($dashbmenu_id, 'jobsearch_field_menu_user_type', true);
                            if ($cusmenu_user_type == 'cand' && !isset($cand_dashpages_arr[$dashbmenu_slug])) {
                                $cand_dashpages_arr[$dashbmenu_slug] = true;
                            } else if ($cusmenu_user_type == 'emp' && !isset($emp_dashpages_arr[$dashbmenu_slug])) {
                                $emp_dashpages_arr[$dashbmenu_slug] = true;
                            } else if ($cusmenu_user_type == 'both') {
                                if (!isset($cand_dashpages_arr[$dashbmenu_slug])) {
                                    $cand_dashpages_arr[$dashbmenu_slug] = true;
                                }
                                if (!isset($emp_dashpages_arr[$dashbmenu_slug])) {
                                    $emp_dashpages_arr[$dashbmenu_slug] = true;
                                }
                            }
                        }
                    }
                }
            }
        }

        public function settings_meta_boxes() {
            add_meta_box('jobsearch-dashbmenu-settings', esc_html__('Menu Settings', 'wp-jobsearch'), array($this, 'meta_box_callback'), 'dashb_menu', 'normal');
        }

        public function meta_box_callback() {
            global $post, $jobsearch_form_fields, $jobsearch_plugin_options;

            $_post_id = $post->ID;
            $cusmenu_type = get_post_meta($_post_id, 'jobsearch_field_menu_type', true);
            ?>
            <div class="jobsearch-post-settings">
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Menu Icon', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field" style="width: 55%;">
                        <?php
                        $field_random = rand(1000000, 9999999);
                        $field_icon_arr = get_post_meta($_post_id, 'jobsearch_field_dashmenu_icon', true);

                        $field_icon = isset($field_icon_arr['icon']) ? $field_icon_arr['icon'] : '';
                        $field_icon_group = isset($field_icon_arr['icon_group']) ? $field_icon_arr['icon_group'] : '';
                        if (class_exists('Careerfy_Icons_Fields')) {
                            echo Careerfy_Icons_Fields::careerfy_icons_fields_callback($field_icon, $field_random, 'jobsearch_field_dashmenu_icon[icon]', $field_icon_group, 'jobsearch_field_dashmenu_icon[icon_group]');
                        } else {
                            echo jobsearch_icon_picker($field_icon, $field_random, 'jobsearch_field_dashmenu_icon[icon]', 'jobsearch-icon-pickerr');
                            ?>
                            <input type="hidden" name="jobsearch_field_dashmenu_icon[icon_group]" value="<?php echo ($field_icon_group) ?>">
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Menu Type', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'menu_type',
                            'options' => array(
                                'content' => esc_html__('Inner Content', 'wp-jobsearch'),
                                'url' => esc_html__('External Link', 'wp-jobsearch'),
                            ),
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                    <script>
                    jQuery(document).on('change', 'select[name="jobsearch_field_menu_type"]', function() {
                        var _this = jQuery(this);
                        if (_this.val() == 'url') {
                            jQuery('#external-url-maincon').slideDown();
                        } else {
                            jQuery('#external-url-maincon').slideUp();
                        }
                    });
                    </script>
                </div>
                <div id="external-url-maincon" class="jobsearch-element-field"<?php echo ($cusmenu_type != 'url' ? ' style="display: none;"' : '') ?>>
                    <div class="elem-label">
                        <label><?php esc_html_e('External URL', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'menu_exturl',
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('User Type', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $user_types_arr = array(
                            'cand' => esc_html__('For Candidate', 'wp-jobsearch'),
                            'emp' => esc_html__('For Employer', 'wp-jobsearch'),
                            'both' => esc_html__('For Both', 'wp-jobsearch'),
                        );
                        $field_params = array(
                            'name' => 'menu_user_type',
                            'options' => apply_filters('jobsearch_post_bk_dashmenus_meta_usertypes', $user_types_arr),
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }

        private static function dashboard_tab_before_html($the_post) {
            $post_id = isset($the_post->ID) ? $the_post->ID : '';
            $post_name = isset($the_post->post_name) ? $the_post->post_name : '';
            $before_html = '<div id="dashboard-tab-' . $post_name . '-' . $post_id . '" class="main-tab-section"><div class="jobsearch-employer-dasboard"><div class="jobsearch-employer-box-section">';
            $before_html .= '<div class="jobsearch-profile-title"><h2>' . ($the_post->post_title) . '</h2></div>';
            return $before_html;
        }

        private static function dashboard_tab_after_html($the_post) {
            $after_html = '</div></div></div>';
            return $after_html;
        }

        public function dashboard_tab_content_add($html = '', $get_tab = '') {
            global $wpdb;

            $user_id = get_current_user_id();
            $is_employer = jobsearch_user_is_employer($user_id);
            $is_candidate = jobsearch_user_is_candidate($user_id);

            $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
            $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
            
            ob_start();

            $cust_dashpages_arr = $wpdb->get_col($post_ids_query);
            if (!empty($cust_dashpages_arr)) {
                foreach ($cust_dashpages_arr as $cust_dashpage_id) {
                    $the_page = get_post($cust_dashpage_id);
                    if (isset($the_page->post_name)) {
                        $menu_post_name = $the_page->post_name;
                        $cusmenu_for_user = get_post_meta($cust_dashpage_id, 'jobsearch_field_menu_user_type', true);
                        $cusmenu_type = get_post_meta($cust_dashpage_id, 'jobsearch_field_menu_type', true);
                        $menu_post_name = urldecode($menu_post_name);
                        if ($cusmenu_type == 'content' && $get_tab == 'cust-' . $menu_post_name) {
                            ob_start();
                            $menu_post_content = $the_page->post_content;
                            echo apply_filters('the_content', $menu_post_content);
                            $menu_post_content = ob_get_clean();
                            if ($cusmenu_for_user == 'emp' && $is_employer) {
                                echo self::dashboard_tab_before_html($the_page);
                                echo ($menu_post_content);
                                echo self::dashboard_tab_after_html($the_page);
                            } else if ($cusmenu_for_user == 'cand' && $is_candidate) {
                                echo self::dashboard_tab_before_html($the_page);
                                echo ($menu_post_content);
                                echo self::dashboard_tab_after_html($the_page);
                            } else if ($cusmenu_for_user == 'both') {
                                echo self::dashboard_tab_before_html($the_page);
                                echo ($menu_post_content);
                                echo self::dashboard_tab_after_html($the_page);
                            }
                            $before_html = self::dashboard_tab_before_html($the_page);
                            $after_html = self::dashboard_tab_after_html($the_page);
                            echo apply_filters('jobsearch_dashmenus_dash_pthe_content_after', '', $menu_post_content, $cusmenu_for_user, $before_html, $after_html);
                        }
                    }
                }
            }
            $html .= ob_get_clean();
            
            return $html;
        }

    }

    return new jobsearch_custom_dash_menu();
}
