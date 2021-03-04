<?php
/**
 * @Manage Columns
 * @return
 *
 */
if (!class_exists('post_type_email')) {

    class post_type_email {

        // The Constructor
        public function __construct() {
            // Adding columns
            add_filter('manage_email_posts_columns', array($this, 'jobsearch_email_columns_add'));
            add_action('manage_email_posts_custom_column', array($this, 'jobsearch_email_columns'), 10, 2);
            add_filter('list_table_primary_column', array($this, 'jobsearch_primary_column'), 10, 2);
            add_action('init', array($this, 'jobsearch_email_register'), 1);
            add_filter('post_row_actions', array($this, 'jobsearch_email_row_actions'));
            add_filter('manage_edit-email_sortable_columns', array($this, 'jobsearch_email_sortable_columns'));
            add_filter('request', array($this, 'jobsearch_email_sort_columns'));
            add_action('admin_head', array($this, 'my_admin_custom_styles'));
            add_action('admin_footer', array($this, 'admin_custom_script'));
            add_filter('views_edit-email', array($this, 'jobsearch_email_clear_log_button'), 10, 1);
            add_action('admin_menu', array($this, 'jobsearch_disable_new_emails'));
            add_action('admin_head', array($this, 'jobsearch_disable_new_emails_ban'));
        }

        public function jobsearch_disable_new_emails() {
            // Hide sidebar link
            global $submenu;
            unset($submenu['edit.php?post_type=email'][10]);
        }

        public static function email_logs_post_type_redirect() {
            ?><script>window.location = "<?php echo admin_url('edit.php?post_type=email'); ?>";</script><?php
        }

        public function jobsearch_disable_new_emails_ban() {
            // Hide link on listing page
            if (isset($_GET['post_type']) && $_GET['post_type'] == 'email') {
                echo '<style type="text/css">#favorite-actions, .add-new-h2, .subsubsub, .search-box, .page-title-action, #screen-meta-links { display:none; }</style>';
            }
        }

        function my_admin_custom_styles() {
            global $pagenow;
            if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'email') {
                $output_css = '<style type="text/css"> 
                    .column-email_title { width:500px !important; overflow:hidden }
                    .column-featured { width:50px !important; overflow:hidden } 
                    .column-status { width:30px !important; overflow:hidden }
                    .column-action { text-align:right !important; width:150px !important; overflow:hidden }
                </style>';
                echo $output_css;
            }
        }

        function admin_custom_script() {
            global $pagenow;
            $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
            if ($pagenow == 'edit.php' && $post_type == 'email') {
                ?>
                <script>
                    var adminmenu = jQuery('#adminmenu');
                    var email_parent_menu = jQuery('.toplevel_page_jobsearch-email-templates-fields');
                    adminmenu.find('>li').removeClass('wp-has-current-submenu').addClass('wp-not-current-submenu');
                    adminmenu.find('>li').removeClass('wp-menu-open');
                    email_parent_menu.removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu');
                    email_parent_menu.addClass('wp-menu-open');
                    email_parent_menu.find('>a').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu');
                    email_parent_menu.find('>ul>li').eq(2).addClass('current');
                </script>
                <?php
            }
        }

        public function jobsearch_email_register() {
            $labels = array(
                'name' => _x('Email Logs', 'post type general name', 'wp-jobsearch'),
                'singular_name' => _x('Email Log', 'post type singular name', 'wp-jobsearch'),
                'menu_name' => _x('Email Logs', 'admin menu', 'wp-jobsearch'),
                'name_admin_bar' => _x('Email Log', 'add new on admin bar', 'wp-jobsearch'),
                'add_new' => _x('Add New', 'email', 'wp-jobsearch'),
                'add_new_item' => __('Add New Email Log', 'wp-jobsearch'),
                'new_item' => __('New Email Log', 'wp-jobsearch'),
                'edit_item' => __('Edit Email Log', 'wp-jobsearch'),
                'view_item' => __('View Email Log', 'wp-jobsearch'),
                'all_items' => __('Email Logs', 'wp-jobsearch'),
                'search_items' => __('Search Email Logs', 'wp-jobsearch'),
                'parent_item_colon' => __('Parent Email Logs:', 'wp-jobsearch'),
                'not_found' => __('No emails found.', 'wp-jobsearch'),
                'not_found_in_trash' => __('No emails found in Trash.', 'wp-jobsearch')
            );

            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'wp-jobsearch'),
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'query_var' => true,
                'rewrite' => null,
                'capability_type' => 'post',
                'has_archive' => false,
                'exclude_from_search' => true,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title')
            );

            register_post_type('email', $args);
        }

        public function jobsearch_email_row_actions($actions) {
            if ('email' == get_post_type()) {
                return array();
            }
            return $actions;
        }

        public function jobsearch_email_columns_add($columns) {
            $new_columns = array(
                'cb' => '<input type="checkbox" />',
                'email_title' => esc_html('Email Log', 'wp-jobsearch'),
                'email_send_to' => esc_html('Send To', 'wp-jobsearch'),
                'email_send_satus' => esc_html__('Sending Status', 'wp-jobsearch'),
                'email_sending_date' => esc_html('Sending Date', 'wp-jobsearch'),
                'status' => force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Status', 'wp-jobsearch') . '"><i class="dashicons dashicons-info"></i></a>'),
                'action' => esc_html('Action', 'wp-jobsearch'),
            );
            return $new_columns;
        }

        public function jobsearch_email_columns($column) {
            global $post;
            switch ($column) {
                case 'email_title' :
                    ?>
                    <div class="email_log">
                        <a href="javascript:void(0);" class="email_title" class="jobsearch-tooltip" title="<?php echo sprintf(__('ID: %d', 'wp-jobsearch'), $post->ID); ?>"><?php echo ucfirst(get_the_title($post->ID)); ?></a>
                    </div>
                    <?php
                    break;
                case 'email_send_satus' :
                    $email_send_satus = get_post_meta($post->ID, 'email_send_satus', true);
                    if ($email_send_satus) {
                        echo esc_html__('Successfully send', 'wp-jobsearch');
                    } else {
                        echo esc_html__('Error', 'wp-jobsearch');
                    }
                    break;
                case 'email_send_to' :
                    $email_send_to = get_post_meta($post->ID, 'email_send_to', true);
                    ?>  
                    <span class="email_send_to"><?php
                        if (!empty($email_send_to)) {
                            echo esc_html($email_send_to);
                        } else {
                            echo esc_html('-');
                        }
                        ?>
                    </span> 
                    <?php
                    break;
                case 'email_sending_date' :
                    $email_sending_date = get_the_time('l, F j, Y H:i:s', $post->ID);
                    echo esc_html($email_sending_date);
                    break;
                case "status" :
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

                    $email_status = get_post_meta($post->ID, 'email_status', true);
                    if ($email_status == 'processed') {
                        echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Processed', 'wp-jobsearch') . '"><i ' . $approved_color_str . ' class="dashicons dashicons-yes" aria-hidden="true"></i></a>');
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
                                'name' => esc_html__('View', 'wp-jobsearch'),
                                'icon' => '<i class="dashicons dashicons-visibility" aria-hidden="true"></i>',
                                'content' => get_the_content($post->ID)
                            );
                        }
                        if (current_user_can('delete_post', $post->ID)) {
                            $admin_actions['delete'] = array(
                                'action' => 'delete',
                                'name' => esc_html__('Delete', 'wp-jobsearch'),
                                'icon' => '<i class="dashicons dashicons-trash" aria-hidden="true"></i>',
                                'url' => get_delete_post_link($post->ID)
                            );
                        }
                    }
                    if (isset($admin_actions) && !empty($admin_actions)) {
                        foreach ($admin_actions as $action) {
                            wp_enqueue_style('jobsearch-fancybox-style');
                            wp_enqueue_script('jobsearch-fancybox-script');
                            if (is_array($action)) {
                                // print_r($action);
                                if (isset($action['action']) && $action['action'] == 'view') {
                                    $rand = rand(1000000, 9999999);
                                    ?>
                                    <script>
                                        jQuery(document).on('click', '.jobsearch-emaillog-btn-<?php echo ($rand) ?>', function () {
                                            jobsearch_modal_popup_open('JobSearchModalEmailLog<?php echo ($rand) ?>');
                                        });
                                    </script>
                                    <?php
                                    printf('<a id="email-log' . $rand . '" class="button button-icon jobsearch-tooltip jobsearch-emaillog-btn-' . ($rand) . '" href="javascript:void(0);" data-tip="%3$s" title="%4$s">%5$s</a>', $action['action'], force_balance_tags($action['content']), esc_attr($action['name']), esc_html($action['name']), force_balance_tags($action['icon']));
                                    //echo '<div style="display: block;"><div id="email-log-popup' . $rand . '">' .
                                    //force_balance_tags($action['content'])
                                    //. '</div></div>';
                                    $popup_args = array('p_content' => $action['content'], 'p_rand' => $rand);
                                    add_action('admin_footer', function () use ($popup_args) {

                                        extract(shortcode_atts(array(
                                            'p_content' => '',
                                            'p_rand' => ''
                                                        ), $popup_args));
                                        ?>
                                        <div class="jobsearch-modal fade" id="JobSearchModalEmailLog<?php echo ($p_rand) ?>">
                                            <div class="modal-inner-area">&nbsp;</div>
                                            <div class="modal-content-area">
                                                <div class="modal-box-area">
                                                    <span class="modal-close"><i class="fa fa-times"></i></span>
                                                    <?php echo ($p_content) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }, 11, 1);
                                    echo "<script>
                                jQuery(document).ready(function() {
                                    jQuery('#email-log" . $rand . "').fancybox({
                                    'titlePosition'		: 'inside',
                                    'transitionIn'		: 'none',
                                    'transitionOut'		: 'none'
                                    });
                                });
                                </script>";
                                } else {
                                    printf('<a class="button button-icon jobsearch-tooltip" href="%2$s" data-tip="%3$s" title="%4$s">%5$s</a>', $action['action'], esc_url($action['url']), esc_attr($action['name']), esc_html($action['name']), force_balance_tags($action['icon']));
                                }
                            } else {
                                echo str_replace('class="', 'class="button ', $action);
                            }
                        }
                    }

                    echo '</div>';
                    break;
            }
        }

        public function jobsearch_primary_column($column, $screen) {
            if ('edit-email' === $screen) {
                $column = 'email_title';
            }
            return $column;
        }

        public function jobsearch_email_sortable_columns($columns) {
            $custom = array(
                'email_send_to' => 'email_send_to',
                'email_title' => 'title',
                'email_send_satus' => 'email_send_satus',
                'email_sending_date' => 'email_sending_date',
            );
            return wp_parse_args($custom, $columns);
        }

        public function jobsearch_email_sort_columns($vars) {
            if (isset($vars['orderby'])) {
                if ('email_send_to' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'email_send_to',
                        'orderby' => 'meta_value'
                    ));
                } else if ('email_send_satus' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'email_send_satus',
                        'orderby' => 'meta_value'
                    ));
                } else if ('email_sending_date' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'orderby' => 'publish_date'
                    ));
                }
            }
            return $vars;
        }

        function jobsearch_email_clear_log_button($views) {
            ?> 
            <ul class="jobsearch-email-log-clear-btn">
                <li>
                    <button class="jobsaerch-email-clear-log"><?php echo esc_html__('Clear Log', 'wp-jobsearch'); ?></button>
                    <span class="ajax-loader"></span>
                </li>
            </ul>
            <?php
            return $views;
        }

    }

    return new post_type_email();
} 
