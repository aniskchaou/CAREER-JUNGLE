<?php

if (!defined('ABSPATH')) {
    die;
}

class Jobsearch_Job_Applicants_Filters {

    // hook things up
    public function __construct() {
        //add_filter('jobsearch_empdash_aplics_btns_aftermore', array($this, 'dash_filters_btn'), 15);
        add_filter('jobseacrh_dash_manag_apps_viewbtns_html', array($this, 'dash_filters_btn'), 15, 2);
        
        add_action('wp_footer', array($this, 'applics_filters_html'), 1);
        
        add_filter('jobsearch_mangejob_applics_list_arr', array($this, '_mangejob_applics_list_arr'), 15);
        
        add_action('wp_ajax_jobsearch_get_skills_recomnd_applic_filters', array($this, 'get_skills_recomnd'));
        
        add_action('wp', array($this, 'add_experience_to_applics'));
    }
    
    public function add_experience_to_applics() {
        if (isset($_GET['view']) && $_GET['view'] == 'applicants' && isset($_GET['job_id']) && $_GET['job_id'] > 0) {
            $job_id = jobsearch_esc_html($_GET['job_id']);
            
            $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
            $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
            if (!empty($job_applicants_list)) {
                foreach ($job_applicants_list as $candidate_id) {
                    if(!metadata_exists('post', $candidate_id, 'jobsearch_candidate_experience_inyears')) {
                        jobsearch_addto_candidate_exp_inyears($candidate_id);
                    }
                }
            }
        }
    }
    
    public function dash_filters_btn($html, $_selected_view) {
        $job_id = isset($_GET['job_id']) ? $_GET['job_id'] : '';
        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
        $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
        
        $_mod_tab = isset($_GET['mod']) && $_GET['mod'] != '' ? $_GET['mod'] : 'applicants';
        if (!empty($job_applicants_list) && $_mod_tab == 'applicants') {
            ob_start();
            ?>
            <div class="sort-list-view">
                <a href="javascript:void(0);" class="jobsearch-applics-filterbtn"><i class="fa fa-filter"></i></a>
                <a href="javascript:void(0);"
                   class="apps-view-btn<?php echo($_selected_view == 'list' ? ' active' : '') ?>"
                   data-view="list"><i class="fa fa-list"></i></a>
                <a href="javascript:void(0);"
                   class="apps-view-btn<?php echo($_selected_view == 'grid' ? ' active' : '') ?>"
                   data-view="grid"><i class="fa fa-bars"></i></a>
            </div>
            <?php
            $html = ob_get_clean();
        }
        return $html;
    }
    
    public function applics_filters_html() {
        if (isset($_GET['view']) && $_GET['view'] == 'applicants' && isset($_GET['job_id']) && $_GET['job_id'] > 0) {
            $job_id = jobsearch_esc_html($_GET['job_id']);
            ?>
            <div class="jobsearch-applics-filterscon">
                <form method="get">
                    <input type="hidden" name="tab" value="manage-jobs">
                    <input type="hidden" name="view" value="applicants">
                    <?php
                    if (isset($_GET['mod']) && $_GET['mod'] != '') {
                        ?>
                        <input type="hidden" name="mod" value="<?php echo jobsearch_esc_html($_GET['mod']) ?>">
                        <?php
                    }
                    ?>
                    <input type="hidden" name="job_id" value="<?php echo ($job_id) ?>">
                    <?php
                    $this->filter_apply_date();
                    $this->filter_sector();
                    $this->filter_experience();
                    $this->filter_skills();
                    do_action('jobsearch_applicant_filters_secs_after');
                    echo apply_filters('jobsearch_custom_fields_filter_box_html', '', 'candidate', 0, array(), '', '');
                    ?>
                    <input type="hidden" name="jobsearch_filters" value="applicants">
                    <input type="submit" class="applics-filter-formbtn" value="<?php esc_html_e('Apply Filters', 'wp-jobsearch'); ?>">
                </form>
                <a href="javascript:void(0);" class="applicfilters-sideclose-btn"><i class="fa fa-times"></i></a>
            </div>
            <?php
        }
    }
    
    public function _mangejob_applics_list_arr($applics_list) {
        global $jobsearch_shortcode_candidates_frontend;
        if (!empty($applics_list) && count($applics_list) > 0 && isset($_GET['jobsearch_filters']) && $_GET['jobsearch_filters'] == 'applicants') {

            $_job_id = isset($_GET['job_id']) ? $_GET['job_id'] : 0;
            
            $all_post_ids = $applics_list;
            
            $left_filter_arr = apply_filters('jobsearch_custom_fields_load_filter_array_html', 'candidate', array(), array());
            
            $cusfields_filter_ids = array();
            if (!empty($left_filter_arr)) {
                $cusfields_filter_ids = $jobsearch_shortcode_candidates_frontend->get_candidate_id_by_filter($left_filter_arr);
            }
            
            if (!empty($cusfields_filter_ids)) {
                $all_post_ids = array_intersect($cusfields_filter_ids, $applics_list);
            }
            
            $args = array(
                'posts_per_page' => "-1",
                'post_type' => 'candidate',
                'post_status' => 'publish',
                'fields' => 'ids', // only load ids
            );
            if (isset($_REQUEST['sector_cat']) && $_REQUEST['sector_cat'] != '') {

                $args['tax_query'][] = array(
                    'taxonomy' => 'sector',
                    'field' => 'slug',
                    'terms' => jobsearch_esc_html($_REQUEST['sector_cat'])
                );
            }
            $get_skills = isset($_REQUEST['applicant_skills']) ? $_REQUEST['applicant_skills'] : '';
            $get_skills = jobsearch_esc_html($get_skills);

            $skills_arr = array();
            if ($get_skills != '') {
                $skills_arr = explode(',', $get_skills);
                if (!empty($skills_arr)) {
                    $args['tax_query'][] = array(
                        'taxonomy' => 'skill',
                        'field' => 'slug',
                        'terms' => $skills_arr
                    );
                }
            }
            
            if (isset($_GET['experience']) && $_GET['experience'] != '') {
                $get_experience = jobsearch_esc_html($_GET['experience']);
                
                if ($get_experience == '10plus') {
                    $args['meta_query'][] = array(
                        'key' => 'jobsearch_candidate_experience_inyears',
                        'value' => 10,
                        'type' => 'numeric',
                        'compare' => '>'
                    );
                } else {
                    $exper_value = array(0, 1);
                    if ($get_experience == '2-3') {
                        $exper_value = array(2, 3);
                    } else if ($get_experience == '4-5') {
                        $exper_value = array(4, 5);
                    } if ($get_experience == '6-10') {
                        $exper_value = array(6, 10);
                    }
                    $args['meta_query'][] = array(
                        'key' => 'jobsearch_candidate_experience_inyears',
                        'value' => $exper_value,
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );
                }
            }
            
            $args['post__in'] = $all_post_ids;
            $args = apply_filters('jobsearch_jobapplics_filters_query_args', $args);
            
            $posts_query = new WP_Query($args);
            
            $post_ids = $posts_query->posts;

            $new_aplics_arr = array();
            if (!empty($post_ids)) {
                foreach ($applics_list as $_candidate_id) {
                    if (in_array($_candidate_id, $post_ids)) {
                        
                        if (isset($_REQUEST['posted']) && $_REQUEST['posted'] != '' && $_REQUEST['posted'] != 'all') {
                            $posted = $_REQUEST['posted'];
                            $candidate_user_id = jobsearch_get_candidate_user_id($_candidate_id);
                            $user_apply_data = get_user_meta($candidate_user_id, 'jobsearch-user-jobs-applied-list', true);
                            $aply_date_time = '';
                            if (!empty($user_apply_data)) {
                                $user_apply_key = array_search($_job_id, array_column($user_apply_data, 'post_id'));
                                $aply_date_time = isset($user_apply_data[$user_apply_key]['date_time']) ? $user_apply_data[$user_apply_key]['date_time'] : '';
                            }
                            if ($aply_date_time > 0) {
                                $in_time_span = 0;
                                $current_timestamp = current_time('timestamp');
                                if ($posted == 'lasthour') {
                                    $in_time_span = strtotime('-1 hours', $current_timestamp);
                                } else if ($posted == 'last24') {
                                    $in_time_span = strtotime('-24 hours', $current_timestamp);
                                } else if ($posted == '7days') {
                                    $in_time_span = strtotime('-7 days', $current_timestamp);
                                } else if ($posted == '14days') {
                                    $in_time_span = strtotime('-14 days', $current_timestamp);
                                } else if ($posted == '30days') {
                                    $in_time_span = strtotime('-30 days', $current_timestamp);
                                }
                                if ($in_time_span <= $aply_date_time) {
                                    $new_aplics_arr[] = $_candidate_id;
                                }
                                //echo date('d-M-Y', $aply_date_time). '<br>';
                            }
                            //
                        } else {
                            $new_aplics_arr[] = $_candidate_id;
                        }
                    }
                }
            }
            wp_reset_postdata();
            
            return $new_aplics_arr;
        }
        
        return $applics_list;
    }
    
    public function filter_apply_date() {
        $posted = isset($_REQUEST['posted']) ? $_REQUEST['posted'] : '';
        $posted = jobsearch_esc_html($posted);
        $rand = rand(100000, 999999);
        ?>
        <div class="jobsearch-filter-responsive-wrap">
            <div class="jobsearch-search-filter-wrap jobsearch-search-filter-toggle">
                <div class="jobsearch-fltbox-title"><a href="javascript:void(0);" class="jobsearch-click-btn"><?php echo esc_html__('Apply Date', 'wp-jobsearch'); ?></a></div>
                <div class="jobsearch-checkbox-toggle">
                    <ul class="jobsearch-checkbox">
                        <li class="no-filter-counts">
                            <input id="lasthour<?php echo absint($rand); ?>" type="radio"
                                   name="posted" <?php if ($posted == 'lasthour') echo 'checked="checked"'; ?>
                                   value="lasthour"/>
                            <label for="lasthour<?php echo absint($rand); ?>"><span></span><?php esc_html_e('Last Hour', 'wp-jobsearch') ?>
                            </label>
                        </li>
                        <li class="no-filter-counts">
                            <input id="last24<?php echo absint($rand); ?>" type="radio"
                                   name="posted" <?php if ($posted == 'last24') echo 'checked="checked"'; ?>
                                   value="last24"/>
                            <label for="last24<?php echo absint($rand); ?>"><span></span><?php esc_html_e('Last 24 hours', 'wp-jobsearch') ?>
                            </label>
                        </li>
                        <li class="no-filter-counts">
                            <input id="7days<?php echo absint($rand); ?>" type="radio"
                                   name="posted" <?php if ($posted == '7days') echo 'checked="checked"'; ?>
                                   value="7days"/>
                            <label for="7days<?php echo absint($rand); ?>"><span></span><?php esc_html_e('Last 7 days', 'wp-jobsearch') ?>
                            </label>
                        </li>
                        <li class="no-filter-counts">
                            <input id="14days<?php echo absint($rand); ?>" type="radio"
                                   name="posted" <?php if ($posted == '14days') echo 'checked="checked"'; ?>
                                   value="14days"/>
                            <label for="14days<?php echo absint($rand); ?>"><span></span><?php esc_html_e('Last 14 days', 'wp-jobsearch') ?>
                            </label>
                        </li>
                        <li class="no-filter-counts">
                            <input id="30days<?php echo absint($rand); ?>" type="radio"
                                   name="posted" <?php if ($posted == '30days') echo 'checked="checked"'; ?>
                                   value="30days"/>
                            <label for="30days<?php echo absint($rand); ?>"><span></span><?php esc_html_e('Last 30 days', 'wp-jobsearch') ?>
                            </label>
                        </li>
                        <li class="no-filter-counts">
                            <input id="all<?php echo absint($rand); ?>" type="radio"
                                   name="posted" <?php if ($posted == 'all' || $posted == '') echo 'checked="checked"'; ?>
                                   value="all"/>
                            <label for="all<?php echo absint($rand); ?>"><span></span><?php esc_html_e('All', 'wp-jobsearch') ?>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function filter_sector() {
        global  $jobsearch_form_fields;
        $sector_name = 'sector_cat';
        $sector = isset($_REQUEST['sector_cat']) ? $_REQUEST['sector_cat'] : '';
        $sector = jobsearch_esc_html($sector);
        $rand = rand(100000, 999999);
        ?>
        <div class="jobsearch-filter-responsive-wrap">
            <div class="jobsearch-search-filter-wrap jobsearch-search-filter-toggle">
                <div class="jobsearch-fltbox-title"><a href="javascript:void(0);" class="jobsearch-click-btn"><?php echo esc_html__('Sector', 'wp-jobsearch'); ?></a></div>
                <div class="jobsearch-checkbox-toggle">
                    <?php
                    // get all candidate types

                    $sector_parent_id = 0;
                    $sector_show_count = 0;
                    $input_type_sector = 'radio';   // if first level then select only sigle sector
                    
                    $sector_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'number' => $sector_show_count,
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $sector_parent_id,
                    );

                    $sector_all_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $sector_parent_id,
                    );
                    $all_sector = get_terms('sector', $sector_args);

                    if (count($all_sector) <= 0) {
                        $sector_args = array(
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'number' => $sector_show_count,
                            'fields' => 'all',
                            'hide_empty' => false,
                            'slug' => '',
                            'parent' => isset($selected_spec->parent) ? $selected_spec->parent : 0,
                        );
                        $sector_all_args = array(
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'fields' => 'all',
                            'hide_empty' => false,
                            'slug' => '',
                            'parent' => isset($selected_spec->parent) ? $selected_spec->parent : 0,
                        );
                        $all_sector = get_terms('sector', $sector_args);

                        if (isset($selected_spec->parent) && $selected_spec->parent != 0) {
                            $input_type_sector = 'checkbox';
                        }
                    } else {
                        if ($sector_parent_id != 0) {    // if parent is not root means not main parent
                            $input_type_sector = 'checkbox';   // if first level then select multiple sector
                        }
                    }

                    if (!empty($all_sector)) {

                        $number_option = 1;
                        $show_sector = 'yes';

                        if ($input_type_sector == 'radio' && $sector != '') {

                            if (is_array($sector) && is_array_empty($sector)) {
                                $show_sector = 'yes';
                            } else {
                                $show_sector = 'yes';
                            }

                        } else {
                            $show_sector = 'yes';
                        }

                        if ($show_sector == 'yes') {

                            if ($input_type_sector == 'checkbox') {

                            }
                            $number_option_flag = 1;
                            echo '<ul class="jobsearch-checkbox">';

                            foreach ($all_sector as $sectoritem) {
                                
                                $candidate_id_para = '';

                                if ($input_type_sector == 'checkbox') {
                                    ?>
                                    <li class="jobsearch-<?php echo ($input_type_sector); ?><?php echo($number_option_flag > 6 ? ' filter-more-fields' : '') ?> no-filter-counts">
                                        <?php
                                        $sector_selected = '';
                                        if ($sector == $sectoritem->slug) {
                                            $sector_selected = ' checked="checked"';
                                        }
                                        $jobsearch_form_fields->radio_field(
                                            array(
                                                'simple' => true,
                                                'id' => 'sector_' . $number_option,
                                                'cus_name' => $sector_name,
                                                'std' => $sectoritem->slug,
                                                'classes' => $sector_name,
                                                'ext_attr' => $sector_selected
                                            )
                                        );
                                        ?>
                                        <label for="sector_<?php echo $number_option; ?>">
                                            <span></span><?php echo $sectoritem->name; ?>
                                        </label>
                                    </li>
                                    <?php
                                } else
                                    if ($input_type_sector == 'radio') {
                                        $sector_selected = '';
                                        if ($sector == $sectoritem->slug) {
                                            $sector_selected = ' checked="checked"';
                                        }
                                        ?>
                                        <li class="jobsearch-<?php echo ($input_type_sector); ?><?php echo($number_option_flag > 6 ? ' filter-more-fields' : '') ?> no-filter-counts">
                                            <?php
                                            $jobsearch_form_fields->radio_field(
                                                array(
                                                    'simple' => true,
                                                    'id' => 'sector_' . $number_option,
                                                    'cus_name' => $sector_name,
                                                    'std' => $sectoritem->slug,
                                                    'classes' => $sector_name,
                                                    'ext_attr' => $sector_selected
                                                )
                                            );
                                            ?>
                                            <label for="sector_<?php echo $number_option; ?>">
                                                <span></span><?php echo $sectoritem->name; ?>
                                            </label>
                                        </li>
                                        <?php
                                    }
                                $number_option++;
                                $number_option_flag++;
                            }
                            echo '</ul>';
                            if ($number_option_flag > 6) {
                                echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                            }
                        }
                    } else {
                        ?>
                        <p><?php esc_html_e('No sector found. Please add from admin > job > sectors.', 'wp-jobsearch') ?></p>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function filter_experience() {
        $experience = isset($_REQUEST['experience']) ? $_REQUEST['experience'] : '';
        $experience = jobsearch_esc_html($experience);
        $rand = rand(100000, 999999);
        ?>
        <div class="jobsearch-filter-responsive-wrap">
            <div class="jobsearch-search-filter-wrap jobsearch-search-filter-toggle">
                <div class="jobsearch-fltbox-title"><a href="javascript:void(0);" class="jobsearch-click-btn"><?php echo esc_html__('Experience', 'wp-jobsearch'); ?></a></div>
                <div class="jobsearch-checkbox-toggle">
                    <ul class="jobsearch-checkbox">
                        <li class="no-filter-counts">
                            <input id="0-1-exp<?php echo absint($rand); ?>" type="radio"
                                   name="experience" <?php if ($experience == '0-1') echo 'checked="checked"'; ?>
                                   value="0-1"/>
                            <label for="0-1-exp<?php echo absint($rand); ?>"><span></span><?php esc_html_e('0 - 1 year', 'wp-jobsearch') ?></label>
                        </li>
                        <li class="no-filter-counts">
                            <input id="2-3-exp<?php echo absint($rand); ?>" type="radio"
                                   name="experience" <?php if ($experience == '2-3') echo 'checked="checked"'; ?>
                                   value="2-3"/>
                            <label for="2-3-exp<?php echo absint($rand); ?>"><span></span><?php esc_html_e('2 - 3 years', 'wp-jobsearch') ?></label>
                        </li>
                        <li class="no-filter-counts">
                            <input id="4-5-exp<?php echo absint($rand); ?>" type="radio"
                                   name="experience" <?php if ($experience == '4-5') echo 'checked="checked"'; ?>
                                   value="4-5"/>
                            <label for="4-5-exp<?php echo absint($rand); ?>"><span></span><?php esc_html_e('4 - 5 years', 'wp-jobsearch') ?></label>
                        </li>
                        <li class="no-filter-counts">
                            <input id="6-10-exp<?php echo absint($rand); ?>" type="radio"
                                   name="experience" <?php if ($experience == '6-10') echo 'checked="checked"'; ?>
                                   value="6-10"/>
                            <label for="6-10-exp<?php echo absint($rand); ?>"><span></span><?php esc_html_e('6 - 10 years', 'wp-jobsearch') ?></label>
                        </li>
                        <li class="no-filter-counts">
                            <input id="10plus-exp<?php echo absint($rand); ?>" type="radio"
                                   name="experience" <?php if ($experience == '10plus') echo 'checked="checked"'; ?>
                                   value="10plus"/>
                            <label for="10plus-exp<?php echo absint($rand); ?>"><span></span><?php esc_html_e('10+ years', 'wp-jobsearch') ?></label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function filter_skills() {
        $get_skills = isset($_REQUEST['applicant_skills']) ? $_REQUEST['applicant_skills'] : '';
        $get_skills = jobsearch_esc_html($get_skills);
        
        $skills_arr = array();
        if ($get_skills != '') {
            $skills_arr = explode(',', $get_skills);
        }
        $rand = rand(100000, 999999);
        ?>
        <div class="jobsearch-filter-responsive-wrap">
            <div class="jobsearch-search-filter-wrap jobsearch-search-filter-toggle">
                <div class="jobsearch-fltbox-title"><a href="javascript:void(0);" class="jobsearch-click-btn"><?php echo esc_html__('Skills', 'wp-jobsearch'); ?> <span id="filter-loder-<?php echo ($rand) ?>" class="skills-add-loader"></span></a></div>
                <div id="skills-filter-main-<?php echo ($rand) ?>" class="jobsearch-checkbox-toggle">
                    <ul class="jobseach-job-skills tagit ui-widget ui-widget-content">
                        <?php
                        if (!empty($skills_arr)) {
                            foreach ($skills_arr as $skill_slug) {
                                if ($skill_slug != '') {
                                    $skill_obj = get_term_by('slug', $skill_slug, 'skill');
                                    if (isset($skill_obj->name)) {
                                        echo '<li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable skill-tag-' . $skill_obj->slug . '" data-st="' . $skill_obj->slug . '"><span class="tagit-label">' . $skill_obj->name . '</span><a class="tagit-close remove-filter-skills"><span class="fa fa-times"></span></a></li>';
                                    }
                                }
                            }
                        }
                        ?>
                        <li class="tagit-new"><input type="text" class="skills-addin-input ui-widget-content ui-autocomplete-input" placeholder="<?php esc_html_e('Find Skills', 'wp-jobsearch'); ?>" autocomplete="off"></li>
                    </ul>
                    <div class="skills-result-con" style="display: none;"></div>
                    <input type="hidden" name="applicant_skills" value="<?php echo ($get_skills) ?>">
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var skills_find_req_<?php echo ($rand) ?>;
            jQuery(document).on('keyup', '.skills-addin-input', function () {
                var this_val = jQuery(this).val();

                if (this_val.length > 1) {
                    var loader_con = jQuery('#filter-loder-<?php echo ($rand) ?>');
                    var results_con = jQuery('#skills-filter-main-<?php echo ($rand) ?>').find('.skills-result-con');
                    if (typeof (skills_find_req_<?php echo ($rand) ?>) !== 'undefined') {
                        skills_find_req_<?php echo ($rand) ?>.abort();
                    }
                    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                    skills_find_req_<?php echo ($rand) ?> = $.ajax({
                        url: jobsearch_dashboard_vars.ajax_url,
                        method: "POST",
                        data: {
                            keyval: this_val,
                            action: 'jobsearch_get_skills_recomnd_applic_filters'
                        },
                        dataType: "json"
                    });
                    skills_find_req_<?php echo ($rand) ?>.done(function (response) {
                        
                        if (response.results == '1') {
                            results_con.html(response.html);
                            results_con.removeAttr('style');
                        } else {
                            results_con.html('');
                            results_con.hide();
                        }
                        loader_con.html('');
                    });
                    skills_find_req_<?php echo ($rand) ?>.complete(function () {
                        //
                    });
                }
            });
            jQuery(document).on('click', '.jobsearch-addskill-totag', function () {
                var _this = jQuery(this);
                var this_label = _this.html();
                var this_slug = _this.attr('data-slug');
                var hiden_skills_inp = jQuery('#skills-filter-main-<?php echo ($rand) ?>').find('input[name=applicant_skills]');
                var appnder_parent = jQuery('#skills-filter-main-<?php echo ($rand) ?>').find('ul.jobseach-job-skills');
                var appnder_con = appnder_parent.find('li.tagit-new');
                
                if (appnder_parent.find('.skill-tag-' + this_slug).length == 0) {
                    appnder_con.before('<li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable skill-tag-' + this_slug + '" data-st="' + this_slug + '"><span class="tagit-label">' + this_label + '</span><a class="tagit-close remove-filter-skills"><span class="fa fa-times"></span></a></li>');
                }
                
                var skill_tohid_arr = [];
                appnder_parent.find('.ui-widget-content').each(function() {
                    var skill_slug = jQuery(this).attr('data-st');
                    skill_tohid_arr.push(skill_slug);
                });
                var skill_tohid_str = skill_tohid_arr.join(',');
                hiden_skills_inp.val(skill_tohid_str);
            });
            jQuery(document).on('click', '.remove-filter-skills', function () {
                var _this = jQuery(this);
                _this.parent('li').remove();
                var hiden_skills_inp = jQuery('#skills-filter-main-<?php echo ($rand) ?>').find('input[name=applicant_skills]');
                var appnder_parent = jQuery('#skills-filter-main-<?php echo ($rand) ?>').find('ul.jobseach-job-skills');
                
                if (appnder_parent.find('.ui-widget-content').length > 0) {
                    var skill_tohid_arr = [];
                    appnder_parent.find('.ui-widget-content').each(function() {
                        var skill_slug = jQuery(this).attr('data-st');
                        skill_tohid_arr.push(skill_slug);
                    });
                    var skill_tohid_str = skill_tohid_arr.join(',');
                    hiden_skills_inp.val(skill_tohid_str);
                } else {
                    hiden_skills_inp.val('');
                }
            });
            jQuery('body').on('click', function (ev) {
                var this_dom = ev.target;
                var thisdom_obj = jQuery(this_dom);
                if (thisdom_obj.parents('#skills-filter-main-<?php echo ($rand) ?>').length > 0) {
                    //
                } else {
                    jQuery('#skills-filter-main-<?php echo ($rand) ?>').find('.skills-result-con').hide();
                }
            });
        </script>
        <?php
    }
    
    public function get_skills_recomnd() {
        global $wpdb;
        
        $input_val = isset($_POST['keyval']) ? $_POST['keyval'] : '';
        
        $taxonomy = 'skill';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            global $sitepress;
            $trans_tble = $wpdb->prefix . 'icl_translations';
            $terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS terms"
                . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                . " LEFT JOIN $trans_tble AS icl_trans ON (terms.term_id = icl_trans.element_id) "
                . " WHERE term_tax.taxonomy = '%s' AND terms.name LIKE '%$input_val%'"
                . " AND icl_trans.language_code='" . $sitepress->get_current_language() . "'"
                . " ORDER BY name ASC", $taxonomy));
        } else {
            $terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS terms"
                . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                . " WHERE term_tax.taxonomy = '%s' AND terms.name LIKE '%$input_val%'"
                . " ORDER BY name ASC", $taxonomy));
        }
        //var_dump($terms);
        $resp_results = '0';
        ob_start();
        if (!empty($terms)) {
            echo '<ul>';
            foreach ($terms as $skill_term) {
                echo '<li><a href="javascript:void(0);" class="jobsearch-addskill-totag" data-slug="' . $skill_term->slug . '">' . $skill_term->name . '</a></li>';
            }
            echo '</ul>';
            $resp_results = '1';
        }
        $html = ob_get_clean();
        
        wp_send_json(array('html' => $html, 'results' => $resp_results));
    }

}

return new Jobsearch_Job_Applicants_Filters;
