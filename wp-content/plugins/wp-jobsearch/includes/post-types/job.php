<?php
/**
 * @Manage Columns
 * @return
 *
 */
if (!class_exists('post_type_job')) {
    add_action('load-edit.php', function() {
        global $typenow; // current post type
        if ($typenow == 'job') {
            add_filter('post_class', function($classes, $class, $postID) {
                $job_status = get_post_meta($postID, 'jobsearch_field_job_status', true);
                $job_expiry = get_post_meta($postID, 'jobsearch_field_job_expiry_date', true);
                //$job_expiry = $job_expiry == '' ? strtotime(current_time('Y-m-d H:i:s')) : $job_expiry;
                if ($job_expiry != '' && $job_expiry <= current_time('timestamp')) {
                    $classes[] = 'jobsearch-job-expired';
                } else {
                    $post_obj = get_post($postID);
                    if ($job_status == 'admin-review') {
                        $classes[] = 'jobsearch-job-adminreview';
                    } else if ($job_status == 'canceled') {
                        $classes[] = 'jobsearch-job-canceled';
                    } else if ($job_status == 'pending') {
                        $classes[] = 'jobsearch-job-pending';
                    } else if (isset($post_obj->post_status) && $post_obj->post_status == 'awaiting-payment') {
                        $classes[] = 'jobsearch-job-awaitpay';
                    }
                }
                return $classes;
            }, 11, 3);
        }
    });

    class post_type_job {

        // The Constructor
        public function __construct() {
            // Adding columns
            add_filter('manage_job_posts_columns', array($this, 'jobsearch_job_columns_add'));
            add_action('manage_job_posts_custom_column', array($this, 'jobsearch_job_columns'), 10, 2);
            add_filter('list_table_primary_column', array($this, 'jobsearch_primary_column'), 10, 2);
            add_action('init', array($this, 'jobsearch_job_register'), 1); // post type register
            add_action('init', array($this, 'jobsearch_job_sector'), 3, 0);
            add_action('admin_footer', array($this, 'admin_job_post_aftr'), 25);
            add_action('admin_footer', array($this, 'admin_custom_script'));
            
            add_action('init', array($this, 'custom_post_status'));
            //
            add_action('admin_init', array($this, 'update_sectors_real_count_meta'));
            //
            add_filter('post_row_actions', array($this, 'jobsearch_job_row_actions'));
            add_filter('manage_edit-job_sortable_columns', array($this, 'jobsearch_job_sortable_columns'));
            add_filter('request', array($this, 'jobsearch_job_sort_columns'));
            add_action('init', array($this, 'jobsearch_job_jobtype'), 0);
            // job type extra fields
            add_action('create_jobtype', array($this, 'jobsearch_job_save_jobtype_fields_added_callback'));
            add_action('edited_jobtype', array($this, 'jobsearch_job_save_jobtype_fields_updated_callback'));
            add_action('jobtype_edit_form_fields', array($this, 'jobsearch_job_edit_jobtype_fields_callback'));
            add_action('jobtype_add_form_fields', array($this, 'jobsearch_job_jobtype_fields_callback'));
            add_action('admin_head', array($this, 'jobsearch_job_admin_custom_styles'));
            add_action('init', array($this, 'jobsearch_job_skills'), 0);

            //
            add_action('restrict_manage_posts', array($this, 'jobs_admin_posts_filter_restrict_manage_posts'));
            add_filter('parse_query', array($this, 'job_customfiltr_posts_filter'), 11, 1);
            //
            
            add_action('wp_ajax_jobsearch_bkaddin_joblocscountr_addbtn', array($this, 'job_locscountr_addbtn'));
            add_action('wp_ajax_jobsearch_bkaddin_joblocscountr_update', array($this, 'bkaddin_joblocscountr_update'));

            add_action('views_edit-job', array($this, 'modified_views_so'), 0);
            add_filter('parse_query', array($this, 'job_query_filter'), 11, 1);
            add_filter('bulk_actions-edit-job', array($this, 'custom_job_filters'));
            add_action('handle_bulk_actions-edit-job', array($this, 'jobs_bulk_actions_handle'), 10, 3);
        }

        public function jobsearch_job_admin_custom_styles() {
            global $pagenow, $post;
            if ($pagenow == 'post.php') {
                $post_id = $post->ID;
                if (get_post_type($post_id) == 'job') {
                    ?>
                    <style type="text/css">
                        #postimagediv {display: none;}
                    </style>
                    <?php
                }
            }
            if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'job') {
                $output_css = '<style type="text/css"> 
                    .column-job_title { min-width:200px !important; max-width:400px !important; overflow:hidden; }
                    .column-job_applications { min-width:120px !important; max-width:120px !important; overflow:hidden; }
                    .column-job_type { min-width:100px !important; max-width:100px !important; overflow:hidden; }
                    .column-location { min-width:100px !important; max-width:200px !important; overflow:hidden; }
                    .column-posted { min-width:100px !important; max-width:200px !important; overflow:hidden; }
                    .column-expiry { min-width:100px !important; max-width:200px !important; overflow:hidden; }
                    .column-posted_by_emp { min-width:150px !important; max-width:300px !important; overflow:hidden; }
                    .column-featured { width:50px !important; overflow:hidden; }
                    .column-filled { width:30px !important; overflow:hidden; }
                    .column-status { width:30px !important; overflow:hidden; }
                    .column-action { text-align:right !important; width:210px !important; overflow:hidden; }
                    tr.jobsearch-job-expired {background-color: #ffcfcf !important;}
                    tr.jobsearch-job-expired td, tr.jobsearch-job-expired th {border-bottom: #ddabab 1px solid !important;}
                    tr.jobsearch-job-pending {background-color: #c8d7e1 !important;}
                    tr.jobsearch-job-pending td, tr.jobsearch-job-pending th {border-bottom: #96bad2 1px solid !important;}
                    tr.jobsearch-job-awaitpay {background-color: #f8dda7 !important;}
                    tr.jobsearch-job-awaitpay td, tr.jobsearch-job-awaitpay th {border-bottom: #fdc24d 1px solid !important;}
                    tr.jobsearch-job-adminreview {background-color: #fff6d9 !important;}
                    tr.jobsearch-job-adminreview td, tr.jobsearch-job-adminreview th {border-bottom: #ffc700 1px solid !important;}
                    tr.jobsearch-job-canceled {background-color: #decccc !important;}
                    tr.jobsearch-job-canceled td, tr.jobsearch-job-canceled th {border-bottom: #ddabab 1px solid !important;}
                </style>';
                echo $output_css;
            }
        }

        public function jobsearch_job_register() {

            $jobsearch__options = get_option('jobsearch_plugin_options');

            $job_slug = isset($jobsearch__options['job_rewrite_slug']) && $jobsearch__options['job_rewrite_slug'] != '' ? $jobsearch__options['job_rewrite_slug'] : 'job';

            $labels = array(
                'name' => _x('Jobs', 'jobs post type general name', 'wp-jobsearch'),
                'singular_name' => _x('Job', 'post type singular name', 'wp-jobsearch'),
                'menu_name' => _x('Jobs', 'admin menu', 'wp-jobsearch'),
                'name_admin_bar' => _x('Job', 'add new on admin bar', 'wp-jobsearch'),
                'add_new' => _x('Add New', 'job', 'wp-jobsearch'),
                'add_new_item' => __('Add New Job', 'wp-jobsearch'),
                'new_item' => __('New Job', 'wp-jobsearch'),
                'edit_item' => __('Edit Job', 'wp-jobsearch'),
                'view_item' => __('View Job', 'wp-jobsearch'),
                'all_items' => __('All Jobs ', 'wp-jobsearch'),
                'search_items' => __('Search Jobs', 'wp-jobsearch'),
                'parent_item_colon' => __('Parent Jobs:', 'wp-jobsearch'),
                'not_found' => __('No jobs found.', 'wp-jobsearch'),
                'not_found_in_trash' => __('No jobs found in Trash.', 'wp-jobsearch')
            );

            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'wp-jobsearch'),
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => $job_slug, 'feed' => false),
                'capability_type' => 'post',
                'has_archive' => false,
                'exclude_from_search' => true,
                'hierarchical' => false,
                //'menu_position' => 25,
                'supports' => array('title', 'editor', 'excerpt', 'thumbnail')
            );
            $args = apply_filters('jobsearch_reg_post_type_job_args', $args);
            register_post_type('job', $args);
        }
        
        public function custom_post_status(){
            register_post_status( 'awaiting-payment', array(
                'label'                     => _x( 'Awaiting Payment', 'job' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'post_type' => 'job',
                'label_count'               => _n_noop( 'Awaiting Payment <span class="count">(%s)</span>', 'Awaiting Payment <span class="count">(%s)</span>' ),
            ) );
        }

        public function jobsearch_job_row_actions($actions) {
            if ('job' == get_post_type()) {
                return array();
            }
            return $actions;
        }

        public function jobs_admin_posts_filter_restrict_manage_posts() {
            $type = 'post';
            if (isset($_GET['post_type'])) {
                $type = $_GET['post_type'];
            }

            //only add filter to post type you want
            if ('job' == $type) {
                $values = array(
                    'expired_jobs' => __('Expired Jobs ', 'wp-jobsearch'),
                    'by_employer' => __('By Employers ', 'wp-jobsearch'),
                );

                $sortby_emp = isset($_GET['jobsearch_field_sortby_emp']) ? $_GET['jobsearch_field_sortby_emp'] : '';
                ?>
                <select name="jobs_sortby">
                    <option value=""><?php _e('Sort By ', 'wp-jobsearch'); ?></option>
                    <?php
                    $current_v = isset($_GET['jobs_sortby']) ? $_GET['jobs_sortby'] : '';
                    foreach ($values as $value => $label) {
                        printf('<option value="%s"%s>%s</option>', $value, $value == $current_v ? ' selected="selected"' : '', $label);
                    }
                    ?>
                </select>
                <div id="sortby-employrs-con" class="sortby-employrs-holdr" style="float: left; position: relative; display: <?php echo ($sortby_emp > 0 ? 'block' : 'none') ?>;">
                    <?php
                    jobsearch_get_custom_post_field($sortby_emp, 'employer', esc_html__('Select Employer', 'wp-jobsearch'), 'sortby_emp');
                    ?>
                </div>
                <script>
                    jQuery(document).on('change', 'select[name=jobs_sortby]', function () {
                        var _thisel = jQuery(this);
                        if (_thisel.val() == 'by_employer') {
                            jQuery('#sortby-employrs-con').slideDown();
                        } else {
                            jQuery('#sortby-employrs-con').slideUp();
                        }
                    });
                </script>
                <?php
            }
        }

        public function job_customfiltr_posts_filter($query) {
            global $pagenow;
            $type = 'post';
            if (isset($_GET['post_type'])) {
                $type = $_GET['post_type'];
            }
            if ('job' == $type && is_admin() && $pagenow == 'edit.php' && isset($_GET['jobs_sortby']) && $_GET['jobs_sortby'] == 'by_employer' && isset($_GET['jobsearch_field_sortby_emp']) && $_GET['jobsearch_field_sortby_emp'] > 0) {
                $custom_filter_arr = array();
                $custom_filter_arr[] = array(
                    'key' => 'jobsearch_field_job_posted_by',
                    'value' => $_GET['jobsearch_field_sortby_emp'],
                    'compare' => '=',
                );
                $query->set('meta_query', $custom_filter_arr);
            }
            if ('job' == $type && is_admin() && $pagenow == 'edit.php' && isset($_GET['jobs_sortby']) && $_GET['jobs_sortby'] == 'expired_jobs') {
                $custom_filter_arr = array();
                $custom_filter_arr[] = array(
                    'key' => 'jobsearch_field_job_expiry_date',
                    'value' => current_time('timestamp'),
                    'compare' => '<=',
                );
                $query->set('meta_query', $custom_filter_arr);
            }
        }

        public function modified_views_so($views) {

            $jobsearch__options = get_option('jobsearch_plugin_options');
            $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';
            remove_filter('parse_query', array(&$this, 'job_query_filter'), 11, 1);
            remove_filter('parse_query', array(&$this, 'job_customfiltr_posts_filter'), 11, 1);
            $args = array(
                'post_type' => 'job',
                'posts_per_page' => '1',
                'post_status' => array('publish', 'draft'),
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_status',
                        'value' => 'pending',
                        'compare' => '=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            $pending_jobs = $jobs_query->found_posts;
            wp_reset_postdata();

            $args = array(
                'post_type' => 'job',
                'posts_per_page' => '1',
                'post_status' => array('publish', 'draft'),
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_status',
                        'value' => 'admin-review',
                        'compare' => '=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            $review_jobs = $jobs_query->found_posts;
            wp_reset_postdata();

            $jobs_meta_qury = array();
            $jobs_meta_qury[] = array(
                'key' => 'jobsearch_field_job_status',
                'value' => 'approved',
                'compare' => '=',
            );
            if ($emporler_approval != 'off') {
                $jobs_meta_qury[] = array(
                    'key' => 'jobsearch_job_employer_status',
                    'value' => 'approved',
                    'compare' => '=',
                );
            }
            $args = array(
                'post_type' => 'job',
                'posts_per_page' => '1',
                'post_status' => array('publish', 'draft'),
                'fields' => 'ids',
                'meta_query' => $jobs_meta_qury,
            );
            $jobs_query = new WP_Query($args);
            $approve_jobs = $jobs_query->found_posts;
            wp_reset_postdata();
            
            $jobs_meta_qury = array();
            $jobs_meta_qury[] = array(
                'key' => 'jobsearch_field_job_status',
                'value' => 'approved',
                'compare' => '=',
            );
            $jobs_meta_qury[] = array(
                'key' => 'jobsearch_field_job_expiry_date',
                'value' => current_time('timestamp'),
                'compare' => '>',
            );
            if ($emporler_approval != 'off') {
                $jobs_meta_qury[] = array(
                    'key' => 'jobsearch_job_employer_status',
                    'value' => 'approved',
                    'compare' => '=',
                );
            }
            $args = array(
                'post_type' => 'job',
                'posts_per_page' => '1',
                'post_status' => array('publish', 'draft'),
                'fields' => 'ids',
                'meta_query' => $jobs_meta_qury,
            );
            $jobs_query = new WP_Query($args);
            $active_jobs = $jobs_query->found_posts;
            wp_reset_postdata();
            
            $jobs_meta_qury = array();
            $jobs_meta_qury[] = array(
                'key' => 'jobsearch_field_job_expiry_date',
                'value' => current_time('timestamp'),
                'compare' => '<',
            );
            $jobs_meta_qury[] = array(
                'key' => 'jobsearch_field_job_expiry_date',
                'value' => '',
                'compare' => '!=',
            );
            $args = array(
                'post_type' => 'job',
                'posts_per_page' => '1',
                'post_status' => array('publish', 'draft'),
                'fields' => 'ids',
                'meta_query' => $jobs_meta_qury,
            );
            $jobs_query = new WP_Query($args);
            $expired_jobs = $jobs_query->found_posts;
            wp_reset_postdata();

            $views['approved'] = '<a href="edit.php?post_type=job&job_status=approved">' . sprintf(esc_html__('Approved (%s)', 'wp-jobsearch'), absint($approve_jobs)) . '</a>';
            $views['admin-review'] = '<a href="edit.php?post_type=job&job_status=admin-review">' . sprintf(esc_html__('Admin Review (%s)', 'wp-jobsearch'), absint($review_jobs)) . '</a>';
            $views['active'] = '<a href="edit.php?post_type=job&job_status=active">' . sprintf(esc_html__('Active Jobs (%s)', 'wp-jobsearch'), absint($active_jobs)) . '</a>';
            $views['expired'] = '<a href="edit.php?post_type=job&job_status=expired">' . sprintf(esc_html__('Expired (%s)', 'wp-jobsearch'), absint($expired_jobs)) . '</a>';
            $views['pending'] = '<a href="edit.php?post_type=job&job_status=pending">' . sprintf(esc_html__('Pending (%s)', 'wp-jobsearch'), absint($pending_jobs)) . '</a>';

            return $views;
        }

        public function job_query_filter($query) {
            global $pagenow;

            $custom_filter_arr = array();
            if (is_admin() && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'job' && isset($_GET['job_status']) && $_GET['job_status'] != '') {
                $jobsearch__options = get_option('jobsearch_plugin_options');
                $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';
                if ($_GET['job_status'] == 'active') {
                    $custom_filter_arr[] = array(
                        'key' => 'jobsearch_field_job_status',
                        'value' => 'approved',
                        'compare' => '=',
                    );
                    if ($emporler_approval != 'off') {
                        $custom_filter_arr[] = array(
                            'key' => 'jobsearch_job_employer_status',
                            'value' => 'approved',
                            'compare' => '=',
                        );
                    }
                    $custom_filter_arr[] = array(
                        'key' => 'jobsearch_field_job_expiry_date',
                        'value' => current_time('timestamp'),
                        'compare' => '>',
                    );
                } else if ($_GET['job_status'] == 'expired') {
                    $custom_filter_arr[] = array(
                        'key' => 'jobsearch_field_job_expiry_date',
                        'value' => current_time('timestamp'),
                        'compare' => '<',
                    );
                    $custom_filter_arr[] = array(
                        'key' => 'jobsearch_field_job_expiry_date',
                        'value' => '',
                        'compare' => '!=',
                    );
                } else {
                    $custom_filter_arr[] = array(
                        'key' => 'jobsearch_field_job_status',
                        'value' => $_GET['job_status'],
                        'compare' => '=',
                    );
                }
            }
            if (!empty($custom_filter_arr)) {
                $query->set('meta_query', $custom_filter_arr);
            }
        }

        public function custom_job_filters($actions) {
            if (is_array($actions)) {
                $actions['approved'] = esc_html__('Approved', 'wp-jobsearch');
                $actions['pending'] = esc_html__('Pending', 'wp-jobsearch');
                $actions['admin-review'] = esc_html__('Admin Review', 'wp-jobsearch');
            }
            return $actions;
        }

        function jobs_bulk_actions_handle($redirect_to, $doaction, $post_ids) {
            if ($doaction == 'approved' || $doaction == 'pending' || $doaction == 'admin-review') {
                if (!empty($post_ids)) {
                    $current_time = current_time('timestamp');
                    foreach ($post_ids as $job_id) {
                        update_post_meta($job_id, 'jobsearch_field_job_status', $doaction);
                        if ($doaction == 'approved') {
                            $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                            // Employer jobs status change according his/her status
                            do_action('jobsearch_employer_update_jobs_status', $job_employer_id);
                            
                            $job_is_inreview = get_post_meta($job_id, 'jobsearch_job_is_under_review', true);
                            $job_expiry_date = get_post_meta($job_id, 'jobsearch_field_job_expiry_date', true);
                            $job_publish_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                            if ($job_publish_date <= $current_time && $job_expiry_date >= $current_time && $job_is_inreview == 'yes') {
                                do_action('jobsearch_newjob_approved_at_backend', $job_id);
                                update_post_meta($job_id, 'jobsearch_job_is_under_review', '');
                            }

                            //
                            $employer_user_id = jobsearch_get_employer_user_id($job_employer_id);
                            $user_obj = get_user_by('ID', $employer_user_id);
                            if (isset($user_obj->ID)) {
                                do_action('jobsearch_job_approved_to_employer', $user_obj, $job_id);
                            }
                        }
                    }
                }
            }
            return $redirect_to;
        }
        
        public function admin_job_post_aftr() {
            global $pagenow;
            if ($pagenow == 'post.php' && isset($_GET['post']) && $_GET['post'] > 0) {
                $_post_id = $_GET['post'];
                if (get_post_type($_post_id) == 'job') {
                    $current_time = current_time('timestamp');
                    
                    $job_is_inreview = get_post_meta($_post_id, 'jobsearch_job_is_under_review', true);
                    $job_status = get_post_meta($_post_id, 'jobsearch_field_job_status', true);
                    
                    $job_expiry_date = get_post_meta($_post_id, 'jobsearch_field_job_expiry_date', true);
                    $job_publish_date = get_post_meta($_post_id, 'jobsearch_field_job_publish_date', true);
                    
                    if ($job_status == 'approved' && $job_publish_date <= $current_time && $job_expiry_date >= $current_time && $job_is_inreview == 'yes') {
                        do_action('jobsearch_newjob_approved_at_backend', $_post_id);
                        update_post_meta($_post_id, 'jobsearch_job_is_under_review', '');
                    }
                }
            }
            
            if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'job') {
                ?>
                <script type="text/javascript">
                    jQuery(document).on('click', '.jobsearch-bk-duplicjob-act', function() {
                        var _this = jQuery(this);
                        var origjob_id = _this.attr('data-id');
                        var the_loader = _this.find('i');
                        var this_loder_class = the_loader.attr('class');

                        if (!_this.hasClass('ajax-loding')) {
                            _this.addClass('ajax-loding');
                            the_loader.attr('class', 'fa fa-refresh fa-spin');

                            var request = jQuery.ajax({
                                url: ajaxurl,
                                method: "POST",
                                data: {
                                    origjob_id: origjob_id,
                                    action: 'jobsearch_add_duplicate_post_byuser',
                                },
                                dataType: "json"
                            });

                            request.done(function (response) {
                                if ('undefined' !== typeof response.duplicate && response.duplicate == '1') {
                                    window.location.reload(true);
                                    return false;
                                }
                                _this.removeClass('ajax-loding');
                                the_loader.attr('class', this_loder_class);
                            });

                            request.fail(function (jqXHR, textStatus) {
                                _this.removeClass('ajax-loding');
                                the_loader.attr('class', this_loder_class);
                            });
                        }
                    });
                </script>
                <?php
            }
        }

        public function jobsearch_job_columns_add($columns) {
            global $sitepress;
            $new_columns = array();
            $new_columns['cb'] = '<input type="checkbox" />';
            $new_columns['job_title'] = esc_html('Position', 'wp-jobsearch');
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
            $new_columns['job_applications'] = esc_html__('Applications', 'wp-jobsearch');
            $new_columns['job_type'] = esc_html__('Type', 'wp-jobsearch');
            $new_columns['location'] = esc_html__('Location', 'wp-jobsearch');
            $new_columns['posted'] = esc_html__('Posted On', 'wp-jobsearch');
            $new_columns['expiry'] = esc_html__('Expiry', 'wp-jobsearch');
            $new_columns['posted_by_emp'] = esc_html__('Posted By ', 'wp-jobsearch') . force_balance_tags('<strong class="jobsearch-tooltip" title="' . esc_html__('Employer Status', 'wp-jobsearch') . '"><i class="dashicons dashicons-info"></i></strong>');
            $new_columns['featured'] = force_balance_tags('<strong class="jobsearch-tooltip" title="' . esc_html__('Featured', 'wp-jobsearch') . '"><i class="dashicons dashicons-star-filled"></i></strong>');
            $new_columns['filled'] = force_balance_tags('<strong class="jobsearch-tooltip" title="' . esc_html__('Filled', 'wp-jobsearch') . '"><i class="dashicons dashicons-admin-users"></i></strong>');
            $new_columns['status'] = force_balance_tags('<strong class="jobsearch-tooltip" title="' . esc_html__('Status', 'wp-jobsearch') . '"><i class="dashicons dashicons-info"></i></strong>');
            $new_columns['action'] = esc_html__('Action', 'wp-jobsearch');

            return $new_columns;
        }

        public function jobsearch_job_columns($column) {
            global $post, $wpdb;
            switch ($column) {
                case 'job_title' :
                    echo '<div class="job_position">';
                    $src = '';
                    $job_field_user = get_post_meta($post->ID, 'jobsearch_field_job_posted_by', true);
                    $post_thumbnail_id = jobsearch_job_get_profile_image($post->ID);

                    if (isset($post_thumbnail_id) && $post_thumbnail_id != '') {
                        $src = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                        $src = isset($src[0]) ? $src[0] : '';
                    }
                    if ($src != '') {
                        echo '<div class="company-logo">';
                        echo '<img src="' . esc_attr($src) . '" alt="' . jobsearch_esc_html(get_the_title($job_field_user)) . '" />';
                        echo '</div>';
                        // Before 1.24.0, logo URLs were stored in post meta.
                    }

                    echo '<a href="' . admin_url('post.php?post=' . $post->ID . '&action=edit') . '" class="job_title" class="jobsearch-tooltip" title="' . sprintf(__('ID: %d', 'wp-jobsearch'), $post->ID) . '">' . jobsearch_esc_html(ucfirst(get_the_title($post->ID))) . '</a>';

                    echo '<div class="sector-list">';
                    $jobtype_list = get_the_term_list($post->ID, 'sector', '', ',', '');
                    if ($jobtype_list) {
                        printf('%1$s', $jobtype_list);
                    }
                    echo '</div>';

                    echo '</div>';
                    break;
                case 'job_applications' :

                    $_job_id = $post->ID;
                    $job_applicants_list = get_post_meta($post->ID, 'jobsearch_job_applicants_list', true);
                    $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
                    if (empty($job_applicants_list)) {
                        $job_applicants_list = array();
                    }

                    $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;

                    $all_aply_methds = jobsearch_job_apply_methods_list();
                    $job_aply_type = get_post_meta($post->ID, 'jobsearch_field_job_apply_type', true);

                    $job_aply_mthod = isset($all_aply_methds[$job_aply_type]) ? $all_aply_methds[$job_aply_type] : '';


                    echo '<a href="' . admin_url('admin.php?page=jobsearch-applicants-list&job_id=' . $post->ID) . '" style="color:#0073aa; font-size:16px;"><strong>' . sprintf(esc_html__('Applicants: %s', 'wp-jobsearch'), $job_applicants_count) . '</strong></a><br>';
                    if ($job_aply_type != 'internal') {
                        if ($job_aply_type == 'with_email') {
                            $job_applics_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts AS posts"
                                            . " LEFT JOIN $wpdb->postmeta AS postmeta ON(posts.ID = postmeta.post_id) "
                                            . " WHERE post_type=%s AND (postmeta.meta_key = 'jobsearch_app_job_id' AND postmeta.meta_value={$_job_id})", 'email_apps'));

                            echo esc_html('Method: ', 'wp-jobsearch') . '<a href="' . admin_url('admin.php?page=jobsearch-emailapps-list&job_id=' . $post->ID) . '" style="color:#0073aa;">' . $job_aply_mthod . ' (' . $job_applics_count . ')</a>';
                        } else {
                            echo esc_html('Method: ', 'wp-jobsearch') . $job_aply_mthod;
                        }
                    }
                    break;
                case 'job_type' :

                    $terms = wp_get_post_terms($post->ID, 'jobtype');

                    if (!empty($terms)) {
                        ?>

                        <?php
                        foreach ($terms as $term) :
                            $jobtype_color = get_term_meta($term->term_id, 'jobsearch_field_jobtype_color', true);
                            $jobtype_textcolor = get_term_meta($term->term_id, 'jobsearch_field_jobtype_textcolor', true);
                            $jobtype_color_str = '';
                            if ($jobtype_color != '') {
                                $jobtype_color_str = ' style="background-color: ' . esc_attr($jobtype_color) . '; color: ' . esc_attr($jobtype_textcolor) . ' "';
                            }
                            ?>

                            <a class="<?php echo $term->slug; ?>">
                                <span class="jobtype-bg" <?php echo force_balance_tags($jobtype_color_str); ?>><?php echo $term->name; ?></span>
                            </a>
                            <?php
                        endforeach;
                    } else {
                        echo esc_html('-');
                    };
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
                        $locat_str .= jobsearch_esc_html($location1);
                    }
                    if ($location2 != '') {
                        $locat_str .= $locat_str != '' ? ' | ' : '';
                        $location2 = ucfirst(str_replace(array("-", "_"), array(" ", " "), $location2));
                        $locat_str .= jobsearch_esc_html($location2);
                    }
                    if ($location3 != '') {
                        $locat_str .= $locat_str != '' ? ' | ' : '';
                        $location3 = ucfirst(str_replace(array("-", "_"), array(" ", " "), $location3));
                        $locat_str .= jobsearch_esc_html($location3);
                    }
                    if ($location4 != '') {
                        $locat_str .= $locat_str != '' ? ' | ' : '';
                        $location4 = ucfirst(str_replace(array("-", "_"), array(" ", " "), $location4));
                        $locat_str .= jobsearch_esc_html($location4);
                    }
                    if ($full_addrs != '') {
                        $locat_str .= $locat_str != '' ? ' | ' : '';
                        $locat_str .= $full_addrs;
                    }

                    echo jobsearch_esc_html($locat_str);
                    break;
                case 'posted_by_emp' :

                    $job_field_user = get_post_meta($post->ID, 'jobsearch_field_job_posted_by', true);

                    if (isset($job_field_user) && !empty($job_field_user)) {
                        echo ' <small class="jobsearch-employer-title"> ' . jobsearch_esc_html(get_the_title($job_field_user)) . ' </small> ';
                        global $jobsearch_plugin_options;
                        $approved_color = isset($jobsearch_plugin_options['jobsearch-approved-color']) ? $jobsearch_plugin_options['jobsearch-approved-color'] : '';
                        $pending_color = isset($jobsearch_plugin_options['jobsearch-pending-color']) ? $jobsearch_plugin_options['jobsearch-pending-color'] : '';
                        $canceled_color = isset($jobsearch_plugin_options['jobsearch-canceled-color']) ? $jobsearch_plugin_options['jobsearch-canceled-color'] : '';
                        $approved_color_str = '';
                        if ($approved_color != '') {
                            $approved_color_str = 'style="background-color:' . $approved_color . ';color:#ffffff"';
                        }
                        $pending_color_str = '';
                        if ($pending_color != '') {
                            $pending_color_str = 'style="background-color:' . $pending_color . ';color:#ffffff"';
                        }
                        $canceled_color_str = '';
                        if ($canceled_color != '') {
                            $canceled_color_str = 'style="background-color:' . $canceled_color . ';color:#ffffff"';
                        }

                        $employer_status = get_post_meta($job_field_user, 'jobsearch_field_employer_approved', true);
                        if ($employer_status == 'on') {
                            echo force_balance_tags('<span class="jobsearch-employer-status"  ' . $approved_color_str . '> ' . esc_html__('Approved', 'wp-jobsearch') . ' </span>');
                        } else {
                            echo force_balance_tags('<span class="jobsearch-employer-status" ' . $pending_color_str . '> ' . esc_html__('Approval Pending', 'wp-jobsearch') . '</span>');
                        }
                    } else {
                        echo '-';
                    }

                    //echo $company_name = jobsearch_job_get_company_name($post->ID, '');
                    break;
                case 'posted' :
                    $posted = get_post_meta($post->ID, 'jobsearch_field_job_publish_date', true);
                    $posted = $posted == '' ? strtotime(current_time('Y-m-d H:i:s')) : $posted;
                    echo date_i18n(get_option('date_format'), $posted);
                    break;
                case 'expiry' :
                    $expiry = get_post_meta($post->ID, 'jobsearch_field_job_expiry_date', true);
                    if ($expiry != '' && $expiry <= current_time('timestamp')) {
                        $shdate = '<div style="background-color: #ff0000; color: #ffffff; display: inline-block; padding: 2px 5px 5px 5px; line-height:1;">' . esc_html__('Expired on ', 'wp-jobsearch') . '</div><br><strong style="color: #ff0000;">' . date_i18n(get_option('date_format'), $expiry) . '</strong>';
                    } else if ($expiry > current_time('timestamp')) {
                        $shdate = esc_html__('Expired on ', 'wp-jobsearch') . '<br>' . date_i18n(get_option('date_format'), $expiry);
                    } else {
                        $shdate = esc_html__('Expiry Date Missing', 'wp-jobsearch');
                    }
                    echo jobsearch_esc_html($shdate);
                    break;
                case 'featured' :
                    $job_featured = get_post_meta($post->ID, 'jobsearch_field_job_featured', true);
                    if ($job_featured == 'on') {
                        echo ('<a href="javascript:void(0);" class="jobsearch-tooltip job-featured-option" data-option="un-feature" data-jobid="' . esc_attr($post->ID) . '" title="' . esc_html__('No', 'wp-jobsearch') . '"><i class="dashicons dashicons-star-filled" aria-hidden="true"></i></a>');
                    } else {
                        echo ('<a href="javascript:void(0);" class="jobsearch-tooltip job-featured-option" data-option="featured" data-jobid="' . esc_attr($post->ID) . '" title="' . esc_html__('Yes', 'wp-jobsearch') . '"><i class="dashicons dashicons-star-empty" aria-hidden="true"></i></a>');
                    }
                    break;
                case 'filled' :
                    $filled = get_post_meta($post->ID, 'jobsearch_field_job_filled', true);
                    if ($filled == 'on') {
                        echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Filled', 'wp-jobsearch') . '"><i class="dashicons dashicons-yes" aria-hidden="true"></i></a>');
                    } else {
                        echo esc_html('-');
                    }
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
                    
                    $job_expiry = get_post_meta($post->ID, 'jobsearch_field_job_expiry_date', true);

                    $job_status = get_post_meta($post->ID, 'jobsearch_field_job_status', true);
                    
                    if ($job_expiry != '' && $job_expiry <= current_time('timestamp')) {
                        echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Expired', 'wp-jobsearch') . '"><i ' . $canceled_color_str . ' class="dashicons dashicons-table-col-delete" aria-hidden="true"></i></a>');
                    } else {
                        if ($post->post_status == 'awaiting-payment') {
                            echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Awaiting for Payment', 'wp-jobsearch') . '"><i style="color: #e89600;" class="dashicons dashicons-hourglass fa-spin fa-lg" aria-hidden="true"></i></a>');
                        } else if ($job_status == 'approved') {
                            echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Approved', 'wp-jobsearch') . '"><i ' . $approved_color_str . ' class="dashicons dashicons-yes" aria-hidden="true"></i></a>');
                        } elseif ($job_status == 'admin-review') {
                            echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Admin Review', 'wp-jobsearch') . '"><i ' . $pending_color_str . ' class="dashicons dashicons-admin-users" aria-hidden="true"></i></a>');
                        } elseif ($job_status == 'canceled') {
                            echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Canceled', 'wp-jobsearch') . '"><i ' . $canceled_color_str . ' class="dashicons dashicons-welcome-comments" aria-hidden="true"></i></a>');
                        } elseif ($job_status == 'pending') {
                            echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Pending', 'wp-jobsearch') . '"><i style="color: #2e4453;" class="dashicons dashicons-clock fa-spin fa-lg" aria-hidden="true"></i></a>');
                        }
                    }
                    break;
                case 'action' :
                    $duplicate_jobs_allow = isset($jobsearch_plugin_options['duplicate_the_job']) ? $jobsearch_plugin_options['duplicate_the_job'] : '';
                    echo '<div class="actions">';
                    $admin_actions = array();
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
                            
                            if ($duplicate_jobs_allow == 'on') {
                                $admin_actions['duplicate'] = array(
                                    'action' => 'duplicate',
                                    'name' => __('Duplicate Job', 'wp-jobsearch'),
                                    'icon' => '<i class="dashicons dashicons-format-aside" aria-hidden="true"></i>',
                                    'url' => "javascript:void(0);"
                                );
                            }
                            
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
                        foreach ($admin_actions as $act_key => $action) {
                            if (is_array($action)) {
                                $extra_classes = '';
                                $extra_attribs = '';
                                if ($act_key == 'duplicate') {
                                    $extra_classes = ' jobsearch-bk-duplicjob-act';
                                    $extra_attribs = ' data-id="' . ($post->ID) . '"';
                                }
                                printf('<a class="button button-icon jobsearch-tooltip' . $extra_classes . '" href="%2$s"' . $extra_attribs . ' data-tip="%3$s" title="%4$s">%5$s</a>', $action['action'], esc_html($action['url']), esc_attr($action['name']), esc_html($action['name']), force_balance_tags($action['icon']));
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
            if ('edit-job' === $screen) {
                $column = 'job_title';
            }
            return $column;
        }

        public function jobsearch_job_sortable_columns($columns) {
            $custom = array(
                'featured' => 'featured',
                'filled' => 'filled',
                'status' => 'status',
                'job_title' => 'title',
                'location' => 'location',
                'posted' => 'posted',
                'expiry' => 'expiry',
            );
            return wp_parse_args($custom, $columns);
        }

        public function jobsearch_job_sort_columns($vars) {
            global $wpdb;

            if (isset($vars['orderby']) && isset($_GET['post_type']) && $_GET['post_type'] == 'job') {
                if ('expiry' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_job_expiry_date',
                        'orderby' => 'meta_value'
                    ));
                } elseif ('posted' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_job_publish_date',
                        'orderby' => 'meta_value'
                    ));
                } elseif ('location' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_location_location1',
                        'orderby' => 'meta_value'
                    ));
                } elseif ('featured' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_job_featured',
                        'orderby' => 'meta_value'
                    ));
                } elseif ('filled' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_job_filled',
                        'orderby' => 'meta_value'
                    ));
                } elseif ('status' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_job_status',
                        'orderby' => 'meta_value'
                    ));
                }
            }
            return $vars;
        }

        public function jobsearch_job_sort_orderby_taxonomy($clauses, $wp_query) {
            if (!is_admin()) {
                return;
            }
            global $wpdb;

            if (isset($wp_query->query['orderby']) && 'jobtype' == $wp_query->query['orderby']) {

                $clauses['join'] .= "
                LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
                LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
                LEFT OUTER JOIN {$wpdb->terms} USING (term_id)";

                $clauses['where'] .= " AND (taxonomy = 'jobtype' OR taxonomy IS NULL)";
                $clauses['groupby'] = "object_id";
                $clauses['orderby'] = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
                $clauses['orderby'] .= ( 'ASC' == strtoupper($wp_query->get('order')) ) ? 'ASC' : 'DESC';
            }

            return $clauses;
        }

        public function jobsearch_job_sector() {
            // Add new taxonomy, make it hierarchical (like sectors)
            $labels = array(
                'name' => _x('Sectors', 'taxonomy general name', 'wp-jobsearch'),
                'singular_name' => _x('Sector', 'taxonomy singular name', 'wp-jobsearch'),
                'search_items' => __('Search Sectors', 'wp-jobsearch'),
                'all_items' => __('All Sectors', 'wp-jobsearch'),
                'parent_item' => __('Parent Sector', 'wp-jobsearch'),
                'parent_item_colon' => __('Parent Sector:', 'wp-jobsearch'),
                'edit_item' => __('Edit Sector', 'wp-jobsearch'),
                'update_item' => __('Update Sector', 'wp-jobsearch'),
                'add_new_item' => __('Add New Sector', 'wp-jobsearch'),
                'new_item_name' => __('New Sector Name', 'wp-jobsearch'),
                'menu_name' => __('Sector', 'wp-jobsearch'),
            );

            $args = array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'show_in_menu' => false,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'sector'),
            );

            register_taxonomy('sector', apply_filters('jobsearch_sector_tax_register_post_types', array('job', 'candidate', 'employer')), $args);
        }

        function admin_custom_script() {
            global $pagenow;
            $taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : '';
            if (($pagenow == 'edit-tags.php' || $pagenow == 'term.php') && $taxonomy == 'sector') {
                ?>
                <script>
                    var adminmenu = jQuery('#adminmenu');
                    adminmenu.find('>li.wp-has-current-submenu > a').removeClass('wp-has-current-submenu');
                    adminmenu.find('>li.wp-has-current-submenu > a').removeClass('wp-menu-open');
                    adminmenu.find('>li').removeClass('wp-has-current-submenu').addClass('wp-not-current-submenu');
                    adminmenu.find('>li').removeClass('wp-menu-open');
                    //
                    adminmenu.find('>li#toplevel_page_edit-tags-taxonomy-sector').removeClass('wp-not-current-submenu').addClass('current');
                    adminmenu.find('>li#toplevel_page_edit-tags-taxonomy-sector > a').removeClass('wp-not-current-submenu').addClass('current');
                </script>
                <?php
            }
            if ($pagenow == 'post.php' && isset($_GET['post']) && $_GET['post'] > 0) {
                $_post_id = $_GET['post'];
                if (get_post_type($_post_id) == 'job') {
                    ?>
                    <script>
                        jQuery('form[name=post]').on('submit', function() {
                            var form_error = false;
                            var comp_field = jQuery('input[name=jobsearch_field_job_posted_by]');
                            if (comp_field.val() == '') {
                                form_error = true;
                            }
                            if (form_error === true) {
                                alert('<?php esc_html_e('Please select posted by employer first.', 'wp-jobsearch') ?>');
                                return false;
                            }
                        });
                    </script>
                    <?php
                }
            }
            if (($pagenow == 'edit-tags.php') && $taxonomy == 'job-location') { ?>
                <script>
                    jQuery(document).ready(function () {
                        var page_tablenav = jQuery('.tablenav.top');
                        page_tablenav.append('<div id="jobsearch-jobslocs-countcon" class="joblocscountr-con" style="float: left; width: 100%; margin-bottom: 15px;"><div class="jobsearch-jobslocscount-loadr" style="float: left;"><span class="spinner is-active"></span></div></div>');
                        var joblocscountr_main_con = jQuery('#jobsearch-jobslocs-countcon').find('.jobsearch-jobslocscount-loadr');
                        var _joblocscountr_request = jQuery.ajax({
                            url: ajaxurl,
                            method: "POST",
                            data: {
                                adding: 'taxloc_joblocscountr_addbtn',
                                action: 'jobsearch_bkaddin_joblocscountr_addbtn',
                            },
                            dataType: "json"
                        });
                        _joblocscountr_request.done(function (response) {
                            joblocscountr_main_con.html('');
                            if (typeof response.html !== 'undefined' && response.html != '') {
                                joblocscountr_main_con.html(response.html);
                            }
                        });
                        _joblocscountr_request.fail(function () {
                            joblocscountr_main_con.html('');
                        });
                    });
                    jQuery(document).on('click', '.jobsearch-countr-updtebtn', function () {
                        var counter_con = jQuery(this).parent('.locs-countr-updtebtncon').find('.counts-lodr');
                        counter_con.html('<span class="spinner is-active"></span>');
                        var _joblocscountr_request = jQuery.ajax({
                            url: ajaxurl,
                            method: "POST",
                            data: {
                                adding: 'taxloc_joblocscountr_update',
                                action: 'jobsearch_bkaddin_joblocscountr_update',
                            },
                            dataType: "json"
                        });
                        _joblocscountr_request.done(function (response) {
                            counter_con.html('');
                            if (typeof response.msg !== 'undefined' && response.msg != '') {
                                counter_con.html(response.msg);
                            }
                        });
                        _joblocscountr_request.fail(function () {
                            counter_con.html('');
                        });
                    });
                </script>
                <?php
            }
        }
        
        public function job_locscountr_addbtn() {
            $html = '<div class="locs-countr-updtebtncon"><a href="javascript:void(0);" class="jobsearch-countr-updtebtn button button-primary">' . __('Update Jobs Location Counts', 'wp-jobsearch') . '</a><strong class="counts-lodr"></strong></div>';
            echo json_encode(array('html' => $html));
            die;
        }
        
        public function bkaddin_joblocscountr_update() {
            $all_locs = jobsearch_get_terms_woutparnt('job-location');
            if (!empty($all_locs) && !is_wp_error($all_locs)) {
                foreach ($all_locs as $term_loc) {
                    $job_args = array(
                        'posts_per_page' => '1',
                        'post_type' => 'job',
                        'post_status' => 'publish',
                        'fields' => 'ids', // only load ids
                        'meta_query' => array(
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'jobsearch_field_location_location1',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location2',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location3',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location4',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                            ),
                            array(
                                'key' => 'jobsearch_field_job_publish_date',
                                'value' => strtotime(current_time('d-m-Y H:i:s')),
                                'compare' => '<=',
                            ),
                            array(
                                'key' => 'jobsearch_field_job_expiry_date',
                                'value' => strtotime(current_time('d-m-Y H:i:s')),
                                'compare' => '>=',
                            ),
                            array(
                                'key' => 'jobsearch_field_job_status',
                                'value' => 'approved',
                                'compare' => '=',
                            )
                        ),
                    );
                    $jobs_query = new WP_Query($job_args);
                    $found_jobs = $jobs_query->found_posts;
                    wp_reset_postdata();

                    update_term_meta($term_loc->term_id, 'active_jobs_loc_count', absint($found_jobs));
                }
            }
            $msg = '&nbsp; ' . __('Location Counts Updated Successfully.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg));
            die;
        }

        public function jobsearch_job_jobtype() {
            // Add new taxonomy, make it hierarchical (like jobtypes)
            $labels = array(
                'name' => _x('Job Types', 'taxonomy general name', 'wp-jobsearch'),
                'singular_name' => _x('Job Type', 'taxonomy singular name', 'wp-jobsearch'),
                'search_items' => __('Search Job Types', 'wp-jobsearch'),
                'all_items' => __('All Job Types', 'wp-jobsearch'),
                'parent_item' => __('Parent Job Type', 'wp-jobsearch'),
                'parent_item_colon' => __('Parent Job Type:', 'wp-jobsearch'),
                'edit_item' => __('Edit Job Type', 'wp-jobsearch'),
                'update_item' => __('Update Job Type', 'wp-jobsearch'),
                'add_new_item' => __('Add New Job Type', 'wp-jobsearch'),
                'new_item_name' => __('New Job Type Name', 'wp-jobsearch'),
                'menu_name' => __('Job Type', 'wp-jobsearch'),
            );

            $args = array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'jobtype'),
            );

            register_taxonomy('jobtype', apply_filters('jobsearch_jobtype_associate_post_types', array('job')), $args);
        }

        public function jobsearch_job_save_jobtype_fields_added_callback($term_id) {
            if (isset($_POST['jobsearch_field_jobtype_image_meta']) && $_POST['jobsearch_field_jobtype_image_meta'] == '1') {
                if (isset($_POST['jobsearch_field_jobtype_color'])) {
                    $jobtype_color = $_POST['jobsearch_field_jobtype_color'];
                    add_term_meta($term_id, 'jobsearch_field_jobtype_color', $jobtype_color, true);
                }
                if (isset($_POST['jobsearch_field_jobtype_textcolor'])) {
                    $jobtype_textcolor = $_POST['jobsearch_field_jobtype_textcolor'];
                    add_term_meta($term_id, 'jobsearch_field_jobtype_textcolor', $jobtype_textcolor, true);
                }
                if (isset($_POST['jobsearch_field_jobtype_img_field'])) {
                    $jobtype_img_field = $_POST['jobsearch_field_jobtype_img_field'];
                    add_term_meta($term_id, 'jobsearch_field_jobtype_img_field', $jobtype_img_field, true);
                }
            }
        }

        public function jobsearch_job_save_jobtype_fields_updated_callback($term_id) {
            if (isset($_POST['jobsearch_field_jobtype_image_meta']) and $_POST['jobsearch_field_jobtype_image_meta'] == '1') {
                if (isset($_POST['jobsearch_field_jobtype_color'])) {
                    $jobtype_color = $_POST['jobsearch_field_jobtype_color'];
                    update_term_meta($term_id, 'jobsearch_field_jobtype_color', $jobtype_color);
                }
                if (isset($_POST['jobsearch_field_jobtype_textcolor'])) {
                    $jobtype_textcolor = $_POST['jobsearch_field_jobtype_textcolor'];
                    update_term_meta($term_id, 'jobsearch_field_jobtype_textcolor', $jobtype_textcolor);
                }
                if (isset($_POST['jobsearch_field_jobtype_img_field'])) {
                    $jobtype_img_field = $_POST['jobsearch_field_jobtype_img_field'];
                    update_term_meta($term_id, 'jobsearch_field_jobtype_img_field', $jobtype_img_field);
                }
                if (isset($_POST['jobtype_icon'])) {
                    $jobtype_icon = $_POST['jobtype_icon'];
                    update_term_meta($term_id, 'jobsearch_field_jobtype_icon_field', $jobtype_icon);
                }
                if (isset($_POST['jobtype_icon_group'])) {
                    $jobtype_icon_group = $_POST['jobtype_icon_group'];
                    update_term_meta($term_id, 'jobsearch_field_jobtype_icon_lib_field', $jobtype_icon_group);
                }
            }
        }

        public function jobsearch_job_edit_jobtype_fields_callback($tag) { //check for existing featured ID
            global $jobsearch_form_fields, $careerfy_icons_fields;
            
            $rand_id = rand(10000000, 99999999);

            $jobtype_color = "";
            $jobtype_textcolor = "";
            wp_enqueue_media();
            $jobtype_coordinates = "";
            $jobtype_url = '';
            if (isset($tag->term_id)) {
                $term_id = $tag->term_id;

                $jobtype_color = get_term_meta($term_id, 'jobsearch_field_jobtype_color', true);
                $jobtype_textcolor = get_term_meta($term_id, 'jobsearch_field_jobtype_textcolor', true);
                $jobtype_url = get_term_meta($term_id, 'jobsearch_field_jobtype_img_field', true);
                $jobtype_icon = get_term_meta($term_id, 'jobsearch_field_jobtype_icon_field', true);
                $term_icon_lib = get_term_meta($term_id, 'jobsearch_field_jobtype_icon_lib_field', true);
                if ($term_icon_lib == '') {
                    $term_icon_lib = 'default';
                }
            }
            $opt_array = array(
                'id' => 'jobtype_image_meta',
                'force_std' => "1",
                'name' => "jobtype_image_meta",
                'return' => false,
            );
            $jobsearch_form_fields->input_hidden_field($opt_array);
            ?>
            <tr>
                <th><label for="cat_f_img_url"> <?php echo esc_html__('Job Type Color', 'wp-jobsearch'); ?></label></th>
                <td>
                    <?php
                    $field_params = array(
                        'name' => 'jobtype_color',
                        'classes' => 'color-picker',
                        'ext_attr' => 'data-alpha="true"',
                        'force_std' => esc_attr($jobtype_color),
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?> 
                </td>
            </tr>
            <tr>
                <th><label for="cat_f_img_url"> <?php echo esc_html__('Job Type Text Color', 'wp-jobsearch'); ?></label></th>
                <td>
                    <?php
                    $field_params = array(
                        'name' => 'jobtype_textcolor',
                        'classes' => 'color-picker',
                        'force_std' => esc_attr($jobtype_textcolor),
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?> 
                </td>
            </tr>
            <?php
            if (is_object($careerfy_icons_fields)) {
                ?>
                <tr class="form-field">
                    <th><label for="cat_cus_icon"> <?php esc_html_e("Choose Icon", "careerfy-frame"); ?></label></th>
                    <td>
                        <?php echo $careerfy_icons_fields->careerfy_icons_fields_callback($jobtype_icon, $rand_id, 'jobtype_icon', $term_icon_lib) ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <th><label for="cat_f_img_url"><?php echo esc_html__('Job Type Image', 'wp-jobsearch'); ?></label></th>
                <td class="jobtype-img-field">
                    <?php
                    $field_params = array(
                        'id' => rand(100000, 999999),
                        'name' => 'jobtype_img_field',
                        'force_std' => esc_url($jobtype_url),
                    );
                    $jobsearch_form_fields->image_upload_field($field_params);
                    ?>
                </td>
            </tr>


            <?php
        }

        public function jobsearch_job_jobtype_fields_callback($tag) { //check for existing featured ID
            global $jobsearch_form_fields, $careerfy_icons_fields;
            
            $rand_id = rand(10000000, 99999999);
            
            wp_enqueue_media();
            if (isset($tag->term_id)) {
                $t_id = $tag->term_id;
            } else {
                $t_id = '';
            }
            $jobtype_image = '';
            $jobtype_color = '';
            $jobtype_textcolor = '';
            ?>
            <div class="form-field">

                <label><?php echo esc_html__('Job Type Color', 'wp-jobsearch'); ?></label>
                <ul class="form-elements" style="margin:0; padding:0;">
                    <li class="to-field" style="width:100%;">
                        <?php
                        $field_params = array(
                            'name' => 'jobtype_color',
                            'classes' => 'color-picker',
                            'ext_attr' => 'data-alpha="true"',
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?> 
                    </li>
                </ul>
                <br> <br>
            </div>
            <div class="form-field">

                <label><?php echo esc_html__('Job Type Text Color', 'wp-jobsearch'); ?></label>
                <ul class="form-elements" style="margin:0; padding:0;">
                    <li class="to-field" style="width:100%;">
                        <?php
                        $field_params = array(
                            'name' => 'jobtype_textcolor',
                            'classes' => 'color-picker',
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?> 
                    </li>
                </ul>
                <br> <br>
            </div>
            <?php
            if (is_object($careerfy_icons_fields)) {
                ?>
                <div class="form-field">
                    <label for="cat_cus_icon"> <?php esc_html_e("Choose Icon", "careerfy-frame"); ?></label>
                    <?php echo $careerfy_icons_fields->careerfy_icons_fields_callback('', $rand_id, 'jobtype_icon') ?>
                </div>
                <?php
            }
            ?>
            <div class="form-field jobtype-img-field">
                <label><?php echo esc_html__('Job Type image', 'wp-jobsearch'); ?></label>
                <ul class="form-elements" style="margin:0; padding:0;">
                    <li class="to-field" style="width:100%;">
                        <?php
                        $field_params = array(
                            'id' => rand(100000, 999999),
                            'name' => 'jobtype_img_field',
                            'force_std' => '',
                        );
                        $jobsearch_form_fields->image_upload_field($field_params);
                        ?>
                    </li>
                </ul> 
            </div> 
            <?php
            $opt_array = array(
                'id' => 'jobtype_image_meta',
                'force_std' => "1",
                'name' => "jobtype_image_meta",
                'return' => false,
            );
            $jobsearch_form_fields->input_hidden_field($opt_array);
        }

        public function jobsearch_job_skills() {
            // Add new taxonomy, make it hierarchical (like skills)
            $labels = array(
                'name' => _x('Skills', 'taxonomy general name', 'wp-jobsearch'),
                'singular_name' => _x('Skill', 'taxonomy singular name', 'wp-jobsearch'),
                'search_items' => __('Search Skills', 'wp-jobsearch'),
                'all_items' => __('All Skills', 'wp-jobsearch'),
                'parent_item' => __('Parent Skill', 'wp-jobsearch'),
                'parent_item_colon' => __('Parent Skill:', 'wp-jobsearch'),
                'edit_item' => __('Edit Skill', 'wp-jobsearch'),
                'update_item' => __('Update Skill', 'wp-jobsearch'),
                'add_new_item' => __('Add New Skill', 'wp-jobsearch'),
                'new_item_name' => __('New Skill Name', 'wp-jobsearch'),
                'menu_name' => __('Skills', 'wp-jobsearch'),
            );

            $args = array(
                'hierarchical' => false,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'skill'),
            );

            register_taxonomy('skill', apply_filters('jobsearch_skill_tax_register_post_types', array('job', 'candidate')), apply_filters('jobsearch_skill_tax_register_argsarr', $args));
        }

        public function update_sectors_real_count_meta() {
            $cachetime = 900;
            $transient = 'jobsearch_sectors_realcount_cache';

            $check_transient = get_transient($transient);
            if (empty($check_transient)) {
                $jobsearch__options = get_option('jobsearch_plugin_options');
                $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';
                
                $element_filter_arr = array();
                $element_filter_arr[] = array(
                    'key' => 'jobsearch_field_job_publish_date',
                    'value' => strtotime(current_time('d-m-Y H:i:s')),
                    'compare' => '<=',
                );

                $element_filter_arr[] = array(
                    'key' => 'jobsearch_field_job_expiry_date',
                    'value' => strtotime(current_time('d-m-Y H:i:s')),
                    'compare' => '>=',
                );

                $element_filter_arr[] = array(
                    'key' => 'jobsearch_field_job_status',
                    'value' => 'approved',
                    'compare' => '=',
                );
                if ($emporler_approval != 'off') {
                    $element_filter_arr[] = array(
                        'key' => 'jobsearch_job_employer_status',
                        'value' => 'approved',
                        'compare' => '=',
                    );
                }

                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                if (!empty($all_sectors) && !is_wp_error($all_sectors)) {

                    foreach ($all_sectors as $term_sector) {
                        $job_args = array(
                            'posts_per_page' => '1',
                            'post_type' => 'job',
                            'post_status' => 'publish',
                            'fields' => 'ids', // only load ids
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'sector',
                                    'field' => 'slug',
                                    'terms' => $term_sector->slug
                                )
                            ),
                            'meta_query' => $element_filter_arr,
                        );
                        $jobs_query = new WP_Query($job_args);
                        $found_jobs = $jobs_query->found_posts;
                        wp_reset_postdata();

                        update_term_meta($term_sector->term_id, 'active_jobs_count', absint($found_jobs));
                    }
                }
                
                //
                $all_sectors = get_terms(array(
                    'taxonomy' => 'jobtype',
                    'hide_empty' => false,
                ));
                if (!empty($all_sectors) && !is_wp_error($all_sectors)) {

                    foreach ($all_sectors as $term_sector) {
                        $job_args = array(
                            'posts_per_page' => '1',
                            'post_type' => 'job',
                            'post_status' => 'publish',
                            'fields' => 'ids', // only load ids
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'jobtype',
                                    'field' => 'slug',
                                    'terms' => $term_sector->slug
                                )
                            ),
                            'meta_query' => $element_filter_arr,
                        );
                        $jobs_query = new WP_Query($job_args);
                        $found_jobs = $jobs_query->found_posts;
                        wp_reset_postdata();

                        update_term_meta($term_sector->term_id, 'active_jobs_count', absint($found_jobs));
                    }
                }

                //
                set_transient($transient, true, $cachetime);
            }
        }

    }

    return new post_type_job();
}
