<?php

use WP_Jobsearch\Candidate_Profile_Restriction;

if (!function_exists('jobsearch_candidate_get_profile_image')) {

    function jobsearch_candidate_get_profile_image($candidate_id) {
        $post_thumbnail_id = '';
        if (isset($candidate_id) && $candidate_id != '' && has_post_thumbnail($candidate_id)) {
            $post_thumbnail_id = get_post_thumbnail_id($candidate_id);
        }
        return $post_thumbnail_id;
    }

}

if (!function_exists('jobsearch_candidate_get_company_name')) {

    function jobsearch_candidate_get_company_name($candidate_id, $before_title = '', $after_title = '') {
        $company_name_str = '';
        $candidate_field_user = get_post_meta($candidate_id, 'jobsearch_field_candidate_posted_by', true);
        if (isset($candidate_field_user) && $candidate_field_user != '') {
            $company_name_str = '<a href="' . get_permalink($candidate_field_user) . '">' . $before_title . get_the_title($candidate_field_user) . $after_title . '</a>';
        }
        return $company_name_str;
    }

}

add_action('jobsearch_user_data_save_onprofile', 'jobsearch_user_data_save_onprofile', 10, 3);

function jobsearch_user_data_save_onprofile($user_id, $post_id, $post_type = 'candidate') {

    $allow_fields = array(
        'jobsearch_field_user_phone',
        'jobsearch_field_candidate_jobtitle',
        'jobsearch_field_candidate_salary',
        'jobsearch_field_user_facebook_url',
        'jobsearch_field_user_twitter_url',
        'jobsearch_field_user_google_plus_url',
        'jobsearch_field_user_linkedin_url',
        'jobsearch_field_user_dribbble_url',
        'jobsearch_field_location_address',
    );

    $cand_custom_fields = get_option('jobsearch_custom_field_candidate');
    if (is_array($cand_custom_fields) && sizeof($cand_custom_fields) > 0) {
        $field_names_counter = 0;
        foreach ($cand_custom_fields as $f_key => $custom_field_saved_data) {
            $cusfield_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
            if ($cusfield_name != '') {
                $allow_fields[] = $cusfield_name;
            }
        }
    }

    $emp_custom_fields = get_option('jobsearch_custom_field_employer');
    if (is_array($emp_custom_fields) && sizeof($emp_custom_fields) > 0) {
        $field_names_counter = 0;
        foreach ($emp_custom_fields as $f_key => $custom_field_saved_data) {
            $cusfield_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
            if ($cusfield_name != '') {
                $allow_fields[] = $cusfield_name;
            }
        }
    }

    $allow_fields = apply_filters('jobsearch_user_metadata_saving_list', $allow_fields);
    $user_obj = get_user_by('ID', $user_id);
    if (isset($user_obj->ID) && get_post_type($post_id) == $post_type) {
        foreach ($allow_fields as $meta_key) {
            $meta_val = get_post_meta($post_id, $meta_key, true);
            update_user_meta($user_id, $meta_key, $meta_val);
        }
    }
    //
}

function jobsearch_candidate_img_url_comn($candidate_id, $dimen = '150') {
    $user_id = jobsearch_get_candidate_user_id($candidate_id);
    $user_gravatar_url = get_avatar_url($user_id, array('size' => 132));
    $user_avatar_dburl = get_post_meta($candidate_id, 'jobsearch_user_avatar_url', true);

    $user_def_avatar_url = '';
    if (isset($user_avatar_dburl['file_url']) && $user_avatar_dburl['file_url'] != '') {
        $user_img_name = $user_avatar_dburl['file_name'];
        $user_img_path = $user_avatar_dburl['file_path'];
        $filetype = $user_avatar_dburl['mime_type'];
        $file_ext = $filetype['ext'];
        $img_150_path = $user_img_path . '/user-img-150.' . $file_ext;
        $user_def_avatar_url = isset($user_avatar_dburl['orig_file_url']) ? $user_avatar_dburl['orig_file_url'] : '';
        if (file_exists($img_150_path)) {
            $user_def_avatar_url = apply_filters('wp_jobsearch_cand_profile_img_url', $user_def_avatar_url, $candidate_id, $dimen);
        } else {
            $user_def_avatar_url = '';
        }
    } else {
        $user_avatar_id = get_post_thumbnail_id($candidate_id);
        if ($user_avatar_id > 0) {
            $user_has_cimg = true;
            $def_img_size = 'thumbnail';
            $def_img_size = apply_filters('jobsearch_cand_dashside_pimg_size', $def_img_size);
            $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, $def_img_size);
            $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
        }
    }
    if ($user_def_avatar_url == '') {
        $user_def_avatar_url = $user_gravatar_url != '' ? $user_gravatar_url : jobsearch_candidate_image_placeholder();
    }
    return $user_def_avatar_url;
}

function jobsearch_candidate_covr_url_comn($candidate_id) {
    global $jobsearch_plugin_options;
    $user_cover_img_url = '';
    $user_avatar_dburl = get_post_meta($candidate_id, 'jobsearch_user_cover_imge', true);
    if (isset($user_avatar_dburl['file_url']) && $user_avatar_dburl['file_url'] != '') {
        $user_cover_img_url = $user_avatar_dburl['file_url'];
        $user_cover_img_url = apply_filters('wp_jobsearch_cand_ccovr_img_url', $user_cover_img_url, $candidate_id);
    } else if (class_exists('JobSearchMultiPostThumbnails')) {
        $cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url('candidate', 'cover-image', $candidate_id);
        if ($cover_image_src != '') {
            $user_cover_img_url = $cover_image_src;
        }
    }
    if ($user_cover_img_url == '') {
        $user_cover_img_url = isset($jobsearch_plugin_options['cand_default_coverimg']['url']) && $jobsearch_plugin_options['cand_default_coverimg']['url'] != '' ? $jobsearch_plugin_options['cand_default_coverimg']['url'] : '';
    }

    return $user_cover_img_url;
}

add_action('jobsearch_user_dash_instart_act', 'jobsearch_cand_movepimg_cover_sett', 15, 1);

function jobsearch_cand_movepimg_cover_sett($user_id) {
    $user_is_candidate = jobsearch_user_is_candidate($user_id);
    if ($user_is_candidate) {
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
        global $jobsearch_uploding_candimg, $jobsearch_download_locations;
        $jobsearch_download_locations = false;
        $jobsearch_uploding_candimg = true;
        add_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);

        $cand_thumb_id = get_post_thumbnail_id($candidate_id);
        $full_image = wp_get_attachment_image_src($cand_thumb_id, 'full');
        if (isset($full_image[0]) && $full_image[0] != '') {
            $img_path = get_attached_file($cand_thumb_id);
            $wp_upload_dir = wp_upload_dir();
            $img_url = $full_image[0];
            $img_base_name = basename($img_url);
            $filetype = wp_check_filetype($img_base_name, null);
            $file_ext = isset($filetype['ext']) ? $filetype['ext'] : '';
            $uplod_direc_path = $wp_upload_dir['path'];
            $uplod_direc_url = $wp_upload_dir['url'];
            $img_new_path = $uplod_direc_path . '/' . $img_base_name;
            @copy($img_path, $img_new_path);
            $new_img_url = $uplod_direc_url . '/' . $img_base_name;
            $file_uniqid = jobsearch_get_unique_folder_byurl($new_img_url);

            // image crop
            $crop_file_url = '';
            $image_editor = wp_get_image_editor($img_new_path);
            if (!is_wp_error($image_editor)) {

                $image_editor->resize(150, 150, true);

                $crop_file_name = $uplod_direc_path . '/user-img-150.' . $file_ext;
                $image_editor->save($crop_file_name);

                //
                $image_350 = wp_get_image_editor($img_new_path);
                $image_350->resize(350, 450, true);
                $crop_file_name = $uplod_direc_path . '/user-img-350.' . $file_ext;
                $image_350->save($crop_file_name);
                //

                $crop_file_url = $uplod_direc_url . '/user-img-150.' . $file_ext;
            }
            // end cropping

            $fileuplod_time = current_time('timestamp');

            $arg_arr = array(
                'file_name' => $img_base_name,
                'mime_type' => $filetype,
                'time' => $fileuplod_time,
                'orig_file_url' => $new_img_url,
                'file_url' => $crop_file_url,
                'file_path' => $uplod_direc_path,
                'file_id' => $file_uniqid,
            );
            update_post_meta($candidate_id, 'jobsearch_user_avatar_url', $arg_arr);

            wp_delete_attachment($cand_thumb_id, true);
        }
        if (class_exists('JobSearchMultiPostThumbnails')) {
            $cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url('candidate', 'cover-image', $candidate_id);
            if ($cover_image_src != '') {
                $user_cover_img_url = $cover_image_src;
                $cover_image_id = JobSearchMultiPostThumbnails::get_post_thumbnail_id('candidate', 'cover-image', $candidate_id);

                $img_path = get_attached_file($cover_image_id);
                $wp_upload_dir = wp_upload_dir();
                $img_url = $user_cover_img_url;
                $img_base_name = basename($img_url);
                $uplod_direc_path = $wp_upload_dir['path'];
                $uplod_direc_url = $wp_upload_dir['url'];
                $img_new_path = $uplod_direc_path . '/' . $img_base_name;
                @copy($img_path, $img_new_path);
                $new_img_url = $uplod_direc_url . '/' . $img_base_name;
                $file_uniqid = jobsearch_get_unique_folder_byurl($new_img_url);

                $filetype = wp_check_filetype($img_base_name, null);
                $fileuplod_time = current_time('timestamp');

                $arg_arr = array(
                    'file_name' => $img_base_name,
                    'mime_type' => $filetype,
                    'time' => $fileuplod_time,
                    'file_url' => $new_img_url,
                    'file_path' => $uplod_direc_path,
                    'file_id' => $file_uniqid,
                );
                update_post_meta($candidate_id, 'jobsearch_user_cover_imge', $arg_arr);

                wp_delete_attachment($cover_image_id, true);
            }
        }
        remove_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);
    }
}

add_filter('wp_jobsearch_cand_profile_img_url', 'wp_jobsearch_user_profile_img_url', 10, 3);

function wp_jobsearch_user_profile_img_url($url, $candidate_id = '', $size = '150') {

    $user_id = jobsearch_get_candidate_user_id($candidate_id);
    $user_gravatar_url = get_avatar_url($user_id, array('size' => 132));
    $url = $user_gravatar_url != '' ? $user_gravatar_url : jobsearch_candidate_image_placeholder();

    $attach_id = $candidate_id;
    if ($attach_id != '') {
        $attach_size = $size;
        if ($attach_id > 0 && get_post_type($attach_id) == 'candidate') {

            $user_avatar_dburl = get_post_meta($attach_id, 'jobsearch_user_avatar_url', true);
            if (isset($user_avatar_dburl['file_url']) && $user_avatar_dburl['file_url'] != '') {

                require_once(ABSPATH . 'wp-admin/includes/file.php');
                WP_Filesystem();
                global $wp_filesystem;

                $folder_path = $user_avatar_dburl['file_path'];
                $user_def_avatar_url = isset($user_avatar_dburl['orig_file_url']) ? $user_avatar_dburl['orig_file_url'] : '';

                $file_name = $user_avatar_dburl['file_name'];
                $filetype = $user_avatar_dburl['mime_type'];
                $file_ext = $filetype['ext'];
                if (!$file_ext) {
                    $file_ext = 'jpg';
                }

                if ($attach_size == 'full') {
                    $file_path = $folder_path . '/' . $file_name;
                    if ($user_def_avatar_url != '') {
                        $file_path = str_replace(get_site_url() . '/', ABSPATH, $user_def_avatar_url);
                    }
                } else {
                    $file_path = $folder_path . '/user-img-150.' . $file_ext;
                }

                $data = @$wp_filesystem->get_contents($file_path);
                $imge_base64 = 'data:image/' . $file_ext . ';base64,' . base64_encode($data);
                return $imge_base64;
            }
        }
    }

    return $url;
}

add_filter('wp_jobsearch_cand_ccovr_img_url', 'wp_jobsearch_user_ccover_img_url', 10, 2);

function wp_jobsearch_user_ccover_img_url($url, $candidate_id = '') {

    global $jobsearch_plugin_options;
    $user_coverimg_url = $user_cover_img_url = isset($jobsearch_plugin_options['cand_default_coverimg']['url']) && $jobsearch_plugin_options['cand_default_coverimg']['url'] != '' ? $jobsearch_plugin_options['cand_default_coverimg']['url'] : '';
    $url = $user_coverimg_url;

    $attach_id = $candidate_id;
    if ($attach_id != '') {
        if ($attach_id > 0 && get_post_type($attach_id) == 'candidate') {

            $user_avatar_dburl = get_post_meta($attach_id, 'jobsearch_user_cover_imge', true);
            if (isset($user_avatar_dburl['file_url']) && $user_avatar_dburl['file_url'] != '') {

                require_once(ABSPATH . 'wp-admin/includes/file.php');
                WP_Filesystem();
                global $wp_filesystem;

                $folder_path = $user_avatar_dburl['file_path'];
                $file_name = $user_avatar_dburl['file_name'];
                $filetype = $user_avatar_dburl['mime_type'];
                $file_ext = $filetype['ext'];

                $file_path = $folder_path . '/' . $file_name;

                $data = @$wp_filesystem->get_contents($file_path);
                $imge_base64 = 'data:image/' . $file_ext . ';base64,' . base64_encode($data);
                return $imge_base64;
            }
        }
    }

    return $url;
}

function jobsearch_candidate_detail_whatsapp_btn($candidate_id, $view = 'view_1') {

    global $jobsearch_plugin_options;

    $cand_profile_restrict = new Candidate_Profile_Restriction;

    $cand_whatsapp_msgallow = isset($jobsearch_plugin_options['cand_whatsapp_msgallow']) ? $jobsearch_plugin_options['cand_whatsapp_msgallow'] : '';
    $cand_whatsapp_defmsg = isset($jobsearch_plugin_options['cand_whatsapp_defmsg']) ? $jobsearch_plugin_options['cand_whatsapp_defmsg'] : '';

    $user_phone = get_post_meta($candidate_id, 'jobsearch_field_user_phone', true);

    if ($user_phone != '' && $cand_whatsapp_msgallow == 'on') {
        $message = $cand_whatsapp_defmsg;
        if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|phone', 'detail_page')) {
            ?>
            <div class="jobsearch-whatsapp-msgcon jobsearch_whatsap_<?php echo($view) ?>">
                <a href="https://wa.me/<?php echo jobsearch_esc_html($user_phone) ?>?text=<?php echo urlencode($message) ?>"
                   target="_blank">
                    <i class="fa fa-whatsapp"></i>
                    <small><?php esc_html_e('WhatsApp', 'wp-jobsearch') ?></small>
                </a>
            </div>
            <?php
        }
    }
}

add_filter('jobsearch_candidate_listing_item_title', 'jobsearch_candidate_listing_item_title', 10, 2);

function jobsearch_candidate_listing_item_title($title = '', $candidate_id = 0) {

    $cand_profile_restrict = new Candidate_Profile_Restriction;
    if ($cand_profile_restrict::cand_field_is_locked('profile_fields|display_name')) {
        $title = $cand_profile_restrict::cand_restrict_display_name();
    }
    return $title;
}

function jobsearch_get_candidate_salary_format($candidate_id = 0, $price = 0, $cur_tag = '') {

    global $jobsearch_currencies_list, $jobsearch_plugin_options;
    $post_custom_currency_switch = isset($jobsearch_plugin_options['post_custom_currency']) ? $jobsearch_plugin_options['post_custom_currency'] : '';
    $candidate_currency = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_currency', true);
    $candidate_currency = jobsearch_esc_html($candidate_currency);
    if ($candidate_currency != 'default' && $post_custom_currency_switch == 'on') {
        $candidate_currency = isset($jobsearch_currencies_list[$candidate_currency]['symbol']) ? $jobsearch_currencies_list[$candidate_currency]['symbol'] : jobsearch_get_currency_symbol();
    } else {
        $candidate_currency = 'default';
    }
    $cur_pos = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_pos', true);
    $candidate_salary_sep = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_sep', true);
    $candidate_salary_deci = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_deci', true);

    $cur_pos = jobsearch_esc_html($cur_pos);
    $candidate_salary_sep = jobsearch_esc_html($candidate_salary_sep);
    $candidate_salary_deci = jobsearch_esc_html($candidate_salary_deci);

    $candidate_salary_deci = $candidate_salary_deci < 10 ? absint($candidate_salary_deci) : 2;

    if ($candidate_currency == 'default') {
        $ret_price = jobsearch_get_price_format($price);
    } else {
        $price = $price > 0 ? trim($price) : 0;
        $price = preg_replace("/[^0-9.]+/iu", "", $price);
        if ($cur_pos == 'left_space') {
            $ret_price = ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . $candidate_currency . ' ' . ($cur_tag != '' ? '</' . $cur_tag . '>' : '') . number_format($price, $candidate_salary_deci, ".", $candidate_salary_sep);
        } else if ($cur_pos == 'right') {
            $ret_price = number_format($price, $candidate_salary_deci, ".", $candidate_salary_sep) . ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . $candidate_currency . ($cur_tag != '' ? '</' . $cur_tag . '>' : '');
        } else if ($cur_pos == 'right_space') {
            $ret_price = number_format($price, $candidate_salary_deci, ".", $candidate_salary_sep) . ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . ' ' . $candidate_currency . ($cur_tag != '' ? '</' . $cur_tag . '>' : '');
        } else {
            $ret_price = ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . $candidate_currency . ($cur_tag != '' ? '</' . $cur_tag . '>' : '') . number_format($price, $candidate_salary_deci, ".", $candidate_salary_sep);
        }
    }
    return $ret_price;
}

if (!function_exists('jobsearch_candidate_current_salary')) {

    function jobsearch_candidate_current_salary($id, $before_str = '', $after_str = '', $cur_tag = '') {
        global $jobsearch_plugin_options, $sitepress;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $post_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';

        $salary_str = $before_str;
        $_post_salary_type = get_post_meta($id, 'jobsearch_field_candidate_salary_type', true);
        $_candidate_salary = get_post_meta($id, 'jobsearch_field_candidate_salary', true);

        $_candidate_salary = jobsearch_esc_html($_candidate_salary);

        $salary_type_val_str = '';
        if (!empty($post_salary_types)) {
            $slar_type_count = 1;
            foreach ($post_salary_types as $post_salary_typ) {
                $post_salary_typ = apply_filters('wpml_translate_single_string', $post_salary_typ, 'JobSearch Options', 'Salary Type - ' . $post_salary_typ, $lang_code);
                if ($_post_salary_type == 'type_' . $slar_type_count) {
                    $salary_type_val_str = $post_salary_typ;
                }
                $slar_type_count++;
            }
        }

        if ($_candidate_salary != '') {
            $salary_str .= jobsearch_get_candidate_salary_format($id, $_candidate_salary, $cur_tag) . ($salary_type_val_str != '' ? ' / ' . $salary_type_val_str : '');
        }
        $salary_str .= $after_str;
        return $salary_str;
    }

}

if (!function_exists('jobsearch_candidate_age')) {

    function jobsearch_candidate_age($id) {
        global $jobsearch_plugin_options;

        $dob_dd = get_post_meta($id, 'jobsearch_field_user_dob_dd', true);
        $dob_mm = get_post_meta($id, 'jobsearch_field_user_dob_mm', true);
        $dob_yy = get_post_meta($id, 'jobsearch_field_user_dob_yy', true);

        //
        if ($dob_dd != '' && $dob_mm != '' && $dob_yy != '') {
            $dob_yy = str_replace('-', '', $dob_yy);
            if ($dob_yy > date('Y')) {
                $dob_yy = date('Y');
            }
            //date in mm/dd/yyyy format; or it can be in other formats as well
            $birthDate = "{$dob_mm}/{$dob_dd}/{$dob_yy}";
            //explode the date to get month, day and year
            $birthDate = explode("/", $birthDate);
            //get age from date or birthdate
            $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
            return $age;
        }

        $current_year = date('Y');
        if ($dob_yy > 0 && $dob_yy < $current_year) {
            $age = ($current_year - $dob_yy);
            return $age;
        }
    }

}

function jobsearch_detail_common_ad_code($args = array()) {
    global $jobsearch_plugin_options;
    if (isset($args['post_type']) && $args['post_type'] != '' && isset($args['position']) && $args['position'] != '') {
        $option_name = $args['post_type'] . '_detail_adcode_' . $args['position'];
        if (isset($jobsearch_plugin_options[$option_name]) && $jobsearch_plugin_options[$option_name] != '') {
            ?>
            <div class="jobsearch-det-adver-wrap <?php echo(isset($args['view']) && $args['view'] != '' ? $args['post_type'] . '_' . $args['position'] . '_' . $args['view'] : '') ?>">
                <div class="detail-adver-codecon">
            <?php echo do_shortcode($jobsearch_plugin_options[$option_name]); ?>
                </div>
            </div>
            <?php
        }
    }
}

add_action('wp_ajax_jobsearch_userreg_form_after_nonce', 'jobsearch_logreg_forms_add_nonce');
add_action('wp_ajax_nopriv_jobsearch_userreg_form_after_nonce', 'jobsearch_logreg_forms_add_nonce');

function jobsearch_logreg_forms_add_nonce() {

    $allow_args = array(
        'input' => array(
            'name' => array(),
            'value' => array(),
            'type' => array(),
        ),
    );

    $form_type = 'register-security';
    if (isset($_POST['secure_form']) && $_POST['secure_form'] != '') {
        $form_type = $_POST['secure_form'];
    }

    ob_start();
    wp_nonce_field('ajax-login-nonce', $form_type);
    $secur_field = ob_get_clean();
    echo wp_kses($secur_field, $allow_args);

    die;
}

if (!function_exists('jobsearch_candidate_get_all_candidatetypes')) {

    function jobsearch_candidate_get_all_candidatetypes($candidate_id, $link_class = 'jobsearch-option-btn', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '') {
        $candidate_type = wp_get_post_terms($candidate_id, 'candidatetype');
        ob_start();
        $html = '';
        if (!empty($candidate_type)) {
            $link_class_str = '';
            if ($link_class != '') {
                $link_class_str = 'class="' . $link_class . '"';
            }
            echo($before_tag);
            foreach ($candidate_type as $term) :
                $candidatetype_color = get_term_meta($term->term_id, 'jobsearch_field_candidatetype_color', true);
                $candidatetype_textcolor = get_term_meta($term->term_id, 'jobsearch_field_candidatetype_textcolor', true);
                $candidatetype_color_str = '';
                if ($candidatetype_color != '') {
                    $candidatetype_color_str = 'style="background-color: ' . esc_attr($candidatetype_color) . '; color: ' . esc_attr($candidatetype_textcolor) . ' "';
                }
                ?>
                <a <?php echo($link_class_str) ?> <?php echo($candidatetype_color_str); ?>>
                <?php
                echo($before_title);
                echo esc_html($term->name);
                echo($after_title);
                ?>
                </a>
                <?php
            endforeach;
            echo($after_tag);
        }
        $html .= ob_get_clean();
        return $html;
    }

}

if (!function_exists('jobsearch_candidate_not_allow_to_mod')) {

    function jobsearch_candidate_not_allow_to_mod($user_id = 0) {
        global $jobsearch_plugin_options;
        if ($user_id <= 0 && is_user_logged_in()) {
            $user_id = get_current_user_id();
        }
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        if ($user_is_candidate) {
            $demo_user_login = isset($jobsearch_plugin_options['demo_user_login']) ? $jobsearch_plugin_options['demo_user_login'] : '';
            $demo_user_mod = isset($jobsearch_plugin_options['demo_user_mod']) ? $jobsearch_plugin_options['demo_user_mod'] : '';
            $demo_candidate = isset($jobsearch_plugin_options['demo_candidate']) ? $jobsearch_plugin_options['demo_candidate'] : '';
            $_demo_user_obj = get_user_by('login', $demo_candidate);
            $_demo_user_id = isset($_demo_user_obj->ID) ? $_demo_user_obj->ID : '';
            if ($user_id == $_demo_user_id && $demo_user_login == 'on' && $demo_user_mod != 'on') {
                return true;
            }
        }
        return false;
    }

}

if (!function_exists('jobsearch_candidate_get_all_sectors')) {

    function jobsearch_candidate_get_all_sectors($candidate_id, $link_class = '', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '') {

        $sectors = wp_get_post_terms($candidate_id, 'sector');
        ob_start();
        $html = '';
        if (!empty($sectors)) {
            $term_ids_arr = array();
            $link_class_str = '';
            if ($link_class != '') {
                $link_class_str = 'class="' . $link_class . '"';
            }
            echo ($before_tag);
            $flag = 0;
            foreach ($sectors as $term) :
                $term_id = isset($term->term_id) ? $term->term_id : '';

                if (in_array($term_id, $term_ids_arr)) {
                    continue;
                }

                $term_ids_arr[] = $term_id;

                if ($flag > 0) {
                    echo ", ";
                }
                ?>
                <a class="<?php echo($link_class) ?>">
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

if (!function_exists('jobsearch_candidate_views_count')) {

    function jobsearch_candidate_views_count($postID) {
        $jobsearch_candidate_views_count = get_post_meta($postID, "jobsearch_candidate_views_count", true);
        if ($jobsearch_candidate_views_count == '') {
            $jobsearch_candidate_views_count = 0;
        }
        if (!isset($_COOKIE["jobsearch_candidate_views_count" . $postID])) {
            setcookie("jobsearch_candidate_views_count" . $postID, time() + 86400);
            $jobsearch_candidate_views_count = $jobsearch_candidate_views_count + 1;
            update_post_meta($postID, 'jobsearch_candidate_views_count', $jobsearch_candidate_views_count);
        }
    }

}

function jobsearch_post_city_contry_txtstr($post_id, $is_country = true, $is_state = false, $is_city = true, $is_full_address = false) {
    global $jobsearch_plugin_options, $sitepress;

    $all_locations_type = isset($jobsearch_plugin_options['all_locations_type']) ? $jobsearch_plugin_options['all_locations_type'] : '';

    $post_location_txt = '';

    $get_post_city = get_post_meta($post_id, 'jobsearch_field_location_location4', true);
    $get_post_city = jobsearch_esc_html($get_post_city);
    if ($get_post_city == '') {
        $get_post_city = get_post_meta($post_id, 'jobsearch_field_location_location3', true);
        $get_post_city = jobsearch_esc_html($get_post_city);
    }

    $get_post_state = get_post_meta($post_id, 'jobsearch_field_location_location2', true);
    $get_post_country = get_post_meta($post_id, 'jobsearch_field_location_location1', true);
    $full_address = get_post_meta($post_id, 'jobsearch_field_location_address', true);

    $get_post_state = jobsearch_esc_html($get_post_state);
    $get_post_country = jobsearch_esc_html($get_post_country);
    $full_address = jobsearch_esc_html($full_address);

    if ($all_locations_type == 'api') {
        $retrn_arr = array();
        if ($get_post_city != '' && $is_city) {
            $retrn_arr[] = $get_post_city;
        }
        if ($get_post_state != '' && $is_state) {
            $retrn_arr[] = $get_post_state;
        }
        if ($get_post_country != '' && $is_country) {
            $retrn_arr[] = $get_post_country;
        }
        if ($full_address != '' && $is_full_address) {
            $retrn_arr[] = $full_address;
        }
        if (!empty($retrn_arr)) {
            $post_location_txt = implode(', ', $retrn_arr);
        }
    } else {
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $sitepress_def_lang = $sitepress->get_default_language();
            $sitepress_curr_lang = $sitepress->get_current_language();

            $sitepress->switch_lang($sitepress_def_lang, true);
        }
        $post_country_tax = $get_post_country != '' ? jobsearch_get_custom_term_by('slug', $get_post_country, 'job-location') : '';
        $post_state_tax = $get_post_state != '' ? jobsearch_get_custom_term_by('slug', $get_post_state, 'job-location') : '';
        $post_city_tax = $get_post_city != '' ? jobsearch_get_custom_term_by('slug', $get_post_city, 'job-location') : '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $sitepress->switch_lang($sitepress_curr_lang, true);
            //var_dump($sitepress->get_current_language());
            if (is_object($post_city_tax) && isset($post_city_tax->term_id) && $is_city) {
                $get_post_cityid = $post_city_tax->term_id;
                $get_post_cityid = apply_filters('wpml_object_id', $get_post_cityid, 'job-location', true);
                $post_city_tax = jobsearch_get_custom_term_by('term_id', $get_post_cityid, 'job-location');
            }
            if (is_object($post_state_tax) && isset($post_state_tax->term_id) && $is_state) {
                $get_post_stateid = $post_state_tax->term_id;
                $get_post_stateid = apply_filters('wpml_object_id', $get_post_stateid, 'job-location', true);
                $post_state_tax = jobsearch_get_custom_term_by('term_id', $get_post_stateid, 'job-location');
            }
            if (is_object($post_country_tax) && isset($post_country_tax->term_id) && $is_country) {
                $get_post_contryid = $post_country_tax->term_id;
                $get_post_contryid = apply_filters('wpml_object_id', $get_post_contryid, 'job-location', true);
                $post_country_tax = jobsearch_get_custom_term_by('term_id', $get_post_contryid, 'job-location');
            }
        }

        $retrn_arr = array();
        if (is_object($post_city_tax) && isset($post_city_tax->name) && $is_city) {
            $retrn_arr[] = $post_city_tax->name;
        }
        if (is_object($post_state_tax) && isset($post_state_tax->name) && $is_state) {
            $retrn_arr[] = $post_state_tax->name;
        }
        if (is_object($post_country_tax) && isset($post_country_tax->name) && $is_country) {
            $retrn_arr[] = $post_country_tax->name;
        }
        if ($full_address != '' && $is_full_address) {
            $retrn_arr[] = $full_address;
        }
        if (!empty($retrn_arr)) {
            $post_location_txt = implode(', ', $retrn_arr);
        }
    }

    if ($post_location_txt == '' && $is_full_address) {
        $post_location_txt = get_post_meta($post_id, 'jobsearch_field_location_address', true);
        $post_location_txt = jobsearch_esc_html($post_location_txt);
    }

    return stripslashes($post_location_txt);
}

if (!function_exists('jobsearch_get_candidate_item_count')) {

    function jobsearch_get_candidate_item_count($left_filter_count_switch, $count_posts_in, $count_arr, $candidate_short_counter, $field_meta_key, $open_house = '') {
        global $wpdb;

        $total_num = 0;
        if ($left_filter_count_switch == 'yes') {
            if (!empty($count_posts_in) && is_array($count_posts_in)) {

                if (isset($count_arr[0]['key']) && $count_arr[0]['key'] != '' && !isset($count_arr[1]['key'])) {
                    $count_arr_o = $count_arr[0];
                    $get_meta_cond = get_meta_condition($count_arr_o);
                    $meta_post_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE {$get_meta_cond}");
                    if (!empty($meta_post_ids)) {
                        $to_countmeta_arr = array_intersect($count_posts_in, $meta_post_ids);
                        $total_num = !empty($to_countmeta_arr) ? count($to_countmeta_arr) : 0;
                    }
                } else if (isset($count_arr[0]['type']) && $count_arr[0]['type'] == 'numeric' && isset($count_arr[1]['key'])) {
                    $count_arr_o = $count_arr[0];
                    $count_arr_1 = $count_arr[1];
                    $the_meta_key = $count_arr_o['key'];
                    $from_meta_val = $count_arr_o['value'];
                    $to_meta_val = $count_arr_1['value'];
                    $meta_post_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='{$the_meta_key}' AND meta_value BETWEEN {$from_meta_val} AND {$to_meta_val}");
                    if (!empty($meta_post_ids)) {
                        $to_countmeta_arr = array_intersect($count_posts_in, $meta_post_ids);
                        $total_num = !empty($to_countmeta_arr) ? count($to_countmeta_arr) : 0;
                    }
                } else {
                    $total_num = !empty($count_posts_in) ? count($count_posts_in) : 0;
                }
            }
        }
        return $total_num;
    }

    function jobsearch_get_candidate_item_count_depricate($left_filter_count_switch, $args, $count_arr, $candidate_short_counter, $field_meta_key, $open_house = '') {
        if ($left_filter_count_switch == 'yes') {
            global $jobsearch_shortcode_candidates_frontend;

            // get all arguments from getting flters
            $left_filter_arr = array();
            $left_filter_arr = $jobsearch_shortcode_candidates_frontend->get_filter_arg($candidate_short_counter, $field_meta_key);
            if (!empty($count_arr)) {
                // check if count array has multiple condition
                foreach ($count_arr as $count_arr_single) {
                    $left_filter_arr[] = $count_arr_single;
                }
            }

            $post_ids = '';
            if (!empty($left_filter_arr)) {
                // apply all filters and get ids
                $post_ids = $jobsearch_shortcode_candidates_frontend->get_candidate_id_by_filter($left_filter_arr);
            }

            if (isset($_REQUEST['location']) && $_REQUEST['location'] != '' && !isset($_REQUEST['loc_polygon_path'])) {
                $post_ids = $jobsearch_shortcode_candidates_frontend->candidate_location_filter($post_ids);
                if (empty($post_ids)) {
                    $post_ids = array(0);
                }
            }

            $all_post_ids = $post_ids;
            if (!empty($all_post_ids)) {
                $args['post__in'] = $all_post_ids;
            }

            $args = apply_filters('jobsearch_candidates_listing_filter_args', $args);

            $restaurant_loop_obj = jobsearch_get_cached_obj('candidate_result_cached_loop_count_obj', $args, 12, false, 'wp_query');
            $restaurant_totnum = $restaurant_loop_obj->found_posts;
            return $restaurant_totnum;
        }
    }

}

if (!function_exists('jobsearch_candidate_skills_set_array')) {

    function jobsearch_candidate_skills_set_array() {

        $skills_array = array(
            'jobsearch_display_name' => array(
                'name' => esc_html__('Full Name', 'wp-jobsearch'),
            ),
            'jobsearch_user_img' => array(
                'name' => esc_html__('Profile Image', 'wp-jobsearch'),
            ),
            'jobsearch_post_title' => array(
                'name' => esc_html__('Job Title', 'wp-jobsearch'),
            ),
            'jobsearch_minimum_salary' => array(
                'name' => esc_html__('Salary', 'wp-jobsearch'),
            ),
            'jobsearch_sectors' => array(
                'name' => esc_html__('Sectors', 'wp-jobsearch'),
            ),
            'jobsearch_description' => array(
                'name' => esc_html__('Description', 'wp-jobsearch'),
            ),
            'jobsearch_social_network' => array(
                'name' => esc_html__('Social Network', 'wp-jobsearch'),
                'list' => array(
                    'jobsearch_facebook' => array(
                        'name' => esc_html__('Facebook', 'wp-jobsearch'),
                    ),
                    'jobsearch_twitter' => array(
                        'name' => esc_html__('Twitter', 'wp-jobsearch'),
                    ),
                    'jobsearch_linkedin' => array(
                        'name' => esc_html__('Linkedin', 'wp-jobsearch'),
                    ),
                ),
            ),
            'contact_info' => array(
                'name' => esc_html__('Contact Information', 'wp-jobsearch'),
                'list' => array(
                    'jobsearch_user_phone' => array(
                        'name' => esc_html__('Phone Number', 'wp-jobsearch'),
                    ),
                    'jobsearch_user_email' => array(
                        'name' => esc_html__('Email', 'wp-jobsearch'),
                    ),
                    'jobsearch_location_address' => array(
                        'name' => esc_html__('Complete Address', 'wp-jobsearch'),
                    ),
                ),
            ),
            'resume' => array(
                'name' => esc_html__('Resume', 'wp-jobsearch'),
                'list' => array(
                    'jobsearch_education_title' => array(
                        'name' => esc_html__('Education', 'wp-jobsearch'),
                    ),
                    'jobsearch_experience_title' => array(
                        'name' => esc_html__('Experience', 'wp-jobsearch'),
                    ),
                    'jobsearch_portfolio_title' => array(
                        'name' => esc_html__('Portfolio', 'wp-jobsearch'),
                    ),
                    'jobsearch_skill_title' => array(
                        'name' => esc_html__('Expertise', 'wp-jobsearch'),
                    ),
                    'jobsearch_award_title' => array(
                        'name' => esc_html__('Honors & Awards', 'wp-jobsearch'),
                    ),
                ),
            ),
            'cv_cover_letter' => array(
                'name' => esc_html__('CV &amp; Cover Letter', 'wp-jobsearch'),
                'list' => array(
                    'jobsearch_candidate_cv' => array(
                        'name' => esc_html__('CV', 'wp-jobsearch'),
                    ),
                    'jobsearch_cover_letter' => array(
                        'name' => esc_html__('Cover Letter', 'wp-jobsearch'),
                    ),
                ),
            ),
        );
        $skills_array = apply_filters('jobsearch_custom_fields_load_precentage_array', 'candidate', $skills_array);
        return $skills_array;
    }

}

if (!function_exists('jobsearch_candidate_skill_percent_count')) {

    function jobsearch_candidate_skill_percent_count($user_id, $return_type = 'return') {
        global $jobsearch_plugin_options;
        $skills_perc = 0;

        $msgs_array = array();

        $is_candidate = jobsearch_user_is_candidate($user_id);
        if ($is_candidate) {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            $skills_array = jobsearch_candidate_skills_set_array();
            foreach ($skills_array as $skill_key => $skill_val) {
                if ($skill_key == 'jobsearch_display_name') {
                    $this_opt_id = str_replace('jobsearch_', '', $skill_key) . '_skill';
                    $def_percentage = isset($jobsearch_plugin_options[$this_opt_id]) ? $jobsearch_plugin_options[$this_opt_id] : '';
                    $candidate_name_title = get_the_title($candidate_id);
                    if ($candidate_name_title != '') {
                        $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                    } else {
                        if ($def_percentage > 0) {
                            $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by Full Name.', 'wp-jobsearch'), $def_percentage . '%');
                        }
                    }
                }
                if ($skill_key == 'jobsearch_user_img') {
                    $this_opt_id = str_replace('jobsearch_', '', $skill_key) . '_skill';
                    $def_percentage = isset($jobsearch_plugin_options[$this_opt_id]) ? $jobsearch_plugin_options[$this_opt_id] : '';

                    $user_avatar_dburl = get_post_meta($candidate_id, 'jobsearch_user_avatar_url', true);

                    if (isset($user_avatar_dburl['file_url']) && $user_avatar_dburl['file_url'] != '') {
                        $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                    } else {
                        if ($def_percentage > 0) {
                            $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by Profile Image.', 'wp-jobsearch'), $def_percentage . '%');
                        }
                    }
                }
                if ($skill_key == 'jobsearch_post_title') {
                    $this_opt_id = str_replace('jobsearch_', '', $skill_key) . '_skill';
                    $def_percentage = isset($jobsearch_plugin_options[$this_opt_id]) ? $jobsearch_plugin_options[$this_opt_id] : '';
                    $candidate_post_title = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                    if ($candidate_post_title != '') {
                        $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                    } else {
                        if ($def_percentage > 0) {
                            $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by Job Title.', 'wp-jobsearch'), $def_percentage . '%');
                        }
                    }
                }
                if ($skill_key == 'jobsearch_minimum_salary') {
                    $this_opt_id = str_replace('jobsearch_', '', $skill_key) . '_skill';
                    $def_percentage = isset($jobsearch_plugin_options[$this_opt_id]) ? $jobsearch_plugin_options[$this_opt_id] : '';
                    $candidate_salary = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary', true);
                    if ($candidate_salary != '') {
                        $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                    } else {
                        if ($def_percentage > 0) {
                            $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by Salary.', 'wp-jobsearch'), $def_percentage . '%');
                        }
                    }
                }
                if ($skill_key == 'jobsearch_sectors') {
                    $this_opt_id = str_replace('jobsearch_', '', $skill_key) . '_skill';
                    $def_percentage = isset($jobsearch_plugin_options[$this_opt_id]) ? $jobsearch_plugin_options[$this_opt_id] : '';
                    $candidate_sectors = wp_get_post_terms($candidate_id, 'sector');
                    if (!empty($candidate_sectors)) {
                        $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                    } else {
                        if ($def_percentage > 0) {
                            $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by Sector.', 'wp-jobsearch'), $def_percentage . '%');
                        }
                    }
                }
                if ($skill_key == 'jobsearch_description') {
                    $this_opt_id = str_replace('jobsearch_', '', $skill_key) . '_skill';
                    $def_percentage = isset($jobsearch_plugin_options[$this_opt_id]) ? $jobsearch_plugin_options[$this_opt_id] : '';
                    $candidate_obj = get_post($candidate_id);
                    $candidate_desc = isset($candidate_obj->post_content) ? $candidate_obj->post_content : '';
                    if ($candidate_desc != '') {
                        $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                    } else {
                        if ($def_percentage > 0) {
                            $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by Description.', 'wp-jobsearch'), $def_percentage . '%');
                        }
                    }
                }
                if ($skill_key == 'jobsearch_social_network') {
                    if (isset($skill_val['list'])) {
                        foreach ($skill_val['list'] as $skill_social_key => $skill_social_val) {
                            $this_opt_id = str_replace('jobsearch_', '', $skill_social_key) . '_skill';
                            $def_percentage = isset($jobsearch_plugin_options[$this_opt_id]) ? $jobsearch_plugin_options[$this_opt_id] : '';
                            //
                            $this_meta_id = 'jobsearch_field_user_' . str_replace('jobsearch_', '', $skill_social_key) . '_url';
                            $candidate_social_val = get_post_meta($candidate_id, $this_meta_id, true);
                            if ($candidate_social_val != '') {
                                $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                            } else {
                                if ($def_percentage > 0) {
                                    $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by %s.', 'wp-jobsearch'), $def_percentage . '%', $skill_social_val['name']);
                                }
                            }
                        }
                    }
                }
                if ($skill_key == 'contact_info') {
                    if (isset($skill_val['list'])) {
                        foreach ($skill_val['list'] as $skill_contact_key => $skill_contact_val) {
                            $this_opt_id = str_replace('jobsearch_', '', $skill_contact_key) . '_skill';
                            $def_percentage = isset($jobsearch_plugin_options[$this_opt_id]) ? $jobsearch_plugin_options[$this_opt_id] : '';
                            //
                            if ($skill_contact_key != 'jobsearch_user_email' && $skill_contact_key != 'jobsearch_user_url') {
                                $this_meta_id = str_replace('jobsearch_', 'jobsearch_field_', $skill_contact_key);
                                $candidate_contact_val = get_post_meta($candidate_id, $this_meta_id, true);
                                if ($candidate_contact_val != '') {
                                    $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                                } else {
                                    if ($def_percentage > 0) {
                                        $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by %s.', 'wp-jobsearch'), $def_percentage . '%', $skill_contact_val['name']);
                                    }
                                }
                            } else {
                                $user_obj = get_user_by('ID', $user_id);
                                if ($skill_contact_key == 'jobsearch_user_email' && isset($user_obj->user_email) && $user_obj->user_email != '') {
                                    $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                                } else {
                                    if ($def_percentage > 0) {
                                        $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by %s.', 'wp-jobsearch'), $def_percentage . '%', $skill_contact_val['name']);
                                    }
                                }
                            }
                        }
                    }
                }
                if ($skill_key == 'resume') {
                    if (isset($skill_val['list'])) {
                        foreach ($skill_val['list'] as $skill_resume_key => $skill_resume_val) {
                            $this_opt_id = str_replace('jobsearch_', '', $skill_resume_key) . '_skill';
                            $def_percentage = isset($jobsearch_plugin_options[$this_opt_id]) ? $jobsearch_plugin_options[$this_opt_id] : '';
                            //
                            $this_meta_id = str_replace('jobsearch_', 'jobsearch_field_', $skill_resume_key);
                            $candidate_resume_val = get_post_meta($candidate_id, $this_meta_id, true);
                            if (!empty($candidate_resume_val)) {
                                $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                            } else {
                                if ($def_percentage > 0) {
                                    $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by %s.', 'wp-jobsearch'), $def_percentage . '%', $skill_resume_val['name']);
                                }
                            }
                        }
                    }
                }
                if ($skill_key == 'cv_cover_letter') {
                    if (isset($skill_val['list'])) {
                        foreach ($skill_val['list'] as $skill_cv_key => $skill_cv_val) {
                            $this_opt_id = str_replace('jobsearch_', '', $skill_cv_key) . '_skill';
                            $def_percentage = isset($jobsearch_plugin_options[$this_opt_id]) ? $jobsearch_plugin_options[$this_opt_id] : '';
                            //
                            if ($skill_cv_key == 'jobsearch_candidate_cv') {
                                $candidate_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);
                                $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
                                $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
                                if ($multiple_cv_files_allow == 'on' && !empty($ca_at_cv_files)) {
                                    $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                                } else if (!empty($candidate_cv_file)) {
                                    $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                                } else {
                                    if ($def_percentage > 0) {
                                        $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by CV.', 'wp-jobsearch'), $def_percentage . '%');
                                    }
                                }
                            }
                            if ($skill_cv_key == 'jobsearch_cover_letter') {
                                $candidate_cover = get_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', true);
                                if (!empty($candidate_cover)) {
                                    $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                                } else {
                                    if ($def_percentage > 0) {
                                        $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by Cover Letter.', 'wp-jobsearch'), $def_percentage . '%');
                                    }
                                }
                            }
                        }
                    }
                }

                //
                if ($skill_key == 'custom_fields') {
                    $field_db_slug = "jobsearch_custom_field_candidate";
                    $jobsearch_post_cus_fields = get_option($field_db_slug);
                    if (is_array($jobsearch_post_cus_fields) && sizeof($jobsearch_post_cus_fields) > 0) {
                        foreach ($jobsearch_post_cus_fields as $custom_field) {
                            $custom_meta_key = isset($custom_field['name']) ? $custom_field['name'] : '';
                            $custom_field_name = isset($custom_field['label']) ? $custom_field['label'] : '';

                            if ($custom_meta_key != '') {
                                $this_opt_id = str_replace('jobsearch_', '', $custom_meta_key) . '_skill';
                                $def_percentage = isset($jobsearch_plugin_options[$this_opt_id]) ? $jobsearch_plugin_options[$this_opt_id] : '';
                                //
                                if (isset($custom_field['type']) && $custom_field['type'] == 'upload_file') {
                                    $custom_meta_key = 'jobsearch_cfupfiles_' . $custom_meta_key;
                                }
                                $custom_f_val = get_post_meta($candidate_id, $custom_meta_key, true);
                                if (!empty($custom_f_val)) {
                                    $skills_perc += ($def_percentage > 0 ? $def_percentage : 0);
                                } else {
                                    if ($def_percentage > 0) {
                                        $msgs_array[] = sprintf(__('<small> %s </small> Increase profile score by %s.', 'wp-jobsearch'), $def_percentage . '%', $custom_field_name);
                                    }
                                }
                            }
                        }
                    }
                }
                //
            }
            update_post_meta($candidate_id, 'overall_skills_percentage', $skills_perc);
        }

        if ($skills_perc > 100) {
            $skills_perc = 100;
        }

        if ($return_type == 'return') {
            return $skills_perc;
        }
        if ($return_type == 'msgs') {
            return $msgs_array;
        }
    }

}

if (!function_exists('jobsearch_candidate_skills_set_plugin_option_array')) {

    add_filter('jobsearch_poptions_apply_jobsett_after', 'jobsearch_candidate_skills_set_plugin_option_array', 1);

    function jobsearch_candidate_skills_set_plugin_option_array($sections) {
        $skills_array = jobsearch_candidate_skills_set_array();
        $jobsearch_setting_options = array();
        $jobsearch_setting_options[] = array(
            'id' => 'jobsearch_candidate_skills',
            'type' => 'button_set',
            'title' => __('Profile Completion', 'wp-jobsearch'),
            'subtitle' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'desc' => '',
            'default' => 'off',
        );
        $jobsearch_setting_options[] = array(
            'id' => 'jobsearch-candidate-skills-percentage',
            'type' => 'text',
            'title' => __('Minimum Score Percentage', 'wp-jobsearch'),
            'subtitle' => __("Set Candidate Profile Score Percentage such as 50. If Candidate's Profile Score Percentage less than this Percentage then He/She will not able to apply any Job.", 'wp-jobsearch'),
            'desc' => '',
            'default' => '50',
        );
        $jobsearch_setting_options[] = array(
            'id' => 'cand_min_listpecent',
            'type' => 'text',
            'title' => __('Minimum Listing Percentage', 'wp-jobsearch'),
            'subtitle' => __("Set Candidate Profile Score Percentage such as 30. If Candidate's Profile Score Percentage less than this Percentage then He/She will not be display in list.", 'wp-jobsearch'),
            'desc' => '',
            'default' => '',
        );
        $jobsearch_setting_options[] = array(
            'id' => 'skill_low_set_color',
            'type' => 'color',
            'transparent' => false,
            'title' => __('Low Profile Color', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => __("Set color for Low Profile. Profile Score percentage from 0 to 25%.", 'wp-jobsearch'),
            'default' => '#ff5b5b',
        );
        $jobsearch_setting_options[] = array(
            'id' => 'skill_med_set_color',
            'type' => 'color',
            'transparent' => false,
            'title' => __('Basic Profile Color', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => __("Set color for Basic Profile. Profile Score percentage from 26% to 50%.", 'wp-jobsearch'),
            'default' => '#ffbb00',
        );
        $jobsearch_setting_options[] = array(
            'id' => 'skill_high_set_color',
            'type' => 'color',
            'transparent' => false,
            'title' => __('Professional Profile Color', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => __("Set color for Professional Profile. Profile Score percentage from 51% to 75%.", 'wp-jobsearch'),
            'default' => '#13b5ea',
        );
        $jobsearch_setting_options[] = array(
            'id' => 'skill_ahigh_set_color',
            'type' => 'color',
            'transparent' => false,
            'title' => __('Complete Profile Color', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => __("Set color for Complete Profile. Profile Score percentage from 76% to 100%.", 'wp-jobsearch'),
            'default' => '#40d184',
        );
        if (is_array($skills_array) && sizeof($skills_array) > 0) {

            foreach ($skills_array as $skills_array_key => $skills_array_set) {

                if (array_key_exists('list', $skills_array_set) && is_array($skills_array_set['list'])) {

                    $skill_sec_name = isset($skills_array_set['name']) ? $skills_array_set['name'] : '';
                    if ($skill_sec_name != '' && $skills_array_key != '') {
                        $jobsearch_setting_options[] = array(
                            'id' => "tab-settings-$skills_array_key-skill",
                            'type' => 'section',
                            'title' => $skill_sec_name,
                            'subtitle' => '',
                            'indent' => true,
                        );
                    }
                    foreach ($skills_array_set['list'] as $skill_list_key => $skill_list_set) {
                        $skill_name = isset($skill_list_set['name']) ? $skill_list_set['name'] : '';
                        if ($skill_list_key != '' && $skill_name != '') {

                            $this_opt_id = str_replace('jobsearch_', '', $skill_list_key) . '_skill';

                            $jobsearch_setting_options[] = array(
                                'id' => $this_opt_id,
                                'type' => 'text',
                                'title' => $skill_name,
                                'desc' => '',
                                'default' => '',
                            );
                        }
                    }
                } else {
                    $skill_name = isset($skills_array_set['name']) ? $skills_array_set['name'] : '';
                    if ($skills_array_key != '' && $skill_name != '') {
                        $this_opt_id = str_replace('jobsearch_', '', $skills_array_key) . '_skill';
                        $jobsearch_setting_options[] = array(
                            'id' => $this_opt_id,
                            'type' => 'text',
                            'title' => $skill_name,
                            'desc' => '',
                            'default' => '',
                        );
                    }
                }
            }
        }

        $sections = array(
            'title' => __('Profile Completion', 'wp-jobsearch'),
            'id' => 'required-skill-set',
            'desc' => '',
            'subsection' => true,
            'fields' => $jobsearch_setting_options,
        );
        return $sections;
    }

}

function jobsearch_upload_candidate_cv($Fieldname = 'file', $post_id = 0, $user_dir_filter = true) {

    global $jobsearch_uploding_resume, $jobsearch_download_locations;
    $jobsearch_download_locations = false;
    $jobsearch_uploding_resume = true;
    $jobsearch__options = get_option('jobsearch_plugin_options');

    if (isset($_FILES[$Fieldname]) && $_FILES[$Fieldname] != '') {
        if ($user_dir_filter === true) {
            add_filter('jobsearch_resume_upload_dir', 'jobsearch_upload_cvmod_path', 10, 1);
        }

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        $orig_upload_file = $upload_file = $_FILES[$Fieldname];

        //var_dump($upload_file);

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $allowed_file_types_list = isset($jobsearch__options['cand_cv_types']) ? $jobsearch__options['cand_cv_types'] : '';
        if (empty($allowed_file_types_list)) {
            $allowed_file_types = array(
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'pdf' => 'application/pdf',
            );
        } else {
            $allowed_file_types = array();
            if (in_array('image/jpeg', $allowed_file_types_list)) {
                $allowed_file_types['jpg|jpeg|jpe'] = 'image/jpeg';
                $allowed_file_types['png'] = 'image/png';
            }
            if (in_array('image/png', $allowed_file_types_list)) {
                $allowed_file_types['jpg|jpeg|jpe'] = 'image/jpeg';
                $allowed_file_types['png'] = 'image/png';
            }
            if (in_array('text/plain', $allowed_file_types_list)) {
                $allowed_file_types['txt|asc|c|cc|h'] = 'text/plain';
            }
            if (in_array('application/msword', $allowed_file_types_list)) {
                $allowed_file_types['doc'] = 'application/msword';
            }
            if (in_array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $allowed_file_types_list)) {
                $allowed_file_types['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            }
            if (in_array('application/pdf', $allowed_file_types_list)) {
                $allowed_file_types['pdf'] = 'application/pdf';
            }
            if (in_array('application/vnd.ms-excel', $allowed_file_types_list)) {
                $allowed_file_types['xla|xls|xlt|xlw'] = 'application/vnd.ms-excel';
            }
            if (in_array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $allowed_file_types_list)) {
                $allowed_file_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            }
        }

        $test_uploaded_file = is_uploaded_file($upload_file['tmp_name']);

        do_action('jobsearch_trigger_before_cv_file_upload', $orig_upload_file, $post_id);

        //
        $candidate_username = 'cv';
        if (get_post_type($post_id) == 'candidate') {
            $candidate_user_id = jobsearch_get_candidate_user_id($post_id);
            $candidate_user_obj = get_user_by('ID', $candidate_user_id);
            $candidate_username = $candidate_user_obj->user_login . '_cv';
        }

        $file_ex_name = $candidate_username . '_' . rand(1000000000, 9999999999) . '_';

        $file_ex_name = apply_filters('jobsearch_cand_cvupload_file_extlabel', $file_ex_name, $post_id);

        if (isset($upload_file['name'])) {
            $upload_file['name'] = $upload_file['name'];
            $upload_file['name'] = $file_ex_name . $upload_file['name'];
        }

        $status_upload = wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_file_types));

        if ($test_uploaded_file && !isset($status_upload['file'])) {
            //$status_upload = jobsearch_wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_file_types));
        }

        if (empty($status_upload['error'])) {

            do_action('jobsearch_act_after_cand_cv_upload', $status_upload, $post_id, $wp_upload_dir);

            $file_url = isset($status_upload['url']) ? $status_upload['url'] : '';

            $upload_file_path = $wp_upload_dir['path'] . '/' . basename($file_url);

            // Check the type of file. We'll use this as the 'post_mime_type'.
            $filetype = wp_check_filetype(basename($file_url), null);

            return $file_url;
        }

        if ($user_dir_filter === true) {
            remove_filter('jobsearch_resume_upload_dir', 'jobsearch_upload_cvmod_path', 10, 1);
        }
    }

    return false;
}

add_action('wp_ajax_wp_jobsearch_get_user_cv_file_download', 'wp_jobsearch_get_user_cv_file_download');
add_action('wp_ajax_nopriv_wp_jobsearch_get_user_cv_file_download', 'wp_jobsearch_get_user_cv_file_download');

function wp_jobsearch_get_user_cv_file_download() {

    $attachment_id = isset($_GET['file_id']) ? $_GET['file_id'] : '';
    $attachment_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

    $error_page_url = home_url('/404_error');

    if ($attachment_id != '') {

        $jobsearch__options = get_option('jobsearch_plugin_options');
        $multiple_cv_files_allow = isset($jobsearch__options['multiple_cv_uploads']) ? $jobsearch__options['multiple_cv_uploads'] : '';

        $is_attachment = false;
        if (is_numeric($attachment_id) && get_post_type($attachment_id) == 'attachment') {
            $is_attachment = true;
        }

        $uplod_dir = wp_upload_dir();
        $uplod_dirpath = $uplod_dir['basedir'];
        $file_path = '';
        // Get post from database 
        if ($is_attachment) {
            $file_post = get_post($attachment_id);
            $file_path = get_attached_file($attachment_id);
            $file_mimetype = $file_post->post_mime_type;
        } else {

            if ($multiple_cv_files_allow == 'on') {
                $ca_at_cv_files = get_post_meta($attachment_user_id, 'candidate_cv_files', true);

                $attach_key = 0;
                if (!empty($ca_at_cv_files)) {
                    $file_url = '';
                    $attach_counter = 0;
                    foreach ($ca_at_cv_files as $ca_at_cv_file) {
                        if (isset($ca_at_cv_file['file_id']) && $ca_at_cv_file['file_id'] == $attachment_id) {
                            $file_url = isset($ca_at_cv_file['file_url']) ? $ca_at_cv_file['file_url'] : '';
                            $attach_key = $attach_counter;
                        }
                        $attach_counter++;
                    }

                    if ($file_url == '') {
                        $file_url = isset($ca_at_cv_files[$attachment_id]['file_url']) ? $ca_at_cv_files[$attachment_id]['file_url'] : '';
                    }
                    $file_mimetype = isset($ca_at_cv_files[$attach_key]['mime_type']) ? $ca_at_cv_files[$attach_key]['mime_type'] : '';
                    $file_mimetype = isset($file_mimetype['type']) ? $file_mimetype['type'] : '';

                    $in_foldr_file = false;
                    if (strpos($file_url, 'jobsearch-user-files/')) {
                        $in_foldr_file = true;
                        $sub_file_url = substr($file_url, strpos($file_url, 'jobsearch-user-files/'), strlen($file_url));
                    } else if (strpos($file_url, 'jobsearch-resumes/')) {
                        $in_foldr_file = true;
                        $sub_file_url = substr($file_url, strpos($file_url, 'jobsearch-resumes/'), strlen($file_url));
                    }

                    if ($in_foldr_file) {
                        $file_path = $uplod_dirpath . '/' . $sub_file_url;
                    } else {
                        $file_path = str_replace(get_site_url() . '/', ABSPATH, $file_url);
                    }
                }
            } else {
                $candidate_cv_file = get_post_meta($attachment_user_id, 'candidate_cv_file', true);
                $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';
                $file_mimetype = isset($candidate_cv_file['mime_type']) ? $candidate_cv_file['mime_type'] : '';
                $file_mimetype = isset($file_mimetype['type']) ? $file_mimetype['type'] : '';
                if (strpos($file_url, 'jobsearch-user-files/')) {
                    $sub_file_url = substr($file_url, strpos($file_url, 'jobsearch-user-files/'), strlen($file_url));
                } else {
                    $sub_file_url = substr($file_url, strpos($file_url, 'jobsearch-resumes/'), strlen($file_url));
                }
                $file_path = $uplod_dirpath . '/' . $sub_file_url;
            }
        }

        if (!$file_path || !file_exists($file_path)) {
            wp_redirect($error_page_url);
        } else {

            if ($is_attachment) {
                $attch_parnt = get_post_ancestors($attachment_id);
                if (isset($attch_parnt[0])) {
                    $attch_parnt = $attch_parnt[0];
                }
            }

            //
            $downlod_err = 1;
            if (!is_user_logged_in()) {
                wp_redirect($error_page_url);
                exit;
            }
            $user_id = get_current_user_id();

            $cur_user_obj = wp_get_current_user();
            if (jobsearch_user_isemp_member($user_id)) {
                $downlod_err = 0;
            }
            $user_is_employer = jobsearch_user_is_employer($user_id);
            if ($user_is_employer) {
                $downlod_err = 0;
            }

            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            if ($user_is_candidate) {
                $user_cand_id = jobsearch_get_user_candidate_id($user_id);
                if ($is_attachment && $user_cand_id == $attch_parnt) {
                    $downlod_err = 0;
                } else if ($user_cand_id == $attachment_user_id) {
                    $downlod_err = 0;
                }
            }

            if (in_array('administrator', (array) $cur_user_obj->roles)) {
                $downlod_err = 0;
            }

            $downlod_err = apply_filters('jobsearch_candidate_getcvfile_acces_err', $downlod_err);

            if ($downlod_err == 1) {
                wp_redirect($error_page_url);
                exit;
            }

            header('Content-Description: File Transfer');
            //header('Content-Type: ' . $file_mimetype);
            header("Content-type: application/force-download");
            header('Content-Dispositon: attachment; filename="' . basename($file_path) . '"');
            header('Content-Transfer-Encoding: Binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . @filesize($file_path));

            ob_end_clean();
            flush();
            @readfile($file_path);
            exit;
        }
    } else {
        wp_redirect($error_page_url);
    }

    die;
}

add_filter('wp_jobsearch_user_cvfile_downlod_url', 'wp_jobsearch_user_cvfile_downlod_url', 10, 3);

function wp_jobsearch_user_cvfile_downlod_url($url, $attach_id = '', $candidate_id = '') {

    $url = add_query_arg(array('action' => 'wp_jobsearch_get_user_cv_file_download', 'file_id' => $attach_id, 'user_id' => $candidate_id), admin_url('admin-ajax.php'));

    return $url;
}

add_action('wp_ajax_wp_jobsearch_get_email_cv_file_download', 'wp_jobsearch_get_email_cv_file_download');
add_action('wp_ajax_nopriv_wp_jobsearch_get_email_cv_file_download', 'wp_jobsearch_get_email_cv_file_download');

function wp_jobsearch_get_email_cv_file_download() {

    $attachment_id = isset($_GET['file_id']) ? $_GET['file_id'] : '';
    $aap_id = isset($_GET['eaap_id']) ? $_GET['eaap_id'] : '';

    $error_page_url = home_url('/404_error');

    if (is_numeric($aap_id) && get_post_type($aap_id) == 'email_apps') {

        $file_path = get_post_meta($aap_id, 'jobsearch_app_att_file_path', true);

        if (!$file_path || !file_exists($file_path)) {
            wp_redirect($error_page_url);
        } else {

            //
            $downlod_err = 1;
            if (!is_user_logged_in()) {
                wp_redirect($error_page_url);
                exit;
            }
            $user_id = get_current_user_id();
            $cur_user_obj = wp_get_current_user();
            $user_is_employer = jobsearch_user_is_employer($user_id);
            if ($user_is_employer) {
                $downlod_err = 0;
            }

            if (in_array('administrator', (array) $cur_user_obj->roles)) {
                $downlod_err = 0;
            }

            if ($downlod_err == 1) {
                wp_redirect($error_page_url);
                exit;
            }

            header('Content-Description: File Transfer');
            header('Content-Type: ' . $file_mimetype);
            header('Content-Dispositon: attachment; filename="' . basename($file_path) . '"');
            header('Content-Transfer-Encoding: Binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . @filesize($file_path));

            ob_clean();
            flush();
            @readfile($file_path);
            exit;
        }
    } else {
        wp_redirect($error_page_url);
    }

    die;
}

add_filter('wp_jobsearch_email_cvfile_downlod_url', 'wp_jobsearch_email_cvfile_downlod_url', 10, 3);

function wp_jobsearch_email_cvfile_downlod_url($url, $attach_id = '', $email_app_id = '') {

    $url = add_query_arg(array('action' => 'wp_jobsearch_get_email_cv_file_download', 'file_id' => $attach_id, 'eaap_id' => $email_app_id), admin_url('admin-ajax.php'));

    return $url;
}

// get user package used apps
function jobsearch_pckg_order_used_apps($order_id = 0) {
    $apps_list_count = 0;
    if ($order_id > 0) {
        $total_apps = get_post_meta($order_id, 'num_of_apps', true);
        $apps_list = get_post_meta($order_id, 'jobsearch_order_apps_list', true);

        if (!empty($apps_list)) {
            $apps_list_count = count(explode(',', $apps_list));
        }
    }

    return $apps_list_count;
}

// get user package remaining apps
function jobsearch_pckg_order_remaining_apps($order_id = 0) {
    $remaining_apps = 0;
    if ($order_id > 0) {
        $total_apps = get_post_meta($order_id, 'num_of_apps', true);
        $used_apps = jobsearch_pckg_order_used_apps($order_id);

        $remaining_apps = $total_apps > $used_apps ? $total_apps - $used_apps : 0;
    }

    return $remaining_apps;
}

// check if user package subscribed
function jobsearch_app_pckg_is_subscribed($pckg_id = 0, $user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'candidate',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_apps = jobsearch_pckg_order_remaining_apps($order_post_id);
            if ($remaining_apps > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_candidate_first_subscribed_app_pkg($user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'ASC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_order_attach_with',
                'value' => 'package',
                'compare' => '=',
            ),
            array(
                'key' => 'package_type',
                'value' => 'candidate',
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_apps = jobsearch_pckg_order_remaining_apps($order_post_id);
            if ($remaining_apps > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user app package expired
function jobsearch_app_pckg_order_is_expired($order_id = 0) {

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s'))) {
        return true;
    }

    $remaining_apps = jobsearch_pckg_order_remaining_apps($order_post_id);
    if ($remaining_apps < 1) {
        return true;
    }
    return false;
}

// get used cvs
function jobsearch_candprofpckg_order_used_apps($order_id = 0) {
    $apps_list_count = 0;
    if ($order_id > 0) {
        $apps_list = get_post_meta($order_id, 'jobsearch_order_apps_list', true);

        if (!empty($apps_list)) {
            $apps_list_count = count(explode(',', $apps_list));
        }
    }

    return $apps_list_count;
}

// get remaining apps
function jobsearch_candprofpckg_order_remaining_apps($order_id = 0) {
    $remaining_apps = 0;
    if ($order_id > 0) {
        $total_apps = get_post_meta($order_id, 'candprof_num_apps', true);
        $used_apps = jobsearch_candprofpckg_order_used_apps($order_id);

        $remaining_apps = $total_apps > $used_apps ? $total_apps - $used_apps : 0;
    }

    return $remaining_apps;
}

// check if user package subscribed
function jobsearch_candprofpckg_is_subscribed($pckg_id = 0, $user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'candidate_profile',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_apps = jobsearch_candprofpckg_order_remaining_apps($order_post_id);
            if ($remaining_apps > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_candprof_first_pkg_subscribed($user_id = 0) {

    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'candidate_profile',
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_apps = jobsearch_candprofpckg_order_remaining_apps($order_post_id);
            if ($remaining_apps > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_candprofpckg_order_is_expired($order_id = 0) {

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s'))) {
        return true;
    }

    $remaining_apps = jobsearch_candprofpckg_order_remaining_apps($order_post_id);

    if ($remaining_apps < 1) {
        return true;
    }
    return false;
}

//
function jobsearch_cand_profile_pckg_is_subscribed($pckg_id = 0, $user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'candidate_profile',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (isset($pkgs_query_posts[0])) {
        return $pkgs_query_posts[0];
    }
    return false;
}

function jobsearch_cand_profile_pkg_is_expired($order_id = 0) {

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s'))) {
        return true;
    }

    return false;
}

//
//
function jobsearch_member_promote_profile_pkg_sub($pckg_id = 0, $user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '1',
        'post_status' => 'wc-completed',
        'order' => 'ASC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_order_attach_with',
                'value' => 'package',
                'compare' => '=',
            ),
            array(
                'key' => 'package_type',
                'value' => 'promote_profile',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => current_time('timestamp'),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;

    wp_reset_postdata();

    if (isset($pkgs_query_posts[0])) {
        return $pkgs_query_posts[0];
    }
    return false;
}

function jobsearch_member_first_promote_profile_pkg($user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '1',
        'post_status' => 'wc-completed',
        'order' => 'ASC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_order_attach_with',
                'value' => 'package',
                'compare' => '=',
            ),
            array(
                'key' => 'package_type',
                'value' => 'promote_profile',
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => current_time('timestamp'),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;

    wp_reset_postdata();

    if (isset($pkgs_query_posts[0])) {
        return $pkgs_query_posts[0];
    }
    return false;
}

function jobsearch_promote_profile_pkg_is_expired($order_id = 0) {

    $expiry_timestamp = get_post_meta($order_id, 'package_expiry_timestamp', true);

    if ($expiry_timestamp <= current_time('timestamp')) {
        return true;
    }
    return false;
}

function jobsearch_member_urgent_pkg_sub($pckg_id = 0, $user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '1',
        'post_status' => 'wc-completed',
        'order' => 'ASC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_order_attach_with',
                'value' => 'package',
                'compare' => '=',
            ),
            array(
                'key' => 'package_type',
                'value' => 'urgent_pkg',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => current_time('timestamp'),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;

    wp_reset_postdata();

    if (isset($pkgs_query_posts[0])) {
        return $pkgs_query_posts[0];
    }
    return false;
}

function jobsearch_member_first_urgent_pkg($user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '1',
        'post_status' => 'wc-completed',
        'order' => 'ASC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_order_attach_with',
                'value' => 'package',
                'compare' => '=',
            ),
            array(
                'key' => 'package_type',
                'value' => 'urgent_pkg',
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => current_time('timestamp'),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;

    wp_reset_postdata();

    if (isset($pkgs_query_posts[0])) {
        return $pkgs_query_posts[0];
    }
    return false;
}

function jobsearch_member_urgent_pkg_is_expired($order_id = 0) {

    $expiry_timestamp = get_post_meta($order_id, 'package_expiry_timestamp', true);

    if ($expiry_timestamp <= current_time('timestamp')) {
        return true;
    }
    return false;
}

add_action('jobsearch_before_add_pkge_fields_in_order', 'jobsearch_add_member_profilepkg_attach', 10, 3);

function jobsearch_add_member_profilepkg_attach($package_id, $order_id, $order_pkg_type) {
    $order_user_id = get_post_meta($order_id, 'jobsearch_order_user', true);
    if ($order_pkg_type == 'candidate_profile' || $order_pkg_type == 'employer_profile') {
        update_user_meta($order_user_id, 'att_profpckg_orderid', $order_id);
    }
}

add_action('jobsearch_before_add_pkge_fields_in_order', 'jobsearch_add_member_promote_profile_datetime', 10, 3);

function jobsearch_add_member_promote_profile_datetime($package_id, $order_id, $order_pkg_type) {
    $order_user_id = get_post_meta($order_id, 'jobsearch_order_user', true);
    if ($order_pkg_type == 'promote_profile') {
        $user_is_candidate = jobsearch_user_is_candidate($order_user_id);
        $user_is_employer = jobsearch_user_is_employer($order_user_id);

        if ($user_is_candidate) {
            $candidate_id = jobsearch_get_user_candidate_id($order_user_id);
            update_post_meta($candidate_id, 'promote_profile_substime', current_time('timestamp'));
            update_post_meta($candidate_id, 'att_promote_profile_pkgorder', $order_id);
        }
        if ($user_is_employer) {
            $employer_id = jobsearch_get_user_employer_id($order_user_id);
            update_post_meta($employer_id, 'promote_profile_substime', current_time('timestamp'));
            update_post_meta($employer_id, 'att_promote_profile_pkgorder', $order_id);
        }
    }
    if ($order_pkg_type == 'employer_profile') {
        $employer_id = jobsearch_get_user_employer_id($order_user_id);
        $pkg_with_promote = get_post_meta($package_id, 'jobsearch_field_emprof_promote_profile', true);
        if ($pkg_with_promote == 'on') {
            update_post_meta($employer_id, 'promote_profile_substime', current_time('timestamp'));
            update_post_meta($employer_id, 'att_promote_profile_pkgorder', $order_id);
        }
    }
    if ($order_pkg_type == 'candidate_profile') {
        $candidate_id = jobsearch_get_user_candidate_id($order_user_id);
        $pkg_with_promote = get_post_meta($package_id, 'jobsearch_field_candprof_promote_profile', true);
        if ($pkg_with_promote == 'on') {
            update_post_meta($candidate_id, 'promote_profile_substime', current_time('timestamp'));
            update_post_meta($candidate_id, 'att_promote_profile_pkgorder', $order_id);
        }
    }
}

add_action('jobsearch_before_add_pkge_fields_in_order', 'jobsearch_add_member_urgentpkg_attach', 10, 3);

function jobsearch_add_member_urgentpkg_attach($package_id, $order_id, $order_pkg_type) {
    $order_user_id = get_post_meta($order_id, 'jobsearch_order_user', true);
    if ($order_pkg_type == 'urgent_pkg') {
        $user_is_candidate = jobsearch_user_is_candidate($order_user_id);
        $user_is_employer = jobsearch_user_is_employer($order_user_id);

        if ($user_is_candidate) {
            $candidate_id = jobsearch_get_user_candidate_id($order_user_id);
            update_post_meta($candidate_id, 'urgent_pkg_substime', current_time('timestamp'));
            update_post_meta($candidate_id, 'att_urgent_pkg_orderid', $order_id);
        }
        if ($user_is_employer) {
            $employer_id = jobsearch_get_user_employer_id($order_user_id);
            update_post_meta($employer_id, 'urgent_pkg_substime', current_time('timestamp'));
            update_post_meta($employer_id, 'att_urgent_pkg_orderid', $order_id);
        }
    }
}

add_action('init', 'jobsearch_sectors_job_counts_initkey', 5);

function jobsearch_sectors_job_counts_initkey() {
    global $wpdb, $sitepress;
    
    $cachetime = 900;
    $transient = 'jobsearch_sectors_job_counts_initkey';
    if (function_exists('icl_object_id')) {
        $transient = 'jobsearch_sectors_job_counts_initkey_' . $sitepress->get_current_language();
    }

    $check_transient = get_transient($transient);
    if (empty($check_transient)) {
        $cats_query = "SELECT terms.term_id FROM $wpdb->terms AS terms";
        $cats_query .= " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) ";
        if (function_exists('icl_object_id')) {
            $trans_tble = $wpdb->prefix . 'icl_translations';
            $cats_query .= " LEFT JOIN $trans_tble AS icl_trans ON (terms.term_id = icl_trans.element_id)";
        }
        $cats_query .= " WHERE term_tax.taxonomy=%s";
        if (function_exists('icl_object_id')) {
            $cats_query .= " AND icl_trans.language_code='" . $sitepress->get_current_language() . "'";
        }
        $cats_query .= " GROUP BY terms.term_id";
        $get_db_terms = $wpdb->get_col($wpdb->prepare($cats_query, 'sector'));
        
        if (!empty($get_db_terms) && !is_wp_error($get_db_terms)) {
            foreach ($get_db_terms as $term_id) {
                if (!metadata_exists('term', $term_id, 'active_jobs_count')) {
                    update_term_meta($term_id, 'active_jobs_count', 0);
                }
            }
        }
        
        set_transient($transient, true, $cachetime);
    }
}

add_action('wp', 'jobsearch_remove_member_promote_profile');

function jobsearch_remove_member_promote_profile() {
    $cachetime = 900;
    $transient = 'jobsearch_remove_exp_member_promote';

    $current_time = current_time('timestamp');

    $check_transient = get_transient($transient);
    if (empty($check_transient)) {

        $emp_args = array(
            'post_type' => 'employer',
            'posts_per_page' => '100',
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => 'att_promote_profile_pkgorder',
                    'value' => '',
                    'compare' => '!=',
                ),
            ),
        );
        $emp_query = new WP_Query($emp_args);
        $emp_posts = $emp_query->posts;
        if (!empty($emp_posts)) {
            foreach ($emp_posts as $employer_id) {
                $mber_feature_bk = get_post_meta($employer_id, '_feature_mber_frmadmin', true);
                $att_promote_pckg = get_post_meta($employer_id, 'att_promote_profile_pkgorder', true);
                $pakg_type = get_post_meta($att_promote_pckg, 'package_type', true);
                if ($mber_feature_bk != 'yes') {
                    if ($pakg_type == 'employer_profile') {
                        $promote_expiry_timestamp = get_post_meta($att_promote_pckg, 'emprof_promote_expiry_timestamp', true);
                        if ($current_time > $promote_expiry_timestamp) {
                            delete_post_meta($employer_id, 'promote_profile_substime');
                            delete_post_meta($employer_id, 'att_promote_profile_pkgorder');
                        }
                    } else {
                        if (jobsearch_promote_profile_pkg_is_expired($att_promote_pckg)) {
                            delete_post_meta($employer_id, 'promote_profile_substime');
                            delete_post_meta($employer_id, 'att_promote_profile_pkgorder');
                        }
                    }
                }
            }
        }
        wp_reset_postdata();

        $cand_args = array(
            'post_type' => 'candidate',
            'posts_per_page' => '100',
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => 'att_promote_profile_pkgorder',
                    'value' => '',
                    'compare' => '!=',
                ),
            ),
        );
        $cand_query = new WP_Query($cand_args);
        $cand_posts = $cand_query->posts;
        if (!empty($cand_posts)) {
            foreach ($cand_posts as $candidate_id) {
                $mber_feature_bk = get_post_meta($candidate_id, '_feature_mber_frmadmin', true);
                $att_promote_pckg = get_post_meta($candidate_id, 'att_promote_profile_pkgorder', true);
                $pakg_type = get_post_meta($att_promote_pckg, 'package_type', true);
                if ($mber_feature_bk != 'yes') {
                    if ($pakg_type == 'candidate_profile') {
                        $promote_expiry_timestamp = get_post_meta($att_promote_pckg, 'candprof_promote_expiry_timestamp', true);
                        if ($current_time > $promote_expiry_timestamp) {
                            delete_post_meta($candidate_id, 'promote_profile_substime');
                            delete_post_meta($candidate_id, 'att_promote_profile_pkgorder');
                        }
                    } else {
                        if (jobsearch_promote_profile_pkg_is_expired($att_promote_pckg)) {
                            delete_post_meta($candidate_id, 'promote_profile_substime');
                            delete_post_meta($candidate_id, 'att_promote_profile_pkgorder');
                        }
                    }
                }
            }
        }
        wp_reset_postdata();
        //
        set_transient($transient, true, $cachetime);
    }
}

function jobsearch_member_promote_profile_iconlab($id = 0, $view = '') {

    $promote_pckg_subtime = get_post_meta($id, 'promote_profile_substime', true);
    $att_promote_pckg = get_post_meta($id, 'att_promote_profile_pkgorder', true);

    // form backend
    $mber_feature_bk = get_post_meta($id, '_feature_mber_frmadmin', true);

    $show_badge = false;

    if (!jobsearch_promote_profile_pkg_is_expired($att_promote_pckg)) {
        $show_badge = true;
    }

    if ($mber_feature_bk == 'yes') {
        $show_badge = true;
    } else if ($mber_feature_bk == 'no') {
        $show_badge = false;
    }

    if ($show_badge) {
        ob_start();
        if ($view == 'simple_employer_list_style3') {
            ?>
            <i class="top-companies-list-feature fa fa-star"></i>
        <?php } else if ($view == 'employer_list') { ?>
            <span class="promotepof-badgeemp"><?php esc_html_e('Featured', 'wp-jobsearch') ?> <i class="fa fa-star" title="<?php esc_html_e('Featured', 'wp-jobsearch') ?>"></i></span>
        <?php } else if ($view == 'employer_detv1') { ?>
            <span class="promotepof-detv1"><?php esc_html_e('Featured', 'wp-jobsearch') ?> <i class="fa fa-star" title="<?php esc_html_e('Featured', 'wp-jobsearch') ?>"></i></span>
        <?php } else if ($view == 'cand_listv1') { ?>
            <span class="promotepof-badge"><?php esc_html_e('Featured', 'wp-jobsearch') ?> <i class="fa fa-star" title="<?php esc_html_e('Featured', 'wp-jobsearch') ?>"></i></span>
        <?php } else if ($view == 'employer_list_grid') { ?>
            <span class="promotepof-badge-grid"><i class="fa fa-star jobsearch-tooltipcon" title="<?php esc_html_e('Featured', 'wp-jobsearch') ?>"></i></span>
        <?php } else { ?>
            <span class="promotepof-badge"><i class="fa fa-star" title="<?php esc_html_e('Featured', 'wp-jobsearch') ?>"></i></span>
            <?php
        }
        $html = ob_get_clean();
        echo apply_filters('jobsearch_member_promot_profile_star_html', $html, $id, $view);
    }
}

function jobsearch_cand_urgent_pkg_iconlab($id = 0, $view = '') {

    $pckg_subtime = get_post_meta($id, 'urgent_pkg_substime', true);
    $att_pckg = get_post_meta($id, 'att_urgent_pkg_orderid', true);

    // form backend
    $cand_urgent_bk = get_post_meta($id, '_urgent_cand_frmadmin', true);

    $show_badge = false;

    if (!jobsearch_promote_profile_pkg_is_expired($att_pckg)) {
        $show_badge = true;
    }

    if ($cand_urgent_bk == 'yes') {
        $show_badge = true;
    } else if ($cand_urgent_bk == 'no') {
        $show_badge = false;
    }

    if ($show_badge) {
        if ($view == 'cand_dclassic' || $view == 'cand_dmodren' || $view == 'cand_listv4') {
            ?>
            <span class="urgntpkg-detilbadge"><?php esc_html_e('urgent', 'wp-jobsearch') ?></span>
        <?php } else if ($view == 'cand_listv1') { ?>
            <span class="urgntpkg-candv1"><?php esc_html_e('urgent', 'wp-jobsearch') ?></span>
        <?php } else if ($view == 'cand_listv6') { ?>
            <span class="urgntpkg-candv5"><?php esc_html_e('urgent', 'wp-jobsearch') ?></span>

        <?php } else if ($view == 'cand_listv2') { ?>
            <div class="urgntpkg-candv2"><span><?php esc_html_e('urgent', 'wp-jobsearch') ?></span></div>
            <?php } else { ?>
            <span class="urgntpkg-badge"><?php esc_html_e('urgent', 'wp-jobsearch') ?></span>
            <?php
        }
    }
}

function jobsearch_empjobs_urgent_pkg_iconlab($emp_id = 0, $post_id = 0, $view = '') {

    $pckg_subtime = get_post_meta($emp_id, 'urgent_pkg_substime', true);
    $att_pckg = get_post_meta($emp_id, 'att_urgent_pkg_orderid', true);
    //
    $post_is_urgent = get_post_meta($post_id, 'jobsearch_field_urgent_job', true);

    // form backend
    $post_urgent_bk = get_post_meta($post_id, '_urgent_job_frmadmin', true);

    $show_badge = false;

    if (!jobsearch_promote_profile_pkg_is_expired($att_pckg)) {
        $show_badge = true;
    }

    if ($post_urgent_bk == 'yes') {
        $show_badge = true;
    } else if ($post_urgent_bk == 'no') {
        $show_badge = false;
    }

    if ($show_badge && $post_is_urgent == 'on') {
        ob_start();
        if ($view == 'post_v_grid' || $view == 'post_v_grid2') { ?>
            <span class="urgntpkg-gridv-badge"><small><?php esc_html_e('urgent', 'wp-jobsearch') ?></small></span>
            <?php } else if ($view == 'job_listv1') { ?>
            <span class="urgntpkg-jobv1"><?php esc_html_e('urgent', 'wp-jobsearch') ?></span>
            <?php } else if ($view == 'job_listin2') { ?>
            <div class="urgntpkg-job-listin2"><span><?php esc_html_e('Urgent', 'wp-jobsearch') ?></span></div>
            <?php } else if ($view == 'style9') { ?>
            <span class="urgntpkg-badge-style9"><?php esc_html_e('urgent', 'wp-jobsearch') ?></span>
            <?php } else if ($view == 'style10') { ?>
            <div class="urgntpkg-badge-style10"><span><?php esc_html_e('urgent', 'wp-jobsearch') ?></span></div>
        <?php } else { ?>
            <span class="urgntpkg-badge"> <small><?php esc_html_e('urgent', 'wp-jobsearch') ?></small></span>
            <?php
        }
        $html = ob_get_clean();
        echo apply_filters('jobsearch_job_urgent_tag_html', $html, $view);
    }
}

//

add_filter('jobsearch_user_attach_cv_file_url', 'jobsearch_user_attach_cv_file_url', 10, 3);

function jobsearch_user_attach_cv_file_url($cv_file_url, $candidate_id, $post_id = 0) {
    global $jobsearch_plugin_options;
    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
    if ($multiple_cv_files_allow == 'on') {
        $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
        if (!empty($ca_at_cv_files)) {
            $files_counter = 1;

            foreach ($ca_at_cv_files as $cv_file_key => $cv_file_val) {
                $file_attach_id = isset($cv_file_val['file_id']) ? $cv_file_val['file_id'] : '';
                $file_url = isset($cv_file_val['file_url']) ? $cv_file_val['file_url'] : '';
                $cv_primary = isset($cv_file_val['primary']) ? $cv_file_val['primary'] : '';
                if ($file_url != '') {
                    $cv_file_url = $file_url;
                    $to_attach_id = $file_attach_id;
                }
                if ($cv_primary == 'yes' && $file_url != '') {
                    $primcv_file_url = $file_url;
                    $prime_attach_id = $file_attach_id;
                }

                $files_counter++;
            }
            if (isset($prime_attach_id)) {
                $cv_file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $primcv_file_url, $prime_attach_id, $candidate_id);
            } else if (isset($to_attach_id)) {
                $cv_file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $cv_file_url, $to_attach_id, $candidate_id);
            }

            if ($post_id > 0) {
                $get_post_apps_cv_att = get_post_meta($post_id, 'post_apps_cv_att', true);
                $attach_cv_job = isset($get_post_apps_cv_att[$candidate_id]) ? $get_post_apps_cv_att[$candidate_id] : '';
                if ($attach_cv_job > 0 && is_numeric($attach_cv_job)) {
                    $att_file_post = get_post($attach_cv_job);
                    if (is_object($att_file_post) && isset($att_file_post->ID)) {
                        $file_attach_id = $att_file_post->ID;
                        $cv_file_url = $att_file_post->guid;
                        $cv_file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $cv_file_url, $file_attach_id, $candidate_id);
                    }
                }
            }
        }
    }
    return $cv_file_url;
}

add_filter('jobsearch_user_attach_cv_file_title', 'jobsearch_user_attach_cv_file_title', 10, 3);

function jobsearch_user_attach_cv_file_title($cv_file_title, $candidate_id, $post_id = 0) {
    global $jobsearch_plugin_options;
    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
    if ($multiple_cv_files_allow == 'on') {
        $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
        if (!empty($ca_at_cv_files)) {
            $files_counter = 1;
            foreach ($ca_at_cv_files as $cv_file_key => $cv_file_val) {
                $file_attach_id = isset($cv_file_val['file_id']) ? $cv_file_val['file_id'] : '';
                $file_url = isset($cv_file_val['file_url']) ? $cv_file_val['file_url'] : '';
                $cv_primary = isset($cv_file_val['primary']) ? $cv_file_val['primary'] : '';
                $att_file_post = get_post($file_attach_id);
                if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment' && is_object($att_file_post) && isset($att_file_post->ID)) {
                    if ($files_counter == 1) {
                        $file_path = get_attached_file($file_attach_id);
                        $cv_file_title = basename($file_path);
                    }
                    if ($cv_primary == 'yes') {
                        $file_path = get_attached_file($file_attach_id);
                        $cv_file_title = basename($file_path);
                    }
                } else {
                    $cv_file_title = isset($cv_file_val['file_name']) ? $cv_file_val['file_name'] : '';
                }
                $files_counter++;
            }
            if ($post_id > 0) {
                $get_post_apps_cv_att = get_post_meta($post_id, 'post_apps_cv_att', true);
                $attach_cv_job = isset($get_post_apps_cv_att[$candidate_id]) ? $get_post_apps_cv_att[$candidate_id] : '';
                if ($attach_cv_job > 0 && is_numeric($attach_cv_job)) {
                    $att_file_post = get_post($attach_cv_job);
                    if (is_object($att_file_post) && isset($att_file_post->ID)) {
                        $file_path = get_attached_file($attach_cv_job);
                        $cv_file_title = basename($file_path);
                    }
                }
            }
        }
    }
    return $cv_file_title;
}

add_action('jobsearch_cand_listin_sh_after_jobs_found', 'jobsearch_cand_listin_totalcands_found_html', 10, 3);
add_filter('jobsearch_cand_listin_top_jobfounds_html', 'jobsearch_cand_listin_top_jobfounds_html', 12, 4);
add_filter('jobsearch_cand_listin_before_top_jobfounds_html', 'jobsearch_cand_listin_before_top_jobfounds_html', 12, 4);
add_filter('jobsearch_cand_listin_after_sort_orders_html', 'jobsearch_cand_listin_after_sort_orders_html', 12, 4);

function jobsearch_cand_listin_totalcands_found_html($post_totnum, $candidate_short_counter, $atts) {

    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $per_page = isset($atts['candidate_per_page']) && absint($atts['candidate_per_page']) > 0 ? $atts['candidate_per_page'] : 0;
        if (isset($_REQUEST['per-page']) && $_REQUEST['per-page'] > 1) {
            $per_page = $_REQUEST['per-page'];
        }
        if ($per_page > 1) {
            $page_num = isset($_REQUEST['candidate_page']) && $_REQUEST['candidate_page'] > 1 ? $_REQUEST['candidate_page'] : 1;
            $start_frm = $page_num > 1 ? (($page_num - 1) * $per_page) : 1;
            $offset = $page_num > 1 ? ($page_num * $per_page) : $per_page;

            $offset = $offset > $post_totnum ? $post_totnum : $offset;

            $strt_toend_disp = absint($post_totnum) > 0 ? ($start_frm > 1 ? ($start_frm + 1) : $start_frm) . ' - ' . $offset : '0';
            ?>
            <div class="displayed-here"><?php printf(esc_html__('Displayed Here: %s Candidates', 'wp-jobsearch'), $strt_toend_disp) ?></div>
            <?php
        } else {
            $per_page = isset($atts['candidate_per_page']) && absint($atts['candidate_per_page']) > 0 ? $atts['candidate_per_page'] : $post_totnum;
            $per_page = $per_page > $post_totnum ? $post_totnum : $per_page;

            $strt_toend_disp = absint($post_totnum) > 0 ? '1 - ' . $per_page : '0';
            ?>
            <div class="displayed-here"><?php printf(esc_html__('Displayed Here: %s Candidates', 'wp-jobsearch'), $strt_toend_disp) ?></div>
            <?php
        }
    }
}

function jobsearch_cand_listin_top_jobfounds_html($html, $post_totnum, $candidate_short_counter, $atts) {
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $html = '';
    }
    return $html;
}

function jobsearch_cand_listin_before_top_jobfounds_html($html, $post_totnum, $candidate_short_counter, $atts) {
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
        echo absint($post_totnum) . '&nbsp;';
        if ($post_totnum == 1) {
            echo esc_html__('Candidate Found', 'wp-jobsearch');
        } else {
            echo esc_html__('Candidates Found', 'wp-jobsearch');
        }
        do_action('jobsearch_cand_listin_sh_after_jobs_found', $post_totnum, $candidate_short_counter, $atts);
        ?>
            </h2>
        </div>
        <?php
        echo '<div class="jobsearch-topsort-holder">';
        $html = ob_get_clean();
    }
    return $html;
}

function jobsearch_cand_listin_after_sort_orders_html($html, $post_totnum, $candidate_short_counter, $atts) {
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $html = '</div>';
    }
    return $html;
}

function jobsearch_phonenum_itltell_input($field_name, $rand_numb, $field_val = '', $atts = array()) {
    $set_before_vals = isset($atts['set_before_vals']) ? $atts['set_before_vals'] : '';
    $is_required_field = isset($atts['is_required']) ? $atts['is_required'] : '';

    $name_atr = $field_name;
    if (isset($atts['sepc_name']) && $atts['sepc_name'] != '') {
        $name_atr = $atts['sepc_name'];
    }
    ?>
    <div id="phon-fieldcon-<?php echo absint($rand_numb) ?>" class="phon-num-fieldcon phonefild-wout-valid">
        <input class="required<?php echo (isset($atts['classes']) && $atts['classes'] != '' ? ' ' . $atts['classes'] : '') ?>" <?php echo ($is_required_field ? 'required' : '') ?> name="<?php echo($name_atr) ?>"
               id="<?php echo($field_name) ?>_<?php echo absint($rand_numb) ?>"
               type="tel" <?php echo($field_val != '' ? 'value="' . $field_val . '"' : '') ?> placeholder="<?php _e('Phone Number', 'wp-jobsearch'); ?> <?php echo ($is_required_field ? ' *' : '') ?>">
        <input id="dial_code_<?php echo absint($rand_numb) ?>" type="hidden" name="dial_code">
        <input id="contry_code_<?php echo absint($rand_numb) ?>" type="hidden" name="contry_iso_code">
    </div>
    <?php
    if (isset($atts['field_icon']) && $atts['field_icon'] == 'yes') {
        ?>
        <i class="jobsearch-icon jobsearch-technology"></i>
        <?php
    }
    ?>
    <script>
        function onChangeContryCallAct<?php echo absint($rand_numb) ?>(iti_randadn) {
            var _this_<?php echo absint($rand_numb) ?> = jQuery('#phon-fieldcon-<?php echo absint($rand_numb) ?>').find('.intl-tel-input .intl-tel-input ul.country-list > li.active');
            var dial_code_val_<?php echo absint($rand_numb) ?> = _this_<?php echo absint($rand_numb) ?>.attr('data-dial-code');
            var contry_code_val_<?php echo absint($rand_numb) ?> = _this_<?php echo absint($rand_numb) ?>.attr('data-country-code');
            var this_contry_name_<?php echo absint($rand_numb) ?> = _this_<?php echo absint($rand_numb) ?>.find('.country-name').html();
            jQuery('#dialcode-con-<?php echo absint($rand_numb) ?>').html('+' + dial_code_val_<?php echo absint($rand_numb) ?>);
            jQuery('#dial_code_<?php echo absint($rand_numb) ?>').val(dial_code_val_<?php echo absint($rand_numb) ?>);
            jQuery('#contry_code_<?php echo absint($rand_numb) ?>').val(contry_code_val_<?php echo absint($rand_numb) ?>);
            jQuery('#contry-name-<?php echo absint($rand_numb) ?>').html('');
            if (typeof contry_code_val_<?php echo absint($rand_numb) ?> !== 'undefined') {
                iti_randadn.setCountry(contry_code_val_<?php echo absint($rand_numb) ?>);
            }
        }

        function jobseachPhoneValidInit<?php echo absint($rand_numb) ?>() {
            var input_<?php echo absint($rand_numb) ?> = document.querySelector("#<?php echo($field_name) ?>_<?php echo absint($rand_numb) ?>");
            var iti_<?php echo absint($rand_numb) ?> = intlTelInput(input_<?php echo absint($rand_numb) ?>);
            var reset_phone_field_<?php echo absint($rand_numb) ?> = function () {
                input_<?php echo absint($rand_numb) ?>.classList.remove("phone-input-error");
            };
    <?php
    if (isset($atts['set_condial_intrvl']) && $atts['set_condial_intrvl'] == 'yes') {
        ?>
        //            var afterLoadIntrvl<?php echo absint($rand_numb) ?> = setInterval(function () {
        //                jQuery('#phon-fieldcon-<?php echo absint($rand_numb) ?>').find('.contry-info-con .country-name-con').attr('id', 'contry-name-<?php echo absint($rand_numb) ?>');
        //                jQuery('#phon-fieldcon-<?php echo absint($rand_numb) ?>').find('.contry-info-con .dialcode-num-con').attr('id', 'dialcode-con-<?php echo absint($rand_numb) ?>');
        //                clearInterval(afterLoadIntrvl<?php echo absint($rand_numb) ?>);
        //            }, 2000);
        <?php
    }
    ?>
            // on blur: validate
            input_<?php echo absint($rand_numb) ?>.addEventListener('blur', function () {
                reset_phone_field_<?php echo absint($rand_numb) ?>();
                if (input_<?php echo absint($rand_numb) ?>.value.trim()) {
                    if (iti_<?php echo absint($rand_numb) ?>.isValidNumber()) {
                        jQuery(input_<?php echo absint($rand_numb) ?>).css({'border': '1px solid #efefef'});
                    } else {
                        input_<?php echo absint($rand_numb) ?>.classList.add("phone-input-error");
                        jQuery(input_<?php echo absint($rand_numb) ?>).css({'border': '1px solid #ff0000'});
                    }
                }
            });
    <?php
    if (wp_is_mobile()) {
        ?>
                jQuery(document).on('click', '.intl-tel-input ul.country-list > li', function () {
                    var _this_<?php echo absint($rand_numb) ?> = jQuery(this);
                    var dial_code_val_<?php echo absint($rand_numb) ?> = _this_<?php echo absint($rand_numb) ?>.attr('data-dial-code');
                    var contry_code_val_<?php echo absint($rand_numb) ?> = _this_<?php echo absint($rand_numb) ?>.attr('data-country-code');
                    var this_contry_name_<?php echo absint($rand_numb) ?> = _this_<?php echo absint($rand_numb) ?>.find('.country-name').html();
                    jQuery('#dialcode-con-<?php echo absint($rand_numb) ?>').html('+' + dial_code_val_<?php echo absint($rand_numb) ?>);
                    jQuery('#dial_code_<?php echo absint($rand_numb) ?>').val(dial_code_val_<?php echo absint($rand_numb) ?>);
                    jQuery('#contry_code_<?php echo absint($rand_numb) ?>').val(contry_code_val_<?php echo absint($rand_numb) ?>);
                    jQuery('#contry-name-<?php echo absint($rand_numb) ?>').html('');
                    iti_<?php echo absint($rand_numb) ?>.setCountry(contry_code_val_<?php echo absint($rand_numb) ?>);
                });
        <?php
    } else {
        ?>
                input_<?php echo absint($rand_numb) ?>.addEventListener("countrychange", function () {
                    onChangeContryCallAct<?php echo absint($rand_numb) ?>(iti_<?php echo absint($rand_numb) ?>);
                });
        <?php
    }
    ?>
            var iti_init_<?php echo absint($rand_numb) ?> = window.intlTelInput(input_<?php echo absint($rand_numb) ?>, {
                initialCountry: "auto",
                geoIpLookup: function (callback_<?php echo absint($rand_numb) ?>) {
                    jQuery.get('https://ipinfo.io', function () {
                    }, "jsonp").always(function (resp) {
                        var countryCode = (resp && resp.country) ? resp.country : "";
                        callback_<?php echo absint($rand_numb) ?>(countryCode);
                        iti_<?php echo absint($rand_numb) ?>.setCountry(countryCode);
                        var countryData_<?php echo absint($rand_numb) ?> = iti_<?php echo absint($rand_numb) ?>.getSelectedCountryData();
                        if (typeof countryData_<?php echo absint($rand_numb) ?>.dialCode !== 'undefined') {
                            //console.log(countryData_<?php echo absint($rand_numb) ?>);
                            //console.log('<?php echo absint($rand_numb) ?>');
                            jQuery("input[<?php echo('id=' . $field_name . '_' . $rand_numb) ?>]").before('<div class="contry-info-con">\
                                <span id="contry-name-<?php echo absint($rand_numb) ?>" class="country-name-con"></span>\
                                <span id="dialcode-con-<?php echo absint($rand_numb) ?>" class="dialcode-num-con">+' + countryData_<?php echo absint($rand_numb) ?>.dialCode + '</span>\
                            </div>');
                            jQuery('#dial_code_<?php echo absint($rand_numb) ?>').val(countryData_<?php echo absint($rand_numb) ?>.dialCode);
                            jQuery('#contry_code_<?php echo absint($rand_numb) ?>').val(countryData_<?php echo absint($rand_numb) ?>.iso2);
                        }
                    });
                },
                preferredCountries: [],
                utilsScript: "<?php echo jobsearch_plugin_get_url('js/utils.js') ?>?<?php echo time() ?>" // just for formatting/placeholders etc
                        });
                        var afterLoadIntFunc<?php echo absint($rand_numb) ?> = setInterval(function () {
                            var selectd_flag_class = jQuery('#phon-fieldcon-<?php echo absint($rand_numb) ?> > .intl-tel-input > .flag-container > .selected-flag .iti-flag').attr('class');
                            jQuery('#phon-fieldcon-<?php echo absint($rand_numb) ?> .selected-flag .iti-flag').attr('class', selectd_flag_class);
                            clearInterval(afterLoadIntFunc<?php echo absint($rand_numb) ?>);
                        }, 500);
                    }

                    jQuery(document).on('click', '#<?php echo($field_name) ?>_<?php echo absint($rand_numb) ?>', function () {
                        if (jQuery('#phon-fieldcon-<?php echo absint($rand_numb) ?>').hasClass('phonefild-wout-valid')) {
                            jQuery('#<?php echo($field_name) ?>_<?php echo absint($rand_numb) ?>').removeAttr('placeholder');
                            jobseachPhoneValidInit<?php echo absint($rand_numb) ?>();
                        }
                        jQuery('#phon-fieldcon-<?php echo absint($rand_numb) ?>').removeClass('phonefild-wout-valid');
                    });
    </script>
    <?php
}

add_filter('careerfy_subheader_post_page_title', 'jobsearch_careerfy_subheader_dash_titles', 11, 2);

function jobsearch_careerfy_subheader_dash_titles($title, $page_id) {
    global $jobsearch_plugin_options;

    $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
    $dashboard_page_id = jobsearch__get_post_id($user_dashboard_page, 'page');
    if ($dashboard_page_id == $page_id) {
        $user_id = get_current_user_id();
        $user_obj = get_user_by('ID', $user_id);
        $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
        $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);

        if ($user_displayname != '') {
            $title = $user_displayname;
        }
    }
    return $title;
}

add_filter('careerfy_subheader_postpage_bg_img', 'jobsearch_careerfy_subheader_userdash_bg_img', 11, 2);

function jobsearch_careerfy_subheader_userdash_bg_img($bg_img, $page_id) {
    global $jobsearch_plugin_options;

    $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
    $dashboard_page_id = jobsearch__get_post_id($user_dashboard_page, 'page');
    if ($dashboard_page_id == $page_id) {
        $user_id = get_current_user_id();
        $user_is_employer = jobsearch_user_is_employer($user_id);
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        if ($user_is_employer) {
            $bg_img = '';
            $employer_id = jobsearch_get_user_employer_id($user_id);
            if (class_exists('JobSearchMultiPostThumbnails')) {
                $employer_cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url('employer', 'cover-image', $employer_id);
                if ($employer_cover_image_src != '') {
                    $bg_img = $employer_cover_image_src;
                }
            }
            if ($bg_img == '') {
                $bg_img = isset($jobsearch_plugin_options['cand_default_coverimg']['url']) ? $jobsearch_plugin_options['cand_default_coverimg']['url'] : '';
            }
        } else if ($user_is_candidate) {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            $bg_img = jobsearch_candidate_covr_url_comn($candidate_id);
        }
    }
    return $bg_img;
}

add_action('admin_init', 'jobsearch_redirect_memb_from_admin_to_dash');
add_action('init', 'jobsearch_redirect_memb_from_admin_to_dash');

function jobsearch_redirect_memb_from_admin_to_dash() {
    global $pagenow;
    //
    if (is_super_admin() || current_user_can('administrator')) {
        return false;
    }
    if (current_user_can('jobsearch_candidate') || current_user_can('jobsearch_employer')) {

        if (is_admin()) {
            $to_view = false;
            if ($pagenow == 'admin-ajax.php' || $pagenow == 'async-upload.php') {
                $to_view = true;
            }

            if ($to_view === false) {
                $jobsearch__options = get_option('jobsearch_plugin_options');
                $page_id = isset($jobsearch__options['user-dashboard-template-page']) ? $jobsearch__options['user-dashboard-template-page'] : '';
                $page_id = jobsearch__get_post_id($page_id, 'page');
                $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');
                wp_redirect($page_url);
                exit();
            }
        }
    }
}

add_action('init', 'jobsearch_allow_memb_candidate_media');

function jobsearch_allow_memb_candidate_media() {

    $role = 'jobsearch_candidate';
    if (!current_user_can($role) || current_user_can('upload_files')) {
        return;
    }
    $subscriber = get_role($role);

    $subscriber = get_role($role);
    $subscriber->add_cap('upload_files');
    $subscriber->add_cap('edit_post');
    $subscriber->add_cap('edit_published_pages');
    $subscriber->add_cap('edit_others_pages');
    $subscriber->add_cap('edit_others_posts');
}

add_action('init', 'jobsearch_allow_memb_employer_media');

function jobsearch_allow_memb_employer_media() {
    $role = 'jobsearch_employer';
    if (!current_user_can($role) || current_user_can('upload_files')) {
        return;
    }
    $subscriber = get_role($role);
    $subscriber->add_cap('upload_files');
    $subscriber->add_cap('edit_post');
    $subscriber->add_cap('edit_published_pages');
    $subscriber->add_cap('edit_others_pages');
    $subscriber->add_cap('edit_others_posts');
}

add_filter('ajax_query_attachments_args', 'jobsearch_show_current_memberuser_attachments');

function jobsearch_show_current_memberuser_attachments($query) {
    $user_id = get_current_user_id();
    if ($user_id && !current_user_can('administrator') && !is_super_admin()) {
        $query['author'] = $user_id;
    }
    return $query;
}

add_filter('media_view_strings', 'jobsearch_show_media_tabs_strings_member', 20, 2);

function jobsearch_show_media_tabs_strings_member($strings, $post) {

    if (is_user_logged_in()) {
        $media_tabs_hide = false;
        $user_id = get_current_user_id();
        if ($user_id && !current_user_can('administrator') && !is_super_admin()) {
            $media_tabs_hide = true;
        }
        if ($media_tabs_hide) {
            $strings['createGalleryTitle'] = '';
            $strings['createPlaylistTitle'] = '';
            $strings['createVideoPlaylistTitle'] = '';
            $strings['setFeaturedImageTitle'] = '';
            $strings['setFeaturedImage'] = '';
            $strings['insertFromUrlTitle'] = '';
        }
    }
    return $strings;
}

//
add_filter('post_type_link', 'jobsearch_candpost_type_link_chnge', 1, 3);

function jobsearch_candpost_type_link_chnge($link, $post = 0) {
    $jobsearch__options = get_option('jobsearch_plugin_options');
    $cand_post_writeslug = isset($jobsearch__options['candidate_rewrite_slug']) && $jobsearch__options['candidate_rewrite_slug'] != '' ? $jobsearch__options['candidate_rewrite_slug'] : 'candidate';

    if ($post->post_type == 'candidate') {
        $candidate_id = $post->ID;

        $cand_profile_restrict = new Candidate_Profile_Restriction;

        $candidate_user_id = jobsearch_get_candidate_user_id($candidate_id);

        $view_candidate = true;
        $restrict_candidates = isset($jobsearch__options['restrict_candidates']) ? $jobsearch__options['restrict_candidates'] : '';

        $view_cand_type = 'fully';
        $emp_cvpbase_restrictions = isset($jobsearch__options['emp_cv_pkgbase_restrictions']) ? $jobsearch__options['emp_cv_pkgbase_restrictions'] : '';
        $restrict_cand_type = isset($jobsearch__options['restrict_candidates_for_users']) ? $jobsearch__options['restrict_candidates_for_users'] : '';
        if ($emp_cvpbase_restrictions == 'on' && $restrict_cand_type != 'only_applicants') {
            $view_cand_type = 'partly';
        }
        if ($restrict_candidates == 'on' && $view_cand_type == 'fully') {
            $view_candidate = false;

            $restrict_candidates_for_users = isset($jobsearch__options['restrict_candidates_for_users']) ? $jobsearch__options['restrict_candidates_for_users'] : '';

            if (is_user_logged_in()) {
                $cur_user_id = get_current_user_id();
                $cur_user_obj = wp_get_current_user();
                if (jobsearch_user_isemp_member($cur_user_id)) {
                    $employer_id = jobsearch_user_isemp_member($cur_user_id);
                    $cur_user_id = jobsearch_get_employer_user_id($employer_id);
                } else {
                    $employer_id = jobsearch_get_user_employer_id($cur_user_id);
                }
                $ucandidate_id = jobsearch_get_user_candidate_id($cur_user_id);
                $employer_dbstatus = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);
                if ($employer_id > 0 && $employer_dbstatus == 'on') {
                    $is_employer = true;
                    $is_applicant = false;
                    //
                    $employer_post_args = array(
                        'post_type' => 'job',
                        'posts_per_page' => '-1',
                        'post_status' => 'publish',
                        'fields' => 'ids',
                        'meta_query' => array(
                            array(
                                'key' => 'jobsearch_field_post_posted_by',
                                'value' => $employer_id,
                                'compare' => '=',
                            ),
                        ),
                    );
                    $employer_jobs_query = new WP_Query($employer_post_args);
                    $employer_jobs_posts = $employer_jobs_query->posts;
                    if (!empty($employer_jobs_posts) && is_array($employer_jobs_posts)) {
                        foreach ($employer_jobs_posts as $employer_post_id) {
                            $finded_result_list = jobsearch_find_index_user_meta_list($employer_post_id, 'jobsearch-user-jobs-applied-list', 'post_id', $candidate_user_id);
                            if (is_array($finded_result_list) && !empty($finded_result_list)) {
                                $is_applicant = true;
                                break;
                            }
                        }
                    }
                    //
                    if ($restrict_candidates_for_users == 'register_resume') {
                        $user_cv_pkg = jobsearch_employer_first_subscribed_cv_pkg($cur_user_id);
                        if (!$user_cv_pkg) {
                            $user_cv_pkg = jobsearch_allin_first_pkg_subscribed($cur_user_id, 'cvs');
                        }
                        if (!$user_cv_pkg) {
                            $user_cv_pkg = jobsearch_emprof_first_pkg_subscribed($cur_user_id, 'cvs');
                        }
                        if ($user_cv_pkg) {
                            $view_candidate = true;
                        } else {
                            if ($is_applicant) {
                                $view_candidate = true;
                            }
                        }
                    } else if ($restrict_candidates_for_users == 'only_applicants') {
                        if ($is_applicant) {
                            $view_candidate = true;
                        }
                    } else {
                        $view_candidate = true;
                    }
                } else if (in_array('administrator', (array) $cur_user_obj->roles)) {
                    $view_candidate = true;
                } else if ($ucandidate_id > 0 && $ucandidate_id == $candidate_id) {
                    $view_candidate = true;
                } else if ($restrict_candidates_for_users == 'register_empcand' && ($ucandidate_id > 0 || $employer_id > 0)) {
                    $view_candidate = true;
                }
            }
        } else if ($view_cand_type == 'partly') {
            $view_candidate = false;
            if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|display_name', 'detail_page')) {
                $view_candidate = true;
            }
        }

        if ($view_candidate) {
            return $link;
        } else {
            return home_url($cand_post_writeslug . '/' . $candidate_id);
        }
    } else {
        return $link;
    }
}

function jobsearch_listins_locfilter_manula_dropdown($loc_filter_collapse, $global_rand_id, $is_ajax, $post_type = 'job') {
    global $jobsearch_form_fields, $job_location_flag, $loc_counter, $jobsearch_plugin_options, $jobsearch_gdapi_allocation;
    $rand_num = rand(1000000, 9999999);
    $lang_code = '';
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $lang_code = $sitepress->get_current_language();
    }
    $required_fields_count = isset($jobsearch_plugin_options['jobsearch-location-required-fields-count']) ? $jobsearch_plugin_options['jobsearch-location-required-fields-count'] : 'all';
    $label_location1 = isset($jobsearch_plugin_options['jobsearch-location-label-location1']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location1'], 'JobSearch Options', 'Location First Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location1'], $lang_code) : esc_html__('Country', 'wp-jobsearch');
    $label_location2 = isset($jobsearch_plugin_options['jobsearch-location-label-location2']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location2'], 'JobSearch Options', 'Location Second Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location2'], $lang_code) : esc_html__('State', 'wp-jobsearch');
    $label_location3 = isset($jobsearch_plugin_options['jobsearch-location-label-location3']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location3'], 'JobSearch Options', 'Location Third Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location3'], $lang_code) : esc_html__('Region', 'wp-jobsearch');
    $label_location4 = isset($jobsearch_plugin_options['jobsearch-location-label-location4']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location4'], 'JobSearch Options', 'Location Forth Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location4'], $lang_code) : esc_html__('City', 'wp-jobsearch');

    $loc_location1 = isset($_REQUEST['location_location1']) ? $_REQUEST['location_location1'] : '';
    $loc_location2 = isset($_REQUEST['location_location2']) ? $_REQUEST['location_location2'] : '';
    $loc_location3 = isset($_REQUEST['location_location3']) ? $_REQUEST['location_location3'] : '';
    $loc_location4 = isset($_REQUEST['location_location4']) ? $_REQUEST['location_location4'] : '';

    $please_select = esc_html__('Please select', 'wp-jobsearch');
    $location_location1 = array('' => $please_select . ' ' . $label_location1);
    $location_location2 = array('' => $please_select . ' ' . $label_location2);
    $location_location3 = array('' => $please_select . ' ' . $label_location3);
    $location_location4 = array('' => $please_select . ' ' . $label_location4);
    $location_obj = jobsearch_custom_get_terms('job-location');
    foreach ($location_obj as $country_arr) {
        $location_location1[$country_arr->slug] = $country_arr->name;
    }
    ?>
    <div class="jobsearch-checkbox-toggle"
         style="display: <?php echo($loc_filter_collapse == 'yes' ? 'none' : 'block') ?>;">
        <script>
    <?php
    if ($is_ajax) {
        ?>
                if (jQuery('.filter_location_location1').length > 0) {
                    jQuery('.filter_location_location1').change();
                }
        <?php
    } else {
        ?>
                jQuery(document).ready(function () {
                    if (jQuery('.filter_location_location1').length > 0) {
                        jQuery('.filter_location_location1').trigger('change');
                    }
                });
        <?php
    }
    ?>
        </script>
        <ul class="jobsearch-row jobsearch-employer-profile-form">
            <li class="jobsearch-column-12">
                <label><?php echo esc_html($label_location1) ?></label>
                <div class="jobsearch-profile-select">
    <?php
    $field_params = array(
        'classes' => 'filter_location_location1 selectize-select',
        'id' => 'location_location1_' . $rand_num,
        'cus_name' => 'location_location1',
        'options' => $location_location1,
        'force_std' => $loc_location1,
        'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location2 . '" data-nextfieldval="' . $loc_location2 . '"',
    );
    $jobsearch_form_fields->select_field($field_params);
    ?>
                </div>
            </li>
    <?php
    if ($required_fields_count > 1 || $required_fields_count == 'all') {
        ?>
                <li class="jobsearch-column-12">
                    <label><?php echo esc_html($label_location2) ?></label>
                    <div class="jobsearch-profile-select">
        <?php
        $field_params = array(
            'classes' => 'filter_location_location2 location_location2_selectize',
            'id' => 'location_location2_' . $rand_num,
            'cus_name' => 'location_location2',
            'options' => $location_location2,
            'force_std' => $loc_location2,
            'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location3 . '" data-nextfieldval="' . $loc_location3 . '"',
        );
        $jobsearch_form_fields->select_field($field_params);
        ?>
                        <span class="jobsearch-field-loader location_location2_<?php echo absint($rand_num); ?>"></span>
                    </div>
                </li>
        <?php
    }
    if ($required_fields_count > 2 || $required_fields_count == 'all') {
        ?>
                <li class="jobsearch-column-12">
                    <label><?php echo esc_html($label_location3) ?></label>
                    <div class="jobsearch-profile-select">
        <?php
        $field_params = array(
            'classes' => 'filter_location_location3 location_location3_selectize',
            'id' => 'location_location3_' . $rand_num,
            'cus_name' => 'location_location3',
            'options' => $location_location3,
            'force_std' => $loc_location3,
            'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location4 . '" data-nextfieldval="' . $loc_location4 . '"',
        );
        $jobsearch_form_fields->select_field($field_params);
        ?>
                        <span class="jobsearch-field-loader location_location3_<?php echo absint($rand_num); ?>"></span>
                    </div>
                </li>
    <?php }
    if ($required_fields_count > 3 || $required_fields_count == 'all') {
        ?>
                <li class="jobsearch-column-12">
                    <label><?php echo esc_html($label_location4) ?></label>
                    <div class="jobsearch-profile-select">
        <?php
        $field_params = array(
            'classes' => 'filter_location_location4 location_location4_selectize',
            'id' => 'location_location4_' . $rand_num,
            'cus_name' => 'location_location4',
            'options' => $location_location4,
            'force_std' => $loc_location4,
            'ext_attr' => ' data-randid="' . $rand_num . '"',
        );
        $jobsearch_form_fields->select_field($field_params);
        ?>
                        <span class="jobsearch-field-loader location_location4_<?php echo absint($rand_num); ?>"></span>
                    </div>
                </li>
                        <?php
                    }
                    ?>
        </ul>
            <?php
            $onclik_func_name = 'jobsearch_job_content_load';
            if ($post_type == 'employer') {
                $onclik_func_name = 'jobsearch_employer_content_load';
            }
            ?>
        <div class="onsubmit-apilocs-con">
            <a href="javascript:void(0);" class="jobsearch-onsubmit-apilocs btn jobsearch-bgcolor"
               onclick="<?php echo($onclik_func_name) ?>(<?php echo absint($global_rand_id); ?>);"><?php esc_html_e('Submit', 'wp-jobsearch') ?></a>
        </div>
    </div>
                    <?php
                }

                add_action('init', 'jobsearch_candpost_type_link_rewrite');

                function jobsearch_candpost_type_link_rewrite() {
                    $jobsearch__options = get_option('jobsearch_plugin_options');
                    $cand_post_writeslug = isset($jobsearch__options['candidate_rewrite_slug']) && $jobsearch__options['candidate_rewrite_slug'] != '' ? $jobsearch__options['candidate_rewrite_slug'] : 'candidate';

                    add_rewrite_rule($cand_post_writeslug . '/([0-9]+)?$', 'index.php?post_type=candidate&p=$matches[1]', 'top');
                }

                function jobsearch_filter_cand_wp_title($title) {

                    $jobsearch__options = get_option('jobsearch_plugin_options');

                    $curr_post = get_post();

                    if (isset($curr_post->post_type) && $curr_post->post_type == 'candidate') {
                        $candidate_id = $curr_post->ID;

                        $cand_profile_restrict = new Candidate_Profile_Restriction;

                        $candidate_user_id = jobsearch_get_candidate_user_id($candidate_id);

                        $view_candidate = true;
                        $restrict_candidates = isset($jobsearch__options['restrict_candidates']) ? $jobsearch__options['restrict_candidates'] : '';

                        $view_cand_type = 'fully';
                        $emp_cvpbase_restrictions = isset($jobsearch__options['emp_cv_pkgbase_restrictions']) ? $jobsearch__options['emp_cv_pkgbase_restrictions'] : '';
                        $restrict_cand_type = isset($jobsearch__options['restrict_candidates_for_users']) ? $jobsearch__options['restrict_candidates_for_users'] : '';
                        if ($emp_cvpbase_restrictions == 'on' && $restrict_cand_type != 'only_applicants') {
                            $view_cand_type = 'partly';
                        }
                        if ($restrict_candidates == 'on' && $view_cand_type == 'fully') {
                            $view_candidate = false;

                            $restrict_candidates_for_users = isset($jobsearch__options['restrict_candidates_for_users']) ? $jobsearch__options['restrict_candidates_for_users'] : '';

                            if (is_user_logged_in()) {
                                $cur_user_id = get_current_user_id();
                                $cur_user_obj = wp_get_current_user();
                                if (jobsearch_user_isemp_member($cur_user_id)) {
                                    $employer_id = jobsearch_user_isemp_member($cur_user_id);
                                    $cur_user_id = jobsearch_get_employer_user_id($employer_id);
                                } else {
                                    $employer_id = jobsearch_get_user_employer_id($cur_user_id);
                                }
                                $ucandidate_id = jobsearch_get_user_candidate_id($cur_user_id);
                                $employer_dbstatus = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);
                                if ($employer_id > 0 && $employer_dbstatus == 'on') {
                                    $is_employer = true;
                                    $is_applicant = false;
                                    //
                                    $employer_post_args = array(
                                        'post_type' => 'job',
                                        'posts_per_page' => '-1',
                                        'post_status' => 'publish',
                                        'fields' => 'ids',
                                        'meta_query' => array(
                                            array(
                                                'key' => 'jobsearch_field_post_posted_by',
                                                'value' => $employer_id,
                                                'compare' => '=',
                                            ),
                                        ),
                                    );
                                    $employer_jobs_query = new WP_Query($employer_post_args);
                                    $employer_jobs_posts = $employer_jobs_query->posts;
                                    if (!empty($employer_jobs_posts) && is_array($employer_jobs_posts)) {
                                        foreach ($employer_jobs_posts as $employer_post_id) {
                                            $finded_result_list = jobsearch_find_index_user_meta_list($employer_post_id, 'jobsearch-user-jobs-applied-list', 'post_id', $candidate_user_id);
                                            if (is_array($finded_result_list) && !empty($finded_result_list)) {
                                                $is_applicant = true;
                                                break;
                                            }
                                        }
                                    }
                                    //
                                    if ($restrict_candidates_for_users == 'register_resume') {
                                        $user_cv_pkg = jobsearch_employer_first_subscribed_cv_pkg($cur_user_id);
                                        if (!$user_cv_pkg) {
                                            $user_cv_pkg = jobsearch_allin_first_pkg_subscribed($cur_user_id, 'cvs');
                                        }
                                        if (!$user_cv_pkg) {
                                            $user_cv_pkg = jobsearch_emprof_first_pkg_subscribed($cur_user_id, 'cvs');
                                        }
                                        if ($user_cv_pkg) {
                                            $view_candidate = true;
                                        } else {
                                            if ($is_applicant) {
                                                $view_candidate = true;
                                            }
                                        }
                                    } else if ($restrict_candidates_for_users == 'only_applicants') {
                                        if ($is_applicant) {
                                            $view_candidate = true;
                                        }
                                    } else {
                                        $view_candidate = true;
                                    }
                                } else if (in_array('administrator', (array) $cur_user_obj->roles)) {
                                    $view_candidate = true;
                                } else if ($ucandidate_id > 0 && $ucandidate_id == $candidate_id) {
                                    $view_candidate = true;
                                } else if ($restrict_candidates_for_users == 'register_empcand' && ($ucandidate_id > 0 || $employer_id > 0)) {
                                    $view_candidate = true;
                                }
                            }
                        } else if ($view_cand_type == 'partly') {
                            $view_candidate = false;
                            if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|display_name', 'detail_page')) {
                                $view_candidate = true;
                            }
                        }
                        if (!$view_candidate) {
                            $title = esc_html__('Unlock to reveal name', 'wp-jobsearch');
                        }
                    }
                    //var_dump($title);

                    return $title;
                }

                add_filter('pre_get_document_title', 'jobsearch_filter_cand_wp_title', 10000, 1);

//
//
                function jobsearch_usersback_sortable_columns($columns) {
                    $custom = array(
                        'username' => 'username',
                        'email' => 'email',
                        'jobsearch_adminprove' => 'jobsearch_adminprove',
                    );
                    return wp_parse_args($custom, $columns);
                }

                add_filter('manage_users_sortable_columns', 'jobsearch_usersback_sortable_columns');

                function jobsearch_usersback_sort_columns($WP_User_Query) {
                    global $wpdb, $pagenow;

                    if (isset($WP_User_Query->query_vars['orderby'])) {
                        if ('jobsearch_adminprove' === $WP_User_Query->query_vars['orderby']) {
                            $WP_User_Query->query_vars["meta_key"] = "jobsearch_accaprov_allow";
                            $WP_User_Query->query_vars["orderby"] = "meta_value";
                        }
                    }
                }

                add_filter('pre_get_users', 'jobsearch_usersback_sort_columns');

                function jobsearch_users_admin_columns_css() {
                    global $pagenow;
                    if ($pagenow == 'users.php') {
                        echo '<style>body.users-php th#jobsearch_adminprove{width:12%;}</style>';
                    }
                }

                add_action('admin_head', 'jobsearch_users_admin_columns_css');

                function jobsearch_users_admin_aprove_js() {
                    global $pagenow;
                    if ($pagenow == 'users.php') {
                        ?>
        <script>
            jQuery(document).on('click', '.user-adminprove-btn', function () {
                var _this = jQuery(this);
                var this_loader = _this.parent('.user-manulaprove-btncon').find('.loader-con');
                var userid = _this.attr('data-id');

                if (!_this.hasClass('has-approved')) {
                    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
                    var request = jQuery.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            user_id: userid,
                            action: 'jobsearch_user_acountaprovl_by_admin',
                        },
                        dataType: "json"
                    });

                    request.done(function (response) {
                        if (typeof response.error !== 'undefined' && response.error == '0') {
                            _this.removeClass('to-approve').addClass('has-approved');
                            this_loader.html('<strong><em style="color:#17aa00;"> (' + response.msg + ')</em></strong>');
                            return false;
                        }
                        this_loader.html('');
                    });

                    request.fail(function (jqXHR, textStatus) {
                        this_loader.html('');
                    });
                }
            });
        </script>
        <?php
    }
}

add_action('admin_footer', 'jobsearch_users_admin_aprove_js');

function jobsearch_users_admin_add_column($column) {
    $column['jobsearch_adminprove'] = esc_html__('Email Verification', 'wp-jobsearch');
    return $column;
}

add_filter('manage_users_columns', 'jobsearch_users_admin_add_column');

/* this will add column value in user list table */

function jobsearch_users_admin_add_colval($val, $column_name, $user_id) {
    switch ($column_name) {

        case 'jobsearch_adminprove' :
            $social_user = false;
            $jobsearch_facebook_id = get_user_meta($user_id, 'jobsearch_facebook_id', true);
            if ($jobsearch_facebook_id != '') {
                $social_user = true;
            }
            $jobsearch_google_id = get_user_meta($user_id, 'jobsearch_google_id', true);
            if ($jobsearch_google_id != '') {
                $social_user = true;
            }
            $jobsearch_linkedin_id = get_user_meta($user_id, 'jobsearch_linkedin_id', true);
            if ($jobsearch_linkedin_id != '') {
                $social_user = true;
            }
            $jobsearch_twitter_id = get_user_meta($user_id, 'jobsearch_twitter_id', true);
            if ($jobsearch_twitter_id != '') {
                $social_user = true;
            }
            $jobsearch_xing_id = get_user_meta($user_id, 'jobsearch_xing_id', true);
            if ($jobsearch_xing_id != '') {
                $social_user = true;
            }
            $user_login_auth = get_user_meta($user_id, 'jobsearch_accaprov_allow', true);
            ob_start();
            if ($user_login_auth == '0') {
                ?>
                <div class="user-manulaprove-btncon">
                    <a href="javascript:void(0);" class="user-adminprove-btn to-approve"
                       data-id="<?php echo($user_id) ?>"
                       style="color: #ff0000; font-weight: bold;"><?php esc_html_e('Verify', 'wp-jobsearch') ?></a>
                    <span class="loader-con"></span>
                </div>
                <?php
            } else if ($social_user) {
                ?>
                <div class="user-manulaprove-btncon">
                    <a class="user-adminprove-btn has-approved"
                       style="color: #17aa00;"><?php esc_html_e('Verified', 'wp-jobsearch') ?></a>
                </div>
                <?php
            } else if ($user_login_auth == '1') {
                ?>
                <div class="user-manulaprove-btncon">
                    <a class="user-adminprove-btn has-approved"
                       style="color: #17aa00;"><?php esc_html_e('Verified', 'wp-jobsearch') ?></a>
                </div>
                <?php
            } else {
                echo '-';
            }
            $html = ob_get_clean();
            return $html;
            break;

        default:
    }
}

add_filter('manage_users_custom_column', 'jobsearch_users_admin_add_colval', 10, 3);

function jobsearch_user_acountaprovl_by_admin() {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $c_user = get_user_by('ID', $user_id);

    $jobsearch__options = get_option('jobsearch_plugin_options');

    $candidate_auto_approve = isset($jobsearch__options['candidate_auto_approve']) ? $jobsearch__options['candidate_auto_approve'] : '';
    $employer_auto_approve = isset($jobsearch__options['employer_auto_approve']) ? $jobsearch__options['employer_auto_approve'] : '';

    $user_is_candidate = jobsearch_user_is_candidate($user_id);
    $user_is_employer = jobsearch_user_is_employer($user_id);

    if ($user_is_candidate && $candidate_auto_approve == 'email') {
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
        update_post_meta($candidate_id, 'jobsearch_field_candidate_approved', 'on');
    }
    if ($user_is_employer && $employer_auto_approve == 'email') {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        update_post_meta($employer_id, 'jobsearch_field_employer_approved', 'on');
    }
    update_user_meta($user_id, 'jobsearch_accaprov_allow', '1');
    $user_pass = get_user_meta($user_id, 'jobsearch_new_user_regtpass', true);
    if ($user_pass != '') {
        $user_pass = base64_decode($user_pass);
        do_action('jobsearch_new_user_register', $c_user, $user_pass);
    }

    echo json_encode(array('error' => '0', 'msg' => esc_html__('Approved', 'wp-jobsearch')));
    die;
}

add_action('wp_ajax_jobsearch_user_acountaprovl_by_admin', 'jobsearch_user_acountaprovl_by_admin');
//
//
add_action('admin_init', 'jobsearch_new_cand_add_bk_action');

function jobsearch_new_cand_add_bk_action() {
    if (isset($_POST['post_type']) && $_POST['post_type'] == 'candidate' && isset($_POST['user_reg_with_email']) && $_POST['user_reg_with_email'] != '') {
        $post_id = $_POST['post_ID'];
        $user_email = $_POST['user_reg_with_email'];

        if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {

            if (!email_exists($user_email)) {
                $user_pass = wp_generate_password(12);

                $username = $user_email;
                if (isset($_POST['post_title']) && $_POST['post_title'] != '') {
                    $username = sanitize_title($_POST['post_title']);
                }

                $create_user = wp_create_user($username, $user_pass, $user_email);

                if (!is_wp_error($create_user)) {
                    $user_id = $create_user;
                    $update_user_arr = array(
                        'ID' => $user_id,
                        'role' => 'jobsearch_candidate'
                    );
                    wp_update_user($update_user_arr);

                    $user_cand_id = get_user_meta($user_id, 'jobsearch_candidate_id', true);
                    if ($user_cand_id > 0 && get_post_type($user_cand_id) == 'candidate') {
                        wp_delete_post($user_cand_id, true);
                    }

                    //
                    update_user_meta($user_id, 'jobsearch_candidate_id', $post_id);
                    update_post_meta($post_id, 'jobsearch_user_id', $user_id);
                    update_post_meta($post_id, 'jobsearch_field_user_email', $user_email);
                    update_user_option($user_id, 'show_admin_bar_front', false);
                    //

                    $c_user = get_user_by('email', $user_email);
                    do_action('jobsearch_new_user_register', $c_user, $user_pass);
                }
            } else {
                $user_obj = get_user_by('email', $user_email);
                if (in_array('administrator', (array) $user_obj->roles)) {
                    return false;
                }
                $user_id = $user_obj->ID;

                $user_cand_id = get_user_meta($user_id, 'jobsearch_candidate_id', true);
                if ($user_cand_id == '') {
                    //
                    update_user_meta($user_id, 'jobsearch_candidate_id', $post_id);
                    update_post_meta($post_id, 'jobsearch_user_id', $user_id);
                    update_post_meta($post_id, 'jobsearch_field_user_email', $user_email);
                    //
                }
            }
        }
    }
}

//

function jobsearch_addto_candidate_exp_inyears($candidate_id) {
    $cand_exp_startdate = get_post_meta($candidate_id, 'jobsearch_field_experience_start_date', true);
    $cand_exp_enddate = get_post_meta($candidate_id, 'jobsearch_field_experience_end_date', true);
    $cand_exp_present = get_post_meta($candidate_id, 'jobsearch_field_experience_date_prsnt', true);
    if (!empty($cand_exp_startdate)) {
        $new_exp_filedsarr = array();
        $exfield_counter = 0;
        foreach ($cand_exp_startdate as $start_date) {

            $exfield_start_date = isset($cand_exp_startdate[$exfield_counter]) ? $cand_exp_startdate[$exfield_counter] : '';
            $exfield_end_date = isset($cand_exp_enddate[$exfield_counter]) ? $cand_exp_enddate[$exfield_counter] : '';
            $exfield_prsnt_field = isset($cand_exp_present[$exfield_counter]) ? $cand_exp_present[$exfield_counter] : '';

            $exp_sort_date = 0;
            if ($exfield_start_date != '') {
                $exp_sort_date = strtotime($exfield_start_date);
            }

            $new_exp_filedsarr[] = array(
                'start_date' => $exfield_start_date,
                'end_date' => $exfield_end_date,
                'present' => $exfield_prsnt_field,
                'sort_date' => $exp_sort_date,
            );

            $exfield_counter++;
        }
        usort($new_exp_filedsarr, function($a, $b) {
            if ($a['sort_date'] == $b['sort_date']) {
                $ret_val = 0;
            }
            $ret_val = ($a['sort_date'] < $b['sort_date']) ? -1 : 1;
            return $ret_val;
        });
        //echo '<pre>';
        //var_dump($new_exp_filedsarr);
        //echo '</pre>';
        $start_year = $new_exp_filedsarr[0]['start_date'];
        $all_arr_end = end($new_exp_filedsarr);
        $end_year = $all_arr_end['end_date'];
        if ($all_arr_end['present'] == 'on') {
            $end_year = current_time('d-m-Y');
        }

        if ($start_year != '') {
            $start_year_str = strtotime($start_year);
            $start_year = date('Y', $start_year_str);
        }
        if ($end_year != '') {
            $end_year_str = strtotime($end_year);
            $end_year = date('Y', $end_year_str);
        }

        if ($start_year > 0 && $end_year > 0 && $end_year > $start_year) {
            $overall_exp = $end_year - $start_year;
        } else {
            $overall_exp = 0;
        }
        $overall_exp = absint($overall_exp);
        update_post_meta($candidate_id, 'jobsearch_candidate_experience_inyears', $overall_exp);
    }
}

add_action('pre_get_terms', 'jobsearch_owncustax_chnge_get_terms');

function jobsearch_owncustax_chnge_get_terms($query) {
    global $pagenow;
    if (isset($query->query_vars)) {
        $qury_vars = $query->query_vars;
        if ($pagenow != 'edit-tags.php' && $pagenow != 'index.php' && isset($qury_vars['taxonomy'][0]) && $qury_vars['taxonomy'][0] == 'job-location') {
            //
            $query->query_vars['taxonomy'][0] = 'jobsearch_owncustax';
        }
    }
}

add_filter('get_terms', 'jobsearch_custom_modify_terms', 12, 4);

function jobsearch_custom_modify_terms($terms, $taxonomy, $query_vars, $term_query) {
    global $wpdb, $pagenow;
    if (isset($taxonomy[0]) && $taxonomy[0] == 'jobsearch_owncustax') {

        if ($pagenow != 'edit.php' && $pagenow != 'index.php' && !is_page() && $pagenow != 'post.php' && $pagenow != 'post-new.php' && $pagenow != 'nav-menus.php' && $pagenow != 'admin-ajax.php') {
            $get_db_terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS terms"
                            . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                            . " WHERE term_tax.taxonomy = %s", 'job-location'));

            $terms = $get_db_terms;
        } else {
            $terms = array();
        }
    }

    return $terms;
}

function jobsearch_get_custom_term_by($field = 'term_id', $value = '0', $taxonomy = 'job-location') {
    global $wpdb;
    if (function_exists('icl_object_id')) {
        global $sitepress;
        $sitepress_curr_lang = $sitepress->get_current_language();

        $trans_tble = $wpdb->prefix . 'icl_translations';
        $terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS terms"
                        . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                        . " LEFT JOIN $trans_tble AS icl_trans ON (terms.term_id = icl_trans.element_id) "
                        . " WHERE term_tax.taxonomy = '%s' AND terms." . $field . "='" . $value . "'"
                        . " AND icl_trans.language_code='" . $sitepress_curr_lang . "'", $taxonomy));
    } else {
        $terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS terms"
                        . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                        . " WHERE term_tax.taxonomy = '%s' AND terms." . $field . "='" . $value . "'", $taxonomy));
    }
    if (isset($terms[0])) {
        return $terms[0];
    }
    return false;
}

function jobsearch_custom_get_terms($taxonomy = 'job-location', $parent = 0, $orderby = 'terms.name', $order = 'ASC', $hide_empty = false) {
    global $wpdb;

    if (function_exists('icl_object_id')) {
        global $sitepress;
        $trans_tble = $wpdb->prefix . 'icl_translations';
        $terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS terms"
                        . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                        . " LEFT JOIN $trans_tble AS icl_trans ON (terms.term_id = icl_trans.element_id) "
                        . " WHERE term_tax.taxonomy = '%s' AND term_tax.parent = " . $parent
                        . " AND icl_trans.element_type='tax_{$taxonomy}'"
                        . " AND icl_trans.language_code='" . $sitepress->get_current_language() . "'"
                        . " GROUP BY terms.term_id"
                        . " ORDER BY " . $orderby . " " . $order, $taxonomy));
    } else {
        $terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS terms"
                        . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                        . " WHERE term_tax.taxonomy = '%s' AND term_tax.parent = " . $parent
                        . " GROUP BY terms.term_id"
                        . " ORDER BY " . $orderby . " " . $order, $taxonomy));
    }
    return $terms;
}

function jobsearch_get_terms_woutparnt($taxonomy = 'job-location', $orderby = 'terms.name', $order = 'ASC', $hide_empty = false) {
    global $wpdb;

    $order_by_metakey = false;
    if (is_array($orderby) && isset($orderby[0]) && $orderby[0] == 'meta_value_num') {
        $order_by_metakey = true;
        $meta_key = $orderby[1];
    }

    if (function_exists('icl_object_id')) {
        global $sitepress;
        $trans_tble = $wpdb->prefix . 'icl_translations';
        $query = "SELECT * FROM $wpdb->terms AS terms";
        $query .= " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id)";
        $query .= " LEFT JOIN $trans_tble AS icl_trans ON (terms.term_id = icl_trans.element_id)";
        if ($order_by_metakey) {
            $query .= " LEFT JOIN $wpdb->termmeta AS term_meta ON(terms.term_id = term_meta.term_id)";
            $query .= " WHERE term_tax.taxonomy='%s' AND term_meta.meta_key='{$meta_key}'";
        } else {
            $query .= " WHERE term_tax.taxonomy='%s'";
        }
        $query .= " AND icl_trans.element_type='tax_{$taxonomy}'";
        $query .= " AND icl_trans.language_code='" . $sitepress->get_current_language() . "'";
        $query .= " GROUP BY terms.term_id";
        if ($order_by_metakey) {
            $query .= " ORDER BY cast(term_meta.meta_value as unsigned) " . $order;
        } else {
            $query .= " ORDER BY " . $orderby . " " . $order;
        }
        $terms = $wpdb->get_results($wpdb->prepare($query, $taxonomy));
    } else {
        $query = "SELECT * FROM $wpdb->terms AS terms";
        $query .= " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id)";
        if ($order_by_metakey) {
            $query .= " LEFT JOIN $wpdb->termmeta AS term_meta ON(terms.term_id = term_meta.term_id)";
            $query .= " WHERE term_tax.taxonomy='%s' AND term_meta.meta_key='{$meta_key}'";
        } else {
            $query .= " WHERE term_tax.taxonomy='%s'";
        }
        $query .= " GROUP BY terms.term_id";
        if ($order_by_metakey) {
            $query .= " ORDER BY cast(term_meta.meta_value as unsigned) " . $order;
        } else {
            $query .= " ORDER BY " . $orderby . " " . $order;
        }
        $terms = $wpdb->get_results($wpdb->prepare($query, $taxonomy));
    }

    return $terms;
}

function jobsearch_get_terms_wlimit($taxonomy = 'job-location', $limit = 10, $offset = 0, $orderby = 'terms.name', $order = 'ASC') {
    global $wpdb;
    if (function_exists('icl_object_id')) {
        global $sitepress;
        $trans_tble = $wpdb->prefix . 'icl_translations';
        $terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS terms"
                        . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                        . " LEFT JOIN $trans_tble AS icl_trans ON (terms.term_id = icl_trans.element_id) "
                        . " WHERE term_tax.taxonomy = '%s'"
                        . " AND icl_trans.element_type='tax_{$taxonomy}'"
                        . " AND icl_trans.language_code='" . $sitepress->get_current_language() . "'"
                        . " GROUP BY terms.term_id"
                        . " ORDER BY " . $orderby . " " . $order
                        . " LIMIT " . $limit . " OFFSET " . $offset, $taxonomy));
    } else {
        $terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS terms"
                        . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                        . " WHERE term_tax.taxonomy = '%s' "
                        . " GROUP BY terms.term_id"
                        . " ORDER BY " . $orderby . " " . $order
                        . " LIMIT " . $limit . " OFFSET " . $offset, $taxonomy));
    }

    return $terms;
}

function jobsearch_get_terms_wcounts($taxonomy = 'job-location', $post_type = 'job', $orderby = 'terms.name', $order = 'ASC') {
    global $wpdb;
    if (function_exists('icl_object_id')) {
        global $sitepress;
        $trans_tble = $wpdb->prefix . 'icl_translations';
        $terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS terms"
                        . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                        . " LEFT JOIN $trans_tble AS icl_trans ON (terms.term_id = icl_trans.element_id) "
                        . " WHERE term_tax.taxonomy = '%s' AND term_meta.meta_key='active_" . $post_type . "s_loc_count' AND term_meta.meta_value > 0 "
                        . " AND icl_trans.element_type='tax_{$taxonomy}'"
                        . " AND icl_trans.language_code='" . $sitepress->get_current_language() . "'"
                        . " GROUP BY terms.term_id"
                        . " ORDER BY " . $orderby . " " . $order, $taxonomy));
    } else {
        $terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS terms"
                        . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                        . " LEFT JOIN $wpdb->termmeta AS term_meta ON(terms.term_id = term_meta.term_id) "
                        . " WHERE term_tax.taxonomy = '%s' AND term_meta.meta_key='active_" . $post_type . "s_loc_count' AND term_meta.meta_value > 0 "
                        . " GROUP BY terms.term_id"
                        . " ORDER BY " . $orderby . " " . $order, $taxonomy));
    }

    return $terms;
}

if (!function_exists('jobsearch_candidate_listing_custom_fields_callback')) {

    function jobsearch_candidate_listing_custom_fields_callback($atts = array(), $candidate_id = '', $candidate_cus_field_arr = array(), $view = 'view-1') {
        $candidate_custom_fields_switch = isset($atts['candidate_custom_fields_switch']) ? $atts['candidate_custom_fields_switch'] : 'no';
        if ($candidate_custom_fields_switch == 'yes' && !empty($candidate_cus_field_arr)) {
            $cus_fields = array(
                'content' => '',
                'candidate_list' => true,
            );

            if ($view == 'default') {
                $cus_fields = apply_filters('jobsearch_custom_fields_list', 'candidate', $candidate_id, $cus_fields, '<li>', '</li>', '', true, true, true, 'jobsearch', $candidate_cus_field_arr);
            } else {
                $cus_fields = apply_filters('jobsearch_custom_fields_list', 'candidate', $candidate_id, $cus_fields, '', '', '', true, true, true, 'jobsearch', $candidate_cus_field_arr);
            }
            if (isset($cus_fields['content']) && $cus_fields['content'] != '') {
                if ($view == 'default') {
                    echo '<ul class="jobsearch-custom-field">' . force_balance_tags($cus_fields['content']) . ' </ul> ';
                } else {
                    echo '<small class="careerfy-candidate-style8-options">' . force_balance_tags($cus_fields['content']) . '</small>';
                }
            }
        }
    }

    add_action('jobsearch_candidate_listing_custom_fields', 'jobsearch_candidate_listing_custom_fields_callback', 10, 4);
}

add_filter('author_link', function ($link, $user_id) {
    $user_is_candidate = jobsearch_user_is_candidate($user_id);
    $user_is_employer = jobsearch_user_is_employer($user_id);
    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        if ($employer_id > 0 && get_post_type($employer_id) == 'employer') {
            $link = get_permalink($employer_id);
        }
    } else if ($user_is_candidate) {
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
        if ($candidate_id > 0 && get_post_type($candidate_id) == 'candidate') {
            $link = get_permalink($candidate_id);
        }
    }

    return $link;
}, 10, 2);

function jobsearch_applicant_pend_profile_review_txt() {

    return '<span class="pending-profile-applicant">' . esc_html__('This applicant profile is not complete.', 'wp-jobsearch') . '<br><small>' . esc_html__('Pending for review.', 'wp-jobsearch') . '</small></span>';
}

function jobsearch_get_applicant_status_tarr($candidate_id, $job_id) {
    global $jobsearch_plugin_options;
    $candidate_skills = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
    if ($candidate_skills == 'on') {
        $candidate_skill_perc = get_post_meta($candidate_id, 'overall_skills_percentage', true);
        $candidate_approve_skill = isset($jobsearch_plugin_options['jobsearch-candidate-skills-percentage']) ? $jobsearch_plugin_options['jobsearch-candidate-skills-percentage'] : 0;
        if (($candidate_approve_skill > 0 && $candidate_skill_perc < $candidate_approve_skill)) {
            return array('status' => 'pending', 'status_str' => esc_html_x('Pending due to incomplete profile', 'applicant status', 'wp-jobsearch'));
        }
    }
    $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);
    $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : '';
    $job_reject_int_list = jobsearch_is_post_ids_array($job_reject_int_list, 'candidate');
    if (!empty($job_reject_int_list) && in_array($candidate_id, $job_reject_int_list)) {
        return array('status' => 'rejected', 'status_str' => esc_html_x('Rejected', 'applicant status', 'wp-jobsearch'));
    }

    $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
    $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
    $job_short_int_list = jobsearch_is_post_ids_array($job_short_int_list, 'candidate');
    if (!empty($job_short_int_list) && in_array($candidate_id, $job_short_int_list)) {
        return array('status' => 'shortlist', 'status_str' => esc_html_x('Shortlist', 'applicant status', 'wp-jobsearch'));
    }
}
