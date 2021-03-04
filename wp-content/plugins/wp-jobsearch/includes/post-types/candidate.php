<?php

/**
 * @Manage Columns
 * @return
 *
 */
if (!class_exists('post_type_candidate')) {

    class post_type_candidate {

        // The Constructor
        public function __construct() {
            // Adding columns
            add_action('admin_footer', array($this, 'change_featimg_meta_title'));
            add_action('admin_footer', array($this, 'candidates_list_js'));
            
            add_filter('manage_candidate_posts_columns', array($this, 'jobsearch_candidate_columns_add'));
            add_action('manage_candidate_posts_custom_column', array($this, 'jobsearch_candidate_columns'), 10, 2);
            add_filter('list_table_primary_column', array($this, 'jobsearch_primary_column'), 10, 2);
            add_action('init', array($this, 'jobsearch_candidate_register'), 1); // post type register
            add_action('init', array($this, 'jobsearch_candidate_sector'), 0);
            add_filter('post_row_actions', array($this, 'jobsearch_candidate_row_actions'));
            add_filter('manage_edit-candidate_sortable_columns', array($this, 'jobsearch_candidate_sortable_columns'));
            add_filter('request', array($this, 'jobsearch_candidate_sort_columns'));
            add_action('admin_head', array($this, 'my_admin_custom_styles'));
            //
            add_action('views_edit-candidate', array($this, 'modified_views_so'), 0);
            add_filter('parse_query', array($this, 'candidates_query_filter'), 11, 1);
            add_filter('bulk_actions-edit-candidate', array($this, 'custom_job_filters'));
            add_action('handle_bulk_actions-edit-candidate', array($this, 'jobs_bulk_actions_handle'), 10, 3);
            //
            add_action('wp_ajax_jobsearch_calc_candidates_applied_jobs_bklist', array($this, 'cand_aplied_calc_in_column'));
            add_action('wp_ajax_jobsearch_bkaddin_candidate_advsrch_filters', array($this, 'bkaddin_advsrch_filters'));
            
            add_action('wp_ajax_jobsearch_bkadmin_resend_activation_mail', array($this, 'resend_activation_mail'));
        }

        function my_admin_custom_styles() {
            global $pagenow;
            if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'candidate') {
                $output_css = '<style type="text/css"> 
                    .column-candidate_title { min-width:200px !important; max-width:500px !important; overflow:hidden }
                    .column-location { min-width:150px !important; max-width:300px !important; overflow:hidden }
                    .column-jobtitle { min-width:150px !important; max-width:300px !important; overflow:hidden }
                    .column-featured { width:10px !important; overflow:hidden }
                    .post-type-candidate .column-applied_jobs { width:108px !important; overflow:hidden; } 
                    .column-filled { width:30px !important; overflow:hidden }
                    .column-status { width:30px !important; overflow:hidden }
                    .column-action { text-align:right !important; width:150px !important; overflow:hidden }
                </style>';
                echo $output_css;
            }
        }

        public function jobsearch_candidate_register() {
            
            $reg_post_type = apply_filters('jobsearch_allow_candidate_post_type_reg', '1');
            
            $jobsearch__options = get_option('jobsearch_plugin_options');
            
            $candidate_slug = isset($jobsearch__options['candidate_rewrite_slug']) && $jobsearch__options['candidate_rewrite_slug'] != '' ? $jobsearch__options['candidate_rewrite_slug'] : 'candidate';
            
            $labels = array(
                'name' => _x('Candidates', 'post type general name', 'wp-jobsearch'),
                'singular_name' => _x('Candidate', 'post type singular name', 'wp-jobsearch'),
                'menu_name' => _x('Candidates', 'admin menu', 'wp-jobsearch'),
                'name_admin_bar' => _x('Candidate', 'add new on admin bar', 'wp-jobsearch'),
                'add_new' => _x('Add New', 'candidate', 'wp-jobsearch'),
                'add_new_item' => __('Add New Candidate', 'wp-jobsearch'),
                'new_item' => __('New Candidate', 'wp-jobsearch'),
                'edit_item' => __('Edit Candidate', 'wp-jobsearch'),
                'view_item' => __('View Candidate', 'wp-jobsearch'),
                'all_items' => __('All Candidates', 'wp-jobsearch'),
                'search_items' => __('Search Candidates', 'wp-jobsearch'),
                'parent_item_colon' => __('Parent Candidates:', 'wp-jobsearch'),
                'not_found' => __('No candidates found.', 'wp-jobsearch'),
                'not_found_in_trash' => __('No candidates found in Trash.', 'wp-jobsearch')
            );

            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'wp-jobsearch'),
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => $candidate_slug),
                'capability_type' => 'post',
                'has_archive' => false,
                'exclude_from_search' => true,
                'hierarchical' => false,
                //'menu_position' => 27,
                'supports' => array('title', 'editor', 'excerpt')
            );

            if ($reg_post_type == '1') {
				$args = apply_filters('jobsearch_reg_post_type_cand_args', $args);
                register_post_type('candidate', $args);
            }
        }
        
        public function change_featimg_meta_title() {
            global $pagenow;
            $post_type = '';
            
            if ($pagenow == 'post.php') {
                $post_id = isset($_GET['post']) ? $_GET['post'] : '';
                $post_obj = get_post($post_id);
                $post_type = isset($post_obj->post_type) ? $post_obj->post_type : '';
            }
            if ($post_type == 'candidate') {
                ?>
                <script>
                    jQuery('#postimagediv > h2').html('<span><?php esc_html_e('Profile Photo', 'wp-jobsearch') ?></span>');
                    jQuery('a#set-post-thumbnail').html('<?php esc_html_e('Set profile photo', 'wp-jobsearch') ?>');
                </script>
                <?php
            }
        }

        public function jobsearch_candidate_row_actions($actions) {
            if ('candidate' == get_post_type()) {
                return array();
            }
            return $actions;
        }
        
        public function bkaddin_advsrch_filters() {
            global $jobsearch_plugin_options;
            $search_title_val = isset($_REQUEST['search_title']) ? $_REQUEST['search_title'] : '';
            $location_val = isset($_REQUEST['search_loc']) ? $_REQUEST['search_loc'] : '';
            $cat_sector_val = isset($_REQUEST['sector_cat']) ? $_REQUEST['sector_cat'] : '';
            
            ob_start();
            ?>
            <div class="jobsearch-top-searchbar jobsearch-typo-wrap candidate-bkend-advncesrh-con">
                <div class="jobsearch-subheader-form">
                    <div class="jobsearch-banner-search">
                        <ul>
                            <li>
                                <div>
                                    <input placeholder="<?php esc_html_e('ID, Title, Keywords, or Phrase', 'wp-jobsearch') ?>" name="search_title" value="<?php echo($search_title_val) ?>" type="text">
                                </div>
                            </li>
                            <?php
                            ob_start();
                            ?>
                            <li>
                                <div class="jobsearch_searchloc_div">
                                    <span class="loc-loader"></span>
                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'wp-jobsearch') ?>" autocomplete="off" class="jobsearch_search_location_field" value="<?php echo urldecode($location_val) ?>" type="text">
                                    <input type="hidden" class="loc_search_keyword" name="search_loc" value="<?php echo urldecode($location_val) ?>">
                                </div>
                            </li>
                            <?php
                            $srch_loc_html = ob_get_clean();
                            echo apply_filters('jobsearch_cand_bk_advsrch_location_field', $srch_loc_html, $location_val);
                            
                            $sectors_args = array(
                                'orderby' => 'name',
                                'order' => 'ASC',
                                'fields' => 'all',
                                'hide_empty' => false,
                            );
                            $all_sectors = get_terms('sector', $sectors_args);
                            ?>
                            <li>
                                <div class="jobsearch-select-style">
                                    <select name="sector_cat" class="selectize-select" placeholder="<?php esc_html_e('Select Sector', 'wp-jobsearch') ?>">
                                        <option value=""><?php esc_html_e('Select Sector', 'wp-jobsearch') ?></option>
                                        <?php
                                        if (!empty($all_sectors)) {
                                            echo jobsearch_sector_terms_hierarchical(0, $all_sectors, '', 0, 0, $cat_sector_val);
                                        }
                                        ?>
                                    </select>
                                </div>
                            </li>
                            <li class="adv-srch-toggler"><a href="javascript:void(0);" class="adv-srch-toggle-btn"><span>+</span> <?php esc_html_e('Advance Search', 'wp-jobsearch') ?></a></li>
                            <li class="jobsearch-banner-submit">
                                <input type="submit" value=""> <i class="jobsearch-icon jobsearch-search"></i>
                            </li>
                        </ul>
                        <?php
                        $top_search_radius = isset($jobsearch_plugin_options['top_search_radius']) ? $jobsearch_plugin_options['top_search_radius'] : '';
                        $top_search_def_radius = isset($jobsearch_plugin_options['top_search_def_radius']) ? $jobsearch_plugin_options['top_search_def_radius'] : 50;
                        $top_search_max_radius = isset($jobsearch_plugin_options['top_search_max_radius']) ? $jobsearch_plugin_options['top_search_max_radius'] : 500;
                        ?>
                        <div class="adv-search-options">
                            <ul>
                                <li class="srch-radius-slidr">
                                    <?php
                                    $tprand_id = rand(1000000, 99999999);
                                    $tpsrch_min = 0;
                                    $tpsrch_field_max = $top_search_max_radius > 0 ? $top_search_max_radius : 500;
                                    $tpsrch_complete_str_first = "";
                                    $tpsrch_complete_str_second = "";
                                    $tpsrch_complete_str = '0';
                                    $tpsrch_complete_str_first = $tpsrch_min;
                                    $tpsrch_complete_str_second = $tpsrch_field_max;
                                    $tpsrch_str_var_name = 'loc_radius';
                                    if (isset($_REQUEST[$tpsrch_str_var_name])) {
                                        $tpsrch_complete_str = $_REQUEST[$tpsrch_str_var_name];
                                        $tpsrch_complete_str_arr = explode("-", $tpsrch_complete_str);
                                        $tpsrch_complete_str_first = isset($tpsrch_complete_str_arr[0]) ? $tpsrch_complete_str_arr[0] : '';
                                        $tpsrch_complete_str_second = isset($tpsrch_complete_str_arr[1]) ? $tpsrch_complete_str_arr[1] : '';
                                    } else {
                                        $tpsrch_complete_str = absint($top_search_def_radius);
                                        $tpsrch_complete_str_first = absint($top_search_def_radius);
                                    }
                                    $to_radius_unit = esc_html__('Km', 'wp-jobsearch');
                                    if ($def_radius_unit == 'miles') {
                                        $to_radius_unit = esc_html__('Miles', 'wp-jobsearch');
                                    }
                                    ?>
                                    <div class="filter-slider-range">
                                        <span class="radius-txt"><?php esc_html_e('Radius:', 'wp-jobsearch') ?></span>
                                        <span id="radius-num-<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>" class="radius-numvr-holdr"><?php echo esc_html($tpsrch_complete_str); ?></span>
                                        <span class="radius-punit"><?php echo($to_radius_unit) ?></span>
                                        <input type="hidden" id="loc-def-radiusval" value="<?php echo esc_html($tpsrch_complete_str) ?>">
                                        <input type="hidden" name="loc_radius" id="<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>" value="">
                                    </div>

                                    <div id="slider-tpsrch<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>"></div>
                                    <script>
                                        var toSetRadiusVal = setInterval(function() {
                                            jQuery('input#<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>').val('');
                                            <?php
                                            if ($tpsrch_complete_str > 0 && $tpsrch_field_max > $tpsrch_complete_str) {
                                            ?>
                                            var initSlideWidthPerc = (<?php echo ($tpsrch_complete_str) ?>/<?php echo absint($tpsrch_field_max); ?>)*100;
                                            jQuery("#slider-tpsrch<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").find('.ui-slider-range').css({width: initSlideWidthPerc + '%'});
                                            <?php
                                            }
                                            ?>
                                            clearInterval(toSetRadiusVal);
                                        }, 1000);

                                        jQuery("#slider-tpsrch<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").slider({
                                            tpsrch: true,
                                            range: "min",
                                            min: <?php echo absint($tpsrch_min); ?>,
                                            max: <?php echo absint($tpsrch_field_max); ?>,
                                            values: [<?php echo absint($tpsrch_complete_str_first); ?>],
                                            slide: function (event, ui) {
                                                var slideWidthPerc = ((ui.values[0])/<?php echo absint($tpsrch_field_max); ?>)*100;
                                                jQuery("#slider-tpsrch<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").find('.ui-slider-range').css({width: slideWidthPerc + '%'});
                                                jQuery("#<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").val(ui.values[0]);
                                                jQuery("#radius-num-<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").html(ui.values[0]);
                                            },
                                        });
                                        jQuery("#<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").val(jQuery("#slider-tpsrch<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").slider("values", 0));
                                    </script>
                                </li>
                                <li>
                                    <?php
                                    $posted = isset($_REQUEST['cand_posted']) ? $_REQUEST['cand_posted'] : '';
                                    ?>
                                    <div class="jobsearch-select-style">
                                        <select name="cand_posted" class="selectize-select" placeholder="<?php esc_html_e('Date Posted', 'wp-jobsearch'); ?>">
                                            <option value=""><?php esc_html_e('Date Posted', 'wp-jobsearch'); ?></option>
                                            <option value="lasthour" <?php echo ($posted == 'lasthour' ? 'selected="selected"' : '') ?>><?php esc_html_e('Last Hour', 'wp-jobsearch') ?></option>
                                            <option value="last24" <?php echo ($posted == 'last24' ? 'selected="selected"' : '') ?>><?php esc_html_e('Last 24 hours', 'wp-jobsearch') ?></option>
                                            <option value="7days" <?php echo ($posted == '7days' ? 'selected="selected"' : '') ?>><?php esc_html_e('Last 7 days', 'wp-jobsearch') ?></option>
                                            <option value="14days" <?php echo ($posted == '14days' ? 'selected="selected"' : '') ?>><?php esc_html_e('Last 14 days', 'wp-jobsearch') ?></option>
                                            <option value="30days" <?php echo ($posted == '30days' ? 'selected="selected"' : '') ?>><?php esc_html_e('Last 30 days', 'wp-jobsearch') ?></option>
                                            <option value="all" <?php echo ($posted == 'all' ? 'selected="selected"' : '') ?>><?php esc_html_e('All', 'wp-jobsearch') ?></option>
                                        </select>
                                    </div>
                                </li>
                                <?php
                                echo apply_filters('jobsearch_custom_fields_top_filters_html', '', 'candidate', 0);
                                ?>
                            </ul>
                        </div>
                    </div>
                    <script>
                    jQuery('.jobsearch_search_location_field').cityAutocomplete();
                    jQuery('.selectize-select').selectize();
                    </script>
                </div>
                <?php
                echo apply_filters('jobsearch_candpost_bk_cus_srchform_after', '');
                ?>
            </div>
            <?php
            $html = ob_get_clean();
            
            echo json_encode(array('html' => $html));
            die;
        }
        
        public function candidates_list_js() {
            global $pagenow, $jobsearch_plugin_options;
            $post_type = '';
            
            if ($pagenow == 'edit.php') {
                $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
            }
            if ($post_type == 'candidate') {
                $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';
                if ($location_map_type == 'mapbox') {
                    wp_enqueue_script('jobsearch-mapbox');
                    wp_enqueue_script('jobsearch-mapbox-geocoder');
                } else {
                    wp_enqueue_script('jobsearch-google-map');
                }
                wp_enqueue_style('jquery-ui');
                wp_enqueue_script('jquery-ui');
                wp_enqueue_script('jobsearch-location-autocomplete');
                wp_enqueue_script('jobsearch-selectize');
                ?>
                <script>
                    jQuery(document).ready(function () {
                        var _this_form = jQuery('#posts-filter');
                        var _post_check_ids = _this_form.find('input[type=checkbox][name^="post"]');
                        
                        var _candidtes_ids = [];
                        if (_post_check_ids.length > 0) {
                            jQuery.each(_post_check_ids, function(_ind, _elm) {
                                var _cand_id = jQuery(this).attr('value');
                                _candidtes_ids.push(_cand_id);
                            });
                        }
                        
                        _candidtes_ids = _candidtes_ids.join();
                        
                        var _all_post_data = {
                            candidate_ids: _candidtes_ids,
                            action: 'jobsearch_calc_candidates_applied_jobs_bklist'
                        }
                        var _cand_request = jQuery.ajax({
                            url: ajaxurl,
                            method: "POST",
                            data: _all_post_data,
                            dataType: "json"
                        });
                        _cand_request.done(function (response) {
                            console.log(response.msg);
                            if ('undefined' !== response.cand_list && response.cand_list) {
                                jQuery.each(response.cand_list, function(_indx, _elem) {
                                    //console.log(_indx + ' : ' + _elem);
                                    jQuery('#cand-apliedjobs-' + _indx).html(_elem);
                                });
                            }
                        });
                        
                        // adding advance search
                        var page_tablenav = jQuery('.tablenav.top');
                        var tablenav_pages_con = page_tablenav.find('.tablenav-pages');
                        if (!tablenav_pages_con.hasClass('no-pages')) {
                            page_tablenav.append('<div id="jobsearch-candadvsrch-filters" class="candadvsrch-filters-con" style="float: left; width: 100%;"><div class="jobsearch-advfiltr-loadr" style="float: left;"><span class="spinner is-active"></span></div></div>');
                            var advsrch_main_con = jQuery('#jobsearch-candadvsrch-filters');
                            var _advsrch_fitr_request = jQuery.ajax({
                                url: ajaxurl,
                                method: "POST",
                                data: {
                                    adding: 'candidate_advsrch_filters',
                                    candidate_ids: _candidtes_ids,
                                    <?php
                                    if (isset($_REQUEST) && !empty($_REQUEST)) {
                                        foreach ($_REQUEST as $requs_key => $requs_val) {
                                            echo ($requs_key . ': ' . "'" . $requs_val . "',") . "\n";
                                        }
                                    }
                                    ?>
                                    action: 'jobsearch_bkaddin_candidate_advsrch_filters',
                                },
                                dataType: "json"
                            });
                            _advsrch_fitr_request.done(function (response) {
                                advsrch_main_con.html('');
                                if (typeof response.html !== 'undefined' && response.html != '') {
                                    advsrch_main_con.html(response.html);
                                }
                            });
                            _advsrch_fitr_request.fail(function () {
                                advsrch_main_con.html('');
                            });
                        }
                    });

                    jQuery(document).on('click', '.adv-srch-toggle-btn', function () {
                        jQuery(this).parents('.candidate-bkend-advncesrh-con').find('.adv-search-options').slideToggle();
                        var slider_input_con = jQuery(this).parents('.candidate-bkend-advncesrh-con').find('.adv-search-options').find('.filter-slider-range');
                        var def_radius_val = slider_input_con.find('#loc-def-radiusval').val();
                        slider_input_con.find('input[name=loc_radius]').val(def_radius_val);
                    });
                    
                    jQuery(document).on('click', '.resend-active-mail', function () {
                        var _this = jQuery(this);
                        var _u_id = _this.attr('data-id');
                        var this_parent = _this.parent('div');
                        var pre_tag = this_parent.find('strong');
                        
                        pre_tag.html('');
                        this_parent.append('<span class="spinner is-active"></span>');
                        var _resnd_mail_request = jQuery.ajax({
                            url: ajaxurl,
                            method: "POST",
                            data: {
                                doing: 'resend_activation_mail',
                                u_id: _u_id,
                                action: 'jobsearch_bkadmin_resend_activation_mail',
                            },
                            dataType: "json"
                        });
                        _resnd_mail_request.done(function (response) {
                            pre_tag.html('<i class="dashicons dashicons-yes" style="color: #94e80d;"></i>');
                        });
                        _resnd_mail_request.complete(function () {
                            this_parent.find('span').remove();
                        });
                    });
                </script>
                <?php
            }
        }
        
        public function resend_activation_mail() {
            $user_id = $_POST['u_id'];
            $user_objj = get_user_by('id', $user_id);
            $code = wp_generate_password(20, false);
            update_user_meta($user_id, 'jobsearch_accaprov_key', $code);
            update_user_meta($user_id, 'jobsearch_accaprov_allow', '0');
            do_action('jobsearch_new_candidate_approval', $user_objj, '');

            echo json_encode(array('success' => '1', 'msg' => ''));
            die;
        }
        
        public function cand_aplied_calc_in_column() {
            $cand_ids = isset($_POST['candidate_ids']) ? $_POST['candidate_ids'] : '';
            
            $cands_list = array();
            $msg = 'No candidate found.';
            if ($cand_ids != '') {
                $cand_ids = explode(',', $cand_ids);
                
                if (!empty($cand_ids)) {
                    foreach ($cand_ids as $candidate_id) {
                        $cand_user_id = jobsearch_get_candidate_user_id($candidate_id);
                        $user_applied_jobs = get_user_meta($cand_user_id, 'jobsearch-user-jobs-applied-list', true);
                        $total_jobs = !empty($user_applied_jobs) ? count($user_applied_jobs) : 0;
                        $cands_list[$candidate_id] = absint($total_jobs);
                    }
                    $msg = 'Calculated';
                }
            }
            
            echo json_encode(array('cand_list' => $cands_list, 'msg' => $msg));
            die;
        }
        
        public function custom_job_filters($actions) {
            if (is_array($actions)) {
                $actions['approved'] = esc_html__('Approved', 'wp-jobsearch');
                $actions['pending'] = esc_html__('Pending', 'wp-jobsearch');
            }
            return apply_filters('jobsearch_add_candactions_bk_list', $actions);
        }

        public function jobs_bulk_actions_handle($redirect_to, $doaction, $post_ids) {
            if ($doaction == 'approved' || $doaction == 'pending') {
                if (!empty($post_ids)) {
                    foreach ($post_ids as $candidate_id) {
                        $user_aproved = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                        if ($user_aproved != 'on') {
                            $user_id = get_post_meta($candidate_id, 'jobsearch_user_id', true);
                            $user_obj = get_user_by('ID', $user_id);
                            if (isset($user_obj->ID)) {
                                do_action('jobsearch_profile_approval_to_candidate', $user_obj);
                            }
                        }
                        
                        $do_save = $doaction == 'approved' ? 'on' : '';
                        update_post_meta($candidate_id, 'jobsearch_field_candidate_approved', $do_save);
                    }
                }
            }
            do_action('jobsearch_doing_candactions_bk_list', $doaction, $post_ids);
            return $redirect_to;
        }

        public function candidates_query_filter($query) {
            global $pagenow, $jobsearch_shortcode_candidates_frontend;

            $custom_filter_arr = $custom_taxquery_arr = array();
            $post__in_query = false;
            $post__in_isarr = array();
            if (is_admin() && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'candidate') {
                $all_post_ids = array();
                if (isset($_GET['candidate_status']) && $_GET['candidate_status'] != '') {
                    if ($_GET['candidate_status'] == 'approved') {
                        $custom_filter_arr[] = array(
                            'key' => 'jobsearch_field_candidate_approved',
                            'value' => 'on',
                            'compare' => '=',
                        );
                    } else {
                        $custom_filter_arr[] = array(
                            'key' => 'jobsearch_field_candidate_approved',
                            'value' => 'on',
                            'compare' => '!=',
                        );
                    }
                }
                if (isset($_GET['sector_cat']) && $_GET['sector_cat'] != '') {
                    $sector_cat = $_GET['sector_cat'];
                    $custom_taxquery_arr[] = array(
                        'taxonomy' => 'sector',
                        'field' => 'slug',
                        'terms' => $sector_cat
                    );
                }
                //
                $left_filter_arr = apply_filters('jobsearch_custom_fields_load_filter_array_html', 'candidate', array(), '');
                $cusfields_post_ids = array();
                if (!empty($left_filter_arr)) {
                    $post__in_query = true;
                    $post__in_isarr[] = 'custom_fields';
                    $srch_post_ids = $this->get_candidate_id_by_filter($left_filter_arr);
                    
                    if (!empty($srch_post_ids)) {
                        $all_post_ids = array_merge($all_post_ids, $srch_post_ids);
                    }
                }
                //
                if (isset($_GET['search_title']) && $_GET['search_title'] != '') {
                    $post__in_query = true;
                    $post__in_isarr[] = 'search_title';
                    $search_keyword = $_GET['search_title'];
                    $srch_post_ids = jobsearch_get_serchable_keywrd_candidate_ids($search_keyword);
                    if (!empty($srch_post_ids) && count($post__in_isarr) > 1) {
                        $all_post_ids = array_intersect($all_post_ids, $srch_post_ids);
                    } else if (!empty($srch_post_ids)) {
                        $all_post_ids = array_merge($all_post_ids, $srch_post_ids);
                    }
                }
                if (isset($_GET['search_loc']) && $_GET['search_loc'] != '') {
                    $post__in_query = true;
                    $post__in_isarr[] = 'search_loc';
                    $search_location = $_GET['search_loc'];

                    $search_location = apply_filters('jobsearch_cand_bksrch_location_getstr', $search_location);
                    $srch_post_ids = $this->candidate_location_filter($search_location);
                    if (!empty($srch_post_ids) && count($post__in_isarr) > 1) {
                        $all_post_ids = array_intersect($all_post_ids, $srch_post_ids);
                    } else if (!empty($srch_post_ids)) {
                        $all_post_ids = array_merge($all_post_ids, $srch_post_ids);
                    }
                }
                if (isset($_GET['cand_posted']) && $_GET['cand_posted'] != '') {
                    $post__in_query = true;
                    $post__in_isarr[] = 'cand_posted';
                    $cand_posted = $_GET['cand_posted'];
                    $srch_post_ids = $this->cand_posted_filter($cand_posted);
                    if (!empty($srch_post_ids) && count($post__in_isarr) > 1) {
                        $all_post_ids = array_intersect($all_post_ids, $srch_post_ids);
                    } else if (!empty($srch_post_ids)) {
                        $all_post_ids = array_merge($all_post_ids, $srch_post_ids);
                    }
                }
                
                //
                if (!empty($custom_filter_arr)) {
                    $query->set('meta_query', $custom_filter_arr);
                }
                if (!empty($custom_taxquery_arr)) {
                    $query->set('tax_query', $custom_taxquery_arr);
                }
                
                //
                if (empty($all_post_ids)) {
                    $all_post_ids = array('0');
                } else {
                    $all_post_ids = array_unique($all_post_ids);
                }
                if ($post__in_query) {
                    $query->set('post__in', $all_post_ids);
                }
            }
        }
        
        public function get_candidate_id_by_filter($left_filter_arr) {
            global $wpdb;
            $meta_post_ids_arr = '';
            $candidate_id_condition = '';

            if (isset($left_filter_arr) && !empty($left_filter_arr)) {
                $meta_post_ids_arr = jobsearch_get_query_whereclase_by_array($left_filter_arr);

                // if no result found in filtration
                if (empty($meta_post_ids_arr)) {
                    $meta_post_ids_arr = array(0);
                }
                $ids = $meta_post_ids_arr != '' ? implode(",", $meta_post_ids_arr) : '0';
                $candidate_id_condition = " ID in (" . $ids . ") AND ";
            }

            $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $candidate_id_condition . " post_type='candidate' AND post_status='publish'");

            if (empty($post_ids)) {
                $post_ids = array(0);
            }
            return $post_ids;
        }
        
        public function candidate_location_filter($location_val) {

            global $wpdb;

            $location_rslt = array();

            if ($location_val != '') {
                
                $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
                $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta ON postmeta.post_id = posts.ID";
                $post_ids_query .= " WHERE post_type='candidate' AND post_status='publish'";
                $post_ids_query .= " AND (";
                $post_ids_query .= " (postmeta.meta_key='jobsearch_field_location_address' AND postmeta.meta_value LIKE '%{$location_val}%') OR";
                $post_ids_query .= " (postmeta.meta_key='jobsearch_field_location_location1' AND postmeta.meta_value LIKE '%{$location_val}%') OR";
                $post_ids_query .= " (postmeta.meta_key='jobsearch_field_location_location2' AND postmeta.meta_value LIKE '%{$location_val}%') OR";
                $post_ids_query .= " (postmeta.meta_key='jobsearch_field_location_location3' AND postmeta.meta_value LIKE '%{$location_val}%')";
                $post_ids_query .= " )";
                $post_ids_query .= " GROUP BY posts.ID";
                
                $location_rslt = $wpdb->get_col($post_ids_query);
            }
            
            return $location_rslt;
        }
        
        public function cand_posted_filter($posted) {
            global $wpdb;
            
            $current_timestamp = current_time('timestamp');
            $default_date_time_formate = 'd-m-Y H:i:s';
            $lastdate = '';
            $now = '';
            if ($posted == 'lasthour') {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-1 hours', $current_timestamp));
            } elseif ($posted == 'last24') {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-24 hours', $current_timestamp));
            } elseif ($posted == '7days') {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-7 days', $current_timestamp));
            } elseif ($posted == '14days') {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-14 days', $current_timestamp));
            } elseif ($posted == '30days') {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-30 days', $current_timestamp));
            }
            
            if ($lastdate != '' && $now != '') {
                $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
                $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta ON postmeta.post_id = posts.ID";
                $post_ids_query .= " WHERE post_type='candidate' AND post_status='publish'";
                $post_ids_query .= " AND (postmeta.meta_key='post_date' AND postmeta.meta_value >= '{$lastdate}')";
                $post_ids_query .= " GROUP BY posts.ID";

                $post_ids = $wpdb->get_col($post_ids_query);
                
                return $post_ids;
            }
        }

        public function modified_views_so($views) {

            remove_filter('parse_query', array(&$this, 'candidates_query_filter'), 11, 1);
            $args = array(
                'post_type' => 'candidate',
                'posts_per_page' => '1',
                'post_status' => 'publish',
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_candidate_approved',
                        'value' => 'on',
                        'compare' => '!=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            $pending_jobs = $jobs_query->found_posts;
            wp_reset_postdata();

            $args = array(
                'post_type' => 'candidate',
                'posts_per_page' => '1',
                'post_status' => 'publish',
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_candidate_approved',
                        'value' => 'on',
                        'compare' => '=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            $approve_jobs = $jobs_query->found_posts;
            wp_reset_postdata();

            $views['approved'] = '<a href="edit.php?post_type=candidate&candidate_status=approved">' . esc_html__('Approved', 'wp-jobsearch') . '</a> (' . absint($approve_jobs) . ')';
            $views['pending'] = '<a href="edit.php?post_type=candidate&candidate_status=pending">' . esc_html__('Pending', 'wp-jobsearch') . '</a> (' . absint($pending_jobs) . ')';

            return $views;
        }

        public function jobsearch_candidate_columns_add($columns) {
            global $sitepress, $jobsearch_plugin_options;
            $candidate_auto_approve = isset($jobsearch_plugin_options['candidate_auto_approve']) ? $jobsearch_plugin_options['candidate_auto_approve'] : '';
            $new_columns = array();
            $new_columns['cb'] = '<input type="checkbox" />';
            $new_columns['candidate_title'] = esc_html('Candidate', 'wp-jobsearch');
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $languages = icl_get_languages('skip_missing=0&orderby=title');
                if ( is_array($languages) && sizeof($languages) > 0 ) {
                    $wpml_options = get_option( 'icl_sitepress_settings' );
                    $default_lang = isset($wpml_options['default_language']) ? $wpml_options['default_language'] : '';
                    $flags_html = '';
                    foreach ( $languages as $lang_code => $language ) {
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
            $new_columns['jobtitle'] = esc_html__('Job Title', 'wp-jobsearch');
            $new_columns['applied_jobs'] = esc_html__('Applied Jobs', 'wp-jobsearch');
            //$new_columns['featured'] = force_balance_tags('<strong class="jobsearch-tooltip" title="' . esc_html__('Featured', 'wp-jobsearch') . '"><i class="dashicons dashicons-star-filled"></i></strong>');
            if ($candidate_auto_approve == 'email' || $candidate_auto_approve == 'admin_email') {
                $new_columns['active_email'] = esc_html__('Activation Email', 'wp-jobsearch');
            }
            $new_columns['status'] = force_balance_tags('<strong class="jobsearch-tooltip" title="' . esc_html__('Status', 'wp-jobsearch') . '"><i class="dashicons dashicons-clock"></i></strong>');
            $new_columns['action'] = esc_html__('Action', 'wp-jobsearch');
            //return array_merge($columns, $new_columns);
            return apply_filters('jobsearch_cand_post_bk_admin_columns', $new_columns);
        }

        public function jobsearch_candidate_columns($column) {
            global $post;
            $_post_id = $post->ID;
            $candidate_user_id = get_post_meta($_post_id, 'jobsearch_user_id', true);
            switch ($column) {
                case 'candidate_title' :
                    echo '<div class="candidate_position">';

                    $src = jobsearch_candidate_img_url_comn($_post_id);
                    
                    if ($src != '') {
                        echo '<div class="company-logo">';
                        echo '<img src="' . esc_attr($src) . '" alt="' . esc_attr(get_the_title($post->ID)) . '" />';
                        echo '</div>';
                        // Before 1.24.0, logo URLs were stored in post meta.
                    }

                    echo '<a href="' . admin_url('post.php?post=' . $post->ID . '&action=edit') . '" class="candidate_title" class="jobsearch-tooltip" title="' . sprintf(__('ID: %d', 'wp-jobsearch'), $post->ID) . '">' . ucfirst(get_the_title($post->ID)) . '</a>';

                    echo '<div class="sector-list">';
                    $candidatetype_list = get_the_term_list($post->ID, 'sector', '', ',', '');
                    if ($candidatetype_list) {
                        printf('%1$s', $candidatetype_list);
                    }
                    echo '</div>';
                    
                    if (class_exists('w357LoginAsUser')) {
                        $w357LoginAsUser = new w357LoginAsUser;
                        $user_obj = get_user_by('ID', $candidate_user_id);
                        if (isset($user_obj->ID)) {

                            $the_user_obj = new WP_User($candidate_user_id);
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
                case 'applied_jobs' :
                    echo '<div id="cand-apliedjobs-' . $post->ID . '" data-id="' . $post->ID . '" class="cand-apliedjobs-span"><span class="spinner is-active"></span></div>';
                    break;
                case 'featured' :
                    $candidate_featured = get_post_meta($post->ID, 'jobsearch_field_candidate_featured', true);
                    if ($candidate_featured == 'on') {
                        echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip candidate-featured-option" data-option="un-feature" data-candidateid="' . esc_attr($post->ID) . '" title="' . esc_html__('No', 'wp-jobsearch') . '"><i class="dashicons dashicons-star-filled" aria-hidden="true"></i></a>');
                    } else {
                        echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip candidate-featured-option" data-option="featured" data-candidateid="' . esc_attr($post->ID) . '" title="' . esc_html__('Yes', 'wp-jobsearch') . '"><i class="dashicons dashicons-star-empty" aria-hidden="true"></i></a>');
                    }
                    break;
                case 'jobtitle' :
                    $jobtitle = get_post_meta($post->ID, 'jobsearch_field_candidate_jobtitle', true);
                    echo jobsearch_esc_html($jobtitle);
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

                    $candidate_status = get_post_meta($post->ID, 'jobsearch_field_candidate_approved', true);
                    if ($candidate_status == 'on') {
                        echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Approved', 'wp-jobsearch') . '"><i ' . $approved_color_str . ' class="dashicons dashicons-yes" aria-hidden="true"></i></a>');
                    } else {
                        echo force_balance_tags('<a href="javascript:void(0);" class="jobsearch-tooltip" title="' . esc_html__('Pending', 'wp-jobsearch') . '"><i ' . $pending_color_str . ' class="dashicons dashicons-clock fa-spin fa-lg" aria-hidden="true"></i></a>');
                    }
                    break;
                case 'active_email':
                    $user_login_auth = get_user_meta($candidate_user_id, 'jobsearch_accaprov_allow', true);
                    if ($user_login_auth == '0') {
                        ?>
                        <div class="resend-mail-con">
                            <a href="javascript:void(0);" class="resend-active-mail" data-id="<?php echo ($candidate_user_id) ?>"><?php esc_html_e('Resend Email', 'wp-jobsearch') ?></a> <strong></strong>
                        </div>
                        <?php
                    } else {
                        echo '-';
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
            
            echo apply_filters('jobsearch_cand_post_bk_admin_columns_val', '', $column, $_post_id);
        }

        public function jobsearch_primary_column($column, $screen) {
            if ('edit-candidate' === $screen) {
                $column = 'candidate_title';
            }
            return $column;
        }

        public function jobsearch_candidate_sortable_columns($columns) {
            $custom = array(
                'jobtitle' => 'jobtitle',
                'candidate_title' => 'title',
                'location' => 'location',
                'status' => 'status',
            );
            return wp_parse_args($custom, $columns);
        }

        public function jobsearch_candidate_sort_columns($vars) {
            if (isset($vars['orderby']) && isset($_GET['post_type']) && $_GET['post_type'] == 'candidate') {
                if ('jobtitle' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_candidate_jobtitle',
                        'orderby' => 'meta_value'
                    ));
                } else if ('location' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_location_location1',
                        'orderby' => 'meta_value'
                    ));
                } else if ('status' === $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => 'jobsearch_field_candidate_approved',
                        'orderby' => 'meta_value'
                    ));
                }
            }
            return $vars;
        }

        public function jobsearch_candidate_sector() {
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
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'sector'),
            );

            register_taxonomy('sector', array('candidate', 'candidate', 'candidate'), $args);
        }

    }
    return new post_type_candidate();
}
