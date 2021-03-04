<?php
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Jobs_Embeddable_Code {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'embeddable_scripts') );
        add_action('init', array($this, 'jobs_embedcode_js'));
        add_shortcode('jobsearch_embeddable_jobs_generator', array($this, 'embed_code_generator'));
    }

    public function embeddable_scripts() {
        
        wp_register_script('jobsearch-embeddable-jobs', jobsearch_plugin_get_url('js/embeddable-jobs-form.js'), array('jquery', 'jobsearch-selectize'), JobSearch_plugin::get_version(), true);

        ob_start();
        jobsearch_get_template_part('embeddable', 'code', 'embed-jobs');
        $code = ob_get_clean();

        ob_start();
        jobsearch_get_template_part('embeddable', 'code-css', 'embed-jobs');
        $css = ob_get_clean();

        wp_localize_script('jobsearch-embeddable-jobs', 'jobsearch_embeddable_jobs_form_args', array(
            'code' => $code,
            'css' => $css,
            'theme_dark' => '',
            'script_url' => home_url('/?embed=wp_jobsearch_embed_jobs'),
            'is_rtl' => is_rtl(),
        ));
    }
    
    public function job_location_filter($location_val) {

        global $wpdb;

        $location_rslt = array();

        if ($location_val != '') {

            $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
            $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta ON postmeta.post_id = posts.ID";
            $post_ids_query .= " WHERE post_type='job' AND post_status='publish'";
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
    
    public function get_job_id_by_filter($left_filter_arr) {
        global $wpdb;
        $meta_post_ids_arr = '';
        $job_id_condition = '';

        if (isset($left_filter_arr) && !empty($left_filter_arr)) {
            $meta_post_ids_arr = jobsearch_get_query_whereclase_by_array($left_filter_arr);

            // if no result found in filtration
            if (empty($meta_post_ids_arr)) {
                $meta_post_ids_arr = array(0);
            }
            $ids = $meta_post_ids_arr != '' ? implode(",", $meta_post_ids_arr) : '0';
            $job_id_condition = " ID in (" . $ids . ") AND ";
        }

        $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $job_id_condition . " post_type='job' AND post_status='publish'");

        if (empty($post_ids)) {
            $post_ids = array(0);
        }
        return $post_ids;
    }
    
    public function job_listings($atts) {
        global $jobsearch_shortcode_jobs_frontend;
        $all_post_ids = array();
        $orig_post_ids = $jobsearch_shortcode_jobs_frontend->job_general_query_filter(array(), $atts);
        
        if (empty($orig_post_ids)) {
            $all_post_ids = array(0);
        } else {
            $all_post_ids = $orig_post_ids;
        }
        
        if (isset($atts['embarg_location']) && $atts['embarg_location'] != '') {
            $loc_val = $atts['embarg_location'];
            $loc_post_ids = $this->job_location_filter($loc_val);
            if (!empty($loc_post_ids)) {
                $all_post_ids = array_intersect($all_post_ids, $loc_post_ids);
            }
        }
        
        if (isset($atts['embarg_keyword']) && $atts['embarg_keyword'] != '') {
            $keyword_val = $atts['embarg_keyword'];
            $keyword_post_ids = jobsearch_get_serchable_keywrd_job_ids($keyword_val);
            if (!empty($keyword_post_ids)) {
                $all_post_ids = array_intersect($all_post_ids, $keyword_post_ids);
            }
        }
        
        //
        $left_filter_arr = apply_filters('jobsearch_custom_fields_load_filter_array_html', 'job', array(), '');
        if (!empty($left_filter_arr)) {
            $srch_post_ids = $this->get_job_id_by_filter($left_filter_arr);

            if (!empty($srch_post_ids)) {
                $all_post_ids = array_intersect($all_post_ids, $srch_post_ids);
            }
        }
        //
        if (empty($all_post_ids)) {
            $all_post_ids = array(0);
        }
        
        $paged = isset($atts['page']) && $atts['page'] > 0 ? $atts['page'] : 1;
        
        $jobs_per_page = isset($atts['embarg_per_page']) && $atts['embarg_per_page'] > 1 ? $atts['embarg_per_page'] : 5;
        $jobs_per_page = $jobs_per_page > 100 ? 100 : $jobs_per_page;
        
        $args = array(
            'posts_per_page' => $jobs_per_page,
            'paged' => $paged,
            'post_type' => 'job',
            'post_status' => 'publish',
            'order' => 'DESC',
            'orderby' => 'ID',
            'fields' => 'ids',
            'post__in' => $all_post_ids,
        );
        if (isset($atts['chosed_employers']) && $atts['chosed_employers'] != '') {
            $chosed_employers = $atts['chosed_employers'];
            $chosed_employers = explode(',', $chosed_employers);
            if (!empty($chosed_employers)) {
                $args['meta_query'][] = array(
                    'key' => 'jobsearch_field_job_posted_by',
                    'compare' => 'IN',
                    'value' => $chosed_employers,
                );
            }
        }
        
        if (isset($atts['embarg_job_sector']) && $atts['embarg_job_sector'] != '' && $atts['embarg_job_sector'] != '0') {
            $args['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => urldecode($atts['embarg_job_sector'])
            );
        }

        if (isset($atts['embarg_job_type']) && $atts['embarg_job_type'] != '' && $atts['embarg_job_type'] != '0') {
            $args['tax_query'][] = array(
                'taxonomy' => 'jobtype',
                'field' => 'slug',
                'terms' => urldecode($atts['embarg_job_type']),
            );
        }
        
        $job_query = new WP_Query($args);
        
        return array($args, $job_query);
    }

    public function jobs_embedcode_js() {
        if (isset($_GET['embed']) && 'wp_jobsearch_embed_jobs' === $_GET['embed']) {
            $page = absint(isset($_GET['page']) ? $_GET['page'] : 1);
            $per_page = isset($_GET['embarg_per_page']) && $_GET['embarg_per_page'] > 1 ? $_GET['embarg_per_page'] : 5;
            $per_page = $per_page > 100 ? 100 : $per_page;
            
            $filter_args = array();
            foreach ($_GET as $filter_data_key => $filter_data_val) {
                $filter_args[$filter_data_key] = $filter_data_val;
            }
            $filter_args = apply_filters('wp_jobsearch_embeddable_jobs_query_args', $filter_args);
            $jobs = $this->job_listings($filter_args);
            
            $jobs_args = $jobs[0];
            $jobs_query = $jobs[1];
            
            $totl_found_jobs = $jobs_query->found_posts;
            $total_pages = 1;
            if ($totl_found_jobs > $per_page) {
                $total_pages = ceil($totl_found_jobs / $per_page);
            }
            $job_posts = $jobs_query->posts;
            ob_start();

            echo '<div class="jobsearch-embeddable-jobs-content">';
            echo '<ul class="jobsearch-embeddable-jobs-listings">';

            if (!empty($job_posts)) :
                foreach ($job_posts as $job_id) {
                    $job_publish_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                    if ($job_publish_date != '') {
                        $job_publish_date = jobsearch_time_elapsed_string($job_publish_date);
                    }
                    $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
                    $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                    $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                    $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
                    $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                    $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                    $company_name = '';
                    
                    $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                    if ($postby_emp_id > 0) {
                        $company_name = '<a href="' . get_permalink($postby_emp_id) . '" target="_blank">@ ' . get_the_title($postby_emp_id) . '</a>';
                    }

                    $job_city_title = jobsearch_post_city_contry_txtstr($job_id, true, false, true);

                    $job_type_str = jobsearch_job_get_all_jobtypes($job_id, 'jobsearch-option-btn');
                    $sector_str = $this->job_get_sectors($job_id, '', '', '', '<li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>', '</li>');
                    
                    set_query_var('job_id', $job_id);
                    set_query_var('post_thumbnail_src', $post_thumbnail_src);
                    set_query_var('jobsearch_job_featured', $jobsearch_job_featured);
                    set_query_var('company_name', $company_name);
                    set_query_var('job_location', $job_city_title);
                    set_query_var('job_publish_date', $job_publish_date);
                    set_query_var('sector_str', $sector_str);
                    set_query_var('job_type_str', $job_type_str);

                    jobsearch_get_template_part('content', 'job-listing', 'embed-jobs');
                }
            else :
                ?>
                <li class="no-results"><?php _e('No jobs found.', 'wp-jobsearch'); ?></li>
                <?php
            endif;

            echo '</ul>';

            if (isset($_GET['embarg_pagination']) && $_GET['embarg_pagination'] == 'yes' && $total_pages > 1) {
                echo '<div id="jobsearch-embeddable-jobs-pagination">';
                if ($page > 1) {
                    echo '<a href="javascript:void(0);" class="jobsearch-embeddable-jobs-prev" onclick="window.embeddable_job_embarg.prev_page(); return false;">' . __('Previous', 'wp-jobsearch') . '</a>';
                }
                if ($page < $total_pages) {
                    echo '<a href="javascript:void(0);" class="jobsearch-embeddable-jobs-next" onclick="window.embeddable_job_embarg.next_page(); return false;">' . __('Next', 'wp-jobsearch') . '</a>';
                }
                echo '</div>';
            }

            echo '</div>';

            $content = ob_get_clean();

            header("Content-Type: text/javascript; charset=" . get_bloginfo('charset'));
            header("Vary: Accept-Encoding");
            header("Expires: " . gmdate("D, d M Y H:i:s", time() + DAY_IN_SECONDS) . " GMT");
            ?>
            if (window['embeddable_job_embarg'] != undefined) {
                window['embeddable_job_embarg']['show_jobs']('embeddable-job-embarg-content', '<?php echo esc_js($content); ?>');
            }
            <?php
            exit;
        }
    }
    
    public function job_get_sectors($job_id, $link_class = '', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '') {
        global $jobsearch_plugin_options;
        $sectors = wp_get_post_terms($job_id, 'sector');
        ob_start();
        $html = '';
        if (!empty($sectors)) {
            $page_id = isset($jobsearch_plugin_options['jobsearch_search_list_page']) ? $jobsearch_plugin_options['jobsearch_search_list_page'] : '';
            $page_id = jobsearch__get_post_id($page_id, 'page');
            $page_id = jobsearch_wpml_lang_page_id($page_id, 'page');
            $result_page = get_permalink($page_id);
            $link_class_str = '';
            if ($link_class != '') {
                $link_class_str = 'class="' . $link_class . '"';
            }
            echo($before_tag);
            $flag = 0;
            foreach ($sectors as $term) :
                
                if (isset($term->term_id) && function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $get_sector_id = $term->term_id;
                    $get_sector_id = apply_filters('wpml_object_id', $get_sector_id, 'sector', true);
                    $term = jobsearch_get_custom_term_by('term_id', $get_sector_id, 'sector');
                }
                if ($flag > 0) {
                    echo ", ";
                }
                echo($before_title);
                ?>
                <a href="<?php echo add_query_arg(array('sector_cat' => $term->slug), $result_page); ?>"
                   class="<?php echo force_balance_tags($link_class) ?>" target="_blank">
                    <?php
                    echo esc_html($term->name);
                    ?>
                </a>
                <?php
                echo($after_title);
                $flag++;
            endforeach;
            echo($after_tag);
        }
        $html .= ob_get_clean();
        return $html;
    }
    
    public function embed_code_generator($atts) {
        
        extract(shortcode_atts(array(
            'site_title' => '',
            'employer_base_jobs' => '',
            'keyword_search' => '',
            'location_search' => '',
            'job_sector' => '',
            'job_type' => '',
            'custom_fields' => '',
        ), $atts));
        
        $rand_num = rand(1000000, 99999999);
        
        wp_enqueue_script('jobsearch-embeddable-jobs');
        ob_start();
        ?>
        <div class="embeddable-jobs-maincon">
            <form class="embeddable-jobs-form">
                <div class="jobsearch-row">
                    <div class="jobsearch-column-3">
                        <input type="hidden" id="site_title" value="<?php echo ($site_title) ?>">
                        <?php
                        if ($employer_base_jobs == 'yes') {
                            ?>
                            <fieldset>
                                <div class="added-embedjobs-emps"></div>
                                <a href="javascript:void(0);" id="chnge-attachuser-toemp"><?php esc_html_e('Choose Employers', 'wp-jobsearch'); ?></a>
                                <input type="hidden" id="chosed_employers">
                                <?php
                                $popup_args = array('p_rand' => $rand_num);
                                add_action('wp_footer', function () use ($popup_args) {

                                    global $wpdb;
                                    extract(shortcode_atts(array(
                                        'p_rand' => ''
                                                    ), $popup_args));

                                    $totl_users = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='employer' AND post_status='publish'");
                                    ?>
                                    <div class="jobsearch-modal empmeta-atchuser-modal fade" id="JobSearchModalAttchUser<?php echo ($p_rand) ?>">
                                        <div class="modal-inner-area">&nbsp;</div>
                                        <div class="modal-content-area">
                                            <div class="modal-box-area">
                                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                                <div class="jobsearch-useratach-popup">
                                                    <?php
                                                    $attusers_query = "SELECT posts.ID,posts.post_title FROM $wpdb->posts AS posts WHERE post_type='employer' AND post_status='publish' ORDER BY ID DESC LIMIT %d";
                                                    $attall_users = $wpdb->get_results($wpdb->prepare($attusers_query, 10), 'ARRAY_A');

                                                    if (!empty($attall_users)) {
                                                        ?>
                                                        <div class="users-list-con">
                                                            <strong class="users-list-hdng"><?php esc_html_e('Employers List', 'wp-jobsearch') ?></strong>

                                                            <div class="user-atchp-srch">
                                                                <label><?php esc_html_e('Search', 'wp-jobsearch') ?></label>
                                                                <input type="text" id="user_srchinput_<?php echo ($p_rand) ?>">
                                                                <span></span>
                                                            </div>

                                                            <div id="inerlist-users-<?php echo ($p_rand) ?>" class="inerlist-users-sec">
                                                                <ul class="jobsearch-users-list">
                                                                    <?php
                                                                    foreach ($attall_users as $attch_usritm) {
                                                                        ?>
                                                                        <li><a href="javascript:void(0);" class="atchuser-itm-btn" data-id="<?php echo ($attch_usritm['ID']) ?>" data-name="<?php echo ($attch_usritm['post_title']) ?>"><?php echo ($attch_usritm['post_title']) ?></a> <span></span></li>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </ul>
                                                                <?php
                                                                if ($totl_users > 10) {
                                                                    $total_pages = ceil($totl_users / 10);
                                                                    ?>
                                                                    <div class="lodmore-users-btnsec">
                                                                        <a href="javascript:void(0);" class="lodmore-users-btn" data-tpages="<?php echo ($total_pages) ?>" data-keyword="" data-gtopage="2"><?php esc_html_e('Load More', 'wp-jobsearch') ?></a>
                                                                    </div>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    } else {
                                                        echo '<p>' . esc_html__('No Employer Found.', 'wp-jobsearch') . '</p>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        var all_added_empsarr = {};
                                        jQuery(document).on('click', '#chnge-attachuser-toemp', function () {
                                            jobsearch_modal_popup_open('JobSearchModalAttchUser<?php echo ($p_rand) ?>');
                                        });
                                        jQuery(document).on('click', '.jobsearch-remembemp-btn', function () {
                                            var _this = jQuery(this);
                                            var this_id = _this.attr('data-id');
                                            var all_empaded_val = jQuery('#chosed_employers').val();
                                            
                                            if (all_empaded_val != '') {
                                                var all_empaded_valarr = all_empaded_val.split(',');
                                                if (all_empaded_valarr.length > 0) {
                                                    var index_elemb = all_empaded_valarr.indexOf(this_id);
                                                    if (index_elemb !== -1) {
                                                        all_empaded_valarr.splice(index_elemb, 1);
                                                    }
                                                    //console.log(all_empaded_valarr);
                                                    delete all_added_empsarr[this_id];
                                                    //console.log(all_added_empsarr);
                                                    jQuery('#chosed_employers').val(all_empaded_valarr.join());
                                                }
                                                _this.parents('.item-embemp-tem').remove();
                                            }
                                        });
                                        jQuery(document).on('click', '.atchuser-itm-btn', function () {
                                            var _this = jQuery(this);
                                            var this_id = _this.attr('data-id');
                                            var this_name = _this.attr('data-name');
                                            all_added_empsarr[this_id] = this_name;
                                            if (typeof all_added_empsarr === 'object') {
                                                var to_add_indivhtml = '';
                                                var to_add_ininpval = [];
                                                for (var key_index of Object.keys(all_added_empsarr)) {
                                                    var elem_val = all_added_empsarr[key_index];
                                                    to_add_indivhtml += '<div id="item-embemp-' + key_index + '" class="item-embemp-tem"><span>' + elem_val + '<em class="jobsearch-remembemp-btn fa fa-close" data-id="' + key_index + '"></em></span></div>';
                                                    to_add_ininpval.push(key_index);
                                                }
                                                if (to_add_indivhtml != '') {
                                                    jQuery('#chosed_employers').val(to_add_ininpval.join());
                                                    jQuery('.added-embedjobs-emps').html(to_add_indivhtml);
                                                }
                                            }
                                        });
                                        jQuery(document).on('click', '.lodmore-users-btn', function (e) {
                                            e.preventDefault();
                                            var _this = jQuery(this),
                                                    total_pages = _this.attr('data-tpages'),
                                                    page_num = _this.attr('data-gtopage'),
                                                    keyword = _this.attr('data-keyword'),
                                                    this_html = _this.html(),
                                                    appender_con = jQuery('#inerlist-users-<?php echo ($p_rand) ?> .jobsearch-users-list');
                                            if (!_this.hasClass('ajax-loadin')) {
                                                _this.addClass('ajax-loadin');
                                                _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

                                                total_pages = parseInt(total_pages);
                                                page_num = parseInt(page_num);
                                                var request = jQuery.ajax({
                                                    url: '<?php echo admin_url('admin-ajax.php') ?>',
                                                    method: "POST",
                                                    data: {
                                                        page_num: page_num,
                                                        keyword: keyword,
                                                        action: 'jobsearch_load_memps_jobmeta_popupinlist',
                                                    },
                                                    dataType: "json"
                                                });

                                                request.done(function (response) {
                                                    if ('undefined' !== typeof response.html) {
                                                        page_num += 1;
                                                        _this.attr('data-gtopage', page_num)
                                                        if (page_num > total_pages) {
                                                            _this.parent('div').hide();
                                                        }
                                                        appender_con.append(response.html);
                                                    }
                                                    _this.html(this_html);
                                                    _this.removeClass('ajax-loadin');
                                                });

                                                request.fail(function (jqXHR, textStatus) {
                                                    _this.html(this_html);
                                                    _this.removeClass('ajax-loadin');
                                                });
                                            }
                                            return false;

                                        });
                                        jQuery(document).on('keyup', '#user_srchinput_<?php echo ($p_rand) ?>', function () {
                                            var _this = jQuery(this);
                                            var loader_con = _this.parent('.user-atchp-srch').find('span');
                                            var appender_con = jQuery('#inerlist-users-<?php echo ($p_rand) ?> .jobsearch-users-list');

                                            loader_con.html('<i class="fa fa-refresh fa-spin"></i>');

                                            var request = jQuery.ajax({
                                                url: '<?php echo admin_url('admin-ajax.php') ?>',
                                                method: "POST",
                                                data: {
                                                    keyword: _this.val(),
                                                    action: 'jobsearch_jobmeta_serchemps_throgh_popup'
                                                },
                                                dataType: "json"
                                            });
                                            request.done(function (response) {
                                                if (typeof response.html !== 'undefined') {
                                                    appender_con.html(response.html);
                                                    jQuery('#inerlist-users-<?php echo ($p_rand) ?>').find('.lodmore-users-btnsec').html(response.lodrhtml);
                                                    if (response.count > 10) {
                                                        jQuery('#inerlist-users-<?php echo ($p_rand) ?>').find('.lodmore-users-btnsec').show();
                                                    } else {
                                                        jQuery('#inerlist-users-<?php echo ($p_rand) ?>').find('.lodmore-users-btnsec').hide();
                                                    }
                                                }
                                                loader_con.html('');
                                            });
                                            request.fail(function (jqXHR, textStatus) {
                                                loader_con.html('');
                                            });
                                        });
                                    </script>
                                    <?php
                                }, 11, 1);
                                ?>
                            </fieldset>
                            <?php
                        }
                        if ($keyword_search == 'yes') {
                            ?>
                            <fieldset>
                                <label for="embarg_keyword"><?php esc_html_e('Keyword', 'wp-jobsearch'); ?></label>
                                <div class="field">
                                    <input type="text" id="embarg_keyword" class="input-text" placeholder="<?php esc_html_e('Enter a keyword to search', 'wp-jobsearch'); ?>" />
                                </div>
                            </fieldset>
                            <?php
                        }
                        if ($location_search == 'yes') {
                            ?>
                            <fieldset>
                                <label for="embarg_location"><?php esc_html_e('Location', 'wp-jobsearch'); ?></label>
                                <div class="field">
                                    <input type="text" id="embarg_location" class="input-text" placeholder="<?php esc_html_e('Optionally Enter a location to search', 'wp-jobsearch'); ?>" />
                                </div>
                            </fieldset>
                            <?php
                        }
                        if ($job_sector == 'yes') {
                            ?>
                            <fieldset>
                                <label for="embarg_job_sector"><?php _e('Job Sector', 'wp-jobsearch'); ?></label>
                                <div class="jobsearch-profile-select">
                                    <?php
                                    $sector_args = array(
                                        'show_option_all' => esc_html__('Select Sector', 'wp-jobsearch'),
                                        'show_option_none' => '',
                                        'class' => 'selectize-select',
                                        'option_none_value' => '',
                                        'orderby' => 'title',
                                        'order' => 'ASC',
                                        'show_count' => 0,
                                        'hide_empty' => 0,
                                        'echo' => 0,
                                        'selected' => '',
                                        'hierarchical' => 1,
                                        'id' => 'embarg_job_sector',
                                        'name' => 'embarg_job_sector',
                                        'depth' => 0,
                                        'taxonomy' => 'sector',
                                        'hide_if_empty' => false,
                                        'value_field' => 'slug',
                                    );
                                    echo wp_dropdown_categories($sector_args);
                                    ?>
                                </div>
                            </fieldset>
                            <?php
                        }
                        if ($job_type == 'yes') {
                            ?>
                            <fieldset>
                                <label for="embarg_job_type"><?php _e('Job Type', 'wp-jobsearch'); ?></label>
                                <div class="jobsearch-profile-select">
                                    <?php
                                    $sector_args = array(
                                        'show_option_all' => esc_html__('Select Type', 'wp-jobsearch'),
                                        'show_option_none' => '',
                                        'class' => 'selectize-select',
                                        'option_none_value' => '',
                                        'orderby' => 'title',
                                        'order' => 'ASC',
                                        'show_count' => 0,
                                        'hide_empty' => 0,
                                        'echo' => 0,
                                        'selected' => '',
                                        'hierarchical' => 1,
                                        'id' => 'embarg_job_type',
                                        'name' => 'embarg_job_type',
                                        'depth' => 0,
                                        'taxonomy' => 'jobtype',
                                        'hide_if_empty' => false,
                                        'value_field' => 'slug',
                                    );
                                    echo wp_dropdown_categories($sector_args);
                                    ?>
                                </div>
                            </fieldset>
                            <?php
                        }
                        if ($custom_fields == 'yes') {
                            $cus_fields_html = apply_filters('jobsearch_custom_fields_top_filters_html', '', 'job', 0, 'enable_search');
                            if ($cus_fields_html != '') {
                                ?>
                                <div class="jobadv-search-fields">
                                    <ul>
                                        <?php echo ($cus_fields_html) ?>
                                    </ul>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <fieldset>
                            <label for="embarg_per_page"><?php esc_html_e('Display Count', 'wp-jobsearch'); ?></label>
                            <div class="field">
                                <input type="text" id="embarg_per_page" class="input-text" value="5" />
                            </div>
                        </fieldset>
                        <fieldset>
                            <label for="embarg_pagination"><?php esc_html_e('Show Pagination?', 'wp-jobsearch'); ?></label>
                            <div class="jobsearch-profile-select">
                                <select id="embarg_pagination" class="selectize-select">
                                    <option value="yes"><?php esc_html_e('Yes', 'wp-jobsearch'); ?></option>
                                    <option value="no"><?php esc_html_e('No', 'wp-jobsearch'); ?></option>
                                </select>
                            </div>
                        </fieldset>
                        <div class="embed-code-getbtncon">
                            <a id="embarg-get-code" href="javascript:void(0);"><?php esc_html_e('Get Embeddable Code', 'wp-jobsearch'); ?></a>
                        </div>
                    </div>
                    <div class="jobsearch-column-9">
                        <div id="embarg-code-wrapper">
                            <div id="embarg-code-preview">
                                <h2><?php esc_html_e('Preview', 'wp-jobsearch'); ?></h2>
                                <p><?php esc_html_e('Embeded Jobs preview will show here.', 'wp-jobsearch'); ?></p>
                            </div>
                            <div id="embarg-code-content">
                                <h2><?php esc_html_e('Embeddable Code', 'wp-jobsearch'); ?></h2>
                                <textarea readonly="readonly" id="embarg-code" rows="10"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

}

//
global $Jobsearch_Jobs_Embeddable_Code;
$Jobsearch_Jobs_Embeddable_Code = new Jobsearch_Jobs_Embeddable_Code();
