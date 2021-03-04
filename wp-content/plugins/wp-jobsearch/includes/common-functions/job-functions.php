<?php
if (!function_exists('jobsearch_job_get_profile_image')) {

    function jobsearch_job_get_profile_image($job_id)
    {
        $post_thumbnail_id = '';
        $job_field_user = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        if (isset($job_field_user) && $job_field_user != '' && has_post_thumbnail($job_field_user)) {
            $post_thumbnail_id = get_post_thumbnail_id($job_field_user);
        }
        return apply_filters('jobsearch_job_emp_logo_thumb_id', $post_thumbnail_id, $job_id);
    }
}


if (!function_exists('jobsearch_job_get_company_name')) {

    function jobsearch_job_get_company_name($job_id, $before_title = '', $after_title = '')
    {
        $company_name_str = '';
        $job_field_user = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        if (isset($job_field_user) && $job_field_user != '') {
            $company_name_str = '<a href="' . get_permalink($job_field_user) . '">' . $before_title . get_the_title($job_field_user) . $after_title . '</a>';
        }
        return apply_filters('jobsearch_job_compny_title_str', $company_name_str, $job_id, $before_title, $after_title);
    }

}

if (!function_exists('jobsearch_check_job_approved_active')) {

    function jobsearch_check_job_approved_active($job_id)
    {
        $current_time = strtotime(current_time('Y-m-d H:i:s'));
        $job_status = get_post_meta($job_id, 'jobsearch_field_job_status', true);
        $job_expiry = get_post_meta($job_id, 'jobsearch_field_job_expiry_date', true);
        $job_employer = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

        if ($job_status == 'approved' && $job_expiry > $current_time && $job_employer > 0) {
            return true;
        }
        return false;
    }

}

function jobsearch_get_job_salary_format($job_id = 0, $price = 0, $cur_tag = '', $salry_with_k = false)
{
    global $jobsearch_currencies_list, $jobsearch_plugin_options;
    $job_custom_currency_switch = isset($jobsearch_plugin_options['job_custom_currency']) ? $jobsearch_plugin_options['job_custom_currency'] : '';
    $job_currency = get_post_meta($job_id, 'jobsearch_field_job_salary_currency', true);
    $job_currency = jobsearch_esc_html($job_currency);
    if ($job_currency != 'default' && $job_custom_currency_switch == 'on') {
        $job_currency = isset($jobsearch_currencies_list[$job_currency]['symbol']) ? $jobsearch_currencies_list[$job_currency]['symbol'] : jobsearch_get_currency_symbol();
    } else {
        $job_currency = 'default';
    }
    $cur_pos = get_post_meta($job_id, 'jobsearch_field_job_salary_pos', true);
    $job_salary_sep = get_post_meta($job_id, 'jobsearch_field_job_salary_sep', true);
    $job_salary_deci = get_post_meta($job_id, 'jobsearch_field_job_salary_deci', true);

    $cur_pos = jobsearch_esc_html($cur_pos);
    $job_salary_sep = jobsearch_esc_html($job_salary_sep);
    $job_salary_deci = jobsearch_esc_html($job_salary_deci);

    $job_salary_deci = $job_salary_deci < 10 ? absint($job_salary_deci) : 2;

    if ($job_currency == 'default') {
        if ($salry_with_k) {
            $price = preg_replace("/[^0-9]+/iu", "", $price);
            if ($price >= 1000 && substr($price, -3) == '000') {
                $ret_price = substr($price, 0, -3) . 'K';
            } else if ($price > 1000 && substr($price, -2) == '00') {
                $to_show_price = substr($price, 0, -2);
                $to_show_price = $to_show_price / 10;
                $ret_price = $to_show_price . 'K';
            } else {
                $ret_price = jobsearch_get_price_format($price);
            }
        } else {
            $ret_price = jobsearch_get_price_format($price);
        }
    } else {
        $price = $price > 0 ? trim($price) : 0;
        if ($salry_with_k) {
            $price = preg_replace("/[^0-9]+/iu", "", $price);
        } else {
            $price = preg_replace("/[^0-9.]+/iu", "", $price);
        }
        if ($salry_with_k) {
            if ($price >= 1000 && substr($price, -3) == '000') {
                $formted_slary_str = substr($price, 0, -3) . 'K';
            } else if ($price > 1000 && substr($price, -2) == '00') {
                $to_show_price = substr($price, 0, -2);
                $to_show_price = $to_show_price / 10;
                $formted_slary_str = $to_show_price . 'K';
            } else {
                $formted_slary_str = number_format($price, $job_salary_deci, ".", $job_salary_sep);
            }
        } else {
            $formted_slary_str = number_format($price, $job_salary_deci, ".", $job_salary_sep);
        }
        if ($cur_pos == 'left_space') {
            $ret_price = ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . $job_currency . ' ' . ($cur_tag != '' ? '</' . $cur_tag . '>' : '') . $formted_slary_str;
        } else if ($cur_pos == 'right') {
            $ret_price = $formted_slary_str . ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . $job_currency . ($cur_tag != '' ? '</' . $cur_tag . '>' : '');
        } else if ($cur_pos == 'right_space') {
            $ret_price = $formted_slary_str . ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . ' ' . $job_currency . ($cur_tag != '' ? '</' . $cur_tag . '>' : '');
        } else {
            $ret_price = ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . $job_currency . ($cur_tag != '' ? '</' . $cur_tag . '>' : '') . $formted_slary_str;
        }
    }
    return $ret_price;
}

function jobsearch_job_apply_methods_list()
{
    $aply_methods = array(
        'internal' => esc_html__('Internal', 'wp-jobsearch'),
        'external' => esc_html__('External URL', 'wp-jobsearch'),
        'with_email' => esc_html__('By Email', 'wp-jobsearch'),
    );
    return $aply_methods;
}

if (!function_exists('jobsearch_job_offered_salary')) {

    function jobsearch_job_offered_salary($job_id, $before_str = '', $after_str = '', $cur_tag = '', $pb_tag = '', $salry_with_k = false)
    {
        global $jobsearch_plugin_options, $sitepress;
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $job_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';
        $salary_str = $before_str;
        $_job_salary_type = get_post_meta($job_id, 'jobsearch_field_job_salary_type', true);
        $_job_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);
        $_job_max_salary = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);

        $_job_salary = jobsearch_esc_html($_job_salary);
        $_job_max_salary = jobsearch_esc_html($_job_max_salary);

        $salary_type_val_str = '';
        if (!empty($job_salary_types)) {
            $slar_type_count = 1;
            foreach ($job_salary_types as $job_salary_typ) {
                $job_salary_typ = apply_filters('wpml_translate_single_string', $job_salary_typ, 'JobSearch Options', 'Salary Type - ' . $job_salary_typ, $lang_code);
                if ($_job_salary_type == 'type_' . $slar_type_count) {
                    $salary_type_val_str = $job_salary_typ;
                }
                $slar_type_count++;
            }
        }

        $pb_strt_tag = '';
        $pb_clos_tag = '';
        if ($pb_tag != '') {
            $pb_strt_tag = '<' . $pb_tag . '>';
            $pb_clos_tag = '</' . $pb_tag . '>';
        }
        if ($_job_salary_type === 'negotiable') {
            $salary_str .= esc_html__('Negotiable', 'wp-jobsearch');
        } else {
            if ($_job_salary != '') {
                if ($_job_max_salary != '') {
                    $salary_str .= jobsearch_get_job_salary_format($job_id, $_job_salary, $cur_tag, $salry_with_k) . ' - ' . jobsearch_get_job_salary_format($job_id, $_job_max_salary, $cur_tag, $salry_with_k) . ($salary_type_val_str != '' ? $pb_strt_tag . ' / ' . $salary_type_val_str . $pb_clos_tag : '');
                } else {
                    $salary_str .= jobsearch_get_job_salary_format($job_id, $_job_salary, $cur_tag, $salry_with_k) . ($salary_type_val_str != '' ? $pb_strt_tag . ' / ' . $salary_type_val_str . $pb_clos_tag : '');
                }
            }
        }
        $salary_str .= $after_str;
        return $salary_str;
    }
}

if (!function_exists('jobsearch_job_get_all_jobtypes')) {

    function jobsearch_job_get_all_jobtypes($job_id, $link_class = 'jobsearch-option-btn', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '', $con_tag = 'a', $fill_type = 'bg_fill')
    {
        $job_type = wp_get_post_terms($job_id, 'jobtype');
        ob_start();
        $html = '';
        if (!empty($job_type)) {
            $link_class_str = '';
            if ($link_class != '') {
                $link_class_str = ' class="' . $link_class . '"';
            }
            echo($before_tag);
            foreach ($job_type as $term) :
                if (isset($term->term_id) && function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $get_jobtype_id = $term->term_id;
                    $get_jobtype_id = apply_filters('wpml_object_id', $get_jobtype_id, 'jobtype', true);
                    $term = jobsearch_get_custom_term_by('term_id', $get_jobtype_id, 'jobtype');
                }
                $jobtype_color = get_term_meta($term->term_id, 'jobsearch_field_jobtype_color', true);
                $jobtype_textcolor = get_term_meta($term->term_id, 'jobsearch_field_jobtype_textcolor', true);
                $jobtype_color_str = '';
                if ($jobtype_color != '') {
                    if ($fill_type == 'border_fill') {
                        //$jobtype_color_str = ' style="background-color: #ffffff; border-color: ' . esc_attr($jobtype_color) . '; color: ' . esc_attr($jobtype_color) . ' "';
                        if ($jobtype_color != '') {
                            $jobtype_color_str .= 'background-color: #ffffff; border-color: ' . esc_attr($jobtype_color) . '; color: ' . esc_attr($jobtype_color) . ';';
                        }
                    } else if ($fill_type == 'no_color') {
                        $jobtype_color_str .= '';
                    } else {
                        //$jobtype_color_str = ' style="background-color: ' . esc_attr($jobtype_color) . '; color: ' . esc_attr($jobtype_textcolor) . ' "';
                        if ($jobtype_color != '') {
                            $jobtype_color_str .= 'background-color: ' . esc_attr($jobtype_color) . ';';
                        }
                        if ($jobtype_textcolor != '') {
                            $jobtype_color_str .= ' color: ' . esc_attr($jobtype_textcolor) . ';';
                        }
                    }
                    if ($jobtype_color_str != '') {
                        $jobtype_color_str = ' style="' . $jobtype_color_str . '"';
                    }
                }
                ?>
                <<?php echo($con_tag) ?><?php echo($link_class_str) ?><?php echo($jobtype_color_str); ?>>
                <?php
                echo($before_title);
                echo esc_html($term->name);
                echo($after_title);
                ?>
                </<?php echo($con_tag) ?>>
            <?php
            endforeach;
            echo($after_tag);
        }
        $html .= ob_get_clean();
        return $html;
    }
}

if (!function_exists('jobsearch_job_get_all_sectors')) {

    function jobsearch_job_get_all_sectors($job_id, $link_class = '', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '')
    {

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
                <a href="<?php echo add_query_arg(array('sector_cat' => $term->slug, 'ajax_filter' => 'true'), $result_page); ?>"
                   class="<?php echo force_balance_tags($link_class) ?>">
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

}

if (!function_exists('jobsearch_job_get_all_skills')) {

    function jobsearch_job_get_all_skills($job_id, $seprator = '', $link_class = '', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '', $listype = 'job')
    {

        global $jobsearch_plugin_options;

        $search_list_page = isset($jobsearch_plugin_options['jobsearch_search_list_page']) ? $jobsearch_plugin_options['jobsearch_search_list_page'] : '';
        if ($listype == 'candidate') {
            $search_list_page = isset($jobsearch_plugin_options['jobsearch_cand_result_page']) ? $jobsearch_plugin_options['jobsearch_cand_result_page'] : '';
        }
        $search_page_obj = $search_list_page != '' ? get_page_by_path($search_list_page, 'OBJECT', 'page') : '';

        $skills = wp_get_post_terms($job_id, 'skill');
        ob_start();
        $html = '';
        if (!empty($skills)) {

            echo($before_tag);
            $flag = 0;
            foreach ($skills as $term) :
                if ($flag > 0) {
                    echo $seprator;
                }
                $skill_page_url = '';
                if (isset($search_page_obj->ID)) {
                    $skill_page_url = add_query_arg(array('skill_in' => $term->slug), get_permalink($search_page_obj->ID));
                } ?>
                <a <?php echo($skill_page_url != '' ? 'href="' . $skill_page_url . ' " ' : '') ?>class="<?php echo($link_class) ?>">
                    <?php
                    echo($before_title);
                    echo esc_html($term->name);
                    echo($after_title);
                    ?>
                </a>
                <?php
                $flag++;
            endforeach;
            echo($after_tag);
        }
        $html .= ob_get_clean();
        return $html;
    }

}

if (!function_exists('jobsearch_job_related_post')) {

    function jobsearch_job_related_post($job_id, $title = '', $number_post = 5, $jobsearch_title_limit = 5, $job_like_class = '', $view = 'view1')
    {

        global $jobsearch_plugin_options;

        $job_detail_rel_jobs = isset($jobsearch_plugin_options['job_detail_rel_jobs']) ? $jobsearch_plugin_options['job_detail_rel_jobs'] : '';

        if ($job_detail_rel_jobs == 'on') {

            $jobsearch_title_limit = isset($jobsearch_plugin_options['related_jobs_title_length']) && $jobsearch_plugin_options['related_jobs_title_length'] > 0 ? $jobsearch_plugin_options['related_jobs_title_length'] : '';

            ob_start();
            $filter_arr2 = array();
            $sectors = wp_get_post_terms($job_id, 'sector');
            $filter_multi_spec_arr = array();
            if (!empty($sectors)) {
                foreach ($sectors as $term) :
                    $filter_multi_spec_arr[] = $term->slug;
                endforeach;
            }
            $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
            $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
            $job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';
            $tax_query = array();
            if (!empty($filter_multi_spec_arr)) {
                $tax_query = array(
                    'taxonomy' => 'sector',
                    'field' => 'slug',
                    'terms' => $filter_multi_spec_arr
                );
            }

            $featured_job_mypost = array(
                'posts_per_page' => $number_post,
                'post_type' => 'job',
                'order' => "DESC",
                'orderby' => 'post_date',
                'post_status' => 'publish',
                'fields' => 'ids',
                'post__not_in' => array($job_id),
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_publish_date',
                        'value' => current_time('timestamp'),
                        'compare' => '<=',
                    ),
                    array(
                        'key' => 'jobsearch_field_job_expiry_date',
                        'value' => current_time('timestamp'),
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'jobsearch_field_job_status',
                        'value' => 'approved',
                        'compare' => '=',
                    ),
                )
            );
            if (!empty($tax_query)) {
                $featured_job_mypost['tax_query'] = array($tax_query);
            }

            // Exclude expired jobs from listing
            $featured_job_mypost = apply_filters('jobsearch_jobs_listing_parameters', $featured_job_mypost);
            $featured_job_loop_count = new WP_Query($featured_job_mypost);
            $featuredjob_count_post = $featured_job_loop_count->found_posts;

            $related_jobs_output = '';
            if ($featuredjob_count_post > 0) {

                if ($view == 'view1') {
                    if ($title != '') { ?>
                        <div class="jobsearch-section-title"><h2><?php echo esc_html($title); ?></h2></div>
                        <?php
                    }
                    ob_start();
                    ?>
                    <div class="jobsearch-job jobsearch-joblisting-classic jobsearch-jobdetail-joblisting">
                        <ul class="jobsearch-row">
                            <?php
                            // getting if record not found
                            while ($featured_job_loop_count->have_posts()) : $featured_job_loop_count->the_post();
                                global $post;
                                $job_id = $post;
                                $job_random_id = rand(1111111, 9999999);
                                $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
                                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, apply_filters('jobsearch_reltedjobs_list_thmb_size', 'jobsearch-job-medium'));
                                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                                $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
                                $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                                $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);

                                $company_name = jobsearch_job_get_company_name($job_id, '@ ');
                                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                                $job_city_title = jobsearch_post_city_contry_txtstr($job_id, true, true, true);
                                $job_city_title = apply_filters('jobsearch_job_detail_relatjobs_location_str', $job_city_title, $job_id);
                                $jobsearch_job_min_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);
                                $jobsearch_job_max_salary = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);

                                $_job_salary_type = get_post_meta($job_id, 'jobsearch_field_job_salary_type', true);

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
                                $job_type_str = jobsearch_job_get_all_jobtypes($job_id, 'jobsearch-option-btn');
                                $sector_str = jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>', '</li>');
                                ?>
                                <li class="jobsearch-column-12">
                                    <div class="jobsearch-joblisting-classic-wrap">
                                        <?php
                                        ob_start();
                                        if ($post_thumbnail_src != '') {
                                            ?>
                                            <figure>
                                                <a href="<?php echo esc_url(get_permalink($job_id)); ?>">
                                                    <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                                </a>
                                            </figure>
                                            <?php
                                        }
                                        $list_emp_img = ob_get_clean();
                                        echo apply_filters('jobsearch_jobs_listing_emp_img_html', $list_emp_img, $job_id, 'view1');
                                        ?>
                                        <div class="jobsearch-joblisting-text">
                                            <div class="jobsearch-list-option">
                                                <h2 class="jobsearch-pst-title">
                                                    <a href="<?php echo esc_url(get_permalink($job_id)); ?>"
                                                       title="<?php echo esc_html(get_the_title($job_id)); ?>">
                                                        <?php echo esc_html(wp_trim_words(get_the_title($job_id), $jobsearch_title_limit)); ?>
                                                    </a>
                                                    <?php
                                                    if ($jobsearch_job_featured == 'on') {
                                                        ?>
                                                        <span><?php echo esc_html__('Featured', 'wp-jobsearch'); ?></span>
                                                        <?php
                                                    }
                                                    ?>
                                                </h2>
                                                <ul>
                                                    <?php if ($company_name != '') {
                                                        ob_start();
                                                        ?>
                                                        <li><?php echo($company_name); ?></li>
                                                        <?php
                                                        $comp_name_html = ob_get_clean();
                                                        echo apply_filters('jobsearch_empname_in_jobdetail_related', $comp_name_html, $job_id, 'view1');
                                                    }
                                                    if (!empty($job_city_title) && $all_location_allow == 'on') {
                                                        ?>
                                                        <li>
                                                            <i class="jobsearch-icon jobsearch-maps-and-flags"></i><?php echo esc_html($job_city_title); ?>
                                                        </li>
                                                        <?php
                                                    }
                                                    if (!empty($sector_str) && $sectors_enable_switch == 'on') {
                                                        echo apply_filters('jobsearch_joblisting_sector_str_html', $sector_str, $job_id, '<li><i class="jobsearch-icon jobsearch-calendar"></i>', '</li>');
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <div class="jobsearch-job-userlist">
                                                <?php
                                                if ($job_type_str != '' && $job_types_switch != 'off') {
                                                    echo($job_type_str);
                                                }
                                                $figcaption_div = true;
                                                $book_mark_args = array(
                                                    'job_id' => $job_id,
                                                    'before_icon' => 'fa fa-heart-o',
                                                    'after_icon' => 'fa fa-heart',
                                                    'anchor_class' => $job_like_class
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
                            ?>
                        </ul>
                    </div>
                    <?php
                    $rel_jobs_html = ob_get_clean();
                    echo apply_filters('jobsearch_job_detail_related_jobs_html', $rel_jobs_html, $featured_job_loop_count);
                    $related_jobs_output = ob_get_clean();
                } elseif ($view == 'view2') {
                    ob_start();
                    $rel_jobs_html = '';
                    $related_args = array(
                        'title' => $title,
                        'featured_job_loop_count' => $featured_job_loop_count,
                        'job_types_switch' => $job_types_switch,
                        'all_location_allow' => $all_location_allow,
                    );
                    do_action('careerfy_job_detail_related_view2', $rel_jobs_html, $related_args);
                    $rel_jobs_html = ob_get_clean();
                    echo apply_filters('jobsearch_job_detail_related_jobs_html', $rel_jobs_html, $featured_job_loop_count);
                    $related_jobs_output = ob_get_clean();
                } elseif ($view == 'view5') { ?>
                    <?php
                    if ($title != '') { ?>
                        <div class="careerfy-content-title-style5"><h2><?php echo esc_html($title); ?></h2></div>
                        <div class="careerfy-job careerfy-jobs-style9">
                        <ul class="row">
                        <?php
                    }
                    // getting if record not found
                    ob_start();
                    while ($featured_job_loop_count->have_posts()) : $featured_job_loop_count->the_post();
                        global $post;
                        $job_id = $post;
                        $job_obj = get_post($job_id);
                        $job_content = isset($job_obj->post_content) ? $job_obj->post_content : '';
                        $job_content = apply_filters('the_content', $job_content);

                        $job_random_id = rand(1111111, 9999999);
                        $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
                        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, apply_filters('jobsearch_reltedjobs_list_thmb_size', 'jobsearch-job-medium'));
                        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                        $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
                        $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                        $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);

                        $company_name = jobsearch_job_get_company_name($job_id, '@ ');
                        $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                        $job_city_title = jobsearch_post_city_contry_txtstr($job_id, true, true, true);
                        $job_city_title = apply_filters('jobsearch_job_detail_relatjobs_location_str', $job_city_title, $job_id);
                        $jobsearch_job_min_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);
                        $jobsearch_job_max_salary = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);

                        $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                        $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true); // get job employer
                        $_job_salary_type = get_post_meta($job_id, 'jobsearch_field_job_salary_type', true);

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

                        $employer_cover_image_src_style_str = '';
                        if ($job_employer_id != '') {
                            if (class_exists('JobSearchMultiPostThumbnails')) {
                                $employer_cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url('employer', 'cover-image', $job_employer_id);
                                if ($employer_cover_image_src != '') {
                                    $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($employer_cover_image_src) . ') no-repeat center/cover; "';
                                }
                            }
                        }
                        $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                        $job_type_str = jobsearch_job_get_all_jobtypes($job_id, 'jobsearch-option-btn');
                        $sector_str = function_exists('jobsearch_job_get_all_sectors') ? jobsearch_job_get_all_sectors($job_id, '', '', '', '', '', 'small') : '';
                        $columns_class = 'col-md-12';
                        ?>
                        <li class="<?php echo($columns_class) ?>">
                            <div class="careerfy-jobs-wrapper-style9">
                                <div class="careerfy-jobs-box1">
                                    <?php if ($jobsearch_job_featured == 'on') { ?>
                                        <span class="careerfy-jobs-style9-featured jobsearch-tooltipcon"
                                              title="Featured"><i
                                                    class="fa fa-star"></i></span>
                                    <?php } ?>
                                    <a class="careerfy-jobs-style9-title"
                                       href="<?php echo esc_url(get_permalink($job_id)); ?>"><?php echo esc_html(wp_trim_words(get_the_title($job_id), 6)); ?></a>
                                    <?php
                                    if ($job_type_str != '' && $job_types_switch != 'off') {
                                        echo($job_type_str);
                                    }
                                    ?>
                                    <?php if (!empty($get_job_location)) { ?>
                                        <span class="careerfy-jobs-style9-loc"><i
                                                    class="fa fa-map-marker"></i> <?php echo($get_job_location) ?></span>
                                    <?php } ?>

                                    <small class="careerfy-jobs-style9-options">
                                        <i class="careerfy-icon careerfy-calendar"></i>
                                        <?php printf(esc_html__('Published %s', 'careerfy'), jobsearch_time_elapsed_string($job_post_date)); ?>
                                    </small>

                                    <small class="careerfy-jobs-style9-options">
                                        <?php if ($jobsearch_job_min_salary != '' && $jobsearch_job_max_salary != '') { ?>
                                            <i class="careerfy-icon careerfy-money"></i><?php echo esc_html__('Salary ', 'wp-jobsearch') . jobsearch_get_currency_symbol() ?><?php echo $jobsearch_job_min_salary . "K" ?>-<?php echo jobsearch_get_currency_symbol() . $jobsearch_job_max_salary . "K " . $salary_type ?>
                                        <?php } ?>
                                    </small>

                                    <?php
                                    if (!empty($job_content)) { ?>
                                        <p><?php echo jobsearch_esc_html(limit_text($job_content, 30)); ?></p>
                                    <?php } else {

                                        if (jobsearch_excerpt(0, $job_id) != '') { ?>
                                            <p><?php echo jobsearch_esc_html(jobsearch_excerpt(35, $job_id)) ?></p>
                                        <?php }
                                    }
                                    ?>

                                </div>
                                <div class="careerfy-jobs-box2">
                                    <?php if (function_exists('jobsearch_empjobs_urgent_pkg_iconlab')) {
                                        jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'style9');
                                    } ?>
                                    <figure>
                                        <?php if ($post_thumbnail_src != '') { ?>
                                            <a <?php echo($employer_cover_image_src_style_str) ?>
                                                    href="<?php the_permalink($job_id); ?>">
                                                <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                            </a>
                                        <?php } ?>
                                    </figure>

                                    <?php if (!empty($company_name)) { ?>
                                        <small><?php echo($company_name) ?></small>
                                    <?php }

                                    if (!empty($sector_str) && $sectors_enable_switch == 'on') { ?>
                                        <small class="careerfy-jobs-style9-company"><?php echo esc_html__('Posted in:', 'careerfy') ?><?php echo($sector_str); ?></small>
                                    <?php }
                                    $book_mark_args = array(
                                        'job_id' => $job_id,
                                        'before_icon' => 'fa fa-heart-o',
                                        'after_icon' => 'fa fa-heart',
                                        'after_label' => esc_html__('Saved', 'careerfy'),
                                        'before_label' => esc_html__('Save job', 'careerfy'),
                                        'container_class' => '',
                                        'anchor_class' => 'careerfy-jobs-like-style9',
                                        'view' => 'style9',
                                    );

                                    do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args); ?>
                                </div>
                            </div>
                        </li>

                    <?php
                    endwhile;
                    wp_reset_postdata(); ?>
                    </ul>
                    </div>
                    <?php $related_jobs_output = ob_get_clean();
                    ?>

                <?php } elseif ($view == 'view3') {
                    ob_start();
                    $rel_jobs_html = '';
                    $related_args = array(
                        'title' => $title,
                        'featured_job_loop_count' => $featured_job_loop_count,
                        'job_types_switch' => $job_types_switch,
                        'all_location_allow' => $all_location_allow,
                    );
                    do_action('careerfy_job_detail_related_view3', $rel_jobs_html, $related_args);
                    $rel_jobs_html = ob_get_clean();
                    echo apply_filters('jobsearch_job_detail_related_jobs_html', $rel_jobs_html, $featured_job_loop_count);
                    $related_jobs_output = ob_get_clean();
                } elseif ($view == 'view4') {

                    ob_start();
                    $rel_jobs_html = '';
                    $related_args = array(
                        'title' => $title,
                        'featured_job_loop_count' => $featured_job_loop_count,
                        'job_types_switch' => $job_types_switch,
                        'all_location_allow' => $all_location_allow,
                    );
                    do_action('careerfy_job_detail_related_view4', $rel_jobs_html, $related_args);
                    $rel_jobs_html = ob_get_clean();
                    echo apply_filters('jobsearch_job_detail_related_jobs_html', $rel_jobs_html, $featured_job_loop_count);
                    $related_jobs_output = ob_get_clean();
                }
            }

            return apply_filters('related_jobs', $related_jobs_output, $job_id);
        }
    }

}

if (!function_exists('jobsearch_job_related_company_post')) {

    function jobsearch_job_related_company_post($job_id, $title = '', $number_post = 5, $jobsearch_title_limit = 5, $view = 'view1')
    {
        ob_start();
        global $jobsearch_plugin_options;

        $job_detail_emprel_jobs = isset($jobsearch_plugin_options['job_detail_emp_jobs']) ? $jobsearch_plugin_options['job_detail_emp_jobs'] : '';
        if ($job_detail_emprel_jobs == 'on') {
            $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

            $jobsearch_title_limit = isset($jobsearch_plugin_options['related_jobs_title_length']) && $jobsearch_plugin_options['related_jobs_title_length'] > 0 ? $jobsearch_plugin_options['related_jobs_title_length'] : '';

            $filter_arr2 = array();
            $company_filter_arr = '';
            $job_posted_by = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            if (isset($job_posted_by) && $job_posted_by != '') {
                $company_filter_arr = array();
                $company_filter_arr = array(
                    'key' => 'jobsearch_field_job_posted_by',
                    'value' => $job_posted_by,
                    'compare' => '=',
                );
            }
            $featured_job_mypost = array(
                'posts_per_page' => $number_post,
                'post_type' => 'job',
                'order' => "DESC",
                'orderby' => 'post_date',
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'fields' => 'ids',
                'post__not_in' => array($job_id),
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_publish_date',
                        'value' => current_time('timestamp'),
                        'compare' => '<=',
                    ),
                    array(
                        'key' => 'jobsearch_field_job_expiry_date',
                        'value' => current_time('timestamp'),
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'jobsearch_field_job_status',
                        'value' => 'approved',
                        'compare' => '=',
                    ),
                    $company_filter_arr
                )
            );

            // Exclude expired jobs from listing
            $featured_job_mypost = apply_filters('jobsearch_jobs_listing_parameters', $featured_job_mypost);
            $featured_job_loop_count = new WP_Query($featured_job_mypost);
            $featuredjob_count_post = $featured_job_loop_count->found_posts;

            if ($featuredjob_count_post > 0) {

                if ($view == 'view2') {
                    $similar_args = array(
                        'title' => $title,
                        'jobsearch_title_limit' => $jobsearch_title_limit,
                        'featured_job_loop_count' => $featured_job_loop_count,
                        'all_location_allowjobsearch-fltcount-title' => $all_location_allow,
                    );

                    do_action('careerfy_similar_jobs', $similar_args);
                } else { ?>
                    <div class="jobsearch_side_box jobsearch_box_view_jobs">
                        <?php
                        if ($title != '') { ?>
                            <div class="jobsearch-wdg-box-title"><h2><?php echo esc_html($title); ?></h2></div>
                        <?php } ?>
                        <ul>
                            <?php
                            // getting if record not found
                            while ($featured_job_loop_count->have_posts()) : $featured_job_loop_count->the_post();
                                global $post;
                                $job_id = $post;
                                $get_job_location = jobsearch_post_city_contry_txtstr($job_id, true, true, true);
                                $sector_str = jobsearch_job_get_all_sectors($job_id, '', '', '', '<span>', '</span>');
                                ?>
                                <li>
                                    <h2 class="jobsearch-pst-title">
                                        <a href="<?php echo esc_url(get_permalink($job_id)); ?>"
                                           title="<?php echo esc_html(get_the_title($job_id)); ?>">
                                            <?php echo esc_html(wp_trim_words(get_the_title($job_id), $jobsearch_title_limit)); ?>
                                        </a>
                                    </h2>
                                    <?php
                                    if (!empty($sector_str)) {
                                        echo force_balance_tags($sector_str);
                                    }
                                    if (!empty($get_job_location) && $all_location_allow == 'on') {
                                        ?>
                                        <small><?php echo esc_html($get_job_location); ?></small>
                                        <?php
                                    }
                                    ?>
                                </li>
                            <?php
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </ul>
                        <a href="<?php echo esc_url(get_permalink($job_posted_by)); ?>"
                           title="<?php echo esc_html(get_the_title($job_id)); ?>"
                           class="jobsearch_box_view_jobs_btn"><?php echo esc_html__('View all jobs', 'wp-jobsearch') ?>
                            <i
                                    class="jobsearch-icon jobsearch-arrows32"></i></a>
                    </div>
                    <?php
                }
            }

        }
        $related_jobs_output = ob_get_clean();
        return apply_filters('jobsearch_sidebar_related_jobs', $related_jobs_output, $job_id);
    }

}

add_action('sector_add_form_fields', 'jobsearch_sector_term_extfields');
add_action('sector_edit_form_fields', 'jobsearch_sector_term_extfields');

function jobsearch_sector_term_extfields($term)
{

    global $jobsearch_form_fields, $wpdb;
    $rand_id = rand(10000000, 99999999);

    wp_enqueue_media();
    wp_enqueue_script('jobsearch-selectize');

    //
    $skills_arr = array();
    $skill_terms = $wpdb->get_col($wpdb->prepare("SELECT terms.term_id FROM $wpdb->terms AS terms"
        . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
        . " WHERE term_tax.taxonomy=%s"
        . " ORDER BY terms.term_id DESC", 'skill'));
    if (!empty($skill_terms) && !is_wp_error($skill_terms)) {
        foreach ($skill_terms as $skillm_id) {
            $term_skill = get_term_by('id', $skillm_id, 'skill');
            $skills_arr[$term_skill->term_id] = $term_skill->name;
        }
    }

    $term_id = '';
    if (isset($term->term_id)) {
        $term_id = $term->term_id;
    }

    if ($term_id == '') {
        echo apply_filters('jobsearch_sector_term_add_cusmeta_fields_before', '');
        ?>
        <div class="form-field">
            <label><?php esc_html_e('Suggested Skills', 'wp-jobsearch') ?></label>
            <?php
            $field_params = array(
                'cus_name' => 'cat_skills[]',
                'options' => $skills_arr,
                'classes' => 'selectize-select-skills',
                'ext_attr' => 'placeholder="' . esc_html__('Choose Suggested Skills', 'wp-jobsearch') . '"',
                'force_std' => ''
            );
            $jobsearch_form_fields->multi_select_field($field_params);
            ?>
        </div>
        <?php
        //
        echo apply_filters('jobsearch_sector_term_add_cusmeta_fields_after', '');
    } else {
        $term_fields = get_term_meta($term_id, 'careerfy_frame_cat_fields', true);

        $term_skills = isset($term_fields['skills']) ? $term_fields['skills'] : '';

        //
        echo apply_filters('jobsearch_sector_term_edit_cusmeta_fields_before', '', $term_id);
        ?>
        <tr class="form-field">
            <th><label><?php esc_html_e('Suggested Skills', 'wp-jobsearch') ?></label></th>
            <td>
                <?php
                $field_params = array(
                    'cus_name' => 'cat_skills[]',
                    'options' => $skills_arr,
                    'classes' => 'selectize-select-skills',
                    'ext_attr' => 'placeholder="' . esc_html__('Choose Suggested Skills', 'wp-jobsearch') . '"',
                    'force_std' => $term_skills
                );
                $jobsearch_form_fields->multi_select_field($field_params);
                ?>
            </td>
        </tr>
        <?php
        echo apply_filters('jobsearch_sector_term_edit_cusmeta_fields_after', '', $term_id);
    }
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery('.selectize-select-skills').selectize({
                //allowEmptyOption: true,
                plugins: ['remove_button'],
            });
        });
    </script>
    <input type="hidden" name="job_sector_custom_fields" value="1">
    <?php
}

add_action('create_sector', 'jobsearch_job_sector_fields_saving');
add_action('edited_sector', 'jobsearch_job_sector_fields_saving');

function jobsearch_job_sector_fields_saving($term_id)
{
    if (isset($_POST['job_sector_custom_fields']) && $_POST['job_sector_custom_fields'] == '1') {
        $term_skills = isset($_POST['cat_skills']) ? $_POST['cat_skills'] : '';

        //
        $term_fields = array(
            'skills' => $term_skills,
        );
        $term_fields = apply_filters('jobsearch_sector_term_save_cusmeta_fields', $term_fields, $term_id);
        update_term_meta($term_id, 'careerfy_frame_cat_fields', $term_fields);
    }
}

if (!function_exists('jobsearch_job_views_count')) {

    function jobsearch_job_views_count($postID)
    {
        $jobsearch_job_views_count = get_post_meta($postID, "jobsearch_job_views_count", true);
        if ($jobsearch_job_views_count == '') {
            $jobsearch_job_views_count = 0;
        }
        if (!isset($_COOKIE["jobsearch_job_views_count" . $postID])) {
            setcookie("jobsearch_job_views_count" . $postID, time() + 86400);
            $jobsearch_job_views_count = $jobsearch_job_views_count + 1;
            update_post_meta($postID, 'jobsearch_job_views_count', $jobsearch_job_views_count);
        }
    }

}
if (!function_exists('jobsearch_employer_views_count')) {

    function jobsearch_employer_views_count($postID)
    {
        $jobsearch_employer_views_count = get_post_meta($postID, "jobsearch_employer_views_count", true);
        if ($jobsearch_employer_views_count == '') {
            $jobsearch_employer_views_count = 0;
        }
        if (!isset($_COOKIE["jobsearch_employer_views_count" . $postID])) {
            setcookie("jobsearch_employer_views_count" . $postID, time() + 86400);
            $jobsearch_employer_views_count = $jobsearch_employer_views_count + 1;
            update_post_meta($postID, 'jobsearch_employer_views_count', $jobsearch_employer_views_count);
        }
    }

}

add_filter('jobsearch_employer_totljobs_query_args', 'jobsearch_employer_totljobs_query_args');

function jobsearch_employer_totljobs_query_args($args)
{
    $args['meta_query'][] = array(
        'key' => 'jobsearch_field_job_publish_date',
        'value' => current_time('timestamp'),
        'compare' => '<=',
    );
    $args['meta_query'][] = array(
        'key' => 'jobsearch_field_job_expiry_date',
        'value' => current_time('timestamp'),
        'compare' => '>=',
    );

    return $args;
}

if (!function_exists('jobsearch_employer_total_jobs_posted')) {

    function jobsearch_employer_total_jobs_posted($employer_id)
    {
        $args = array(
            'post_type' => 'job',
            'posts_per_page' => '1',
            'post_status' => 'publish',
            'fields' => 'ids',
            'order' => 'DESC',
            'orderby' => 'ID',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_job_posted_by',
                    'value' => $employer_id,
                    'compare' => '=',
                ),
                array(
                    'key' => 'jobsearch_field_job_status',
                    'value' => 'approved',
                    'compare' => '=',
                ),
            ),
        );

        $args = apply_filters('jobsearch_employer_totljobs_query_args', $args);

        $jobs_query = new WP_Query($args);
        $total_jobs = $jobs_query->found_posts;
        return absint($total_jobs);
    }

}

if (!function_exists('jobsearch_job_send_message_employer_callback')) {

    function jobsearch_job_send_message_employer_callback()
    {
        global $jobsearch_plugin_options;

        $send_message_job_id = $_REQUEST['send_message_job_id'];
        $send_message_content = $_REQUEST['send_message_content'];
        $send_message_subject = $_REQUEST['send_message_subject'];
        $send_message_name = $_REQUEST['send_message_uname'];

        if ($send_message_name == '') {
            $msg = esc_html__('Please put your name.', 'wp-jobsearch');
            echo json_encode(array('html' => $msg, 'error' => '1'));
            wp_die();
        }
        if ($send_message_content == '') {
            $msg = esc_html__('Please put a message for the employer.', 'wp-jobsearch');
            echo json_encode(array('html' => $msg, 'error' => '1'));
            wp_die();
        }
        if (!is_user_logged_in()) {
            $send_message_email = $_REQUEST['send_message_uemail'];
            if ($send_message_email != '' && filter_var($send_message_email, FILTER_VALIDATE_EMAIL)) {
                $user_data = $send_message_email;
            } else {
                $msg = esc_html__('Please enter a valid email address.', 'wp-jobsearch');
                echo json_encode(array('html' => $msg, 'error' => '1'));
                wp_die();
            }
        } else {
            $user_data = wp_get_current_user();
            // send to employer email 
            $cur_user_id = isset($user_data->ID) ? $user_data->ID : '';
            $user_candidate_id = jobsearch_get_user_candidate_id($cur_user_id);
            if ($user_candidate_id > 0) {
                $candidate_status = get_post_meta($user_candidate_id, 'jobsearch_field_candidate_approved', true);
                if ($candidate_status != 'on') {
                    $msg = esc_html__('Your profile is not approved yet.', 'wp-jobsearch');
                    echo json_encode(array('html' => $msg, 'error' => '1'));
                    wp_die();
                }
            }
        }
        do_action('jobsearch_candidate_message_employer', $user_data, $send_message_job_id, $send_message_name, $send_message_subject, $send_message_content);
        echo json_encode(array('html' => esc_html__('Your Message has been sent, we will contact you shortly', 'wp-jobsearch'), 'error' => '0'));
        wp_die();
    }

    add_action('wp_ajax_jobsearch_job_send_message_employer', 'jobsearch_job_send_message_employer_callback');
    add_action('wp_ajax_nopriv_jobsearch_job_send_message_employer', 'jobsearch_job_send_message_employer_callback');
}

if (!function_exists('jobsearch_job_send_message_html_callback')) {
    function jobsearch_job_send_message_html_callback($arg = array())
    {
        global $jobsearch_plugin_options;
        $captcha_switch = isset($jobsearch_plugin_options['captcha_switch']) ? $jobsearch_plugin_options['captcha_switch'] : '';
        $job_det_contact_form = isset($jobsearch_plugin_options['job_det_contact_form']) ? $jobsearch_plugin_options['job_det_contact_form'] : '';
        $jobsearch_sitekey = isset($jobsearch_plugin_options['captcha_sitekey']) ? $jobsearch_plugin_options['captcha_sitekey'] : '';
        $send_message_form_rand = rand(1000, 99999);

        extract(shortcode_atts(array(
            'user_displayname' => '',
            'job_id' => '',
        ), $arg));

        if ($job_det_contact_form == 'off')
            return;

        if ($job_det_contact_form == 'cand_login') {
            $user_id = get_current_user_id();
            if (empty(jobsearch_user_is_candidate($user_id))) {
                echo "<p>" . esc_html__("Required 'Candidate' login for send message", 'wp-jobsearch') . "</p>";
                return;
            }
        }

        ob_start();
        ?>
        <div class="jobsearch_side_box jobsearch_box_contact_form">
            <form method="post"
                  id="jobsearch_send_message_form<?php echo esc_html($send_message_form_rand); ?>">
                <div class="careerfy-widget-title">
                    <h2><?php echo esc_html__('Contact Form', 'careerfy'); ?></h2></div>
                <ul>
                    <li<?php echo(is_user_logged_in() ? ' style="display: none;"' : '') ?>>
                        <div class="input-field">
                            <input placeholder="<?php echo esc_html__('Name', 'wp-jobsearch'); ?>"
                                   type="text" name="send_message_uname"
                                   value="<?php echo($user_displayname) ?>"/>
                            <i class="careerfy-icon careerfy-user"></i>
                        </div>
                    </li>
                    <?php
                    if (!is_user_logged_in()) { ?>
                        <li>
                            <div class="input-field">
                                <input type="text"
                                       placeholder="<?php echo esc_html__('Email', 'wp-jobsearch'); ?>"
                                       name="send_message_uemail"/>
                            </div>
                        </li>
                    <?php } ?>
                    <li>
                        <input placeholder="<?php echo esc_html__('Subject', 'careerfy'); ?>"
                               type="text" name="send_message_subject" value="">
                    </li>
                    <li>
                                                <textarea placeholder="<?php echo esc_html__('Message', 'careerfy'); ?>"
                                                          name="send_message_content"></textarea>
                    </li>
                    <li>
                        <div class="input-field-submit">
                            <input type="submit" class="send-message-submit-btn"
                                   data-action="jobsearch_job_send_message_employer"
                                   data-randid="<?php echo esc_html($send_message_form_rand); ?>"
                                   name="send_message_content"
                                   value="<?php echo esc_html__('Send now', 'careerfy') ?>"/>
                            <?php jobsearch_terms_and_con_link_txt(); ?>
                        </div>
                        <div class="message-box message-box-<?php echo esc_html($send_message_form_rand); ?>"></div>
                        <input type="hidden" name="send_message_job_id"
                               value="<?php echo absint($job_id); ?>"/>
                    </li>
                    <?php
                    if ($captcha_switch == 'on') {
                        wp_enqueue_script('jobsearch_google_recaptcha');
                        ?>
                        <li>
                            <script>
                                var recaptcha_cand_contact;
                                var jobsearch_multicap = function () {
                                    //Render the recaptcha_cand_contact on the element with ID "recaptcha1"
                                    recaptcha_cand_contact = grecaptcha.render('recaptcha_cand_contact', {
                                        'sitekey': '<?php echo($jobsearch_sitekey); ?>', //Replace this with your Site key
                                        'theme': 'light'
                                    });
                                };
                                jQuery(document).ready(function () {
                                    jQuery('.recaptcha-reload-a').click();
                                });
                            </script>
                            <div class="recaptcha-reload" id="recaptcha_cand_contact_div">
                                <?php echo jobsearch_recaptcha('recaptcha_cand_contact'); ?>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </form>
        </div>
        <?php

        $html = ob_get_clean();
        echo $html;
    }

    add_action('jobsearch_job_send_message_html', 'jobsearch_job_send_message_html_callback', 10, 1);
}

if (!function_exists('jobsearch_job_send_message_html_filter_callback')) {
    add_filter('jobsearch_job_send_message_html_filter', 'jobsearch_job_send_message_html_filter_callback', 10, 2);

    function jobsearch_job_send_message_html_filter_callback($html, $arg = array())
    {
        global $jobsearch_plugin_options;
        extract(shortcode_atts(array(
            'job_employer_id' => '',
            'job_id' => '',
            'btn_class' => '',
            'view' => '',
            'btn_text' => '',
        ), $arg));

        $job_det_contact_form = isset($jobsearch_plugin_options['job_det_contact_form']) ? $jobsearch_plugin_options['job_det_contact_form'] : '';

        $send_message_btn_class = 'jobsearch-open-signin-tab';
        if (is_user_logged_in()) {
            if (jobsearch_user_is_candidate()) {
                $send_message_btn_class = 'jobsearch-sendmessage-popup-btn';
            } else {
                $send_message_btn_class = 'jobsearch-sendmessage-messsage-popup-btn';
            }
        }

        $btn_class_new = 'jobsearch-sendmessage-btn';
        if (isset($btn_class) && !empty($btn_class)) {
            $btn_class_new = $btn_class;
        }
        $btn_text_new = esc_html__('Contact Employer', 'wp-jobsearch');
        if (isset($btn_text) && !empty($btn_text)) {
            $btn_text_new = $btn_text;
        }


        $view_from_pop = false;
        if (is_user_logged_in() && jobsearch_user_is_candidate()) {
            $view_from_pop = true;
        }
        if ($job_det_contact_form == 'on') {
            $view_from_pop = true;
            $send_message_btn_class = 'jobsearch-sendmessage-popup-btn';
        }

        ob_start();
        if ($job_det_contact_form != 'off') {
            if ($view == 'job-detail-style5') { ?>
                <a href="javascript:void(0);"
                   class="<?php echo esc_html($btn_class_new); ?> <?php echo esc_html($send_message_btn_class); ?>"><i
                            class="careerfy-icon careerfy-user-1"></i> <?php echo($btn_text_new) ?></a>
            <?php } else { ?>

                <a href="javascript:void(0);"
                   class="<?php echo esc_html($btn_class_new); ?> <?php echo esc_html($send_message_btn_class); ?>"><i
                            class="jobsearch-icon jobsearch-envelope"></i> <?php echo($btn_text_new) ?></a>
                <?php
            }
            if ($view_from_pop) {
                $popup_args = array(
                    'job_employer_id' => $job_employer_id,
                    'job_id' => $job_id,
                );
                add_action('wp_footer', function () use ($popup_args) {

                    global $jobsearch_plugin_options;
                    extract(shortcode_atts(array(
                        'job_employer_id' => '',
                        'job_id' => ''
                    ), $popup_args));
                    $send_message_form_rand = rand(1000, 99999);

                    $current_user = wp_get_current_user();
                    $user_id = get_current_user_id();
                    $user_displayname = isset($current_user->display_name) ? $current_user->display_name : '';
                    $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $current_user);
                    ?>
                    <div class="jobsearch-modal fade" id="JobSearchModalSendMessage">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                <div class="jobsearch-send-message-form">
                                    <form method="post"
                                          id="jobsearch_send_message_form<?php echo esc_html($send_message_form_rand); ?>">
                                        <div class="jobsearch-user-form">
                                            <ul>
                                                <li <?php echo(is_user_logged_in() ? ' style="display: none;"' : '') ?>>
                                                    <label>
                                                        <?php echo esc_html__('Name', 'wp-jobsearch'); ?>:
                                                    </label>
                                                    <div class="input-field">
                                                        <input type="text" name="send_message_uname"
                                                               value="<?php echo($user_displayname) ?>"/>
                                                    </div>
                                                </li>
                                                <?php
                                                if (!is_user_logged_in()) { ?>
                                                    <li>
                                                        <label>
                                                            <?php echo esc_html__('Email', 'wp-jobsearch'); ?>:
                                                        </label>
                                                        <div class="input-field">
                                                            <input type="text" name="send_message_uemail"/>
                                                        </div>
                                                    </li>
                                                <?php } ?>
                                                <li>
                                                    <label>
                                                        <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>:
                                                    </label>
                                                    <div class="input-field">
                                                        <input type="text" name="send_message_subject" value=""/>
                                                    </div>
                                                </li>
                                                <li>
                                                    <label>
                                                        <?php echo esc_html__('Message', 'wp-jobsearch'); ?>:
                                                    </label>
                                                    <div class="input-field">
                                                        <textarea name="send_message_content"></textarea>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="input-field-submit">
                                                        <input type="submit" class="send-message-submit-btn"
                                                               data-action="jobsearch_job_send_message_employer"
                                                               data-randid="<?php echo esc_html($send_message_form_rand); ?>"
                                                               name="send_message_content"
                                                               value="<?php echo esc_html__('Send', 'wp-jobsearch'); ?>"/>
                                                        <?php jobsearch_terms_and_con_link_txt(); ?>
                                                    </div>
                                                    <div class="message-boxx message-box-<?php echo esc_html($send_message_form_rand); ?>"
                                                         style="float: left; width: 100%; padding: 6px;"></div>
                                                    <input type="hidden" name="send_message_job_id"
                                                           value="<?php echo absint($job_id); ?>"/>
                                                </li>
                                            </ul>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php
                }, 11, 1);
            } else {
                add_action('wp_footer', function () {
                    $rand_numb = rand(1000000, 9999999);
                    ?>
                    <div class="jobsearch-modal fade" id="JobSearchModalSendMessageWarning">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                <div class="jobsearch-send-message-form">
                                    <div class="send-message-warning">
                                        <span><?php echo esc_html__("Required 'Candidate' login for send message", 'wp-jobsearch'); ?> </span>
                                        <span><?php echo esc_html__("Click here to", 'wp-jobsearch'); ?> <a
                                                    href="<?php echo wp_logout_url(get_permalink()); ?>"><?php echo esc_html__("logout", 'wp-jobsearch'); ?></a> </span>
                                        <span><?php echo esc_html__("And try again", 'wp-jobsearch'); ?> </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                });
            }
        }
        $html .= ob_get_clean();
        return $html;
    }

}

add_action('wp_footer', 'jobsearch_job_apply_btn_candidate_role_warning');

function jobsearch_job_apply_btn_candidate_role_warning()
{ ?>
    <div class="jobsearch-modal fade" id="JobSearchModalApplyJobWarning">
        <div class="modal-inner-area">&nbsp</div>
        <div class="modal-content-area">
            <div class="modal-box-area">
                <span class="modal-close"><i class="fa fa-times"></i></span>
                <div class="jobsearch-send-message-form">
                    <div class="send-message-warning">
                        <span><?php echo esc_html__("Required 'Candidate' login to applying this job.", 'wp-jobsearch'); ?> </span>
                        <span><?php echo esc_html__("Click here to", 'wp-jobsearch'); ?> <a
                                    href="<?php echo wp_logout_url(get_permalink()); ?>"><?php echo esc_html__("logout", 'wp-jobsearch'); ?></a> </span>
                        <span><?php echo esc_html__("And try again", 'wp-jobsearch'); ?> </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function jobsearch_job_instamatch_cand_ids($job_id)
{
    global $wpdb, $jobsearch_plugin_options;

    $match_sector_field = 'off';
    $match_skill_field = 'off';
    $match_jobtitle_field = 'off';
    $match_loc_field = 'off';

    $match_cands_by = isset($jobsearch_plugin_options['job_instamatch_by_seprates']) ? $jobsearch_plugin_options['job_instamatch_by_seprates'] : '';
    if (!empty($match_cands_by)) {
        $match_sector_field = in_array('sector', $match_cands_by) ? 'on' : 'off';
        $match_skill_field = in_array('skills', $match_cands_by) ? 'on' : 'off';
        $match_jobtitle_field = in_array('jobtitle', $match_cands_by) ? 'on' : 'off';
        $match_loc_field = in_array('location', $match_cands_by) ? 'on' : 'off';
    }

    $max_results = isset($jobsearch_plugin_options['job_instamatch_max_cands']) ? $jobsearch_plugin_options['job_instamatch_max_cands'] : '';
    $max_results = $max_results > 0 ? $max_results : 100;

    $ret_ids = array();

    $cands_query = "SELECT ID FROM $wpdb->posts AS posts";
    $cands_query .= " LEFT JOIN $wpdb->postmeta AS postmeta ON (posts.ID = postmeta.post_id)";
    $cands_query .= " WHERE posts.post_type='candidate' AND posts.post_status='publish'";
    $cands_query .= " AND postmeta.meta_key='jobsearch_field_candidate_approved' AND postmeta.meta_value='on'";
    $cands_query .= " group by posts.ID";
    $cand_ids = $wpdb->get_col($cands_query);
    //var_dump($cand_ids);
    if (!empty($cand_ids)) {
        //
        $job_job_title = get_the_title($job_id);

        //
        $job_all_sectors = wp_get_post_terms($job_id, 'sector');
        $job_sectrs_arr = array();
        if (!empty($job_all_sectors)) {
            foreach ($job_all_sectors as $job_alsec) {
                if (isset($job_alsec->term_id)) {
                    $job_sectrs_arr[] = $job_alsec->term_id;
                }
            }
        }
        //
        $job_all_skills = wp_get_post_terms($job_id, 'skill');
        $job_skils_arr = array();
        if (!empty($job_all_skills)) {
            foreach ($job_all_skills as $job_alskill) {
                if (isset($job_alskill->name)) {
                    $job_skils_arr[] = $job_alskill->name;
                }
            }
        }
        //
        $job_loc1 = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
        $job_loc2 = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
        $job_loc3 = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
        $job_full_adres = get_post_meta($job_id, 'jobsearch_field_location_address', true);

        foreach ($cand_ids as $cand_id) {
            //
            $validation_arr = array();
            //
            $cand_sectors_arr = array();
            $cand_sectors = wp_get_post_terms($cand_id, 'sector');
            if ($match_sector_field != 'off') {
                $validation_arr['sectors'] = '0';
                if (!empty($cand_sectors)) {
                    foreach ($cand_sectors as $cand_sector) {
                        $cand_sectors_arr[] = $cand_sector->term_id;
                    }
                    if (!empty($job_sectrs_arr) && !empty($cand_sectors_arr)) {
                        $mached_sectors = false;
                        $mached_secs_arr = array_intersect($job_sectrs_arr, $cand_sectors_arr);
                        if (!empty($mached_secs_arr)) {
                            $mached_sectors = true;
                        }
                        if ($mached_sectors) {
                            $validation_arr['sectors'] = '1';
                        }
                    }
                }
            }
            //
            $cand_skills_arr = array();
            $cand_skills = wp_get_post_terms($cand_id, 'skill');
            if ($match_skill_field != 'off') {
                $validation_arr['skills'] = '0';
                if (!empty($cand_skills)) {
                    foreach ($cand_skills as $cand_skill) {
                        $cand_skills_arr[] = $cand_skill->name;
                    }
                    if (!empty($job_skils_arr) && !empty($cand_skills_arr)) {
                        $mached_skills = false;

                        foreach ($cand_skills_arr as $cand_skill_name) {
                            foreach ($job_skils_arr as $job_skill_name) {
                                if ($cand_skill_name != '' && $job_skill_name != '' && $cand_skill_name != $job_skill_name && @preg_match("/{$cand_skill_name}/i", $job_skill_name)) {
                                    $mached_skills = true;
                                }
                            }
                        }

                        $mached_skls_arr = array_intersect($job_skils_arr, $cand_skills_arr);
                        if (!empty($mached_skls_arr)) {
                            $mached_skills = true;
                        }

                        if ($mached_skills) {
                            $validation_arr['skills'] = '1';
                        }
                    }
                }
            }
            //
            if ($match_loc_field == 'on') {
                $validation_arr['location'] = '0';
                $cand_loc1 = get_post_meta($cand_id, 'jobsearch_field_location_location1', true);
                $cand_loc2 = get_post_meta($cand_id, 'jobsearch_field_location_location2', true);
                $cand_loc3 = get_post_meta($cand_id, 'jobsearch_field_location_location3', true);
                $cand_full_adres = get_post_meta($cand_id, 'jobsearch_field_location_address', true);

                $loc_mached = false;
                if ($job_loc1 != '' && $cand_loc1 != '' && $job_loc1 == $cand_loc1) {
                    $loc_mached = true;
                }
                if ($job_loc2 != '' && $cand_loc2 != '' && $job_loc2 == $cand_loc2) {
                    $loc_mached = true;
                }
                if ($job_loc3 != '' && $cand_loc3 != '' && $job_loc3 == $cand_loc3) {
                    $loc_mached = true;
                }
                if ($job_full_adres != '' && $cand_full_adres != '' && @preg_match("/{$job_full_adres}/i", $cand_full_adres)) {
                    $loc_mached = true;
                }
                if ($loc_mached === true) {
                    $validation_arr['location'] = '1';
                }
            }
            //
            if ($match_jobtitle_field != 'off') {
                $cand_job_title = get_post_meta($cand_id, 'jobsearch_field_candidate_jobtitle', true);
                $validation_arr['job_title'] = '0';
                if ($job_job_title != '' && $cand_job_title != '' && @preg_match("/{$cand_job_title}/i", $job_job_title)) {
                    $validation_arr['job_title'] = '1';
                }
            }
            //var_dump($validation_arr);
            $add_to_list = false;
            if (!empty($validation_arr)) {
                //
                foreach ($validation_arr as $validate_ar) {
                    if ($validate_ar == '1') {
                        $add_to_list = true;
                        break;
                    }
                }
            }
            if ($add_to_list === true && !in_array($cand_id, $ret_ids)) {
                $ret_ids[] = $cand_id;
            }
        }
    }
    if (!empty($ret_ids) && count($ret_ids) > $max_results) {
        $ret_ids = array_slice($ret_ids, 0, $max_results, true);
    }
    //var_dump($ret_ids);
    return $ret_ids;
}

//add_action('wp', 'match__for_test_2');

function match__for_test_2()
{
    echo '<pre>';
    var_dump(jobsearch_job_instamatch_cand_ids(0));
    echo '</pre>';
}

add_action('jobsearch_newjob_approved_at_backend', 'jobsearch_job_attaching_instamatch_cands');
add_action('jobsearch_newjob_posted_at_frontend', 'jobsearch_job_attaching_instamatch_cands', 30);

function jobsearch_job_attaching_instamatch_cands($job_id)
{
    global $jobsearch_plugin_options;

    $insta_match_cands = isset($jobsearch_plugin_options['job_posttin_instamatch_cand']) ? $jobsearch_plugin_options['job_posttin_instamatch_cand'] : '';

    if ($insta_match_cands == 'on') {
        $match_cand_ids = jobsearch_job_instamatch_cand_ids($job_id);
        if (!empty($match_cand_ids)) {
            update_post_meta($job_id, 'jobsearch_instamatch_cands', $match_cand_ids);
            update_post_meta($job_id, 'jobsearch_instamatch_cands_fortag', $match_cand_ids);

            foreach ($match_cand_ids as $cand_id) {
                $cand_user_id = get_post_meta($cand_id, 'jobsearch_user_id', true);
                jobsearch_create_user_meta_list($job_id, 'jobsearch_instamatch_job_ids', $cand_user_id);
                $cand_user_obj = get_user_by('ID', $cand_user_id);
                if (isset($cand_user_obj->ID)) {
                    do_action('jobsearch_instamatch_at_jobpost_email', $cand_user_obj, $job_id);
                }
            }
        }
    }
}

add_action('wp_ajax_jobsearch_job_instamatch_moveto_applicant', 'jobsearch_job_instamatch_moveto_applicant');

function jobsearch_job_instamatch_moveto_applicant()
{

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';

    if ($job_id > 0 && $candidate_id > 0) {

        $user_id = get_post_meta($candidate_id, 'jobsearch_user_id', true);

        $current_user_id = get_current_user_id();
        $curuser_is_employer = jobsearch_user_is_employer($current_user_id);
        if ($curuser_is_employer) {
            $c_user = wp_get_current_user();
        } else {
            $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            $emp_user_id = jobsearch_get_employer_user_id($job_employer_id);
            $c_user = get_user_by('ID', $emp_user_id);
        }

        //
        $job_instamatch_list = get_post_meta($job_id, 'jobsearch_instamatch_cands', true);

        $job_short_int_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
        $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : array();

        if (!in_array($candidate_id, $job_short_int_list)) {
            $job_short_int_list[] = $candidate_id;

            $job_short_int_list = implode(',', $job_short_int_list);
            update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_short_int_list);
            jobsearch_create_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $user_id);
            //
            if (!empty($job_instamatch_list)) {
                $new_instamatch_list = array();
                foreach ($job_instamatch_list as $instamatch_itm) {
                    if ($instamatch_itm != $candidate_id) {
                        $new_instamatch_list[] = $instamatch_itm;
                    }
                }
                update_post_meta($job_id, 'jobsearch_instamatch_cands', $new_instamatch_list);
            }
            //

            $msg = esc_html__('Added', 'wp-jobsearch');
            $error = '0';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            wp_die();
        } else {
            if (!empty($job_instamatch_list)) {
                $new_instamatch_list = array();
                foreach ($job_instamatch_list as $instamatch_itm) {
                    if ($instamatch_itm != $candidate_id) {
                        $new_instamatch_list[] = $instamatch_itm;
                    }
                }
                update_post_meta($job_id, 'jobsearch_instamatch_cands', $new_instamatch_list);
            }
            $msg = esc_html__('Moved', 'wp-jobsearch');
            $error = '0';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            wp_die();
        }
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_multi_move_instamatch_to_apps', 'jobsearch_multi_move_instamatch_to_apps');

function jobsearch_multi_move_instamatch_to_apps()
{

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $_candidate_ids = isset($_POST['_candidate_ids']) ? $_POST['_candidate_ids'] : '';

    $_candidate_ids = $_candidate_ids != '' ? explode(',', $_candidate_ids) : '';
    if (!empty($_candidate_ids) && $job_id > 0) {
        $current_user_id = get_current_user_id();
        $curuser_is_employer = jobsearch_user_is_employer($current_user_id);
        if ($curuser_is_employer) {
            $c_user = wp_get_current_user();
        } else {
            $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            $emp_user_id = jobsearch_get_employer_user_id($job_employer_id);
            $c_user = get_user_by('ID', $emp_user_id);
        }
        //
        $job_instamatch_list = get_post_meta($job_id, 'jobsearch_instamatch_cands', true);

        $job_short_int_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
        $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : array();
        //
        foreach ($_candidate_ids as $candidate_id) {
            if (!in_array($candidate_id, $job_short_int_list)) {
                $cand_user_id = get_post_meta($candidate_id, 'jobsearch_user_id', true);
                $job_short_int_list[] = $candidate_id;
                jobsearch_create_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $cand_user_id);
            }
        }
        $job_short_int_list = implode(',', $job_short_int_list);
        update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_short_int_list);

        //
        if (!empty($job_instamatch_list)) {
            $new_instamatch_list = array();
            foreach ($job_instamatch_list as $instamatch_itm) {
                if (!in_array($instamatch_itm, $_candidate_ids)) {
                    $new_instamatch_list[] = $instamatch_itm;
                }
            }
            update_post_meta($job_id, 'jobsearch_instamatch_cands', $new_instamatch_list);
        }
        //

        $msg = esc_html__('Moving', 'wp-jobsearch');
        $error = '0';
        echo json_encode(array('msg' => $msg, 'error' => $error));
        wp_die();
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('jobsearch_job_feature_expire_cron', 'jobsearch_job_feature_expire_cron_callback');

function jobsearch_job_feature_expire_cron_callback()
{
    $args = array(
        'post_type' => 'job',
        'posts_per_page' => 100,
        'post_status' => 'publish',
        'fields' => 'ids',
        'order' => 'DESC',
        'orderby' => 'ID',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_field_job_featured',
                'value' => 'on',
                'compare' => '=',
            ),
        ),
    );
    $jobs_query = new WP_Query($args);

    $jobs_posts = $jobs_query->posts;

    if (!empty($jobs_posts)) {
        foreach ($jobs_posts as $job_id) {
            $job_feature_till = get_post_meta($job_id, 'jobsearch_field_job_feature_till', true);
            if ($job_feature_till == '') {
                $job_feature_till = current_time('d-m-Y H:i:s');
            }
            if (strtotime($job_feature_till) <= strtotime(current_time('d-m-Y H:i:s'))) {
                update_post_meta($job_id, 'jobsearch_field_job_featured', 'off');
            }
        }
    }
}

if (!function_exists('jobsearch_job_send_to_email_mail')) {
    add_action('wp_ajax_jobsearch_user_send_email_to_friend', 'jobsearch_job_send_to_email_mail');
    add_action('wp_ajax_nopriv_jobsearch_user_send_email_to_friend', 'jobsearch_job_send_to_email_mail');

    function jobsearch_job_send_to_email_mail()
    {
        $subject = isset($_POST['send_email_subject']) ? $_POST['send_email_subject'] : '';
        $email_msg = isset($_POST['send_email_content']) ? $_POST['send_email_content'] : '';
        $uemail = isset($_POST['send_email_to']) ? $_POST['send_email_to'] : '';
        $email_job = isset($_POST['send_email_job_id']) ? $_POST['send_email_job_id'] : '';

        $cnt_email = get_bloginfo('admin_email');

        if ($email_msg != '') {
            $email_msg = nl2br($email_msg);
            $email_msg = '<p>' . $email_msg . '</p>';
        } else {
            $msg = esc_html__('Please type your message.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg, 'error' => '1'));
            wp_die();
        }

        if ($uemail != '' && filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
            $uemail = esc_html($uemail);
        } else {
            $msg = esc_html__('Please Enter a valid email.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg, 'error' => '1'));
            wp_die();
        }

        if ($subject != '') {
            $subject = esc_html($subject);
        } else {
            $msg = esc_html__('Please type the Subject.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg, 'error' => '1'));
            wp_die();
        }

        $send_msg = sprintf(__('Job Link: <a href="%s">%s</a>', 'wp-jobsearch'), get_permalink($email_job), get_the_title($email_job)) . ' <br><br> (' . get_permalink($email_job) . ')' . ' <br><br> ' . $email_msg;
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        add_filter('wp_mail_content_type', function () {
            return 'text/html';
        });
        //
        add_filter('wp_mail_from', function () {
            $p_mail_from = get_bloginfo('admin_email');
            return $p_mail_from;
        });
        //
        add_filter('wp_mail_from_name', function () {
            $p_mail_from = get_bloginfo('name');
            return $p_mail_from;
        });
        //
        if (wp_mail($uemail, $subject, $send_msg)) {
            $msg = esc_html__('Mail sent successfully', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg));
        } else {
            $msg = esc_html__('Error! There is some problem.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg, 'error' => '1'));
        }
        wp_die();
    }

}

if (!function_exists('jobsearch_job_send_to_email_callback')) {
    add_action('jobsearch_job_send_to_email_filter', 'jobsearch_job_send_to_email_callback', 10, 1);

    function jobsearch_job_send_to_email_callback($arg = array())
    {
        global $jobsearch_plugin_options;

        extract(shortcode_atts(array(
            'job_id' => '',
            'btn_class' => '',
        ), $arg));

        $send_email_btnopt = isset($jobsearch_plugin_options['job_detail_email_btn']) ? $jobsearch_plugin_options['job_detail_email_btn'] : '';

        if ($send_email_btnopt == 'on') {
            $btn_class_new = '';
            if (isset($btn_class) && !empty($btn_class)) {
                $btn_class_new = $btn_class;
            }
            ?>
            <a href="javascript:void(0);"
               class="<?php echo($btn_class_new); ?> active jobsearch-send-email-popup-btn"><i
                        class="jobsearch-icon jobsearch-envelope"></i> <?php echo esc_html__('Email Job', 'wp-jobsearch') ?>
            </a>
            <?php
            $popup_args = array(
                'job_id' => $job_id,
            );
            add_action('wp_footer', function () use ($popup_args) {

                global $jobsearch_plugin_options;
                extract(shortcode_atts(array(
                    'job_id' => ''
                ), $popup_args));
                $send_message_form_rand = rand(100000, 999999);
                ob_start();
                if(is_admin()) return false;

                ?>
                <div class="jobsearch-modal fade" id="JobSearchSendEmailModal">
                    <div class="modal-inner-area">&nbsp;</div>
                    <div class="modal-content-area">
                        <div class="modal-box-area">
                            <span class="modal-close"><i class="fa fa-times"></i></span>
                            <div class="jobsearch-send-message-form">
                                <form method="post" id="jobsearch_send_to_email_form">
                                    <div class="jobsearch-user-form">
                                        <ul>
                                            <li>
                                                <label>
                                                    <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>:
                                                </label>
                                                <div class="input-field">
                                                    <input type="text" name="send_email_subject">
                                                </div>
                                            </li>
                                            <li>
                                                <label>
                                                    <?php echo esc_html__('Email Address', 'wp-jobsearch'); ?>:
                                                </label>
                                                <div class="input-field">
                                                    <input type="text" name="send_email_to">
                                                </div>
                                            </li>
                                            <li>
                                                <label>
                                                    <?php echo esc_html__('Message', 'wp-jobsearch'); ?>:
                                                </label>
                                                <div class="input-field">
                                                    <textarea name="send_email_content"></textarea>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="input-field-submit">
                                                    <input type="submit"
                                                           class="send-message-submit-btn send-job-email-btn"
                                                           value="<?php esc_html_e('Send', 'wp-jobsearch') ?>">
                                                </div>
                                                <div class="send-email-loader-box"></div>
                                                <div class="send-email-msg-box"></div>
                                                <input type="hidden" name="send_email_job_id"
                                                       value="<?php echo absint($job_id); ?>">
                                                <input type="hidden" name="action"
                                                       value="jobsearch_user_send_email_to_friend">
                                                <?php
                                                jobsearch_terms_and_con_link_txt();
                                                ?>
                                            </li>
                                        </ul>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                <?php
                $form_html = ob_get_clean();
                echo apply_filters('jobsearch_jobdet_emailtofrnd_form_html', $form_html, $job_id);
            }, 11, 1);
        }
    }

}

add_action('bulk_edit_custom_box', 'jobsearch_quick_edit_fields_job', 10, 2);
function jobsearch_quick_edit_fields_job($column_name, $post_type)
{
    // you can check post type as well but is seems not required because your columns are added for specific CPT anyway
    switch ($column_name) :
        case 'posted_by_emp':
            {

                $rand_num = rand(10000000, 999999999);
                ob_start();
                echo '<div class="inline-edit-group inline-edit-col-right jobsearch-cusqedit-fields" style="float:left;">';
                wp_nonce_field('jobsearch_q_edit_nonce', 'jobsearch_nonce');
                ?>
                <label class="alignleft">
                    <legend class="inline-edit-legend"><?php esc_html_e('Change Employer', 'wp-jobsearch') ?></legend>
                    <div class="elem-field inline-edit-col">
                        <?php
                        //jobsearch_get_custom_post_field('', 'employer', esc_html__('employer', 'wp-jobsearch'), 'job_posted_by');
                        ?>
                        <div class="attachd-user-mcon" style="position: relative; display: inline-block;">
                            <?php
                            $elsemp_title = esc_html__('N/L', 'wp-jobsearch');
                            ?>
                            <strong class="atch-userlogin"><?php echo($elsemp_title) ?></strong>
                            <p class="atch-useremail"><?php _e('User email : <span>N/L</span>', 'wp-jobsearch') ?></p>
                            <p class="atch-userphone"><?php _e('User Phone : <span>N/L</span>', 'wp-jobsearch') ?></p>

                            <input type="hidden" name="jobsearch_field_job_posted_by">
                        </div>
                        <div class="change-userbtn-con">
                            <a href="javascript:void(0);"
                               id="chnge-attachuser-toemp"><?php esc_html_e('Change Employer', 'wp-jobsearch') ?></a>
                        </div>
                        <?php
                        $popup_args = array('p_id' => 0, 'p_rand' => $rand_num);
                        add_action('admin_footer', function () use ($popup_args) {

                            global $wpdb;
                            extract(shortcode_atts(array(
                                'p_id' => '',
                                'p_rand' => ''
                            ), $popup_args));

                            $totl_users = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='employer' AND post_status='publish'");
                            ?>
                            <div class="jobsearch-modal empmeta-atchuser-modal fade"
                                 id="JobSearchModalAttchUser<?php echo($p_rand) ?>">
                                <div class="modal-inner-area">&nbsp;</div>
                                <div class="modal-content-area">
                                    <div class="modal-box-area">
                                        <div class="jobsearch-useratach-popup">
                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                            <?php
                                            $attusers_query = "SELECT posts.ID,posts.post_title FROM $wpdb->posts AS posts WHERE post_type='employer' AND post_status='publish' ORDER BY ID DESC LIMIT %d";
                                            $attall_users = $wpdb->get_results($wpdb->prepare($attusers_query, 10), 'ARRAY_A');

                                            if (!empty($attall_users)) {
                                                ?>
                                                <div class="users-list-con">
                                                    <strong class="users-list-hdng"><?php esc_html_e('Employers List', 'wp-jobsearch') ?></strong>

                                                    <div class="user-atchp-srch">
                                                        <label><?php esc_html_e('Search', 'wp-jobsearch') ?></label>
                                                        <input type="text" id="user_srchinput_<?php echo($p_rand) ?>">
                                                        <span></span>
                                                    </div>

                                                    <div id="inerlist-users-<?php echo($p_rand) ?>"
                                                         class="inerlist-users-sec">
                                                        <ul class="jobsearch-users-list">
                                                            <?php
                                                            foreach ($attall_users as $attch_usritm) {
                                                                ?>
                                                                <li><a href="javascript:void(0);"
                                                                       class="atchuser-itm-btn"
                                                                       data-id="<?php echo($attch_usritm['ID']) ?>"><?php echo($attch_usritm['post_title']) ?></a>
                                                                    <span></span></li>
                                                                <?php
                                                            }
                                                            ?>
                                                        </ul>
                                                        <?php
                                                        if ($totl_users > 10) {
                                                            $total_pages = ceil($totl_users / 10);
                                                            ?>
                                                            <div class="lodmore-users-btnsec">
                                                                <a href="javascript:void(0);" class="lodmore-users-btn"
                                                                   data-tpages="<?php echo($total_pages) ?>"
                                                                   data-keyword=""
                                                                   data-gtopage="2"><?php esc_html_e('Load More', 'wp-jobsearch') ?></a>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                            } else {
                                                echo '<p>' . esc_html__('No User Found.', 'wp-jobsearch') . '</p>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                jQuery(document).on('click', '#chnge-attachuser-toemp', function () {
                                    jobsearch_modal_popup_open('JobSearchModalAttchUser<?php echo($p_rand) ?>');
                                });
                                jQuery(document).on('click', '.atchuser-itm-btn', function () {
                                    var _this = jQuery(this);
                                    var loader_con = _this.parent('li').find('span');
                                    var parentl_con = jQuery('.attachd-user-mcon');
                                    var atch_usernme_con = parentl_con.find('.atch-userlogin');
                                    var atch_useremail_con = parentl_con.find('.atch-useremail span');
                                    var atch_userphone_con = parentl_con.find('.atch-userphone span');
                                    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');

                                    var request = $.ajax({
                                        url: ajaxurl,
                                        method: "POST",
                                        data: {
                                            id: _this.attr('data-id'),
                                            p_id: '<?php echo($p_id) ?>',
                                            action: 'jobsearch_jobmeta_atchemp_throgh_popup'
                                        },
                                        dataType: "json"
                                    });
                                    request.done(function (response) {
                                        if (typeof response.username !== 'undefined') {
                                            atch_usernme_con.html(response.username);
                                            atch_useremail_con.html(response.email);
                                            atch_userphone_con.html(response.phone);
                                            jQuery('input[name=jobsearch_field_job_posted_by]').val(response.id);
                                            jQuery('.jobsearch-modal').removeClass('fade-in').addClass('fade');
                                            jQuery('body').removeClass('jobsearch-modal-active');
                                        }
                                        loader_con.html('');
                                    });
                                    request.fail(function (jqXHR, textStatus) {
                                        loader_con.html('');
                                    });
                                });
                                jQuery(document).on('click', '.lodmore-users-btn', function (e) {
                                    e.preventDefault();
                                    var _this = jQuery(this),
                                        total_pages = _this.attr('data-tpages'),
                                        page_num = _this.attr('data-gtopage'),
                                        keyword = _this.attr('data-keyword'),
                                        this_html = _this.html(),
                                        appender_con = jQuery('#inerlist-users-<?php echo($p_rand) ?> .jobsearch-users-list');
                                    if (!_this.hasClass('ajax-loadin')) {
                                        _this.addClass('ajax-loadin');
                                        _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

                                        total_pages = parseInt(total_pages);
                                        page_num = parseInt(page_num);
                                        var request = jQuery.ajax({
                                            url: ajaxurl,
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
                                jQuery(document).on('keyup', '#user_srchinput_<?php echo($p_rand) ?>', function () {
                                    var _this = jQuery(this);
                                    var loader_con = _this.parent('.user-atchp-srch').find('span');
                                    var appender_con = jQuery('#inerlist-users-<?php echo($p_rand) ?> .jobsearch-users-list');

                                    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');

                                    var request = $.ajax({
                                        url: ajaxurl,
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
                                            jQuery('#inerlist-users-<?php echo($p_rand) ?>').find('.lodmore-users-btnsec').html(response.lodrhtml);
                                            if (response.count > 10) {
                                                jQuery('#inerlist-users-<?php echo($p_rand) ?>').find('.lodmore-users-btnsec').show();
                                            } else {
                                                jQuery('#inerlist-users-<?php echo($p_rand) ?>').find('.lodmore-users-btnsec').hide();
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
                    </div>
                </label>
                <?php
                echo '</div>';
                echo '<div class="inline-edit-group inline-edit-col-right jobsearch-cusqedit-fields" style="float:left;">';

                //
                ?>
                <script>
                    jQuery(document).ready(function () {
                        jQuery('#job-expiry-date-<?php echo($rand_num) ?>').datetimepicker({
                            timepicker: true,
                            format: 'd-m-Y H:i:s'
                        });
                        jQuery('#job-deadline-date-<?php echo($rand_num) ?>').datetimepicker({
                            timepicker: true,
                            format: 'd-m-Y H:i:s'
                        });
                    });
                </script>
                <label class="alignleft">
                    <legend class="inline-edit-legend"><?php esc_html_e('Application Deadline Date', 'wp-jobsearch') ?></legend>
                    <div class="inline-edit-col">
                        <label>
                            <input id="job-deadline-date-<?php echo($rand_num) ?>" type="text"
                                   placeholder="<?php esc_html_e('Choose Date', 'wp-jobsearch') ?>"
                                   name="jobsearch_deadlinedate_bulk">
                        </label>
                    </div>
                </label>
                <label class="alignleft">
                    <legend class="inline-edit-legend"><?php esc_html_e('Expiry Date', 'wp-jobsearch') ?></legend>
                    <div class="inline-edit-col">
                        <label>
                            <input id="job-expiry-date-<?php echo($rand_num) ?>" type="text"
                                   placeholder="<?php esc_html_e('Choose Date', 'wp-jobsearch') ?>"
                                   name="jobsearch_expirydate_bulk">
                        </label>
                    </div>
                </label>
                <?php
                echo '</div>';
                $all_html = ob_get_clean();
                echo($all_html);
                break;
            }

    endswitch;
}

add_action('wp_ajax_jobsearch_quick_save_bulk_job', 'jobsearch_quick_edit_jobs_save');

function jobsearch_quick_edit_jobs_save()
{

    // check nonce
    if (!wp_verify_nonce($_POST['nonce'], 'jobsearch_q_edit_nonce')) {
        die;
    }

    if (isset($_POST['post_ids']) && !empty($_POST['post_ids'])) {
        foreach ($_POST['post_ids'] as $id) {
            if (isset($_POST['posted_by']) && $_POST['posted_by'] != '') {
                update_post_meta($id, 'jobsearch_field_job_posted_by', $_POST['posted_by']);
            }
            if (isset($_POST['deadline_date']) && $_POST['deadline_date'] != '') {
                $deadline_date_str = absint(strtotime($_POST['deadline_date']));
                update_post_meta($id, 'jobsearch_field_job_application_deadline_date', $deadline_date_str);
            }
            if (isset($_POST['expiry_date']) && $_POST['expiry_date'] != '') {
                $expiry_date_str = absint(strtotime($_POST['expiry_date']));
                update_post_meta($id, 'jobsearch_field_job_expiry_date', $expiry_date_str);
            }
        }
    }
    die;
}

if (!function_exists('jobsearch_jobs_update_cron_once')) {
    add_action('init', 'jobsearch_jobs_update_cron_once', 10);

    function jobsearch_jobs_update_cron_once()
    {
        $check_option = get_option('jobsearch_jobs_update_cron_once');

        if ($check_option == '') {
            $args = array(
                'post_type' => 'job',
                'posts_per_page' => '-1',
                'post_status' => 'publish',
                'fields' => 'ids',
                'order' => 'DESC',
                'orderby' => 'ID',
            );

            $jobs_query = new WP_Query($args);
            if (isset($jobs_query->posts) && !empty($jobs_query->posts)) {
                $all_jobs = $jobs_query->posts;
                foreach ($all_jobs as $job_id) {
                    $job_employer = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                    $employer_obj = get_post($job_employer);

                    if (is_object($employer_obj) && isset($employer_obj->ID)) {
                        $employer_status = get_post_meta($job_employer, 'jobsearch_field_employer_approved', true);
                        if ($employer_status == 'on') {
                            update_post_meta($job_id, 'jobsearch_job_employer_status', 'approved');
                        } else {
                            update_post_meta($job_id, 'jobsearch_job_employer_status', '');
                        }
                    }
                }
                update_option('jobsearch_jobs_update_cron_once', '1');
            }
        }
        //
    }

}

function jobsearch_remove_exfeatkeys_jobs_query($meta_query, $add_forc_feat = 'no')
{
    $new_meta_qury = array();
    if (isset($meta_query[0]) && !empty($meta_query[0])) {
        foreach ($meta_query[0] as $meta_field) {
            if (isset($meta_field['key']) && $meta_field['key'] == 'jobsearch_field_job_featured') {
                continue;
            } else if (isset($meta_field[0]['key']) && $meta_field[0]['key'] == 'jobsearch_field_job_featured') {
                continue;
            } else {
                $new_meta_qury[] = $meta_field;
            }
        }
    }
    if ($add_forc_feat == 'yes') {
        $new_meta_qury[] = array(
            "key" => "jobsearch_field_job_featured",
            "value" => "on",
            "compare" => "="
        );
    }
    return $new_meta_qury;
}

if (!function_exists('jobsearch_employer_update_jobs_status')) {
    add_action('jobsearch_employer_update_jobs_status', 'jobsearch_employer_update_jobs_status', 10, 1);

    function jobsearch_employer_update_jobs_status($employer_id)
    {

        $employer_obj = get_post($employer_id);

        if (is_object($employer_obj) && isset($employer_obj->ID)) {

            $employer_status = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);

            $args = array(
                'post_type' => 'job',
                'posts_per_page' => '-1',
                'post_status' => 'publish',
                'fields' => 'ids',
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_posted_by',
                        'value' => $employer_id,
                        'compare' => '=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            if (isset($jobs_query->posts) && !empty($jobs_query->posts)) {
                $all_jobs = $jobs_query->posts;
                foreach ($all_jobs as $job_id) {
                    if ($employer_status == 'on') {
                        update_post_meta($job_id, 'jobsearch_job_employer_status', 'approved');
                    } else {
                        update_post_meta($job_id, 'jobsearch_job_employer_status', '');
                    }
                }
            }
        }
    }
}
//
add_filter('jobsearch_job_det_applybtn_alhtml', 'jobsearch_job_det_applybtn_alhtml', 10, 4);
add_filter('jobsearch_job_defdet_applybtn_boxhtml', 'jobsearch_job_det_applybtn_boxhtml', 10, 2);
add_filter('jobsearch_job_defdetail_after_detcont_html', 'jobsearch_job_det_applybtn_acthtml', 10, 4);

function jobsearch_job_det_applybtn_alhtml($html, $arg, $use_in, $job_id)
{
    if (wp_is_mobile() && $use_in == 'filter') {
        $popup_args = array('html' => $html, 'arg' => $arg, 'job_id' => $job_id);
        add_action('wp_footer', function () use ($popup_args) {

            extract(shortcode_atts(array(
                'html' => '',
                'arg' => '',
                'job_id' => '',
            ), $popup_args));

            $job_aply_method = get_post_meta($job_id, 'jobsearch_field_job_apply_type', true);
            if ($job_aply_method != 'none') {
                ob_start();
                echo '<div class="instastcky-aplybtn-con">' . $html . '</div>';
                $html = ob_get_clean();
                echo apply_filters('jobsearch_job_det_apply_mobile_btn_html', $html, $job_id);
            }
        }, 11, 1);
        $html = '';
    } else {
        $html = $html;
    }
    return $html;
}

function jobsearch_job_det_applybtn_boxhtml($html, $job_id)
{
    $job_aply_method = get_post_meta($job_id, 'jobsearch_field_job_apply_type', true);
    if ($job_aply_method == 'none') {
        $html = '';
        return $html;
    }
    if (wp_is_mobile()) {
        $html = '';
    } else {
        $html = $html;
    }
    return $html;
}

function jobsearch_job_det_applybtn_acthtml($html, $job_id, $use_in = 'filter', $job_view = 'view1')
{
    global $jobsearch_plugin_options;

    $job_aply_method = get_post_meta($job_id, 'jobsearch_field_job_apply_type', true);
    if ($job_aply_method == 'none') {
        $html = '';
        return $html;
    }

    if ($use_in == 'filter' && !wp_is_mobile()) {
        $html = '';
        return $html;
    }

    $current_date = strtotime(current_time('d-m-Y H:i:s'));
    $application_deadline = get_post_meta($job_id, 'jobsearch_field_job_application_deadline_date', true);
    ob_start();

    if (wp_is_mobile()) {
        if ($job_view == 'view4') {
            echo '<div class="careerfy-jobdetail-four-links insta-apply-stickycon">';
        } else if ($job_view == 'view3') {
            echo '<div class="widget_jobdetail_three_apply_wrap insta-apply-stickycon">';
        } else if ($job_view == 'view2') {
            echo '<div class="widget widget_application_apply insta-apply-stickycon">';
        } else {
            echo '<div class="jobsearch_side_box jobsearch_apply_job insta-apply-stickycon">';
        }
    }
    if ($job_view == 'view7') { ?>

        <?php if (wp_is_mobile()) { ?>
            <div class="insta-sypapply-stickycon"></div>
            <?php
        }
        if ($application_deadline != '' && $application_deadline <= $current_date) { ?>
            <span class="deadline-closed"><?php esc_html_e('The Application deadline closed.', 'wp-jobsearch'); ?></span>
        <?php } else {

            $arg = array(
                'classes' => 'jobsearch-job-apply-now jobsearch-applyjob-btn jobsearch-job-apply-btn-con',
                'btn_before_label' => esc_html__('Quick Apply', 'wp-jobsearch'),
                'btn_after_label' => esc_html__('Successfully Applied', 'wp-jobsearch'),
                'btn_applied_label' => esc_html__('Applied', 'wp-jobsearch'),
                'job_id' => $job_id
            ); ?>
            <?php
            $apply_filter_btn = apply_filters('jobsearch_job_applications_btn', '', $arg);
            echo apply_filters('jobsearch_job_det_applybtn_alhtml', $apply_filter_btn, $arg, $use_in, $job_id);
        }
    } else if ($job_view == 'view6') { ?>

        <?php if (wp_is_mobile()) { ?>
            <div class="insta-sypapply-stickycon"></div>
            <?php
        }
        if ($application_deadline != '' && $application_deadline <= $current_date) { ?>
            <span class="deadline-closed"><?php esc_html_e('The Application deadline closed.', 'wp-jobsearch'); ?></span>
        <?php } else {

            $arg = array(
                'classes' => 'widget_application_apply_btn jobsearch-applyjob-btn jobsearch-job-apply-btn-con jobsearch-applyjob-btn-style6',
                'btn_before_label' => esc_html__('Apply Now', 'wp-jobsearch'),
                'btn_after_label' => esc_html__('Successfully Applied', 'wp-jobsearch'),
                'btn_applied_label' => esc_html__('Applied', 'wp-jobsearch'),
                'job_id' => $job_id
            ); ?>
            <div class="jobsearch-applyjob-btn-wrapper">
            <?php

            $apply_filter_btn = apply_filters('jobsearch_job_applications_quick_detail_btn', '', $arg);
            echo apply_filters('jobsearch_job_det_applybtn_alhtml', $apply_filter_btn, $arg, $use_in, $job_id);

            $facebook_login = isset($jobsearch_plugin_options['facebook-social-login']) ? $jobsearch_plugin_options['facebook-social-login'] : '';
            $linkedin_login = isset($jobsearch_plugin_options['linkedin-social-login']) ? $jobsearch_plugin_options['linkedin-social-login'] : '';
            $google_social_login = isset($jobsearch_plugin_options['google-social-login']) ? $jobsearch_plugin_options['google-social-login'] : '';
            $apply_social_platforms = isset($jobsearch_plugin_options['apply_social_platforms']) ? $jobsearch_plugin_options['apply_social_platforms'] : '';
            if (!is_user_logged_in() && ($facebook_login == 'on' || $linkedin_login == 'on' || $google_social_login == 'on') && !empty($apply_social_platforms)) { ?>

                <div class="jobsearch-applywith-title">
                    <small><?php echo esc_html__('OR apply with', 'wp-jobsearch') ?></small>
                </div>
                <div class="jobsearch_apply_job">
                    <ul>
                        <?php
                        $apply_args_fb = array(
                            'job_id' => $job_id,
                            'classes' => 'widget_application_apply_btn facebook jobsearch-applyjob-fb-btn',
                            'label' => esc_html__('Facebook', 'wp-jobsearch'),
                            'view' => 'job6',
                        );
                        $apply_args_link = array(
                            'job_id' => $job_id,
                            'classes' => 'widget_application_apply_btn linkedin jobsearch-applyjob-linkedin-btn',
                            'label' => esc_html__('LinkedIn', 'wp-jobsearch'),
                            'view' => 'job6',
                        );
                        $apply_args_google = array(
                            'job_id' => $job_id,
                            'classes' => 'widget_application_apply_btn google jobsearch-applyjob-google-btn',
                            'label' => esc_html__('Google+', 'wp-jobsearch'),
                            'view' => 'job1',
                        );
                        if (in_array('facebook', $apply_social_platforms)) {
                            do_action('jobsearch_apply_job_with_fb', $apply_args_fb);
                        }
                        if (in_array('linkedin', $apply_social_platforms)) {
                            do_action('jobsearch_apply_job_with_linkedin', $apply_args_link);
                        }
                        if (in_array('google', $apply_social_platforms)) {
                            do_action('jobsearch_apply_job_with_google', $apply_args_google);
                        }
                        ?>
                    </ul>
                </div>

                <span class="apply-msg" style="display: none;"></span>
                </div>
            <?php } ?>

        <?php }
    } else if ($job_view == 'view5') {
        if (wp_is_mobile()) { ?>
            <div class="insta-sypapply-stickycon"></div>
            <?php
        }
        if ($application_deadline != '' && $application_deadline <= $current_date) { ?>
            <span class="deadline-closed"><?php esc_html_e('The Application deadline closed.', 'wp-jobsearch'); ?></span>
        <?php } else {

            $arg = array(
                'classes' => 'widget_application_apply_btn jobsearch-applyjob-btn jobsearch-applyjob-btn-style5 jobsearch-job-apply-btn-con',
                'btn_before_label' => esc_html__('Apply for this job', 'wp-jobsearch'),
                'btn_after_label' => esc_html__('Successfully Applied', 'wp-jobsearch'),
                'btn_applied_label' => esc_html__('Applied', 'wp-jobsearch'),
                'before_icon' => 'careerfy-icon careerfy-briefcase-work',
                'job_id' => $job_id
            );
            $apply_filter_btn = apply_filters('jobsearch_job_applications_btn', '', $arg);
            echo apply_filters('jobsearch_job_det_applybtn_alhtml', $apply_filter_btn, $arg, $use_in, $job_id);

            $facebook_login = isset($jobsearch_plugin_options['facebook-social-login']) ? $jobsearch_plugin_options['facebook-social-login'] : '';
            $linkedin_login = isset($jobsearch_plugin_options['linkedin-social-login']) ? $jobsearch_plugin_options['linkedin-social-login'] : '';
            $google_social_login = isset($jobsearch_plugin_options['google-social-login']) ? $jobsearch_plugin_options['google-social-login'] : '';
            $apply_social_platforms = isset($jobsearch_plugin_options['apply_social_platforms']) ? $jobsearch_plugin_options['apply_social_platforms'] : '';
            if (!is_user_logged_in() && ($facebook_login == 'on' || $linkedin_login == 'on' || $google_social_login == 'on') && !empty($apply_social_platforms)) { ?>

                <?php
                $apply_args_fb = array(
                    'job_id' => $job_id,
                    'classes' => 'widget_application_apply_btn facebook jobsearch-applyjob-fb-btn',
                    'label' => esc_html__('Apply with Facebook', 'wp-jobsearch'),
                    'view' => 'job5',
                );
                $apply_args_link = array(
                    'job_id' => $job_id,
                    'classes' => 'widget_application_apply_btn linkedin jobsearch-applyjob-linkedin-btn',
                    'label' => esc_html__('Apply with LinkedIn', 'wp-jobsearch'),
                    'view' => 'job5',
                );
                $apply_args_google = array(
                    'job_id' => $job_id,
                    'classes' => 'widget_application_apply_btn google jobsearch-applyjob-google-btn',
                    'label' => esc_html__('Apply with Google+', 'wp-jobsearch'),
                    'view' => 'job4',
                );
                if (in_array('facebook', $apply_social_platforms)) {
                    do_action('jobsearch_apply_job_with_fb', $apply_args_fb);
                }
                if (in_array('linkedin', $apply_social_platforms)) {
                    do_action('jobsearch_apply_job_with_linkedin', $apply_args_link);
                }
                if (in_array('google', $apply_social_platforms)) {
                    do_action('jobsearch_apply_job_with_google', $apply_args_google);
                }
                ?>
                <span class="apply-msg" style="display: none;"></span>
                <?php
            }
        }
    } else if ($job_view == 'view4') {
        if (wp_is_mobile()) {
            ?>
            <div class="insta-sypapply-stickycon"></div>
            <?php
        }
        if ($application_deadline != '' && $application_deadline <= $current_date) {
            ?>
            <span class="deadline-closed"><?php esc_html_e('closed.', 'wp-jobsearch'); ?></span>
            <?php
        } else {
            $arg = array(
                'classes' => 'color1 jobsearch-job-apply-btn-con',
                'btn_before_label' => esc_html__('Apply Now', 'wp-jobsearch'),
                'btn_after_label' => esc_html__('Successfully Applied', 'wp-jobsearch'),
                'btn_applied_label' => esc_html__('Applied', 'wp-jobsearch'),
                'job_id' => $job_id
            );
            $apply_filter_btn = apply_filters('jobsearch_job_applications_btn', '', $arg);
            echo apply_filters('jobsearch_job_det_applybtn_alhtml', $apply_filter_btn, $arg, $use_in, $job_id);

            $facebook_login = isset($jobsearch_plugin_options['facebook-social-login']) ? $jobsearch_plugin_options['facebook-social-login'] : '';
            $linkedin_login = isset($jobsearch_plugin_options['linkedin-social-login']) ? $jobsearch_plugin_options['linkedin-social-login'] : '';
            $google_social_login = isset($jobsearch_plugin_options['google-social-login']) ? $jobsearch_plugin_options['google-social-login'] : '';
            $apply_social_platforms = isset($jobsearch_plugin_options['apply_social_platforms']) ? $jobsearch_plugin_options['apply_social_platforms'] : '';
            if (!is_user_logged_in() && ($facebook_login == 'on' || $linkedin_login == 'on' || $google_social_login == 'on') && !empty($apply_social_platforms)) {
                $apply_args_fb = array(
                    'job_id' => $job_id,
                    'classes' => 'color2 jobsearch-applyjob-fb-btn',
                    'view' => 'job4',
                );
                $apply_args_linkdin = array(
                    'job_id' => $job_id,
                    'classes' => 'color3 jobsearch-applyjob-linkedin-btn',
                    'view' => 'job4',
                );
                $apply_args_google = array(
                    'job_id' => $job_id,
                    'classes' => 'color4 jobsearch-applyjob-google-btn',
                    'view' => 'job4',
                );

                if (in_array('facebook', $apply_social_platforms)) {
                    do_action('jobsearch_apply_job_with_fb', $apply_args_fb);
                }
                if (in_array('linkedin', $apply_social_platforms)) {
                    do_action('jobsearch_apply_job_with_linkedin', $apply_args_linkdin);
                }
                if (in_array('google', $apply_social_platforms)) {
                    do_action('jobsearch_apply_job_with_google', $apply_args_google);
                }
                ?>
                <span class="apply-msg" style="display: none;"></span>
                <?php
            }
        }
    } else if ($job_view == 'view3') {
        if (wp_is_mobile()) { ?>
            <div class="insta-sypapply-stickycon"></div>
            <?php
        }
        if ($application_deadline != '' && $application_deadline <= $current_date) { ?>
            <span class="deadline-closed"><?php esc_html_e('The Application deadline closed.', 'wp-jobsearch'); ?></span>
        <?php } else {

            $arg = array(
                'classes' => 'jobsearch_box_jobdetail_three_apply_link jobsearch-job-apply-btn-con',
                'btn_before_label' => esc_html__('Apply to this job', 'wp-jobsearch'),
                'btn_after_label' => esc_html__('Successfully Applied', 'wp-jobsearch'),
                'btn_applied_label' => esc_html__('Applied', 'wp-jobsearch'),
                'job_id' => $job_id
            );
            $apply_filter_btn = apply_filters('jobsearch_job_applications_btn', '', $arg);
            echo apply_filters('jobsearch_job_det_applybtn_alhtml', $apply_filter_btn, $arg, $use_in, $job_id);

            $facebook_login = isset($jobsearch_plugin_options['facebook-social-login']) ? $jobsearch_plugin_options['facebook-social-login'] : '';
            $linkedin_login = isset($jobsearch_plugin_options['linkedin-social-login']) ? $jobsearch_plugin_options['linkedin-social-login'] : '';
            $google_social_login = isset($jobsearch_plugin_options['google-social-login']) ? $jobsearch_plugin_options['google-social-login'] : '';
            $apply_social_platforms = isset($jobsearch_plugin_options['apply_social_platforms']) ? $jobsearch_plugin_options['apply_social_platforms'] : '';
            if (!is_user_logged_in() && ($facebook_login == 'on' || $linkedin_login == 'on' || $google_social_login == 'on') && !empty($apply_social_platforms)) {
                ?>
                <div class="careerfy-applywith-title">
                    <small><?php echo esc_html__('oR Apply with', 'wp-jobsearch') ?></small>
                </div>
                <ul class="jobsearch_box_jobdetail_three_apply_social_icon">
                    <?php
                    $apply_args_fb = array(
                        'job_id' => $job_id,
                        'view' => 'job3',
                        'classes' => 'fa fa-facebook-f jobsearch-applyjob-fb-btn',
                    );
                    $apply_args_link = array(
                        'job_id' => $job_id,
                        'view' => 'job3',
                        'classes' => 'fa fa-linkedin jobsearch-applyjob-linkedin-btn',
                    );
                    $apply_args_google = array(
                        'job_id' => $job_id,
                        'view' => 'job3',
                        'classes' => 'fa fa-google-plus jobsearch-applyjob-google-btn',
                    );
                    if (in_array('facebook', $apply_social_platforms)) {
                        do_action('jobsearch_apply_job_with_fb', $apply_args_fb);
                    }
                    if (in_array('linkedin', $apply_social_platforms)) {
                        do_action('jobsearch_apply_job_with_linkedin', $apply_args_link);
                    }
                    //if (in_array('google', $apply_social_platforms)) {
                    do_action('jobsearch_apply_job_with_google', $apply_args_google);
                    //}
                    ?>
                </ul>
                <span class="apply-msg" style="display: none;"></span>
                <?php
            }
        }
    } else if ($job_view == 'view2') {

        if (wp_is_mobile()) { ?>
            <div class="insta-sypapply-stickycon"></div>
            <?php
        }
        if ($application_deadline != '' && $application_deadline <= $current_date) { ?>
            <span class="deadline-closed"><?php esc_html_e('The Application deadline closed.', 'wp-jobsearch'); ?></span>
            <?php
        } else {
            $arg = array(
                'classes' => 'widget_application_apply_btn jobsearch-applyjob-btn jobsearch-job-apply-btn-con',
                'btn_before_label' => esc_html__('Apply for this job', 'wp-jobsearch'),
                'btn_after_label' => esc_html__('Successfully Applied', 'wp-jobsearch'),
                'btn_applied_label' => esc_html__('Applied', 'wp-jobsearch'),
                'job_id' => $job_id
            );
            $apply_filter_btn = apply_filters('jobsearch_job_applications_btn', '', $arg);
            echo apply_filters('jobsearch_job_det_applybtn_alhtml', $apply_filter_btn, $arg, $use_in, $job_id);

            $facebook_login = isset($jobsearch_plugin_options['facebook-social-login']) ? $jobsearch_plugin_options['facebook-social-login'] : '';
            $linkedin_login = isset($jobsearch_plugin_options['linkedin-social-login']) ? $jobsearch_plugin_options['linkedin-social-login'] : '';
            $google_social_login = isset($jobsearch_plugin_options['google-social-login']) ? $jobsearch_plugin_options['google-social-login'] : '';
            $apply_social_platforms = isset($jobsearch_plugin_options['apply_social_platforms']) ? $jobsearch_plugin_options['apply_social_platforms'] : '';
            if (!is_user_logged_in() && ($facebook_login == 'on' || $linkedin_login == 'on' || $google_social_login == 'on') && !empty($apply_social_platforms)) {
                ?>
                <div class="careerfy-applywith-title">
                    <small><?php echo esc_html__('OR Apply with', 'wp-jobsearch') ?></small>
                </div>
                <div class="jobsearch_apply_job">
                    <ul>
                        <?php
                        $apply_args_fb = array(
                            'job_id' => $job_id,
                            'classes' => 'widget_application_apply_btn facebook jobsearch-applyjob-fb-btn',
                            'label' => esc_html__('Apply with Facebook', 'wp-jobsearch'),
                            'view' => 'job6',
                        );
                        $apply_args_link = array(
                            'job_id' => $job_id,
                            'classes' => 'widget_application_apply_btn linkedin jobsearch-applyjob-linkedin-btn',
                            'label' => esc_html__('Apply with LinkedIn', 'wp-jobsearch'),
                            'view' => 'job6',
                        );
                        $apply_args_google = array(
                            'job_id' => $job_id,
                            'classes' => 'widget_application_apply_btn google jobsearch-applyjob-google-btn',
                            'label' => esc_html__('Apply with Google+', 'wp-jobsearch'),
                            'view' => 'job1',
                        );
                        if (in_array('facebook', $apply_social_platforms)) {
                            do_action('jobsearch_apply_job_with_fb', $apply_args_fb);
                        }
                        if (in_array('linkedin', $apply_social_platforms)) {
                            do_action('jobsearch_apply_job_with_linkedin', $apply_args_link);
                        }
                        if (in_array('google', $apply_social_platforms)) {
                            do_action('jobsearch_apply_job_with_google', $apply_args_google);
                        }
                        ?>
                    </ul>
                </div>
                <span class="apply-msg" style="display: none;"></span>
                <?php
            }
        }
    } else { ?>
        <div class="jobsearch_apply_job_wrap">
            <?php
            if (wp_is_mobile()) { ?>
                <div class="insta-sypapply-stickycon"></div>
                <?php
            }

            if ($application_deadline != '' && $application_deadline <= $current_date) {
                ?>
                <span class="deadline-closed"><?php esc_html_e('The Application deadline closed.', 'wp-jobsearch'); ?></span>
                <?php
            } else {
                $btn_txt = "<small>".esc_html__('Apply for the job', 'wp-jobsearch')."</small>";
                $arg = array(
                    'classes' => 'jobsearch-applyjob-btn jobsearch-job-apply-btn-con',
                    'btn_before_label' => $btn_txt,
                    'btn_after_label' => esc_html__('Successfully Applied', 'wp-jobsearch'),
                    'btn_applied_label' => esc_html__('Applied', 'wp-jobsearch'),
                    'job_id' => $job_id
                );
                $apply_filter_btn = apply_filters('jobsearch_job_applications_btn', '', $arg);
                echo apply_filters('jobsearch_job_det_applybtn_alhtml', $apply_filter_btn, $arg, $use_in, $job_id);
            }

            $job_apply_deadline_sw = isset($jobsearch_plugin_options['job_appliction_deadline']) ? $jobsearch_plugin_options['job_appliction_deadline'] : '';

            if ($job_apply_deadline_sw != 'off' && $application_deadline != '' && $application_deadline > $current_date) {
                $creat_date = date('Y-m-d H:i:s', $application_deadline);
                $creat_date = date_create($creat_date);
                $creat_date2 = date('Y-m-d H:i:s', $current_date);
                $creat_date2 = date_create($creat_date2);
                $date_diff = date_diff($creat_date, $creat_date2);
                $date_diff = json_decode(json_encode($date_diff), true);
                $app_deadline_rtime = '';
                $app_deadline_rtime .= (isset($date_diff['y']) && $date_diff['y'] > 0) ? (' ' . $date_diff['y'] . esc_html__('y', 'wp-jobsearch')) : '';
                $app_deadline_rtime .= isset($date_diff['m']) && $date_diff['m'] > 0 ? ' ' . $date_diff['m'] . esc_html__('m', 'wp-jobsearch') : '';
                $app_deadline_rtime .= isset($date_diff['d']) && $date_diff['d'] > 0 ? ' ' . $date_diff['d'] . esc_html__('d', 'wp-jobsearch') : '';
                $app_deadline_rtime .= isset($date_diff['h']) && $date_diff['h'] > 0 ? ' ' . $date_diff['h'] . esc_html__('h', 'wp-jobsearch') : '';
                $app_deadline_rtime .= isset($date_diff['i']) && $date_diff['i'] > 0 ? ' ' . $date_diff['i'] . esc_html__('min', 'wp-jobsearch') : '';
                ?>

                <span class="jobsearch-application-ending"><?php printf(esc_html__('Application ends in %s', 'wp-jobsearch'), $app_deadline_rtime) ?></span>
                <?php
            }
            $facebook_login = isset($jobsearch_plugin_options['facebook-social-login']) ? $jobsearch_plugin_options['facebook-social-login'] : '';
            $linkedin_login = isset($jobsearch_plugin_options['linkedin-social-login']) ? $jobsearch_plugin_options['linkedin-social-login'] : '';
            $google_social_login = isset($jobsearch_plugin_options['google-social-login']) ? $jobsearch_plugin_options['google-social-login'] : '';

            if ($application_deadline != '' && $application_deadline <= $current_date) {
                // check for social apply in case
                // job deadline is passed
            } else {
                $apply_social_platforms = isset($jobsearch_plugin_options['apply_social_platforms']) ? $jobsearch_plugin_options['apply_social_platforms'] : '';

                if (!is_user_logged_in() && ($facebook_login == 'on' || $linkedin_login == 'on' || $google_social_login == 'on') && !empty($apply_social_platforms)) {
                    ?>
                    <div class="jobsearch-applywith-title">
                        <small><?php echo esc_html__('OR apply with', 'wp-jobsearch') ?></small>
                    </div>
                    <p class="jobsearch-easy-apply-txt"><?php echo esc_html__('An easy way to apply for this job. Use the following social media.', 'wp-jobsearch') ?></p>
                    <ul>
                        <?php
                        $apply_args = array(
                            'job_id' => $job_id
                        );
                        if (in_array('facebook', $apply_social_platforms)) {
                            do_action('jobsearch_apply_job_with_fb', $apply_args);
                        }
                        if (in_array('linkedin', $apply_social_platforms)) {
                            do_action('jobsearch_apply_job_with_linkedin', $apply_args);
                        }
                        if (in_array('google', $apply_social_platforms)) {
                            do_action('jobsearch_apply_job_with_google', $apply_args);
                        }
                        ?>
                    </ul>
                    <span class="apply-msg" style="display: none;"></span>
                    <?php
                }
            }
            echo apply_filters('jobsearch_apply_job_btn_inaftr', '', $job_id);
            ?>
        </div>
        <?php
    }
    if (wp_is_mobile()) {
        echo '</div>';
        ?>
        <script>
            jQuery(document).ready(function () {

                var stickyBtnHtml = jQuery('.instastcky-aplybtn-con').html();
                var $sticky = jQuery('.instastcky-aplybtn-con');
                var $stickyrStopper = jQuery('.insta-sypapply-stickycon');

                if (!!$sticky.offset()) { // make sure ".sticky" element exists

                    var generalBoxHeight = jQuery('.insta-apply-stickycon').innerHeight();
                    var stickyTop = $sticky.offset().top;

                    var stickyStopperPosition = $stickyrStopper.offset().top - 52;
                    var stopPoint = stickyStopperPosition;

                    var stickBtnHeight = $sticky.innerHeight();
                    var bodyToBtnHeight = jQuery(window).height();
                    var btm_stopPoint = bodyToBtnHeight - stickBtnHeight;

                    var actStopPoint = stopPoint - btm_stopPoint;
                    //console.log(actStopPoint);

                    jQuery(window).scroll(function () { // scroll event
                        var windowTop = jQuery(window).scrollTop(); // returns number
                        //console.log(windowTop);
                        if (windowTop >= actStopPoint) {
                            //$sticky.addClass('remove-sticky-apply');
                            jQuery('.instastcky-aplybtn-con').html('');
                            $stickyrStopper.html(stickyBtnHtml);
                        } else {
                            //$sticky.removeClass('remove-sticky-apply');
                            jQuery('.instastcky-aplybtn-con').html(stickyBtnHtml);
                            $stickyrStopper.html('');
                        }
                    });
                }
            });
        </script>
        <?php
    }
    $html = ob_get_clean();

    return $html;
}

//

add_action('wp_ajax_jobsearch_admin_meta_job_apps_list', 'jobsearch_admin_meta_job_apps_list');

function jobsearch_admin_meta_job_apps_list()
{

    $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';
    $_job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
    $_job_applicants_list = jobsearch_is_post_ids_array($_job_applicants_list, 'candidate');
    if (empty($_job_applicants_list)) {
        $_job_applicants_list = array();
    }

    $cand_args = array(
        'posts_per_page' => -1,
        'post_type' => 'candidate',
        'post_status' => 'publish',
        'fields' => 'ids',
        'order' => 'DESC',
        'orderby' => 'ID',
    );
    $all_cands_query = new WP_Query($cand_args);
    wp_reset_postdata();

    $all_cands_list = $all_cands_query->posts;

    ob_start();
    ?>
    <select name="job_all_apps_list[]" multiple="multiple" class="applicants-selectize"
            placeholder="<?php esc_html_e('Select Candidates', 'wp-jobsearch') ?>">
        <?php
        if (!empty($all_cands_list)) {
            foreach ($all_cands_list as $candidate_id) {
                ?>
                <option value="<?php echo($candidate_id) ?>" <?php echo(in_array($candidate_id, $_job_applicants_list) ? 'selected="selected"' : '') ?>><?php echo get_the_title($candidate_id) ?></option>
                <?php
            }
        }
        ?>
    </select>
    <script>
        jQuery('.applicants-selectize').selectize({
            plugins: ['remove_button'],
        });
    </script>
    <?php
    $html = ob_get_clean();

    echo json_encode(array('html' => $html));
    die;
}

function jobsearch_remov_job_applicant_bycid($job_id, $candidate_id)
{
    if ($job_id > 0 && $candidate_id > 0) {

        $user_id = jobsearch_get_candidate_user_id($candidate_id);

        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
        $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
        if (empty($job_applicants_list)) {
            $job_applicants_list = array();
        }

        if (!empty($job_applicants_list)) {
            //

            if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
                unset($job_applicants_list[$key]);

                $job_applicants_list = implode(',', $job_applicants_list);
                update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
                jobsearch_remove_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $user_id);
            }
        }
    }
}

//
add_action('wp_ajax_jobsearch_admin_meta_job_apps_list_save', 'jobsearch_admin_meta_job_apps_listsave');

function jobsearch_admin_meta_job_apps_listsave()
{

    $all_apps = isset($_POST['all_apps']) ? $_POST['all_apps'] : '';
    $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';

    $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
    $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
    if (empty($job_applicants_list)) {
        $job_applicants_list = array();
    }

    if (!empty($job_applicants_list)) {
        foreach ($job_applicants_list as $jobapp_id) {
            jobsearch_remov_job_applicant_bycid($job_id, $jobapp_id);
        }
    }

    $sjob_applicants_list = array();
    if (!empty($all_apps)) {
        foreach ($all_apps as $app_id) {
            $cand_user_id = jobsearch_get_candidate_user_id($app_id);
            jobsearch_create_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $cand_user_id);

            //
            if (!in_array($app_id, $sjob_applicants_list)) {
                $sjob_applicants_list[] = $app_id;
            }
        }
    }
    if (!empty($sjob_applicants_list)) {
        $sjob_applicants_list = implode(',', $sjob_applicants_list);
    } else {
        $sjob_applicants_list = '';
    }
    update_post_meta($job_id, 'jobsearch_job_applicants_list', $sjob_applicants_list);

    $msg = esc_html__('Applicants Saved', 'wp-jobsearch');

    echo json_encode(array('msg' => $msg));
    die;
}

add_action('careerfy_mega_menu_cus_items_before', 'jobsearch_mega_menu_listing_items_count', 10, 2);

function jobsearch_mega_menu_listing_items_count($item, $item_id)
{
    if (isset($item->menu_item_parent) && $item->menu_item_parent == '0') { ?>
        <p class="field-view description description-wide">
            <label for="edit-menu-item-counlisfor-<?php echo absint($item_id); ?>">
                <?php
                $item_counlisfor = get_post_meta($item_id, '_menu_item_counlisfor', true);
                ?>
                <?php _e('Show count for', 'wp-jobsearch'); ?><br/>
                <select id="edit-menu-item-counlisfor-<?php echo absint($item_id); ?>"
                        class="widefat edit-menu-item-counlisfor"
                        name="menu-item-counlisfor[<?php echo absint($item_id); ?>]">
                    <option value=""><?php _e('Select Listing Type', 'wp-jobsearch'); ?></option>
                    <option<?php echo($item->counlisfor == 'jobs' ? ' selected="selected"' : '') ?>
                            value="jobs"><?php _e('Jobs', 'wp-jobsearch'); ?></option>
                    <option<?php echo($item->counlisfor == 'candidates' ? ' selected="selected"' : '') ?>
                            value="candidates"><?php _e('Candidates', 'wp-jobsearch'); ?></option>
                    <option<?php echo($item->counlisfor == 'employers' ? ' selected="selected"' : '') ?>
                            value="employers"><?php _e('Employers', 'wp-jobsearch'); ?></option>
                </select>
            </label>
        </p>
        <p id="menu-counliscolr-main-<?php echo absint($item_id); ?>" class="field-view description description-wide"
           style="display: <?php echo($item->counlisfor != '' ? 'block' : 'none') ?>;">
            <label for="edit-menu-item-counliscolr-<?php echo absint($item_id); ?>">
                <?php
                $item_counlisfor = get_post_meta($item_id, '_menu_item_counliscolr', true);
                ?>
                <?php _e('Count Color', 'wp-jobsearch'); ?><br/>
            </label>
            <input type="text" id="edit-menu-item-counliscolr-<?php echo absint($item_id); ?>"
                   class="jobsearch-bk-color edit-menu-item-counliscolr"
                   name="menu-item-counliscolr[<?php echo absint($item_id); ?>]"
                   value="<?php echo esc_attr($item->counliscolr); ?>"/>
        </p>
        <script>
            jQuery(document).on('change', '#edit-menu-item-counlisfor-<?php echo absint($item_id); ?>', function () {
                var hidshow_con = jQuery('#menu-counliscolr-main-<?php echo absint($item_id); ?>');
                if (jQuery(this).val() == '') {
                    hidshow_con.hide();
                } else {
                    hidshow_con.show();
                }
            });
        </script>
        <?php
    }
}

add_filter('careerfy_mega_add_custom_nav_fields_filtr', 'jobsearch_menu_listing_count_fields_filtr', 10, 1);

function jobsearch_menu_listing_count_fields_filtr($menu_item)
{

    $menu_item->counlisfor = get_post_meta($menu_item->ID, '_menu_item_counlisfor', true);
    $menu_item->counliscolr = get_post_meta($menu_item->ID, '_menu_item_counliscolr', true);
    return $menu_item;
}

add_action('careerfy_mega_menu_items_save', 'jobsearch_menu_listing_count_fields_saving', 10, 1);

function jobsearch_menu_listing_count_fields_saving($menu_item_db_id)
{

    if (isset($_POST['menu-item-counlisfor'][$menu_item_db_id])) {
        $menu_item_counlisfor = $_POST['menu-item-counlisfor'][$menu_item_db_id];
    } else {
        $menu_item_counlisfor = '';
    }
    if (isset($_POST['menu-item-counliscolr'][$menu_item_db_id])) {
        $menu_item_counliscolr = $_POST['menu-item-counliscolr'][$menu_item_db_id];
    } else {
        $menu_item_counliscolr = '';
    }

    update_post_meta($menu_item_db_id, '_menu_item_counlisfor', $menu_item_counlisfor);
    update_post_meta($menu_item_db_id, '_menu_item_counliscolr', $menu_item_counliscolr);
}

if (!function_exists('jobsearch_job_listing_custom_fields_callback')) {

    function jobsearch_job_listing_custom_fields_callback($atts = array())
    {


        //print_r($atts);

        $job_custom_fields_switch = isset($atts['job_custom_fields_switch']) ? $atts['job_custom_fields_switch'] : '';
        //echo $job_elem_custom_fields = isset($atts['job_elem_custom_fields']) ? $atts['job_elem_custom_fields'] : '';
        //echo '1';
        if ($job_custom_fields_switch == 'yes') {
            //echo '1';
        }
    }

    add_action('jobsearch_job_listing_custom_fields', 'jobsearch_job_listing_custom_fields_callback', 10, 1);
}

if (!function_exists('jobsearch_job_listing_deadline_callback')) {

    function jobsearch_job_listing_deadline_callback($atts = array(), $job_id = '')
    {
        //echo $job_id;
        $job_deadline_date = get_post_meta($job_id, 'jobsearch_field_job_application_deadline_date', true);
        $job_deadline_switch = isset($atts['job_deadline_switch']) ? $atts['job_deadline_switch'] : '';
        $jobsearch_last_date_formated = date_i18n(get_option('date_format'), strtotime($job_deadline_date));

        if ($job_deadline_switch == 'Yes') {
            echo '<li><i class="jobsearch-icon jobsearch-calendar"></i>' . $jobsearch_last_date_formated . '</li>';
        }
    }

    add_action('jobsearch_job_listing_deadline', 'jobsearch_job_listing_deadline_callback', 10, 2);
}

add_action('jobsearch_job_listin_sh_after_jobs_found', 'jobsearch_jobs_listin_totaljobs_found_html', 10, 4);
add_filter('jobsearch_job_listin_top_jobfounds_html', 'jobsearch_jobs_listin_top_jobfounds_html', 10, 4);
add_filter('jobsearch_job_listin_before_top_jobfounds_html', 'jobsearch_jobs_listin_before_top_jobfounds_html', 10, 5);
add_filter('jobsearch_job_listin_before_top_jobfounds_quick_detail_html', 'jobsearch_jobs_listin_before_top_jobfounds_quick_detail_html', 10, 5);
add_filter('jobsearch_job_listin_after_sort_orders_html', 'jobsearch_jobs_listin_after_top_jobsorts_html', 10, 4);

function jobsearch_jobs_listin_totaljobs_found_html($job_totnum, $job_short_counter, $atts, $topfeat_postfounds = 0)
{
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $per_page = isset($atts['job_per_page']) && absint($atts['job_per_page']) > 0 ? $atts['job_per_page'] : 0;
        if (isset($_REQUEST['per-page']) && $_REQUEST['per-page'] > 1) {
            $per_page = $_REQUEST['per-page'];
        }
        $page_num = isset($_REQUEST['job_page']) && $_REQUEST['job_page'] > 1 ? $_REQUEST['job_page'] : 1;
        if ($topfeat_postfounds > 0) {
            $topfeat_per_page = isset($atts['num_of_feat_jobs']) ? $atts['num_of_feat_jobs'] : '';
            $topfeat_per_page = $topfeat_per_page > 0 ? $topfeat_per_page : 5;
            $to_adstrt_featjobnum = ($page_num - 1) * $topfeat_per_page;
            $to_adstrt_featjobnum = $to_adstrt_featjobnum > $topfeat_postfounds ? $topfeat_postfounds : $to_adstrt_featjobnum;
            //
            $to_add_featjobnum = $page_num * $topfeat_per_page;
            $to_add_featjobnum = $to_add_featjobnum > $topfeat_postfounds ? $topfeat_postfounds : $to_add_featjobnum;
        }
        if ($per_page > 1) {
            $start_frm = $page_num > 1 ? (($page_num - 1) * $per_page) : 1;
            $offset = $page_num > 1 ? ($page_num * $per_page) : $per_page;
            //
            if ($topfeat_postfounds > 0) {
                $start_frm = $page_num > 1 ? ($start_frm + $to_adstrt_featjobnum) : $start_frm;
                $offset = $offset + $to_add_featjobnum;
            }
            //
            $offset = $offset > $job_totnum ? $job_totnum : $offset;
            $strt_toend_disp = absint($job_totnum) > 0 ? ($start_frm > 1 ? ($start_frm + 1) : $start_frm) . ' - ' . $offset : '0';
            ?>
            <div class="displayed-here"><?php printf(esc_html__('Displayed Here: %s Jobs', 'wp-jobsearch'), $strt_toend_disp) ?></div>
            <?php
        } else {
            $per_page = isset($atts['job_per_page']) && absint($atts['job_per_page']) > 0 ? $atts['job_per_page'] : $job_totnum;
            $per_page = $per_page > $job_totnum ? $job_totnum : $per_page;
            //
            if ($topfeat_postfounds > 0) {
                $per_page = $per_page + $to_add_featjobnum;
            }
            //
            $strt_toend_disp = absint($job_totnum) > 0 ? '1 - ' . $per_page : '0';
            ?>
            <div class="displayed-here"><?php printf(esc_html__('Displayed Here: %s Jobs', 'wp-jobsearch'), $strt_toend_disp) ?></div>
            <?php
        }
    }
}

function jobsearch_jobs_listin_top_jobfounds_html($html, $job_totnum, $job_short_counter, $atts)
{
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $html = '';
    }
    return $html;
}

//
function jobsearch_jobs_listin_before_top_jobfounds_quick_detail_html($html, $job_totnum, $job_short_counter, $atts, $topfeat_postfounds)
{
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    wp_enqueue_style('jobsearch-job-alerts', jobsearch_plugin_get_url('modules/job-alerts/css/job-alerts.css'));
    wp_enqueue_script('jobsearch-job-alerts-scripts', jobsearch_plugin_get_url('modules/job-alerts/js/job-alerts.js'), array(), JobSearch_plugin::get_version(), true);

    if ($counts_on) {
        ob_start();
        ?>
        <div class="jobsearch-filterable jobsearch-filter-sortable jobsearch-topfound-title">
            <h2 class="jobsearch-fltcount-title">
                <?php
                echo absint($job_totnum) . '&nbsp;';
                if ($job_totnum == 1) {
                    echo esc_html__('Job Found', 'wp-jobsearch');
                } else {
                    echo esc_html__('Jobs Found', 'wp-jobsearch');
                }
                do_action('jobsearch_job_listin_sh_after_jobs_found', $job_totnum, $job_short_counter, $atts, $topfeat_postfounds);
                //
                ?>
            </h2>
        </div>
        <?php
        echo '<div class="jobsearch-topsort-holder">';
        $html = ob_get_clean();
    }
    return $html;
}

function jobsearch_jobs_listin_before_top_jobfounds_html($html, $job_totnum, $job_short_counter, $atts, $topfeat_postfounds)
{
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }

    if ($counts_on) {
        ob_start();
        ?>
        <div class="jobsearch-filterable jobsearch-filter-sortable jobsearch-topfound-title">
            <h2 class="jobsearch-fltcount-title">
                <?php
                echo absint($job_totnum) . '&nbsp;';
                if ($job_totnum == 1) {
                    echo esc_html__('Job Found', 'wp-jobsearch');
                } else {
                    echo esc_html__('Jobs Found', 'wp-jobsearch');
                }
                do_action('jobsearch_job_listin_sh_after_jobs_found', $job_totnum, $job_short_counter, $atts, $topfeat_postfounds);
                ?>
            </h2>
        </div>
        <?php
        echo '<div class="jobsearch-topsort-holder">';
        $html = ob_get_clean();
    }
    return $html;
}

function jobsearch_jobs_listin_after_top_jobsorts_html($html, $job_totnum, $job_short_counter, $atts)
{
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $html = '</div>';
    }
    return $html;
}

add_action('before_delete_post', 'jobsearch_delete_job_expiry_cron');

function jobsearch_delete_job_expiry_cron($post_id)
{
    if (get_post_type($post_id) == 'job') {
        $job_employer_id = get_post_meta($post_id, 'jobsearch_field_job_posted_by', true);
        $user_id = jobsearch_get_employer_user_id($job_employer_id);
        wp_clear_scheduled_hook('jobsearch_job_expiry_cron_event_' . $post_id, array($post_id, $user_id));
    }
}

//
if (!function_exists('jobsearch_jobmeta_atchemp_throgh_popup')) {

    function jobsearch_jobmeta_atchemp_throgh_popup()
    {

        $emp_id = absint($_POST['id']);
        $job_id = absint($_POST['p_id']);

        $employer_user_id = jobsearch_get_employer_user_id($emp_id);
        $user_obj = get_user_by('ID', $employer_user_id);

        $atch_user_logname = esc_html__('N/L', 'wp-jobsearch');
        $useremail = esc_html__('N/L', 'wp-jobsearch');
        $user_phone = get_post_meta($emp_id, 'jobsearch_field_user_phone', true);
        $user_phone = $user_phone != '' ? $user_phone : esc_html__('N/L', 'wp-jobsearch');

        if (get_post_type($emp_id) == 'employer') {
            $atch_user_logname = get_the_title($emp_id);
        }

        if (is_object($user_obj) && isset($user_obj->user_email)) {
            $atch_user_logname = isset($user_obj->display_name) ? $user_obj->display_name : '';
            $atch_user_logname = apply_filters('jobsearch_user_display_name', $atch_user_logname, $user_obj);
            $useremail = $user_obj->user_email;
        }
        echo json_encode(array('id' => $emp_id, 'username' => $atch_user_logname, 'email' => $useremail, 'phone' => $user_phone));

        wp_die();
    }

    add_action('wp_ajax_jobsearch_jobmeta_atchemp_throgh_popup', 'jobsearch_jobmeta_atchemp_throgh_popup');
}

if (!function_exists('jobsearch_load_memps_jobmeta_popupinlist')) {

    function jobsearch_load_memps_jobmeta_popupinlist()
    {

        global $wpdb;
        $page_num = absint($_POST['page_num']);
        if ($page_num > 1) {
            $offset = ($page_num - 1) * 10;
        } else {
            $offset = 10;
        }
        $keyword = ($_POST['keyword']);

        $attusers_query = "SELECT posts.ID,posts.post_title FROM $wpdb->posts AS posts";
        if ($keyword != '') {
            $attusers_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
            $attusers_query .= " ON postmeta.post_id = posts.ID";
        }
        $attusers_query .= " WHERE post_type='employer' AND post_status='publish'";
        if ($keyword != '') {
            $keyword = sanitize_text_field($keyword);
            $attusers_query .= " AND (posts.post_title LIKE '%{$keyword}%' OR (postmeta.meta_key='jobsearch_field_user_email' AND postmeta.meta_value='{$keyword}'))";
            $attusers_query .= " GROUP BY posts.ID";
        }
        $attusers_query .= " ORDER BY ID DESC LIMIT 10 OFFSET " . $offset;

        $attall_users = $wpdb->get_results($attusers_query, 'ARRAY_A');

        ob_start();
        if (!empty($attall_users)) {
            foreach ($attall_users as $attch_usritm) {
                ?>
                <li><a href="javascript:void(0);" class="atchuser-itm-btn"
                       data-id="<?php echo($attch_usritm['ID']) ?>"
                       data-name="<?php echo($attch_usritm['post_title']) ?>"><?php echo($attch_usritm['post_title']) ?></a>
                    <span></span></li>
                <?php
            }
        }

        $html = ob_get_clean();

        echo json_encode(array('html' => $html));

        wp_die();
    }

    add_action('wp_ajax_jobsearch_load_memps_jobmeta_popupinlist', 'jobsearch_load_memps_jobmeta_popupinlist');
    add_action('wp_ajax_nopriv_jobsearch_load_memps_jobmeta_popupinlist', 'jobsearch_load_memps_jobmeta_popupinlist');
}

if (!function_exists('jobsearch_jobmeta_serchemps_throgh_popup')) {

    function jobsearch_jobmeta_serchemps_throgh_popup()
    {

        global $wpdb;
        $keyword = ($_POST['keyword']);

        $attusers_query = "SELECT posts.ID,posts.post_title FROM $wpdb->posts AS posts";
        if ($keyword != '') {
            $attusers_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
            $attusers_query .= " ON postmeta.post_id = posts.ID";
        }
        $attusers_query .= " WHERE post_type='employer' AND post_status='publish'";
        if ($keyword != '') {
            $keyword = sanitize_text_field($keyword);
            $attusers_query .= " AND (posts.post_title LIKE '%{$keyword}%' OR (postmeta.meta_key='jobsearch_field_user_email' AND postmeta.meta_value='{$keyword}'))";
        }

        $attusers_query .= " GROUP BY posts.ID ORDER BY ID DESC LIMIT 10";

        $attall_users = $wpdb->get_results($attusers_query, 'ARRAY_A');

        $countusrs_query = "SELECT posts.ID FROM $wpdb->posts AS posts";
        if ($keyword != '') {
            $countusrs_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
            $countusrs_query .= " ON postmeta.post_id = posts.ID";
        }
        $countusrs_query .= " WHERE post_type='employer' AND post_status='publish'";
        if ($keyword != '') {
            $keyword = sanitize_text_field($keyword);
            $countusrs_query .= " AND (posts.post_title LIKE '%{$keyword}%' OR (postmeta.meta_key='jobsearch_field_user_email' AND postmeta.meta_value='{$keyword}'))";
            $countusrs_query .= " GROUP BY posts.ID";
        }
        //var_dump($countusrs_query);
        $totl_users_cols = $wpdb->get_col($countusrs_query);
        $totl_users = !empty($totl_users_cols) ? count($totl_users_cols) : 0;

        $total_pages = 1;
        if ($totl_users > 10) {
            $total_pages = ceil($totl_users / 10);
        }

        ob_start();
        if ($total_pages > 1) {
            ?>
            <a href="javascript:void(0);" class="lodmore-users-btn" data-tpages="<?php echo($total_pages) ?>"
               data-keyword="<?php echo($keyword) ?>"
               data-gtopage="2"><?php esc_html_e('Load More', 'wp-jobsearch') ?></a>
            <?php
        }
        $lodrhtml = ob_get_clean();

        ob_start();
        if (!empty($attall_users)) {
            foreach ($attall_users as $attch_usritm) {
                ?>
                <li>
                    <a href="javascript:void(0);" class="atchuser-itm-btn" data-id="<?php echo($attch_usritm['ID']) ?>"
                       data-name="<?php echo($attch_usritm['post_title']) ?>"><?php echo($attch_usritm['post_title']) ?></a>
                    <span></span>
                </li>
                <?php
            }
        } else {
            ?>
            <li><?php esc_html_e('No Employer Found.', 'wp-jobsearch') ?></li>
            <?php
        }

        $html = ob_get_clean();

        echo json_encode(array('html' => $html, 'count' => $totl_users, 'lodrhtml' => $lodrhtml));

        wp_die();
    }

    add_action('wp_ajax_jobsearch_jobmeta_serchemps_throgh_popup', 'jobsearch_jobmeta_serchemps_throgh_popup');
    add_action('wp_ajax_nopriv_jobsearch_jobmeta_serchemps_throgh_popup', 'jobsearch_jobmeta_serchemps_throgh_popup');
}

add_action('wpseo_register_extra_replacements', function () {
    wpseo_register_var_replacement('%%jobsearch_job_employer%%', 'jobsearch_yoast_jobemp_snippet_var', 'advanced', 'Job Employer');
});

function jobsearch_yoast_jobemp_snippet_var()
{
    $job_id = get_the_ID();
    $job_emp = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
    $value = get_the_title($job_emp);
    return $value;
}

add_filter('jobsearch_empname_in_joblistin', 'jobsearch_job_emp_name_html', 11, 3);
add_filter('jobsearch_empname_in_jobdetail_related', 'jobsearch_job_emp_name_html', 11, 3);
add_filter('jobsearch_empname_in_jobdetail', 'jobsearch_job_emp_name_html', 11, 3);

function jobsearch_job_emp_name_html($html, $job_id, $view = 'view1')
{
    $jobsearch__options = get_option('jobsearch_plugin_options');
    $disable_job_emp = isset($jobsearch__options['job_empcomp_disp']) ? $jobsearch__options['job_empcomp_disp'] : '';
    if ($disable_job_emp == 'on') {
        $html = '';
    }
    return $html;
}

add_action('wp_ajax_jobsearch_sectscount_add_to_spancons_action', 'jobsearch_sectscount_add_to_spancons_action');
add_action('wp_ajax_nopriv_jobsearch_sectscount_add_to_spancons_action', 'jobsearch_sectscount_add_to_spancons_action');

function jobsearch_sectscount_add_to_spancons_action()
{
    global $jobsearch_shortcode_jobs_frontend;
    if (isset($_POST['locat_ids'])) {
        $post_ids = $sh_atts = array();
        $all_post_ids = $jobsearch_shortcode_jobs_frontend->job_general_query_filter($post_ids, $sh_atts);
        $sect_view = isset($_POST['sect_view']) ? $_POST['sect_view'] : '';
        $loc_ids = $_POST['locat_ids'];
        $loc_ids_arr = explode(',', $loc_ids);
        if (!empty($loc_ids_arr)) {
            $found_jobs_arr = array();
            foreach ($loc_ids_arr as $sectid) {
                $term_sector = get_term_by('id', $sectid, 'sector');
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
                    'meta_query' => array(
                        array(
                            'relation' => 'OR',
                            array(
                                'key' => 'jobsearch_field_job_filled',
                                'compare' => 'NOT EXISTS',
                            ),
                            array(
                                array(
                                    'key' => 'jobsearch_field_job_filled',
                                    'value' => 'on',
                                    'compare' => '!=',
                                ),
                            ),
                        ),
                    ),
                );
                if (!empty($all_post_ids)) {
                    $job_args['post__in'] = $all_post_ids;
                } else {
                    $job_args['post__in'] = array(0);
                }
                $jobs_query = new WP_Query($job_args);
                $found_jobs = $jobs_query->found_posts;

                if ($sect_view == 'view5' || $sect_view == 'view7') {
                    $found_jobs_html = $found_jobs;
                } else if ($sect_view == 'view3' || $sect_view == 'view6' || $sect_view == 'view9') {
                    if ($found_jobs == 1) {
                        $found_jobs_html = '(' . sprintf(esc_html__('%s Job', 'wp-jobsearch'), $found_jobs) . ')';
                    } else {
                        $found_jobs_html = '(' . sprintf(esc_html__('%s Jobs', 'wp-jobsearch'), $found_jobs) . ')';
                    }
                } else {
                    if ($found_jobs == 1) {
                        $found_jobs_html = '(' . sprintf(esc_html__('%s Vacancy', 'wp-jobsearch'), $found_jobs) . ')';
                    } else {
                        $found_jobs_html = '(' . sprintf(esc_html__('%s Vacancies', 'wp-jobsearch'), $found_jobs) . ')';
                    }
                }
                $found_jobs_arr[] = $found_jobs_html;
                wp_reset_postdata();
            }
            $found_jobs_str = implode(',', $found_jobs_arr);
            echo json_encode(array('counts' => $found_jobs_str));
        }
    }
    die;
}

add_action('wp_ajax_jobsearch_jobtypecount_add_to_spancons_action', 'jobsearch_jobtypecount_add_to_spancons_action');
add_action('wp_ajax_nopriv_jobsearch_jobtypecount_add_to_spancons_action', 'jobsearch_jobtypecount_add_to_spancons_action');

function jobsearch_jobtypecount_add_to_spancons_action()
{
    global $jobsearch_shortcode_jobs_frontend;
    if (isset($_POST['locat_ids'])) {
        $post_ids = $sh_atts = array();
        $all_post_ids = $jobsearch_shortcode_jobs_frontend->job_general_query_filter($post_ids, $sh_atts);
        $sect_view = isset($_POST['sect_view']) ? $_POST['sect_view'] : '';
        $loc_ids = $_POST['locat_ids'];
        $loc_ids_arr = explode(',', $loc_ids);
        if (!empty($loc_ids_arr)) {
            $found_jobs_arr = array();
            foreach ($loc_ids_arr as $sectid) {
                $term_sector = get_term_by('id', $sectid, 'jobtype');
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
                    'meta_query' => array(
                        array(
                            'relation' => 'OR',
                            array(
                                'key' => 'jobsearch_field_job_filled',
                                'compare' => 'NOT EXISTS',
                            ),
                            array(
                                array(
                                    'key' => 'jobsearch_field_job_filled',
                                    'value' => 'on',
                                    'compare' => '!=',
                                ),
                            ),
                        ),
                    ),
                );
                if (!empty($all_post_ids)) {
                    $job_args['post__in'] = $all_post_ids;
                } else {
                    $job_args['post__in'] = array(0);
                }
                $jobs_query = new WP_Query($job_args);
                $found_jobs = $jobs_query->found_posts;

                if ($sect_view == 'view5' || $sect_view == 'view7') {
                    $found_jobs_html = $found_jobs;
                } else if ($sect_view == 'view3' || $sect_view == 'view6' || $sect_view == 'view9') {
                    if ($found_jobs == 1) {
                        $found_jobs_html = '(' . sprintf(esc_html__('%s Job', 'wp-jobsearch'), $found_jobs) . ')';
                    } else {
                        $found_jobs_html = '(' . sprintf(esc_html__('%s Jobs', 'wp-jobsearch'), $found_jobs) . ')';
                    }
                } else {
                    if ($found_jobs == 1) {
                        $found_jobs_html = '(' . sprintf(esc_html__('%s Vacancy', 'wp-jobsearch'), $found_jobs) . ')';
                    } else {
                        $found_jobs_html = '(' . sprintf(esc_html__('%s Vacancies', 'wp-jobsearch'), $found_jobs) . ')';
                    }
                }
                $found_jobs_arr[] = $found_jobs_html;
                wp_reset_postdata();
            }
            $found_jobs_str = implode(',', $found_jobs_arr);
            echo json_encode(array('counts' => $found_jobs_str));
        }
    }
    die;
}

// for rank math seo plugin
add_filter('cmb2_script_dependencies', function ($dependencies) {

    if (isset($dependencies['jquery-ui-datetimepicker'])) {
        unset($dependencies['jquery-ui-datetimepicker']);
    }
    return $dependencies;
}, 30);

function jobsearch_make_job_to_expiry_cron($job_id, $user_id)
{
    $up_post = array(
        'ID' => $job_id,
        'post_status' => 'draft',
    );
    wp_update_post($up_post);
    update_post_meta($job_id, 'jobsearch_field_job_status', 'pending');

    update_post_meta($job_id, 'jobsearch_job_single_exp_cron', '');

    //
    $c_user = get_user_by('ID', $user_id);
    do_action('jobsearch_job_expire_to_employer', $c_user, $job_id);
}

add_filter('jobsearch_listing_url_query_vals_result', function ($qry_val_value, $qry_var, $entity_type = 'job') {
    global $sitepress;
    $lang_code = '';
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $lang_code = $sitepress->get_current_language();
    }

    if ($qry_var == 'posted') {
        if ($qry_val_value == 'lasthour') {
            $qry_val_value = esc_html__('Last Hour', 'wp-jobsearch');
        } else if ($qry_val_value == 'last24') {
            $qry_val_value = esc_html__('Last 24 hours', 'wp-jobsearch');
        } else if ($qry_val_value == '7days') {
            $qry_val_value = esc_html__('Last 7 days', 'wp-jobsearch');
        } else if ($qry_val_value == '14days') {
            $qry_val_value = esc_html__('Last 14 days', 'wp-jobsearch');
        } else if ($qry_val_value == '30days') {
            $qry_val_value = esc_html__('Last 30 days', 'wp-jobsearch');
        } else if ($qry_val_value == 'all') {
            $qry_val_value = esc_html__('All', 'wp-jobsearch');
        }
    }
    if ($qry_var == 'sector_cat') {
        $sector_obj = get_term_by('slug', $qry_val_value, 'sector');
        if (isset($sector_obj->name)) {
            $qry_val_value = $sector_obj->name;
        }
    }
    if ($qry_var == 'job_type') {
        $jobtype_obj = get_term_by('slug', $qry_val_value, 'jobtype');
        if (isset($jobtype_obj->name)) {
            $qry_val_value = $jobtype_obj->name;
        }
    }
    if ($qry_var == 'sort-by') {
        if ($qry_val_value == 'recent') {
            $qry_val_value = esc_html__('Most Recent', 'wp-jobsearch');
        } else if ($qry_val_value == 'featured') {
            $qry_val_value = esc_html__('Featured', 'wp-jobsearch');
        } else if ($qry_val_value == 'alphabetical') {
            $qry_val_value = esc_html__('Alphabet Order', 'wp-jobsearch');
        } else if ($qry_val_value == 'most_viewed') {
            $qry_val_value = esc_html__('Most Viewed', 'wp-jobsearch');
        }
    }

    //custom fields
    $field_db_slug = "jobsearch_custom_field_" . $entity_type;
    $custom_all_fields_saved_data = get_option($field_db_slug);
    $count_node = time();
    $all_fields_name_str = '';
    if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
        $field_names_counter = 0;
        foreach ($custom_all_fields_saved_data as $cusf_key => $custom_field_saved_data) {
            $cusf_type = isset($custom_field_saved_data['type']) ? $custom_field_saved_data['type'] : '';
            $cusf_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
            $cusf_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
            $dropdown_field_options = isset($custom_field_saved_data['options']) ? $custom_field_saved_data['options'] : '';
            if (isset($dropdown_field_options['value']) && count($dropdown_field_options['value']) > 0 && $qry_var == $cusf_name) {
                $option_counter = 0;
                foreach ($dropdown_field_options['value'] as $option) {
                    if ($option != '') {
                        if ($dropdown_field_options['label'][$option_counter] != '') {
                            $option_val = $option;
                            $option_label = $dropdown_field_options['label'][$option_counter];
                            $option_label = stripslashes($option_label);
                            if ($qry_val_value == $option_val) {
                                $qry_val_value = apply_filters('wpml_translate_single_string', $option_label, 'Custom Fields', 'Dropdown Option Label - ' . $option_label, $lang_code);
                                break;
                            }
                        }
                    }
                    $option_counter++;
                }
            }
        }
    }

    return $qry_val_value;
}, 10, 3);

add_filter('jobsearch_listing_url_query_vars_result', function ($qry_var, $entity_type = 'job') {
    global $sitepress;

    $lang_code = '';
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $lang_code = $sitepress->get_current_language();
    }

    if ($qry_var == 'search_title') {
        $qry_var = esc_html__('Keyword Search', 'wp-jobsearch');
    }

    if ($qry_var == 'posted') {
        $qry_var = esc_html__('Date Posted', 'wp-jobsearch');
    }
    if ($qry_var == 'sector_cat') {
        $qry_var = esc_html__('Sector', 'wp-jobsearch');
    }
    if ($qry_var == 'job_type') {
        $qry_var = esc_html__('Job Type', 'wp-jobsearch');
    }
    if ($qry_var == 'loc_radius') {
        $qry_var = esc_html__('Radius', 'wp-jobsearch');
    }
    if ($qry_var == 'location') {
        $qry_var = esc_html__('Locations', 'wp-jobsearch');
    }
    if ($qry_var == 'location_location1') {
        $qry_var = esc_html__('Country', 'wp-jobsearch');
    }
    if ($qry_var == 'location_location2') {
        $qry_var = esc_html__('State', 'wp-jobsearch');
    }
    if ($qry_var == 'location_location3') {
        $qry_var = esc_html__('City', 'wp-jobsearch');
    }
    if ($qry_var == 'sort-by') {
        $qry_var = esc_html__('Sort By', 'wp-jobsearch');
    }
    if ($qry_var == 'job_salary_type' || $qry_var == 'candidate_salary_type') {
        $qry_var = esc_html__('Salary Types', 'wp-jobsearch');
    }
    if ($qry_var == 'jobsearch_field_job_salary' || $qry_var == 'jobsearch_field_candidate_salary') {
        $qry_var = esc_html__('Salary', 'wp-jobsearch');
    }
    //custom fields
    $field_db_slug = "jobsearch_custom_field_" . $entity_type;
    $custom_all_fields_saved_data = get_option($field_db_slug);
    $all_fields_name_str = '';
    if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
        $field_names_counter = 0;
        foreach ($custom_all_fields_saved_data as $cusf_key => $custom_field_saved_data) {
            $cusf_type = isset($custom_field_saved_data['type']) ? $custom_field_saved_data['type'] : '';
            $cusf_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
            $cusf_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
            if ($qry_var == $cusf_name) {
                $cusf_labl_str = ucfirst(str_replace(array('_', '-'), array(' ', ' '), $cusf_type)) . ' Field Label - ';
                $qry_var = apply_filters('wpml_translate_single_string', $cusf_label, 'Custom Fields', $cusf_labl_str . $cusf_label, $lang_code);
                break;
            }
        }
    }
    return apply_filters('jobsearch_listing_url_query_innervar_byfiltr', $qry_var, $entity_type);
}, 10, 2);

add_action('wp_ajax_jobsearch_external_job_applying_act', 'jobsearch_external_job_applying_act');
add_action('wp_ajax_nopriv_jobsearch_external_job_applying_act', 'jobsearch_external_job_applying_act');

function jobsearch_external_job_applying_act() {
    $job_id = $_POST['job_id'];

    $job_applied_list = get_post_meta($job_id, 'jobsearch_external_job_apply_data', true);
    $job_applied_list = !empty($job_applied_list) ? $job_applied_list : array();
    
    $to_add = true;
    
    $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
    $user_ip = jobsearch_get_user_ip_address();
    
    $found_col = array_search($user_ip, array_column($job_applied_list, 'ip_address'));
    
    if ($found_col !== false) {
        $to_add = false;
    }
    
    $applied_data = array(
        'ip_address' => $user_ip,
        'user_agent' => $user_agent,
        'time' => current_time('timestamp'),
    );
    if (is_user_logged_in()) {
        $cur_user_obj = wp_get_current_user();
        $user_id = get_current_user_id();
        $candidate_id = jobsearch_get_user_candidate_id($user_id);

        $user_email = $cur_user_obj->user_email;
        $found_col = array_search($user_email, array_column($job_applied_list, 'email'));

        if ($found_col !== false) {
            $to_add = false;
        }

        $applied_data['email'] = $user_email;
        if (get_post_type($candidate_id) == 'candidate') {
            $applied_data['name'] = get_the_title($candidate_id);
        } else {
            $applied_data['name'] = $cur_user_obj->display_name;
        }
    }
    if ($to_add) {
        $job_applied_list[] = $applied_data;
        update_post_meta($job_id, 'jobsearch_external_job_apply_data', $job_applied_list);
    }
    wp_send_json(array('status' => 'applied'));
}

function jobsearch_google_job_posting($job_id)
{
    global $jobsearch_currencies_list, $jobsearch_gdapi_allocation;
    $jobsearch__options = get_option('jobsearch_plugin_options');
    $google_jobs_posting = isset($jobsearch__options['google_jobs_posting']) ? $jobsearch__options['google_jobs_posting'] : '';

    $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';

    if ($google_jobs_posting == 'on') {
        $job_title = get_the_title($job_id);
        $job_obj = get_post($job_id);
        $job_desc = isset($job_obj->post_content) ? $job_obj->post_content : '';
        $job_desc = apply_filters('the_content', $job_desc);

        $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        $employer_post = get_post($job_employer_id);
        if (isset($employer_post->ID)) {
            $emp_user_id = jobsearch_get_employer_user_id($job_employer_id);
            $emp_user_obj = get_user_by('ID', $emp_user_id);
            $emp_user_url = isset($emp_user_obj->user_url) ? $emp_user_obj->user_url : '';
        }
        if (isset($emp_user_url) && $emp_user_url != '') {
            $emp_user_url = $emp_user_url;
        } else {
            $emp_user_url = home_url();
        }
        $employer_name = get_the_title($job_employer_id);
        $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'medium');
        $emp_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
        $emp_thumbnail_src = $emp_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $emp_thumbnail_src;

        $get_job_contry = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
        $get_job_region = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
        $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
        $get_job_full_adres = get_post_meta($job_id, 'jobsearch_field_location_address', true);
        $get_job_postalcode = get_post_meta($job_id, 'jobsearch_field_location_postalcode', true);

        if ($all_locations_type == 'api') {
            $api_contries_list = $jobsearch_gdapi_allocation::get_countries();
            if (is_array($api_contries_list) && $get_job_contry != '' && in_array($get_job_contry, $api_contries_list)) {
                $get_job_contry = array_search($get_job_contry, $api_contries_list);
            }
        }

        $the_full_addres = explode(', ', $get_job_full_adres);
        $adres_locality = isset($the_full_addres[0]) && $the_full_addres[0] != '' ? $the_full_addres[0] : $get_job_full_adres;
        if ($get_job_region == '') {
            $get_job_region = isset($the_full_addres[1]) && $the_full_addres[1] != '' ? $the_full_addres[1] : '';
        }
        if ($get_job_contry == '') {
            $get_job_contry = isset($the_full_addres[2]) && $the_full_addres[2] != '' ? $the_full_addres[2] : '';
        }

        $woo_currency = get_option('woocommerce_currency');

        $job_currency = get_post_meta($job_id, 'jobsearch_field_job_salary_currency', true);
        $job_currency = ($job_currency != '' && $job_currency != 'default' ? $job_currency : $woo_currency);

        if ($job_currency == '') {
            $job_currency = 'USD';
        }

        $_job_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);

        $_job_salary = $_job_salary > 0 ? $_job_salary : 0;
        $_job_salary = str_replace(array(','), array(''), $_job_salary);


        $_job_salary_max = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);

        $_job_salary_max = $_job_salary_max > 0 ? $_job_salary_max : 0;
        $_job_salary_max = str_replace(array(','), array(''), $_job_salary_max);
        if ($_job_salary_max < $_job_salary) {
            $_job_salary_max = $_job_salary;
        }

        $job_posted_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
        $job_expiry_date = get_post_meta($job_id, 'jobsearch_field_job_expiry_date', true);

        $job_types = wp_get_post_terms($job_id, 'jobtype');
        $job_type = isset($job_types[0]->name) ? $job_types[0]->name : 'CONTRACTOR';

        $job_salary_types = isset($jobsearch__options['job-salary-types']) ? $jobsearch__options['job-salary-types'] : '';
        $_job_salary_type = get_post_meta($job_id, 'jobsearch_field_job_salary_type', true);
        $salary_type_val_str = 'hour';
        if (!empty($job_salary_types)) {
            $slar_type_count = 1;
            foreach ($job_salary_types as $job_salary_typ) {
                if ($_job_salary_type == 'type_' . $slar_type_count) {
                    $salary_type_val_str = $job_salary_typ;
                }
                $slar_type_count++;
            }
        }

        if ($job_title != '' && $job_desc != '' && $job_posted_date > 0 && $job_expiry_date > 0) {
            ?>
            <script type="application/ld+json">
                {
                    "@context": "http://schema.org/",
                    "@type": "JobPosting",
                    "title": "<?php echo($job_title) ?>",
                    "description": "<?php echo esc_html__($job_desc,'wp-jobsearch') ?>",
                    "identifier": {
                        "@type": "PropertyValue",
                        "name": "<?php echo($employer_name) ?>",
                        "value": "<?php echo($job_employer_id) ?>"
                    },
                    "datePosted": "<?php echo date('Y-m-d', $job_posted_date) ?>",
                    "validThrough": "<?php echo date('Y-m-d', $job_expiry_date) ?>
                    T<?php echo date('H:i', $job_expiry_date) ?>
                    ",
                    "employmentType": "<?php echo ($job_type) ?>",
                    "hiringOrganization": {
                        "@type": "Organization",
                        "name": "<?php echo($employer_name) ?>",
                        "sameAs": "<?php echo esc_url($emp_user_url) ?>",
                        "logo": "<?php echo($emp_thumbnail_src) ?>"
                    },
                    "jobLocation": {
                        "@type": "Place",
                        "address": {
                            "@type": "PostalAddress",
                            "streetAddress": "<?php echo($adres_locality) ?>",
                            "addressLocality": "<?php echo($get_job_city) ?>",
                            "addressRegion": "<?php echo($get_job_region) ?>",
                            "postalCode": "<?php echo ($get_job_postalcode) ?>",
                            "addressCountry": "<?php echo($get_job_contry) ?>"
                        }
                    },
                    "baseSalary": {
                        "@type": "MonetaryAmount",
                        "currency": "<?php echo($job_currency) ?>",
                        "value": {
                            "@type": "QuantitativeValue",
                            "minValue": "<?php echo ($_job_salary) ?>",
                            "maxValue": "<?php echo ($_job_salary_max) ?>",
                            "unitText": "<?php echo ($salary_type_val_str) ?>"
                        }
                    }
                }


            </script>
            <?php
        }
    }
}