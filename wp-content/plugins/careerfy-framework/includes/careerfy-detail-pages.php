<?php
add_filter('job_detail_pages_styles', 'job_detail_pages_styles_callback', 10, 1);
add_filter('candidate_detail_pages_styles', 'candidate_detail_pages_styles_callback', 10, 1);
add_filter('employer_detail_pages_styles', 'employer_detail_pages_styles_callback', 10, 1);
add_filter('careerfy_job_detail_page_style_display', 'careerfy_detail_page_style_display_callback', 10, 2);
add_filter('careerfy_emp_detail_page_style_display', 'careerfy_emp_detail_page_style_display_callback', 10, 2);
add_filter('careerfy_cand_detail_page_style_display', 'careerfy_cand_detail_page_style_display_callback', 10, 2);
add_filter('careerfy_employer_team_members_view', 'careerfy_employer_team_members_view_callback', 10, 2);
add_action('add_meta_boxes', 'add_job_detail_style_metaboxes');
add_action('careerfy_job_detail_related_view4', 'careerfy_job_detail_related_view4_callback', 10, 2);
add_action('careerfy_job_detail_related_view2', 'careerfy_job_detail_related_view2_callback', 10, 2);
add_action('careerfy_job_detail_related_view3', 'careerfy_job_detail_related_view3_callback', 10, 2);
add_action('careerfy_similar_jobs', 'careerfy_similar_jobs_callback', 0, 1);
function careerfy_cand_detail_page_style_display_callback($cand_options_view = '', $cand_id = '')
{
    $post_style = '';
    $cand_post_style = get_post_meta($cand_id, 'careerfy_field_candidate_post_detail_style', true);
    $cand_post_style = isset($cand_post_style) && !empty($cand_post_style) ? $cand_post_style : '';
    if (empty($cand_post_style)) {
        $post_style = $cand_options_view;
    } else {
        $post_style = $cand_post_style;
    }
    return $post_style;
}

function careerfy_emp_detail_page_style_display_callback($emp_options_view = '', $emp_id = '')
{
    $post_style = '';
    $emp_post_style = get_post_meta($emp_id, 'careerfy_field_employer_post_detail_style', true);
    $emp_post_style = isset($emp_post_style) && !empty($emp_post_style) ? $emp_post_style : '';

    if (empty($emp_post_style)) {
        $post_style = $emp_options_view;
    } else {
        $post_style = $emp_post_style;
    }
    return $post_style;
}

function careerfy_detail_page_style_display_callback($job_options_view = '', $job_id = '')
{
    $job_post_style = get_post_meta($job_id, 'careerfy_field_job_post_detail_style', true);
    $job_post_style = isset($job_post_style) && !empty($job_post_style) ? $job_post_style : '';

    if (empty($job_post_style)) {
        return $job_options_view;
    } else {
        return $job_post_style;
    }
}

function add_job_detail_style_metaboxes()
{
    add_meta_box('careerfy_job_style_metabox', 'job Detail Styles', 'careerfy_job_style_metabox_callback', 'job', 'side', 'default');
    add_meta_box('careerfy_candidate_style_metabox', 'Candidate Detail Styles', 'careerfy_candidate_style_metabox_callback', 'candidate', 'side', 'default');
    add_meta_box('careerfy_employer_style_metabox', 'Employer Detail Styles', 'careerfy_employer_style_metabox_callback', 'employer', 'side', 'default');
}

function careerfy_employer_style_metabox_callback()
{
    global $careerfy_form_fields;
    ?>
    <div class="careerfy-post-layout">
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Select Style', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $url = admin_url();
                $link_path = '<a href="' . $url . 'admin.php?page=jobsearch_options" target="__blank"> ' . esc_html__(' Jobsearch Options ', 'careerfy-frame') . '</a>';
                $field_params = array(
                    'name' => 'employer_post_detail_style',
                    'options' => array(
                        '' => __('Default', 'careerfy-frame'),
                        'view1' => __('Employer  1', 'careerfy-frame'),
                        'view2' => __('Employer  2', 'careerfy-frame'),
                        'view3' => __('Employer  3', 'careerfy-frame'),
                        'view4' => __('Employer  4', 'careerfy-frame'),
                    ),
                );
                $careerfy_form_fields->select_field($field_params);
                ?>
            </div>
            <?php echo '<p>' . esc_html__('You can change default style from  ', 'careerfy-frame') . $link_path . '<p>'; ?>
        </div>
    </div>
    <?php
}

function careerfy_candidate_style_metabox_callback()
{
    global $careerfy_form_fields;
    $url = admin_url();
    $link_path = '<a href="' . $url . 'admin.php?page=jobsearch_options" target="__blank"> ' . esc_html__(' Jobsearch Options ', 'careerfy-frame') . '</a>';
    ?>
    <div class="careerfy-post-layout">
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Select Style', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'candidate_post_detail_style',
                    'options' => array(
                        '' => __('Default', 'careerfy-frame'),
                        'view1' => __('Candidate  1', 'careerfy-frame'),
                        'view2' => __('Candidate  2', 'careerfy-frame'),
                        'view3' => __('Candidate  3', 'careerfy-frame'),
                        'view4' => __('Candidate  4', 'careerfy-frame'),
                        'view5' => __('Candidate  5', 'careerfy-frame'),
                    ),
                );
                $careerfy_form_fields->select_field($field_params);
                ?>
            </div>
            <?php echo '<p>' . esc_html__('You can change default style from  ', 'careerfy-frame') . $link_path . '<p>'; ?>
        </div>
    </div>
    <?php
}

function careerfy_job_style_metabox_callback()
{
    global $careerfy_form_fields;
    $url = admin_url();
    $link_path = '<a href="' . $url . 'admin.php?page=jobsearch_options" target="__blank"> ' . esc_html__(' Jobsearch Options ', 'careerfy-frame') . '</a>';
    ?>
    <div class="careerfy-post-layout">
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Select Style', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'job_post_detail_style',
                    'options' => array(
                        '' => __('Default', 'careerfy-frame'),
                        'view1' => __('Job 1', 'careerfy-frame'),
                        'view2' => __('Job 2', 'careerfy-frame'),
                        'view3' => __('Job 3', 'careerfy-frame'),
                        'view4' => __('Job 4', 'careerfy-frame'),
                        'view5' => __('Job 5', 'careerfy-frame'),
                    ),
                );
                $careerfy_form_fields->select_field($field_params);
                ?>
            </div>
            <?php echo '<p>' . esc_html__('You can change default style from  ', 'careerfy-frame') . $link_path . '<p>'; ?>
        </div>
    </div>
    <?php
}

function careerfy_employer_team_members_view_callback($html = '', $ajax_data = array())
{
    $total_pages = isset($ajax_data['total_pages']) ? $ajax_data['total_pages'] : 1;
    $cur_page = isset($ajax_data['cur_page']) ? $ajax_data['cur_page'] : 1;
    $employer_id = isset($ajax_data['employer_id']) ? $ajax_data['employer_id'] : 1;
    $class_pref = isset($ajax_data['class_pref']) && $ajax_data['class_pref'] != '' ? $ajax_data['class_pref'] : 'jobsearch';
    $team_style = isset($ajax_data['team_style']) ? $ajax_data['team_style'] : 'default';
    if ($team_style != 'team2') {
        return $html;
    }
    $per_page_results = 4;
    $start = ($cur_page) * ($per_page_results);
    $offset = $per_page_results;
    $exfield_list = get_post_meta($employer_id, 'jobsearch_field_team_title', true);
    $exfield_list_val = get_post_meta($employer_id, 'jobsearch_field_team_description', true);
    $team_designationfield_list = get_post_meta($employer_id, 'jobsearch_field_team_designation', true);
    $team_experiencefield_list = get_post_meta($employer_id, 'jobsearch_field_team_experience', true);
    $team_imagefield_list = get_post_meta($employer_id, 'jobsearch_field_team_image', true);
    $team_facebookfield_list = get_post_meta($employer_id, 'jobsearch_field_team_facebook', true);
    $team_googlefield_list = get_post_meta($employer_id, 'jobsearch_field_team_google', true);
    $team_twitterfield_list = get_post_meta($employer_id, 'jobsearch_field_team_twitter', true);
    $team_linkedinfield_list = get_post_meta($employer_id, 'jobsearch_field_team_linkedin', true);
    $exfield_list = array_slice($exfield_list, $start, $offset);
    $exfield_list_val = array_slice($exfield_list_val, $start, $offset);
    $team_designationfield_list = array_slice($team_designationfield_list, $start, $offset);
    $team_experiencefield_list = array_slice($team_experiencefield_list, $start, $offset);
    $team_imagefield_list = array_slice($team_imagefield_list, $start, $offset);
    $team_facebookfield_list = array_slice($team_facebookfield_list, $start, $offset);
    $team_googlefield_list = array_slice($team_googlefield_list, $start, $offset);
    $team_twitterfield_list = array_slice($team_twitterfield_list, $start, $offset);
    $team_linkedinfield_list = array_slice($team_linkedinfield_list, $start, $offset);

    ob_start();
    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
        $total_team = sizeof($exfield_list);
        $rand_num_ul = rand(1000000, 99999999);
        $exfield_counter = 0;
        foreach ($exfield_list as $exfield) {
            $rand_num = rand(1000000, 99999999);
            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
            $team_designationfield_val = isset($team_designationfield_list[$exfield_counter]) ? $team_designationfield_list[$exfield_counter] : '';
            $team_experiencefield_val = isset($team_experiencefield_list[$exfield_counter]) ? $team_experiencefield_list[$exfield_counter] : '';
            $team_imagefield_val = isset($team_imagefield_list[$exfield_counter]) ? $team_imagefield_list[$exfield_counter] : '';
            $team_facebookfield_val = isset($team_facebookfield_list[$exfield_counter]) ? $team_facebookfield_list[$exfield_counter] : '';
            $team_googlefield_val = isset($team_googlefield_list[$exfield_counter]) ? $team_googlefield_list[$exfield_counter] : '';
            $team_twitterfield_val = isset($team_twitterfield_list[$exfield_counter]) ? $team_twitterfield_list[$exfield_counter] : '';
            $team_linkedinfield_val = isset($team_linkedinfield_list[$exfield_counter]) ? $team_linkedinfield_list[$exfield_counter] : '';
            ?>
            <li class="careerfy-column-6">
                <script>
                    jQuery(document).ready(function () {
                        jQuery('a[id^="fancybox_notes"]').fancybox({
                            'titlePosition': 'inside',
                            'transitionIn': 'elastic',
                            'transitionOut': 'elastic',
                            'width': 400,
                            'height': 250,
                            'padding': 40,
                            'autoSize': false
                        });
                    });
                </script>
                <figure>
                    <a id="fancybox_notes<?php echo($rand_num) ?>" href="#notes<?php echo($rand_num) ?>"><img
                                src="<?php echo($team_imagefield_val) ?>" alt=""></a>
                    <figcaption>
                        <h2><a id="fancybox_notes_txt<?php echo($rand_num) ?>"
                               href="#notes<?php echo($rand_num) ?>"><?php echo($exfield) ?></a></h2>
                        <span><?php echo($team_designationfield_val) ?></span>
                        <?php
                        if ($team_experiencefield_val != '') {
                            echo '<span>' . sprintf(esc_html__('Experience: %s', 'careerfy'), $team_experiencefield_val) . '</span>';
                        }

                        //
                        if ($team_facebookfield_val != '' || $team_googlefield_val != '' || $team_twitterfield_val != '' || $team_linkedinfield_val != '') {
                            ?>
                            <ul class="jobsearch-social-icons">
                                <?php
                                if ($team_facebookfield_val != '') { ?>
                                    <li><a href="<?php echo($team_facebookfield_val) ?>" data-original-title="facebook"
                                           class="jobsearch-icon jobsearch-facebook-logo"></a></li>
                                    <?php
                                }
                                if ($team_googlefield_val != '') {
                                    ?>
                                    <li><a href="<?php echo($team_googlefield_val) ?>" data-original-title="google-plus"
                                           class="jobsearch-icon jobsearch-google-plus-logo-button"></a></li>
                                    <?php
                                }
                                if ($team_twitterfield_val != '') {
                                    ?>
                                    <li><a href="<?php echo($team_twitterfield_val) ?>" data-original-title="twitter"
                                           class="jobsearch-icon jobsearch-twitter-logo"></a></li>
                                    <?php
                                }
                                if ($team_linkedinfield_val != '') {
                                    ?>
                                    <li><a href="<?php echo($team_linkedinfield_val) ?>" data-original-title="linkedin"
                                           class="jobsearch-icon jobsearch-linkedin-button"></a></li>
                                    <?php } ?>
                            </ul>
                            <?php
                        }
                        ?>
                    </figcaption>
                </figure>
            </li>
            <div id="notes<?php echo($rand_num) ?>" style="display: none;"><?php echo($exfield_val) ?></div>
            <?php
            $exfield_counter++;
        }
    }
    $html = ob_get_clean();
    return $html;
}

function job_detail_pages_styles_callback($sec_array = array())
{
    $sec_array[] = array(
        'id' => 'jobsearch_job_detail_views',
        'type' => 'select',
        'title' => __('Job Detail Styles', 'careerfy-frame'),
        'desc' => '',
        'options' => array(
            'view1' => __('Job detail 1', 'careerfy-frame'),
            'view2' => __('Job detail 2', 'careerfy-frame'),
            'view3' => __('Job detail 3', 'careerfy-frame'),
            'view4' => __('Job detail 4', 'careerfy-frame'),
            'view5' => __('Job detail 5', 'careerfy-frame'),
        ),
        'default' => '',
    );

    return $sec_array;
}

function employer_detail_pages_styles_callback($employer_arr = array())
{
    $employer_arr[] = array(
        'id' => 'jobsearch_emp_detail_views',
        'type' => 'select',
        'title' => __('Employer Detail Styles', 'careerfy-frame'),
        'desc' => '',
        'options' => array(
            'view1' => __('Employer detail 1', 'careerfy-frame'),
            'view2' => __('Employer detail 2', 'careerfy-frame'),
            'view3' => __('Employer detail 3', 'careerfy-frame'),
            'view4' => __('Employer detail 4', 'careerfy-frame'),
        ),
        'default' => '',
    );
    return $employer_arr;
}

function candidate_detail_pages_styles_callback($candidate_arr = array())
{
    $all_page = array();
    $args = array(
        'sort_order' => 'asc',
        'sort_column' => 'post_title',
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'meta_key' => '',
        'meta_value' => '',
        'authors' => '',
        'child_of' => 0,
        'parent' => -1,
        'exclude_tree' => '',
        'number' => '',
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish'
    );
    $pages = get_pages($args);
    if (!empty($pages)) {
        $all_page[''] = __('Select Page', 'careerfy-frame');
        foreach ($pages as $page) {
            $all_page[$page->post_name] = $page->post_title;
        }
    }
    $candidate_arr[] = array(
        'id' => 'jobsearch_cand_detail_views',
        'type' => 'select',
        'title' => __('Candidate Detail Styles', 'careerfy-frame'),
        'desc' => '',
        'options' => array(
            'view1' => __('Candidate detail 1', 'careerfy-frame'),
            'view2' => __('Candidate detail 2', 'careerfy-frame'),
            'view3' => __('Candidate detail 3', 'careerfy-frame'),
            'view4' => __('Candidate detail 4', 'careerfy-frame'),
            'view5' => __('Candidate detail 5', 'careerfy-frame'),
        ),
        'default' => '',
    );
    return $candidate_arr;
}

function careerfy_job_detail_related_view2_callback($html = '', $related_atts = array())
{
    extract($related_atts);
    global $jobsearch_plugin_options;
    if ($title != '') { ?>
        <div class="careerfy-joblisting-view4-title"><h2><?php echo esc_html($title); ?></h2></div>
    <?php } ?>
    <div class="careerfy-job-listing careerfy-joblisting-view4">
        <ul class="row">
            <?php

            $jobsearch_title_limit = isset($jobsearch_plugin_options['related_jobs_title_length']) && $jobsearch_plugin_options['related_jobs_title_length'] > 0 ? $jobsearch_plugin_options['related_jobs_title_length'] : '';
            $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
            if ($featured_job_loop_count->have_posts()) {
                $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';

                $ads_rep_counter = 1;
                while ($featured_job_loop_count->have_posts()) : $featured_job_loop_count->the_post();
                    global $post, $jobsearch_member_profile;
                    $job_id = $post;
                    $job_random_id = rand(1111111, 9999999);
                    $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                    $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-job-medium');
                    $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : jobsearch_no_image_placeholder();
                    $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                    $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                    $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                    $company_name = function_exists('jobsearch_job_get_company_name') ? jobsearch_job_get_company_name($job_id, '@ ') : '';
                    $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                    $job_type_str = function_exists('jobsearch_job_get_all_jobtypes') ? jobsearch_job_get_all_jobtypes($job_id, '', '', '', '', '', 'span') : '';
                    $sector_str = function_exists('jobsearch_job_get_all_sectors') ? jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="careerfy-icon careerfy-filter-tool-black-shape"></i>', '</li>') : '';
                    $job_salary = jobsearch_job_offered_salary($job_id);
                    $job_city_title = '';
                    $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
                    if ($get_job_city == '') {
                        $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
                    }
                    if ($get_job_city == '') {
                        $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                    }

                    $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
                    if (is_object($job_city_tax)) {
                        $job_city_title = $job_city_tax->name;
                    }
                    $skills_list = jobsearch_job_get_all_skills($job_id);
                    $current_time = time();
                    $elaspedtime = ($current_time) - ($job_post_date);
                    $hourz = 24 * 60 * 60;

                    $get_job_location = '';
                    $get_job_country = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                    $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
                    $get_job_location = '';
                    if (isset($get_job_country) && !empty($get_job_country) || isset($get_job_city) && !empty($get_job_city)) {
                        if (isset($get_job_country) && !empty($get_job_country) && isset($get_job_city) && !empty($get_job_city)) {
                            $get_job_location = ucfirst($get_job_country) . ',' . ucfirst($get_job_city);
                        } elseif (isset($get_job_country) && !empty($get_job_country)) {
                            $get_job_location = ucfirst($get_job_country);
                        } elseif (isset($get_job_city) && !empty($get_job_city)) {
                            $get_job_location = ucfirst($get_job_city);
                        }
                    }
                    ?>
                    <li class="col-md-12">
                        <div class="careerfy-joblisting-wrap">
                            <?php
                            ob_start();
                            ?>
                            <div class="careerfy-joblisting-media">
                                <?php
                                if ($post_thumbnail_src != '') {
                                    ?>
                                    <figure>
                                        <a href="<?php the_permalink(); ?>">
                                            <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                        </a>
                                    </figure>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            $list_emp_img = ob_get_clean();
                            echo apply_filters('jobsearch_jobs_listing_emp_img_html', $list_emp_img, $job_id, 'view2');
                            ?>
                            <div class="careerfy-joblisting-text">
                                <h2>
                                    <a href="<?php echo esc_url(get_permalink($job_id)); ?>"
                                       title="<?php echo get_the_title($job_id); ?>">
                                        <?php echo esc_html(wp_trim_words(get_the_title($job_id), $jobsearch_title_limit)); ?>
                                    </a>
                                    <?php
                                    if ($job_type_str != '' && $job_types_switch == 'on') {
                                        echo($job_type_str);
                                    }
                                    ?>
                                </h2>
                                <?php
                                ob_start();
                                ?>
                                <div class="careerfy-company-name"><?php echo($company_name) ?></div>
                                <?php
                                $list_emp_title = ob_get_clean();
                                echo apply_filters('jobsearch_jobs_listing_emp_titleanchr_html', $list_emp_title, $job_id, 'view2');

                                if ($job_city_title != '' && $all_location_allow == 'on') {
                                    ?>
                                    <small><i class="fa fa-map-marker"></i> <?php echo($get_job_location) ?></small>
                                    <?php
                                }
                                if ($skills_list != '') { ?>
                                    <div class="careerfy-job-skills">
                                        <?php echo force_balance_tags($skills_list); ?>
                                    </div>
                                <?php } ?>
                                <div class="clearfix"></div>
                                <?php
                                if (jobsearch_excerpt(0, $job_id) != '') { ?>
                                    <div class="jobsearch-list-excerpt">
                                        <p><?php echo jobsearch_excerpt(0, $job_id) ?></p>
                                    </div>
                                <?php } ?>
                            </div>

                            <?php
                            if ($jobsearch_job_featured == 'on') { ?>
                                <span class="careerfy-joblisting-view4-featured"><?php echo esc_html__('Featured', 'careerfy-frame'); ?></span>
                            <?php } elseif ($elaspedtime > $hourz) { ?>
                                <span class="careerfy-joblisting-view4-date"><?php echo get_the_date('F j, Y', $job_id); ?></span>
                            <?php } else { ?>
                                <span class="careerfy-joblisting-view4-new"><?php echo esc_html__('New', 'careerfy-frame'); ?></span>
                            <?php } ?>
                        </div>
                    </li>
                <?php
                endwhile;
                wp_reset_postdata();
            }
            ?>
        </ul>
    </div>
    <?php
}

function careerfy_job_detail_related_view3_callback($html = '', $related_atts = array())
{
    extract($related_atts);
    global $jobsearch_plugin_options;
    if ($title != '') { ?>
        <div class="careerfy-section-title"><h2><?php echo esc_html($title); ?></h2></div>
        <?php
    }
    $jobsearch_title_limit = isset($jobsearch_plugin_options['related_jobs_title_length']) && $jobsearch_plugin_options['related_jobs_title_length'] > 0 ? $jobsearch_plugin_options['related_jobs_title_length'] : '';
    ?>
    <div class="careerfy-job careerfy-joblisting-classic careerfy-jobdetail-joblisting">
        <ul class="careerfy-row">
            <?php
            if ($featured_job_loop_count->have_posts()) {
                $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';

                $ads_rep_counter = 1;
                while ($featured_job_loop_count->have_posts()) : $featured_job_loop_count->the_post();
                    global $post, $jobsearch_member_profile;
                    $job_id = $post;
                    $job_random_id = rand(1111111, 9999999);
                    $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                    $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-job-medium');
                    $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : jobsearch_no_image_placeholder();
                    $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                    $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                    $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                    $company_name = function_exists('jobsearch_job_get_company_name') ? jobsearch_job_get_company_name($job_id, '@ ') : '';
                    $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                    $job_type_str = function_exists('jobsearch_job_get_all_jobtypes') ? jobsearch_job_get_all_jobtypes($job_id, 'careerfy-option-btn', '', '', '', '', 'span') : '';
                    $sector_str = function_exists('jobsearch_job_get_all_sectors') ? jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="careerfy-icon careerfy-filter-tool-black-shape"></i>', '</li>') : '';
                    $job_salary = jobsearch_job_offered_salary($job_id);
                    $job_city_title = '';
                    $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
                    if ($get_job_city == '') {
                        $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
                    }
                    if ($get_job_city == '') {
                        $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                    }
                    $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
                    if (is_object($job_city_tax)) {
                        $job_city_title = $job_city_tax->name;
                    }
                    $skills_list = jobsearch_job_get_all_skills($job_id);
                    $current_time = time();
                    $elaspedtime = ($current_time) - ($job_post_date);
                    $hourz = 24 * 60 * 60;

                    $get_job_location = '';
                    $get_job_country = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                    $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
                    $get_job_location = '';
                    if (isset($get_job_country) && !empty($get_job_country) || isset($get_job_city) && !empty($get_job_city)) {
                        if (isset($get_job_country) && !empty($get_job_country) && isset($get_job_city) && !empty($get_job_city)) {
                            $get_job_location = ucfirst($get_job_country) . ',' . ucfirst($get_job_city);
                        } elseif (isset($get_job_country) && !empty($get_job_country)) {
                            $get_job_location = ucfirst($get_job_country);
                        } elseif (isset($get_job_city) && !empty($get_job_city)) {
                            $get_job_location = ucfirst($get_job_city);
                        }
                    }
                    ?>
                    <li class="careerfy-column-12">
                        <div class="careerfy-joblisting-classic-wrap">
                            <?php if ($post_thumbnail_src != '') { ?>
                                <figure>
                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                    </a>
                                </figure>
                            <?php } ?>
                            <div class="careerfy-joblisting-text">
                                <div class="careerfy-list-option">
                                    <h2>
                                        <a href="<?php echo esc_url(get_permalink($job_id)); ?>"
                                           title="<?php echo get_the_title($job_id); ?>">
                                            <?php echo esc_html(wp_trim_words(get_the_title($job_id), $jobsearch_title_limit)); ?>
                                        </a> <?php
                                        if ($jobsearch_job_featured == 'on') { ?>
                                            <span><?php echo esc_html__('Featured', 'careerfy-frame'); ?></span>
                                        <?php } ?>
                                    </h2>
                                    <ul>
                                        <?php
                                        ob_start();
                                        ?>
                                        <li><?php echo($company_name) ?></li>
                                        <?php
                                        $comp_name_html = ob_get_clean();
                                        echo apply_filters('jobsearch_empname_in_jobdetail_related', $comp_name_html, $job_id, 'view3');

                                        if ($get_job_location != '' && $all_location_allow == 'on') {
                                            ?>
                                            <li>
                                                <i class="jobsearch-icon jobsearch-maps-and-flags"></i> <?php echo($get_job_location) ?>
                                            </li>
                                            <?php
                                        }
                                        echo $sector_str = jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="careerfy-icon careerfy-filter-tool-black-shape"></i>', '</li>');
                                        ?>
                                    </ul>
                                </div>
                                <div class="careerfy-job-userlist">
                                    <?php
                                    if ($job_type_str != '' && $job_types_switch == 'on') {
                                        echo($job_type_str);
                                    }
                                    $book_mark_args = array(
                                        'job_id' => $job_id,
                                        'before_icon' => 'fa fa-heart-o',
                                        'after_icon' => 'fa fa-heart',
                                        'container_class' => '',
                                        'anchor_class' => 'careerfy-job-like',
                                    );
                                    do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                    ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </li>
                <?php
                endwhile;
                wp_reset_postdata();
            }
            ?>
        </ul>
    </div>
    <?php
}

function careerfy_similar_jobs_callback($similar_atts = array())
{
    global $jobsearch_plugin_options;
    extract($similar_atts);
    $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

    echo '<div class = "jobsearch_side_box jobsearch_box_similarjobs">';
    if ($title != '') { ?>
        <h2><?php echo esc_html($title); ?></h2>
    <?php } ?>

    <ul>
        <?php
        // getting if record not found
        while ($featured_job_loop_count->have_posts()) : $featured_job_loop_count->the_post();
            global $post;
            $job_id = $post;
            $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
            $company_name = jobsearch_job_get_company_name($job_id, '');
            $application_deadline = get_post_meta($job_id, 'jobsearch_field_job_application_deadline_date', true);
            $current_date = strtotime(current_time('d-m-Y H:i:s'));
            $min_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);
            $max_salary = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);
            $salary = '';
            if (isset($min_salary) && !empty($min_salary) || isset($max_salary) && !empty($max_salary)) {
                if (isset($min_salary) && !empty($min_salary) && isset($max_salary) && !empty($max_salary)) {
                    $salary = $min_salary . ' - ' . $max_salary;
                } elseif (isset($min_salary) && !empty($min_salary)) {
                    $salary = $min_salary;
                } elseif (isset($max_salary) && !empty($max_salary)) {
                    $salary = $max_salary;
                }
            }
            ?>
            <li>
                <h5><a href="<?php echo esc_url(get_permalink($job_id)); ?>"
                       title="<?php echo esc_html(get_the_title($job_id)); ?>"><?php echo esc_html(wp_trim_words(get_the_title($job_id), $jobsearch_title_limit)); ?></a>
                </h5>
                <?php if (!empty($get_job_location) && $all_location_allow == 'on') {
                    ?>
                    <span><i class="jobsearch-icon jobsearch-maps-and-flags"></i><?php echo esc_html($get_job_location); ?></span>
                    <?php
                }

                if (isset($salary) && !empty($salary)) {
                    ?>
                    <span><i class="fa fa-money"></i> <?php echo($salary); ?></span>
                    <?php
                }
                if (isset($company_name) && !empty($company_name)) {
                    ?><span><i class="careerfy-icon careerfy-building"></i> <?php echo($company_name); ?></span><?php
                }
                if ($application_deadline != '' && $application_deadline <= $current_date) {
                    ?>
                    <a class="jobsearch_box_similarjobs_btn"
                       href="javascipt:void(0)"><?php esc_html_e('closed.', 'careerfy-frame'); ?></a>
                    <?php
                } else {
                    $arg = array(
                        'classes' => 'jobsearch_box_similarjobs_btn',
                        'btn_before_label' => esc_html__('Apply', 'careerfy-frame'),
                        'btn_after_label' => esc_html__('Successfully Applied', 'careerfy-frame'),
                        'btn_applied_label' => esc_html__('Applied', 'careerfy-frame'),
                        'job_id' => $job_id
                    );
                    $apply_filter_btn = apply_filters('jobsearch_job_applications_btn', '', $arg);
                    echo force_balance_tags($apply_filter_btn) . '';
                }
                ?>
            </li>
        <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </ul>
    <?php
    echo '</div>';
}

function careerfy_job_detail_related_view4_callback($html = '', $related_atts = array())
{
    extract($related_atts);
    global $jobsearch_plugin_options;
    if ($title != '') { ?>
        <div class="careerfy-section-title"><h2><?php echo esc_html($title); ?></h2></div>
        <?php
    }
    $jobsearch_title_limit = isset($jobsearch_plugin_options['related_jobs_title_length']) && $jobsearch_plugin_options['related_jobs_title_length'] > 0 ? $jobsearch_plugin_options['related_jobs_title_length'] : '';
    ?>
    <div class="careerfy-job careerfy-joblisting-plain">
        <ul class="row">
            <?php
            if ($featured_job_loop_count->have_posts()) {
                $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';
                $ads_rep_counter = 1;
                while ($featured_job_loop_count->have_posts()) : $featured_job_loop_count->the_post();
                    global $post, $jobsearch_member_profile;
                    $job_id = $post;
                    $job_random_id = rand(1111111, 9999999);
                    $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                    $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-job-medium');
                    $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : jobsearch_no_image_placeholder();
                    $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                    $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                    $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                    $company_name = function_exists('jobsearch_job_get_company_name') ? jobsearch_job_get_company_name($job_id, '@ ') : '';
                    $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                    $_job_salary_type = get_post_meta($job_id, 'jobsearch_field_job_salary_type', true);

                    $job_type_str = function_exists('jobsearch_job_get_all_jobtypes') ? jobsearch_job_get_all_jobtypes($job_id, 'careerfy-joblisting-plain-status', '', '', '', '', 'span') : '';
                    $sector_str = function_exists('jobsearch_job_get_all_sectors') ? jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="careerfy-icon careerfy-filter-tool-black-shape"></i>', '</li>') : '';
                    $job_salary = jobsearch_job_offered_salary($job_id);
                    $job_city_title = '';
                    $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
                    if ($get_job_city == '') {
                        $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
                    }
                    if ($get_job_city == '') {
                        $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                    }
                    $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
                    if (is_object($job_city_tax)) {
                        $job_city_title = $job_city_tax->name;
                    }

                    $salary_type = '';
                    if ($_job_salary_type == 'type_1') {
                        $salary_type = 'Monthly';
                    } else if ($_job_salary_type == 'type_2') {
                        $salary_type = 'Weekly';
                    } else if ($_job_salary_type == 'type_3') {
                        $salary_type = 'Hourly';
                    } else {
                        $salary_type = 'Negotiable';
                    }

                    $skills_list = jobsearch_job_get_all_skills($job_id);
                    $current_time = time();
                    $elaspedtime = ($current_time) - ($job_post_date);
                    $hourz = 24 * 60 * 60;
                    $min_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);
                    $max_salary = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);
                    $salary = '';
                    if (isset($min_salary) && !empty($min_salary) || isset($max_salary) && !empty($max_salary)) {
                        if (isset($min_salary) && !empty($min_salary) && isset($max_salary) && !empty($max_salary)) {
                            $salary = jobsearch_get_currency_symbol() . $min_salary . '  - ' . jobsearch_get_currency_symbol() . $max_salary . ' / ' . $salary_type;
                        } elseif (isset($min_salary) && !empty($min_salary)) {
                            $salary = jobsearch_get_currency_symbol() . $min_salary . ' / ' . $salary_type;
                        } elseif (isset($max_salary) && !empty($max_salary)) {
                            $salary = jobsearch_get_currency_symbol() . $max_salary . ' / ' . $salary_type;
                        }
                    }
                    $job_max_salary = jobsearch_job_offered_salary($job_id);
                    ?>
                    <li class="col-md-12">
                        <div class="careerfy-joblisting-plain-wrap">
                            <?php if ($post_thumbnail_src != '') { ?>
                                <figure>
                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                    </a>
                                </figure>
                            <?php } ?>
                            <div class="careerfy-joblisting-plain-text">
                                <div class="careerfy-joblisting-plain-left">
                                    <h2>
                                        <a href="<?php echo esc_url(get_permalink($job_id)); ?>"
                                           title="<?php echo get_the_title($job_id); ?>">
                                            <?php echo esc_html(wp_trim_words(get_the_title($job_id), $jobsearch_title_limit)); ?>
                                        </a> <?php
                                        if ($jobsearch_job_featured == 'on') { ?>
                                            <span><?php echo esc_html__('Featured', 'careerfy-frame'); ?></span>
                                        <?php } ?>
                                    </h2>
                                    <ul>
                                        <?php
                                        ob_start();
                                        ?>
                                        <li><span><?php echo($company_name) ?></span></li>
                                        <?php
                                        $comp_name_html = ob_get_clean();
                                        echo apply_filters('jobsearch_empname_in_jobdetail_related', $comp_name_html, $job_id, 'view4');
                                        echo $sector_str = jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="careerfy-icon careerfy-filter-tool-black-shape"></i>', '</li>');
                                        if (isset($salary) && !empty($salary)) { ?>
                                            <li>
                                                <i class="careerfy-icon careerfy-money-line"></i><?php echo($job_max_salary); ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div class="careerfy-joblisting-plain-right">
                                    <?php
                                    if ($get_job_location != '' && $all_location_allow == 'on') {
                                        ?>
                                        <small><i class="fa fa-map-marker"></i> <?php echo($get_job_location) ?></small>
                                        <?php
                                    }
                                    $book_mark_args = array(
                                        'job_id' => $job_id,
                                        'before_icon' => 'fa fa-heart-o',
                                        'after_icon' => 'fa fa-heart',
                                        'container_class' => 'shortlist-container',
                                        'anchor_class' => 'careerfy-job-like',
                                    );
                                    do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);

                                    if ($job_type_str != '' && $job_types_switch == 'on') {
                                        echo($job_type_str);
                                    }
                                    ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </li>
                <?php
                endwhile;
                wp_reset_postdata();
            }
            ?>
        </ul>
    </div>
    <?php
}