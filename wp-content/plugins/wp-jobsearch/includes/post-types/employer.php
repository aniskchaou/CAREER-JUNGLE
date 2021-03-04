<?php
/**
 * @Manage Columns
 * @return
 *
 */
if (!class_exists('post_type_employer')) {

    class post_type_employer {

        // The Constructor
        public function __construct() {
            // Adding columns

            add_action('admin_footer', array($this, 'change_featimg_meta_title'));
            add_action('admin_footer', array($this, 'employers_list_js'));
            add_filter('manage_employer_posts_columns', array($this, 'jobsearch_employer_columns_add'));
            add_action('manage_employer_posts_custom_column', array($this, 'jobsearch_employer_columns'), 10, 2);
            add_filter('list_table_primary_column', array($this, 'jobsearch_primary_column'), 10, 2);
            add_action('init', array($this, 'jobsearch_employer_register'), 1); // post type register
            
            add_action('admin_init', array($this, 'update_emps_total_jobs'));

            add_filter('post_row_actions', array($this, 'jobsearch_employer_row_actions'));
            add_filter('manage_edit-employer_sortable_columns', array($this, 'jobsearch_employer_sortable_columns'));
            add_filter('request', array($this, 'jobsearch_employer_sort_columns'));
            add_action('admin_head', array($this, 'my_admin_custom_styles'));
            //add_action('init', array($this, 'jobsearch_employer_tags'), 0);
            //
            add_action('views_edit-employer', array($this, 'modified_views_so'), 0);
            add_filter('parse_query', array($this, 'employers_query_filter'), 11, 1);
            add_filter('bulk_actions-edit-employer', array($this, 'custom_job_filters'));
            add_action('handle_bulk_actions-edit-employer', array($this, 'jobs_bulk_actions_handle'), 10, 3);

            add_action('wp_ajax_jobsearch_calc_employers_posted_jobs_bklist', array($this, 'emp_jobs_calc_in_column'));
        }

        function my_admin_custom_styles() {
            global $pagenow;
            if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'employer') {
                $output_css = '<style type="text/css"> 
                    .column-employer_title { min-width:150px !important; max-width:300px !important; overflow:hidden; }
                    .column-location { min-width:150px !important; max-width:300px !important; overflow:hidden; }
                    .post-type-employer .column-featured { width:50px !important; overflow:hidden; } 
                    .post-type-employer .column-posted_jobs { width:108px !important; overflow:hidden; } 
                    .column-status { width:30px !important; overflow:hidden; }
                    .column-action { text-align:right !important; width:150px !important; overflow:hidden; }
                </style>';
                echo $output_css;
            }
        }

        public function jobsearch_employer_register() {

            $jobsearch__options = get_option('jobsearch_plugin_options');

            $employer_slug = isset($jobsearch__options['employer_rewrite_slug']) && $jobsearch__options['employer_rewrite_slug'] != '' ? $jobsearch__options['employer_rewrite_slug'] : 'employer';

            $labels = array(
                'name' => _x('Employers', 'post type general name', 'wp-jobsearch'),
                'singular_name' => _x('Employer', 'post type singular name', 'wp-jobsearch'),
                'menu_name' => _x('Employers', 'admin menu', 'wp-jobsearch'),
                'name_admin_bar' => _x('Employer', 'add new on admin bar', 'wp-jobsearch'),
                'add_new' => _x('Add New', 'employer', 'wp-jobsearch'),
                'add_new_item' => __('Add New Employer', 'wp-jobsearch'),
                'new_item' => __('New Employer', 'wp-jobsearch'),
                'edit_item' => __('Edit Employer', 'wp-jobsearch'),
                'view_item' => __('View Employer', 'wp-jobsearch'),
                'all_items' => __('All Employers', 'wp-jobsearch'),
                'featured_image' => __('Company logo', 'ritefoodies'),
                'set_featured_image' => __('Set company logo', 'ritefoodies'),
                'remove_featured_image' => __('Remove company logo', 'ritefoodies'),
                'use_featured_image' => __('Use as company logo', 'ritefoodies'),
                'search_items' => __('Search Employers', 'wp-jobsearch'),
                'parent_item_colon' => __('Parent Employers:', 'wp-jobsearch'),
                'not_found' => __('No employers found.', 'wp-jobsearch'),
                'not_found_in_trash' => __('No employers found in Trash.', 'wp-jobsearch')
            );

            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'wp-jobsearch'),
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => $employer_slug),
                'capability_type' => 'post',
                'has_archive' => false,
                'exclude_from_search' => true,
                'hierarchical' => false,
                //'menu_position' => 26,
                'supports' => array('title', 'editor', 'excerpt', 'thumbnail')
            );

			$args = apply_filters('jobsearch_reg_post_type_emp_args', $args);
            register_post_type('employer', $args);
        }

        public function change_featimg_meta_title() {
            global $pagenow;
            $post_type = '';

            if ($pagenow == 'post.php') {
                $post_id = isset($_GET['post']) ? $_GET['post'] : '';
                $post_obj = get_post($post_id);
                $post_type = isset($post_obj->post_type) ? $post_obj->post_type : '';
            }
        }

        public function employers_list_js() {
            global $pagenow, $wpdb;
            $post_type = '';

            if ($pagenow == 'edit.php') {
                $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
            }
            if ($post_type == 'employer') {
                ?>
                <script>
                    jQuery(document).ready(function () {
                        var _this_form = jQuery('#posts-filter');
                        var _post_check_ids = _this_form.find('input[type=checkbox][name^="post"]');

                        var _emplyers_ids = [];
                        if (_post_check_ids.length > 0) {
                            jQuery.each(_post_check_ids, function (_ind, _elm) {
                                var _emp_id = jQuery(this).attr('value');
                                _emplyers_ids.push(_emp_id);
                            });
                        }

                        _emplyers_ids = _emplyers_ids.join();

                        var _all_post_data = {
                            employer_ids: _emplyers_ids,
                            action: 'jobsearch_calc_employers_posted_jobs_bklist'
                        }
                        var _emp_request = jQuery.ajax({
                            url: ajaxurl,
                            method: "POST",
                            data: _all_post_data,
                            dataType: "json"
                        });
                        _emp_request.done(function (response) {
                            console.log(response.msg);
                            if ('undefined' !== response.emp_list && response.emp_list) {
                                jQuery.each(response.emp_list, function (_indx, _elem) {
                                    //console.log(_indx + ' : ' + _elem);
                                    jQuery('#emp-actjobs-' + _indx).html(_elem);
                                });
                            }
                        });
                    });
                </script>
                <?php
            }
        }

        public function emp_jobs_calc_in_column() {
            $emp_ids = isset($_POST['employer_ids']) ? $_POST['employer_ids'] : '';

            $emps_list = array();
            $msg = 'No employer found.';
            if ($emp_ids != '') {
                $emp_ids = explode(',', $emp_ids);

                if (!empty($emp_ids)) {
                    foreach ($emp_ids as $employer_id) {
                        //
                        $total_jobs = get_post_meta($employer_id, 'jobsearch_emp_totl_jobs', true);
                        $emps_list[$employer_id] = absint($total_jobs);
                    }
                    $msg = 'Calculated';
                }
            }

            echo json_encode(array('emp_list' => $emps_list, 'msg' => $msg));
            die;
        }

        public function jobsearch_employer_row_actions($actions) {
            if ('employer' == get_post_type()) {
                return array();
            }
            return $actions;
        }

        public function custom_job_filters($actions) {
            if (is_array($actions)) {
                $actions['approved'] = esc_html__('Approved', 'wp-jobsearch');
                $actions['pending'] = esc_html__('Pending', 'wp-jobsearch');
            }
            return $actions;
        }

        function jobs_bulk_actions_handle($redirect_to, $doaction, $post_ids) {
            if ($doaction == 'approved' || $doaction == 'pending') {
                if (!empty($post_ids)) {
                    foreach ($post_ids as $employer_id) {
                        $user_aproved = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);
                        if ($user_aproved != 'on') {
                            $user_id = get_post_meta($employer_id, 'jobsearch_user_id', true);
                            $user_obj = get_user_by('ID', $user_id);
                            if (isset($user_obj->ID)) {
                                do_action('jobsearch_profile_approval_to_employer', $user_obj);
                            }
                        }
                        
                        $do_save = $doaction == 'approved' ? 'on' : '';
                        update_post_meta($employer_id, 'jobsearch_field_employer_approved', $do_save);
                        if ($doaction == 'approved') {
                            // Employer jobs status change according his/her status
                            do_action('jobsearch_employer_update_jobs_status', $employer_id);
                        }
                    }
                }
            }
            return $redirect_to;
        }

        public function employers_query_filter($query) {
            global $pagenow;

            $custom_filter_arr = array();
            if (is_admin() && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'employer' && isset($_GET['employer_status']) && $_GET['employer_status'] != '') {
                if ($_GET['employer_status'] == 'approved') {
                    $custom_filter_arr[] = array(
                        'key' => 'jobsearch_field_employer_approved',
                        'value' => 'on',
                        'compare' => '=',
                    );
                } else {
                    $custom_filter_arr[] = array(
                        'key' => 'jobsearch_field_employer_approved',
                        'value' => 'on',
                        'compare' => '!=',
                    );
                }
            }
            if (!empty($custom_filter_arr)) {
                $query->set('meta_query', $custom_filter_arr);
            }
        }

        public function modified_views_so($views) {

            remove_filter('parse_query', array(&$this, 'employers_query_filter'), 11, 1);
            $args = array(
                'post_type' => 'employer',
                'posts_per_page' => '1',
                'post_status' => 'publish',
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_employer_approved',
                        'value' => 'on',
                        'compare' => '!=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            $pending_jobs = $jobs_query->found_posts;
            wp_reset_postdata();

            $args = array(
                'post_type' => 'employer',
                'posts_per_page' => '1',
                'post_status' => 'publish',
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_employer_approved',
                        'value' => 'on',
                        'compare' => '=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            $approve_jobs = $jobs_query->found_posts;
            wp_reset_postdata();

            $views['approved'] = '<a href="edit.php?post_type=employer&employer_status=approved">' . esc_html__('Approved', 'wp-jobsearch') . '</a> (' . absint($approve_jobs) . ')';
            $views['pending'] = '<a href="edit.php?post_type=employer&employer_status=pending">' . esc_html__('Pending', 'wp-jobsearch') . '</a> (' . absint($pending_jobs) . ')';

            return $views;
        }

        public function jobsearch_employer_columns_add($columns) {
            global $sitepress;
            $new_columns = array();
            $new_columns['cb'] = '<input type="checkbox" />';
            $new_columns['employer_title'] = esc_html__('Employer', 'wp-jobsearch');
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $languages = icl_get_languages('skip_missing=0&orderby=title');
                if (is_array($languages) && sizeof($languages) > 0) {
                    $wpml_options = get_option('icl_sitepress_settings');
                    $default_lang = isset($wpml_options['default_language']) ? $wpml_options['default_language'] : '';
                    $flags_html = '';
                    foreach ($languages as $lang_code => $language) {
                        if ($default_lang == $lang_code) {
                            continue;
                        }
                        $flag_url = ICL_PLUGIN_URL . '/res/flags/' . $lang_code . '.png';
                        $flags_html .= '<img src="' . $flag_url . '" width="18" height="12" alt="' . (isset($language['translated_name']) ? $language['translated_name'] : '') . '" title="' . (isset($language['translated_name']) ? $language['translated_name'] : '') . '" style="margin:2px">';
                    }
                    $new_columns['icl_translations'] = $flags_html;
                }
            }
            $new_columns['location'] = esc_html__('Location', 'wp-jobsearch');
            $new_columns['posted_jobs'] = esc_html__('Posted Jobs', 'wp-jobsearch');
            $new_columns['featured'] = force_balance_tags('<strong class="jobsearch-tooltip" title="' . esc_html__('Featured', 'wp-jobsearch') . '"><i class="dashicons dashicons-star-filled"></i></strong>');
            $new_columns['status'] = force_balance_tags('<strong class="jobsearch-tooltip" title="' . esc_html__('Status', 'wp-jobsearch') . '"><i class="dashicons dashicons-info"></i></strong>');
            $new_columns['action'] = esc_html__('Action', 'wp-jobsearch');
            //return array_merge($columns, $new_columns);
            return apply_filters('jobsearch_emp_post_bk_admin_columns', $new_columns);
        }

        public function jobsearch_employer_columns($column) {
            global $post;
            $_post_id = $post->ID;
            $employer_user_id = get_post_meta($_post_id, 'jobsearch_user_id', true);
            switch ($column) {
                case 'employer_title' :
                    echo '<div class="employer_position">';

                    $src = '';
                    if (has_post_thumbnail($post->ID)) {
                        $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
                        $src = isset($src[0]) ? $src[0] : '';
                    }
                    if ($src != '') {
                        echo '<div class="company-logo">';
                        echo '<img src="' . esc_attr($src) . '" alt="' . esc_attr(get_the_title($post->ID)) . '" />';
                        echo '</div>';
                        // Before 1.24.0, logo URLs were stored in post meta.
                    }

                    echo '<a href="' . admin_url('post.php?post=' . $post->ID . '&action=edit') . '" class="employer_title" class="jobsearch-tooltip" title="' . sprintf(__('ID: %d', 'wp-jobsearch'), $post->ID) . '">' . ucfirst(get_the_title($post->ID)) . '</a>';

                    echo '<div class="sector-list">';
                    $employertype_list = get_the_term_list($post->ID, 'sector', '', ',', '');
                    if ($employertype_list) {
                        printf('%1$s', $employertype_list);
                    }
                    echo '</div>';
                    
                    if (class_exists('w357LoginAsUser')) {
                        $w357LoginAsUser = new w357LoginAsUser;
                        $user_obj = get_user_by('ID', $employer_user_id);
                        if (isset($user_obj->ID)) {

                            $the_user_obj = new WP_User($employer_user_id);
                            $login_as_user_url = $w357LoginAsUser->build_the_login_as_user_url($the_user_obj);
                            $login_as_link = '<a class="button w357-login-as-user-btn" href="' . esc_url($login_as_user_url) . '" title="'.esc_html__('Login as', 'login-as-user').': ' . $w357LoginAsUser->login_as_type($the_user_obj, false) . '"><span class="dashicons dashicons-admin-users"></span> '.esc_html__('Login as', 'login-as-user').': <strong>' . $w357LoginAsUser->login_as_type($the_user_obj) . '</strong></a>';
                            echo ($login_as_link);
                        }
                    }

                    echo '</div>';
                    break;
                
                case 'location' :
                    $locat_str = '';
                    $location1 = get_post_meta($post->ID, 'jobsearch_field_location_location1', true);
                    $location2 = get_post_meta($post->ID, 'jobsearch_field_location_location2', true);
                    $location3 = get_post_meta($post->ID, 'jobsearch_field_location_location3', true);
                    $location4 = get_post_meta($post->ID, 'jobsearch_field_location_location4', true);
                    $full_addrs = get_post_meta($post->ID, 'jobsearch_field_location_address', true);
                    if ($location1 != '') {
                        $location1 = ucfirst(str_replace(array("-", "_"), array(" ", " "), $location1));
                        $locat_str .= $location1;
                    }
                    if ($location2 != '') {
                        $locat_str .= $locat_str != '' ? ' | ' : '';
                        $location2 = ucfirst(str_replace(array("-", "_"), array(" ", " "), $location2));
                        $locat_str .= $location2;
                    }
                    if ($location3 != '') {
                        $locat_str .= $locat_str != '' ? ' | ' : '';
                        $location3 = ucfirst(str_replace(array("-", "_"), array(" ", " "), $location3));
                        $locat_str .= $location3;
                    }
                    if ($location4 != '') {
                        $locat_str .= $locat_str != '' ? ' | ' : '';
                        $location4 = ucfirst(str_replace(array("-", "_"), array(" ", " "), $location4));
                        $locat_str .= $location4;
                    }
                    if ($full_addrs != '') {
                        $locat_str .= $locat_str != '' ? ' | ' : '';
                        $locat_str .= $full_addrs;
                    }
                    
                    echo jobsearch_esc_html($locat_str);
                    break;
                case 'posted_jobs' :
                    echo '<div id="emp-actjobs-' . $post->ID . '" data-id="' . $post->ID . '" class="emp-activejobs-span"><span class="spinner is-active"></span></div>';
                    break;
                case 'featured' :
                    $att_promote_pckg = get_post_meta($post->ID, 'att_promote_profile_pkgorder', true);
                    // form backend
                    $mber_feature_bk = get_post_meta($post->ID, '_feature_mber_frmadmin', true);

                    $show_badge = false;

                    if ($att_promote_pckg != '' && !jobsearch_promote_profile_pkg_is_expired($att_promote_pckg)) {
                        $show_badge = true;
                    }

                    if ($mber_feature_bk == 'yes') {
                        $show_badge = true;
                    } else if ($mber_feature_bk == 'no') {
                        $show_badge = false;
                    }
                    if ($show_badge) {
                        echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" data-option="un-feature" data-employerid="' . esc_attr($post->ID) . '" title="' . esc_html__('No', 'wp-jobsearch') . '"><i class="dashicons dashicons-star-filled" aria-hidden="true"></i></a>');
                    } else {
                        echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" data-option="featured" data-employerid="' . esc_attr($post->ID) . '" title="' . esc_html__('Yes', 'wp-jobsearch') . '"><i class="dashicons dashicons-star-empty" aria-hidden="true"></i></a>');
                    }
                    break;
                case 'status' :
                    global $jobsearch_plugin_options;
                    $approved_color = isset($jobsearch_plugin_options['jobsearch-approved-color']) ? $jobsearch_plugin_options['jobsearch-approved-color'] : '';
                    $pending_color = isset($jobsearch_plugin_options['jobsearch-pending-color']) ? $jobsearch_plugin_options['jobsearch-pending-color'] : '';
                    $canceled_color = isset($jobsearch_plugin_options['jobsearch-canceled-color']) ? $jobsearch_plugin_options['jobsearch-canceled-color'] : '';
                    $approved_color_str = '';
                    if ($approved_color != '') {
                        $approved_color_str = 'style="color:' . $approved_color . '"';
                    }
                    $pending_color_str = '';
                    if ($pending_color != '') {
                        $pending_color_str = 'style="color:' . $pending_color . '"';
                    }
                    $canceled_color_str = '';
                    if ($canceled_color != '') {
                        $canceled_color_str = 'style="color:' . $canceled_color . '"';
                    }

                    $employer_status = get_post_meta($post->ID, 'jobsearch_field_employer_approved', true);
                    if ($employer_status == 'on') {
                        echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Approved', 'wp-jobsearch') . '"><i ' . $approved_color_str . ' class="dashicons dashicons-yes" aria-hidden="true"></i></a>');
                    } else {
                        echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Pending', 'wp-jobsearch') . '"><i ' . $pending_color_str . ' class="dashicons dashicons-clock fa-spin fa-lg" aria-hidden="true"></i></a>');
                    }
                    break;
                case 'action' :
                    echo '<div class="actions">';

                    if ($post->post_status !== 'trash') {
                        if (current_user_can('read_post', $post->ID)) {
                            $admin_actions['view'] = array(
                                'action' => 'view',
                                'name' => __('View', 'wp-jobsearch'),
                                'icon' => '<i class="dashicons dashicons-visibility" aria-hidden="true"></i>',
                                'url' => get_permalink($post->ID)
                            );
                        }
                        if (current_user_can('edit_post', $post->ID)) {
                            $admin_actions['edit'] = array(
                                'action' => 'edit',
                                'name' => __('Edit', 'wp-jobsearch'),
                                'icon' => '<i class="dashicons dashicons-edit" aria-hidden="true"></i>',
                                'url' => get_edit_post_link($post->ID)
                            );
                        }
                        if (current_user_can('delete_post', $post->ID)) {
                            $admin_actions['delete'] = array(
                                'action' => 'delete',
                                'name' => __('Delete', 'wp-jobsearch'),
                                'icon' => '<i class="dashicons dashicons-trash" aria-hidden="true"></i>',
                                'url' => get_delete_post_link($post->ID)
                            );
                        }
                    }

                    if (isset($admin_actions) && !empty($admin_actions)) {
                        foreach ($admin_actions as $action) {
                            if (is_array($action)) {
                                printf('<a class="button button-icon jobsearch-tooltip" href="%2$s" data-tip="%3$s" title="%4$s">%5$s</a>', $action['action'], esc_url($action['url']), esc_attr($action['name']), esc_html($action['name']), force_balance_tags($action['icon']));
                            } else {
                                echo str_replace('class="', 'class="button ', $action);
                            }
                        }
                    }

                    echo '</div>';
                    break;
            }

            echo apply_filters('jobsearch_emp_post_bk_admin_columns_val', '', $column, $_post_id);
        }

        public function update_emps_total_jobs() {
            global $wpdb;
            $cachetime = 1800;
            $transient = 'jobsearch_update_emps_totjobs';

            $check_transient = get_transient($transient);
            if (empty($check_transient)) {
                $sql = "SELECT ID FROM $wpdb->posts AS posts";
                $sql .= " WHERE post_type=%s";
                $get_db_emps = $wpdb->get_col($wpdb->prepare($sql, 'employer'));
                if (!empty($get_db_emps)) {
                    foreach ($get_db_emps as $emp_id) {
                        $sql = "SELECT COUNT(*) FROM $wpdb->posts AS posts";
                        $sql .= " INNER JOIN $wpdb->postmeta AS postmeta";
                        $sql .= " ON postmeta.post_id = posts.ID";
                        $sql .= " WHERE post_type=%s";
                        $sql .= " AND (postmeta.meta_key='jobsearch_field_job_posted_by' AND postmeta.meta_value={$emp_id})";
                        $emp_jobs_count = $wpdb->get_col($wpdb->prepare($sql, 'job'));

                        $emp_jobs_count = isset($emp_jobs_count[0]) ? $emp_jobs_count[0] : 0;

                        update_post_meta($emp_id, 'jobsearch_emp_totl_jobs', $emp_jobs_count);
                    }
                }
                set_transient($transient, true, $cachetime);
            }
        }

        public function jobsearch_primary_column($column, $screen) {
            if ('edit-employer' === $screen) {
                $column = 'employer_title';
            }
            return $column;
        }

        public function jobsearch_employer_sortable_columns($columns) {
            $custom = array(
                'employer_title' => 'title',
                'location' => 'location',
                'featured' => 'featured',
                'status' => 'status',
                'posted_jobs' => 'posted_jobs',
            );
            return wp_parse_args($custom, $columns);
        }

        public function jobsearch_employer_sort_columns($vars) {
            if (isset($vars['orderby']) && isset($_GET['post_type']) && $_GET['post_type'] == 'employer') {
                if ('location' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_location_location1',
                        'orderby' => 'meta_value'
                    ));
                } else if ('featured' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_employer_featured',
                        'orderby' => 'meta_value'
                    ));
                } else if ('status' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_employer_approved',
                        'orderby' => 'meta_value'
                    ));
                } else if ('posted_jobs' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_emp_totl_jobs',
                        'orderby' => 'meta_value_num'
                    ));
                }
            }
            return $vars;
        }

        public function jobsearch_employer_tags() {
            // Add new taxonomy, make it hierarchical (like tags)
            $labels = array(
                'name' => _x('Tags', 'taxonomy general name', 'wp-jobsearch'),
                'singular_name' => _x('Tag', 'taxonomy singular name', 'wp-jobsearch'),
                'search_items' => __('Search Tags', 'wp-jobsearch'),
                'all_items' => __('All Tags', 'wp-jobsearch'),
                'parent_item' => __('Parent Tag', 'wp-jobsearch'),
                'parent_item_colon' => __('Parent Tag:', 'wp-jobsearch'),
                'edit_item' => __('Edit Tag', 'wp-jobsearch'),
                'update_item' => __('Update Tag', 'wp-jobsearch'),
                'add_new_item' => __('Add New Tag', 'wp-jobsearch'),
                'new_item_name' => __('New Tag Name', 'wp-jobsearch'),
                'menu_name' => __('Tag', 'wp-jobsearch'),
            );

            $args = array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'tag'),
            );

            register_taxonomy('tag', array('employer'), $args);
        }

    }

    return new post_type_employer();
}
