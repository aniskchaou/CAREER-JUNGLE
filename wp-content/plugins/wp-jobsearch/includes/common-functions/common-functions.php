<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * common functions files
 * html fields
 * @return functions
 */
if (!function_exists('jobsearch_pagination')) {

    /*
     * Pagination.
     * @return markup
     */

    function jobsearch_pagination($jobsearch_query = '', $return = false) {
        global $wp_query;
        $jobsearch_big = 999999999; // need an unlikely integer
        $jobsearch_cus_query = $wp_query;
        if (!empty($jobsearch_query)) {
            $jobsearch_cus_query = $jobsearch_query;
        }
        $jobsearch_html = '<div class="jobsearch-pagination-blog">';

        $jobsearch_html .= paginate_links(array(
            'base' => str_replace($jobsearch_big, '%#%', esc_url(get_pagenum_link($jobsearch_big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $jobsearch_cus_query->max_num_pages,
            'prev_text' => '<span><i class="jobsearch-icon jobsearch-arrows4"></i></span>',
            'next_text' => '<span><i class="jobsearch-icon jobsearch-arrows4"></i></span>',
            'type' => 'list'
        ));

        $jobsearch_html .= '</div>';

        if ($return == true) {
            return $jobsearch_html;
        } else {
            echo force_balance_tags($jobsearch_html);
        }
    }

}

add_action('wp_head', 'jobsearch_wp_head_common_js', 99);

function jobsearch_wp_head_common_js() {
    $lang_locale = get_bloginfo('language');
    $lang_locale = substr($lang_locale, 0, 2);
    if ($lang_locale != '') {
        ?>
        <script>
            jQuery(document).ready(function () {
                if (typeof jQuery.datetimepicker !== 'undefined') {
                    jQuery.datetimepicker.setLocale('<?php echo($lang_locale) ?>');
                }
            });
        </script>
        <?php
    }
}

add_filter('pre_get_avatar_data', 'jobsearch_user_avatar_data_args', 10, 2);

function jobsearch_user_avatar_data_args($args, $id_or_email) {
    if (filter_var($id_or_email, FILTER_VALIDATE_EMAIL)) {
        $user_obj = get_user_by('email', $id_or_email);
    } else {
        $user_obj = get_user_by('ID', $id_or_email);
    }
    $user_id = isset($user_obj->ID) ? $user_obj->ID : '';
    $user_is_candiadte = jobsearch_user_is_candidate($user_id);
    $user_is_employer = jobsearch_user_is_employer($user_id);
    if ($user_is_candiadte) {
        $default = jobsearch_candidate_image_placeholder();
        $args['default'] = $default;
    }
    if ($user_is_employer) {
        $default = jobsearch_employer_image_placeholder();
        $args['default'] = $default;
    }
    return $args;
}

add_filter('get_avatar', 'jobsearch_get_user_def_avatar', 10, 5);

function jobsearch_get_user_def_avatar($avatar, $id_or_email, $size, $default, $alt) {
    if (filter_var($id_or_email, FILTER_VALIDATE_EMAIL)) {
        $user_obj = get_user_by('email', $id_or_email);
    } else {
        $user_obj = get_user_by('ID', $id_or_email);
    }
    $user_id = isset($user_obj->ID) ? $user_obj->ID : '';
    $user_is_candiadte = jobsearch_user_is_candidate($user_id);
    $user_is_employer = jobsearch_user_is_employer($user_id);
    if ($user_is_candiadte) {
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
        $user_avatar_dburl = get_post_meta($candidate_id, 'jobsearch_user_avatar_url', true);
        if (isset($user_avatar_dburl['file_url']) && $user_avatar_dburl['file_url'] != '') {
            $user_img_name = $user_avatar_dburl['file_name'];
            $user_img_path = $user_avatar_dburl['file_path'];
            $img_full_path = $user_img_path . '/' . $user_img_name;
            $avatar_url = isset($user_avatar_dburl['orig_file_url']) ? $user_avatar_dburl['orig_file_url'] : '';
            if (file_exists($img_full_path)) {
                $avatar_url = apply_filters('wp_jobsearch_cand_profile_img_url', $avatar_url, $candidate_id, '150');
                $avatar = '<img alt="" src="' . $avatar_url . '" width="32" height="32">';
            }
        }
    }
    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        $user_avatar_id = get_post_thumbnail_id($employer_id);
        if ($user_avatar_id > 0) {
            $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
            $user_img_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
            $avatar = '<img alt="" src="' . $user_img_url . '" width="32" height="32">';
        }
    }
    return $avatar;
}

function jobsearch_wp_handle_upload(&$file, $overides, $action = 'wp_handle_upload') {
    $upload_file_arr = array('error' => '');

    add_filter('upload_dir', 'jobsearch_user_upload_files_path');
    $wp_upload_dir = wp_upload_dir();
    remove_filter('upload_dir', 'jobsearch_user_upload_files_path');
    $uploads_dir = $wp_upload_dir['path'];

    $file = apply_filters("{$action}_prefilter", $file);
    if (isset($overides['mimes']) && isset($file['type']) && in_array($file['type'], $overides['mimes'])) {
        $base_name = basename($file["name"]);
        $target_file = $uploads_dir . '/' . $base_name;
        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (file_exists($target_file)) {
            $base_name = str_replace('.' . $file_ext, '', basename($file["name"])) . rand(100000000, 999999999) . '.' . $file_ext;
            $target_file = $uploads_dir . '/' . $base_name;
        }

        $test_uploaded_file = is_uploaded_file($file['tmp_name']);

        if ('wp_handle_upload' === $action) {
            $move_new_file = @ move_uploaded_file($file['tmp_name'], $target_file);
        } else {
            // use copy and unlink because rename breaks streams.
            $move_new_file = @ copy($file['tmp_name'], $target_file);
            unlink($file['tmp_name']);
        }

        if (false === $move_new_file) {
            $upload_file_arr['error'] = 'File Upload Failed.';
        } else {
            $upload_file_arr['file'] = $target_file;
            $upload_file_arr['url'] = $wp_upload_dir['url'] . '/' . $base_name;
            $upload_file_arr['type'] = $file['type'];
        }
    }

    return $upload_file_arr;
}

function jobsearch_wp_handle_upload_wuunlink(&$file, $overides, $action = 'wp_handle_upload') {
    $upload_file_arr = array('error' => '');

    add_filter('upload_dir', 'jobsearch_user_upload_files_path');
    $wp_upload_dir = wp_upload_dir();
    remove_filter('upload_dir', 'jobsearch_user_upload_files_path');
    $uploads_dir = $wp_upload_dir['path'];

    $file = apply_filters("{$action}_prefilter", $file);
    if (isset($overides['mimes']) && isset($file['type']) && in_array($file['type'], $overides['mimes'])) {
        $base_name = basename($file["name"]);
        $target_file = $uploads_dir . '/' . $base_name;
        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (file_exists($target_file)) {
            $base_name = str_replace('.' . $file_ext, '', basename($file["name"])) . rand(100000000, 999999999) . '.' . $file_ext;
            $target_file = $uploads_dir . '/' . $base_name;
        }

        $test_uploaded_file = is_uploaded_file($file['tmp_name']);

        $move_new_file = @ copy($file['tmp_name'], $target_file);

        if (false === $move_new_file) {
            $upload_file_arr['error'] = 'File Upload Failed.';
        } else {
            $upload_file_arr['file'] = $target_file;
            $upload_file_arr['url'] = $wp_upload_dir['url'] . '/' . $base_name;
            $upload_file_arr['type'] = $file['type'];
        }
    }

    return $upload_file_arr;
}

function jobsearch_user_upload_files_path($dir = '') {
    $cus_dir = 'jobsearch-user-files';
    $dir_path = array(
        'path' => $dir['basedir'] . '/' . $cus_dir,
        'url' => $dir['baseurl'] . '/' . $cus_dir,
        'subdir' => $cus_dir,
    );
    return $dir_path + $dir;
}

function jobsearch_usercand_upload_files_path($dir = '') {
    $cus_dir = 'jobsearch-candidates';
    $dir_path = array(
        'path' => $dir['basedir'] . '/' . $cus_dir,
        'url' => $dir['baseurl'] . '/' . $cus_dir,
        'subdir' => $cus_dir,
    );
    return $dir_path + $dir;
}

add_filter('upload_dir', 'jobsearch_candimg_upload_dir');

function jobsearch_upload_candimg_path($path) {
    global $jobsearch_uploding_resume, $jobsearch_download_locations;
    $jobsearch_uploding_resume = false;
    $jobsearch_download_locations = false;
    return 'jobsearch-candidates/' . JobSearch_plugin::get_uniquid();
    //return 'jobsearch-candidates/' . uniqid();
}

function jobsearch_candimg_upload_dir($pathdata) {
    global $jobsearch_uploding_candimg;
    if ($jobsearch_uploding_candimg === true) {
        $img_folder = 'jobsearch-candidates/';

        $dir = untrailingslashit(apply_filters('jobsearch_candimg_upload_dir', $img_folder));

        if (empty($pathdata['subdir'])) {
            $pathdata['path'] = $pathdata['path'] . '/' . $dir;
            $pathdata['url'] = $pathdata['url'] . '/' . $dir;
            $pathdata['subdir'] = '/' . $dir;
        } else {
            $new_subdir = '/' . $dir . $pathdata['subdir'];
            $pathdata['path'] = str_replace($pathdata['subdir'], $new_subdir, $pathdata['path']);
            $pathdata['url'] = str_replace($pathdata['subdir'], $new_subdir, $pathdata['url']);
            $pathdata['subdir'] = $new_subdir;
        }
    }
    return $pathdata;
}

add_filter('upload_dir', 'jobsearch_resume_upload_dir');

function jobsearch_upload_cvmod_path($path) {
    global $jobsearch_uploding_candimg, $jobsearch_download_locations;
    $jobsearch_uploding_candimg = false;
    $jobsearch_download_locations = false;
    return 'jobsearch-resumes/' . JobSearch_plugin::get_uniquid();
}

function jobsearch_resume_upload_dir($pathdata) {
    global $jobsearch_uploding_resume;
    if ($jobsearch_uploding_resume === true) {
        $resume_folder = 'jobsearch-resumes/';

        $dir = untrailingslashit(apply_filters('jobsearch_resume_upload_dir', $resume_folder));

        if (empty($pathdata['subdir'])) {
            $pathdata['path'] = $pathdata['path'] . '/' . $dir;
            $pathdata['url'] = $pathdata['url'] . '/' . $dir;
            $pathdata['subdir'] = '/' . $dir;
        } else {
            $new_subdir = '/' . $dir . $pathdata['subdir'];
            $pathdata['path'] = str_replace($pathdata['subdir'], $new_subdir, $pathdata['path']);
            $pathdata['url'] = str_replace($pathdata['subdir'], $new_subdir, $pathdata['url']);
            $pathdata['subdir'] = $new_subdir;
        }
    }
    return $pathdata;
}

add_filter('upload_dir', 'jobsearch_resume_upload_dir');
//
//
add_action('wp', 'wp_jobsearch_set_user_files_foldr_access');

function wp_jobsearch_set_user_files_foldr_access() {
    global $wp_filesystem, $jobsearch_download_locations;
    $jobsearch_download_locations = false;
    require_once ABSPATH . '/wp-admin/includes/file.php';
    if (false === ($creds = request_filesystem_credentials(wp_nonce_url('post.php'), '', false, false, array()))) {
        return true;
    }
    if (!WP_Filesystem($creds)) {
        request_filesystem_credentials(wp_nonce_url('post.php'), '', true, false, array());
        return true;
    }

    add_filter('upload_dir', 'jobsearch_user_upload_files_path');
    $wp_upload_dir = wp_upload_dir();
    $files_dir = $wp_upload_dir['path'] . '/';
    if (!is_dir($files_dir)) {
        mkdir($files_dir);
    }

    remove_filter('upload_dir', 'jobsearch_user_upload_files_path');

    $htaccs_file = $files_dir . '.htaccess';

    //$file_contnt = 'deny from all';
    $file_contnt = 'order allow,deny
    <Files ~ "\.(jpg|jpeg|png|gif)$">
       allow from all
    </Files>';
    $wp_filesystem->put_contents($htaccs_file, $file_contnt, FS_CHMOD_FILE);
    //
    add_filter('upload_dir', 'jobsearch_usercand_upload_files_path');

    $wp_upload_dir = wp_upload_dir();

    $files_dir = $wp_upload_dir['path'] . '/';
    if (!is_dir($files_dir)) {
        mkdir($files_dir);
    }

    remove_filter('upload_dir', 'jobsearch_usercand_upload_files_path');

    $htaccs_file = $files_dir . '.htaccess';

    $file_contnt = 'deny from all';

    $wp_filesystem->put_contents($htaccs_file, $file_contnt, FS_CHMOD_FILE);
}

add_filter('jobsearch_user_display_name', 'jobsearch_get_user_display_name', 10, 2);

function jobsearch_get_user_display_name($display_name, $user_obj) {
    $user_id = isset($user_obj->ID) ? $user_obj->ID : 0;
    if ($user_id > 0) {
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);
        //
        if ($user_is_employer) {
            $employer_id = jobsearch_get_user_employer_id($user_id);
            $display_name = get_the_title($employer_id);
        } else if ($user_is_candidate) {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            $display_name = get_the_title($candidate_id);
        }
    }
    return apply_filters('jobsearch_cus_user_display_name_chh', $display_name, $user_id);
}

function jobsearch_addd_taxnomy_level_space($level) {
    $space_html = '';
    if ($level > 1) {
        for ($sp = 1; $sp <= $level; $sp++) {
            $space_html .= '&nbsp; &nbsp; ';
        }
    } else if ($level == 1) {
        $space_html .= '&nbsp; &nbsp; ';
    }
    return $space_html;
}

function jobsearch_sector_terms_hierarchical($id, $terms, $output = '', $parent_id = 0, $level = 0, $selected_sector = '') {

    $sel_sector = '';
    if ($selected_sector != '') {
        $sel_sector = $selected_sector;
    } else if ($id > 0) {
        $sectors = wp_get_post_terms($id, 'sector');
        if (!empty($sectors)) {
            foreach ($sectors as $sel_sectr) {
                $sel_sector = isset($sel_sectr->slug) ? $sel_sectr->slug : '';
            }
        }
    }

    foreach ($terms as $term) {
        if ($parent_id == $term->parent) {

            ob_start();
            ?>
            <option value="<?php echo($term->slug) ?>" <?php echo($sel_sector == $term->slug ? 'selected="selected"' : '') ?>><?php echo (jobsearch_addd_taxnomy_level_space($level)) . ($term->name) ?></option>
            <?php
            $output .= ob_get_clean();

            $output = jobsearch_sector_terms_hierarchical($id, $terms, $output, $term->term_id, $level + 1, $selected_sector);
        }
    }
    return $output;
}

function jobsearch_input_post_vals_validate($post_data) {
    if (!empty($post_data)) {
        foreach ($post_data as $post_input_key => $post_input_val) {
            if (is_array($post_input_val)) {
                $post_data[$post_input_key] = $post_input_val;
            } else if (strpos($post_input_val, 'alert(') > 0) {
                $post_data[$post_input_key] = '';
            } else if (strpos($post_input_val, 'alert)') > 0) {
                $post_data[$post_input_key] = '';
            } else if (strpos($post_input_val, 'focus=') > 0) {
                $post_data[$post_input_key] = '';
            } else if (strpos($post_input_val, 'onerror=') > 0) {
                $post_data[$post_input_key] = '';
            } else if (strpos($post_input_val, 'window.location=') > 0) {
                $post_data[$post_input_key] = '';
            } else {
                $post_data[$post_input_key] = $post_input_val;
            }
        }
    }
    return $post_data;
}

if (!function_exists('jobsearch_excerpt')) {

    /*
     * Custom excerpt.
     * @return content
     */

    function jobsearch_excerpt($length = '', $id = '', $read_more = false, $cont = false) {

        $excerpt = '';

        if ($id > 0) {
            $post_obj = get_post($id);

            $excerpt = isset($post_obj->post_excerpt) ? $post_obj->post_excerpt : '';
            if ($length > 0) {
                $excerpt = wp_trim_words($excerpt, $length, '...');
            }
        }

        return $excerpt;
    }

}

if (!function_exists('jobsearch_excerpt_contnt')) {

    /*
     * Custom excerpt.
     * @return content
     */

    function jobsearch_excerpt_contnt($length = '', $id = '', $read_more = false, $cont = false) {
        $excerpt = '';
        if ($id > 0) {
            $post_obj = get_post($id);
            $excerpt = isset($post_obj->post_excerpt) ? $post_obj->post_excerpt : '';
            if ($excerpt == '') {
                $excerpt = isset($post_obj->post_content) ? $post_obj->post_content : '';
            }
            if ($length > 0) {
                $excerpt = wp_trim_words($excerpt, $length, '...');
            }
        }

        return $excerpt;
    }

}

if (!function_exists('jobsearch_icon_picker')) {

    /*
     * Icon Picker.
     * @return markup
     */

    function jobsearch_icon_picker($value = '', $id = '', $name = '', $class = 'jobsearch-icon-pickerr') {
        $html = "
        <script>
        jQuery(document).ready(function ($) {
            var this_icons;
            var rand_num = " . $id . ";
            var e9_element = $('#e9_element_' + rand_num).fontIconPicker({
                theme: 'fip-bootstrap'
            });
            icons_load_call.always(function () {
                this_icons = loaded_icons;
                // Get the class prefix
                var classPrefix = this_icons.preferences.fontPref.prefix,
                                icomoon_json_icons = [],
                                icomoon_json_search = [];
                $.each(this_icons.icons, function (i, v) {
                        icomoon_json_icons.push(classPrefix + v.properties.name);
                        if (v.icon && v.icon.tags && v.icon.tags.length) {
                                icomoon_json_search.push(v.properties.name + ' ' + v.icon.tags.join(' '));
                        } else {
                                icomoon_json_search.push(v.properties.name);
                        }
                });
                // Set new fonts on fontIconPicker
                e9_element.setIcons(icomoon_json_icons, icomoon_json_search);
                // Show success message and disable
                $('#e9_buttons_' + rand_num + ' button').removeClass('btn-primary').addClass('btn-success').text('Successfully loaded icons').prop('disabled', true);
            })
            .fail(function () {
                // Show error message and enable
                $('#e9_buttons_' + rand_num + ' button').removeClass('btn-primary').addClass('btn-danger').text('Error: Try Again?').prop('disabled', false);
            });
        });
        </script>";
        $html .= '
        <input type="text" id="e9_element_' . $id . '" class="' . $class . '" name="' . $name . '" value="' . $value . '">
        <span id="e9_buttons_' . $id . '" style="display:none">\
            <button autocomplete="off" type="button" class="btn btn-primary">Load from IcoMoon selection.json</button>
        </span>';
        return $html;
    }

}

if (!function_exists('jobsearch_wpml_lang_page_id')) {

    function jobsearch_wpml_lang_page_id($id = '', $post_type = '', $lang_code = '') {
        global $sitepress;
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $id != '' && is_numeric($id) && $post_type != '') {
            $lang_code = $lang_code == '' ? $sitepress->get_current_language() : $lang_code;
            $object_id = icl_object_id($id, $post_type, false, $lang_code);
            if ($object_id <= 0) {
                $object_id = $id;
            }
            return $object_id;
        } else {
            return $id;
        }
    }

}

if (!function_exists('jobsearch_wpml_lang_url')) {

    function jobsearch_wpml_lang_url() {
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {

            global $sitepress;
            $server_uri = $_SERVER['REQUEST_URI'];
            $server_uri = explode('/', $server_uri);
            $active_langs = $sitepress->get_active_languages();
            if (is_array($active_langs) && sizeof($active_langs) > 0) {
                foreach ($server_uri as $uri) {

                    if (array_key_exists($uri, $active_langs)) {
                        return $uri;
                    }
                }
            }
        }
        return false;
    }

}

function jobsearch_remove_extra_slashes($string) {
    $string = preg_replace('/\\\\{2,}/', '\\', $string);
    return $string;
}

if (!function_exists('jobsearch_wpml_parse_url')) {

    function jobsearch_wpml_parse_url($lang = 'en', $url) {

        $fir_url = home_url('/');
        if (strpos($fir_url, '/' . $lang . '/') !== false) {
            
        }
        $tail_url = substr($url, strlen($fir_url), strlen($url));

        $trans_url = $fir_url . $lang . '/' . $tail_url;

        return $trans_url;
    }

}

if (!function_exists('jobsearch_wpml_ls_filter')) {
    add_filter('icl_ls_languages', 'jobsearch_wpml_ls_filter');

    function jobsearch_wpml_ls_filter($languages) {
        global $sitepress;
        if (strpos(basename($_SERVER['REQUEST_URI']), 'dashboard') !== false || strpos(basename($_SERVER['REQUEST_URI']), 'tab') !== false) {

            $request_query = str_replace('?', '', basename($_SERVER['REQUEST_URI']));

            $request_query = explode('&', $request_query);

            $request_quer = '';

            $query_count = 1;

            if (is_array($request_query)) {
                foreach ($request_query as $quer) {
                    if (strpos($quer, 'page_id') !== false || strpos($quer, 'lang') !== false) {
                        continue;
                    }
                    if ($query_count == 1) {
                        $request_quer .= $quer;
                    } else {
                        $request_quer .= '&' . $quer;
                    }
                    $query_count++;
                }
            }

            if (is_array($languages) && sizeof($languages) > 0) {
                foreach ($languages as $lang_code => $language) {
                    if (strpos($languages[$lang_code]['url'], '?') !== false) {
                        $languages[$lang_code]['url'] = $languages[$lang_code]['url'] . '&' . $request_quer;
                    } else {
                        $languages[$lang_code]['url'] = $languages[$lang_code]['url'] . '?' . $request_quer;
                    }
                }
            }
        }
        return $languages;
    }

}

if (!function_exists('jobsearch_wpml_auto_translated_pages')) {
    add_action('init', 'jobsearch_wpml_auto_translated_pages', 15);

    function jobsearch_wpml_auto_translated_pages() {
        global $jobsearch_plugin_options, $sitepress;

        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {

            $wpml_lang_pages = get_option('jobsearch_wpml_lang_pages_ids');
            $options_pages_ids = (!empty($wpml_lang_pages) ? $wpml_lang_pages : array());

            $dashboard_page_id = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $dashboard_page_id = jobsearch__get_post_id($dashboard_page_id, 'page');

            if ($dashboard_page_id > 0) {
                $dash_pages_ids = (isset($options_pages_ids['dashboard_pages_ids']) && !empty($options_pages_ids['dashboard_pages_ids']) ? $options_pages_ids['dashboard_pages_ids'] : array());

                $def_trid = $sitepress->get_element_trid($dashboard_page_id);

                $wpml_options = get_option('icl_sitepress_settings');
                $default_lang = isset($wpml_options['default_language']) ? $wpml_options['default_language'] : '';
                $languages = icl_get_languages('skip_missing=0&orderby=code');
                if (is_array($languages) && sizeof($languages) > 0) {
                    foreach ($languages as $lang_code => $language) {
                        if ($default_lang == $lang_code) {
                            continue;
                        }

                        if (!array_key_exists($lang_code . '_page_id', $dash_pages_ids)) {
                            $ru_args = array(
                                'post_title' => $lang_code . ' ' . wp_strip_all_tags('User Dashboard'),
                                'post_content' => '',
                                'post_status' => 'publish',
                                'post_type' => 'page'
                            );
                            //creating post with arguments above and assign post id to $ru_post_id
                            $ru_post_id = wp_insert_post($ru_args);
                            $options_pages_ids['dashboard_pages_ids'][$lang_code . '_page_id'] = $ru_post_id;
                            update_post_meta($ru_post_id, '_wp_page_template', 'user-dashboard-template.php');

                            //change language and trid of second post to match russian and default post trid
                            $sitepress->set_element_language_details($ru_post_id, 'post_page', $def_trid, $lang_code);
                        }
                    }
                }
                //
            }
            //

            update_option('jobsearch_wpml_lang_pages_ids', $options_pages_ids);
        }
    }

}

if (!function_exists('jobsearch_wpml_lang_page_permalink')) {

    function jobsearch_wpml_lang_page_permalink($id = '', $post_type = '', $lang_code = '') {

        if ($page_id = jobsearch_wpml_lang_page_id($id, $post_type, $lang_code)) {
            return get_permalink($page_id);
        } else {
            return false;
        }
    }

}

if (!function_exists('jobsearch_wpml_lang_code_field')) {

    function jobsearch_wpml_lang_code_field($field_type = 'hidden') {
        if (defined('ICL_LANGUAGE_CODE')) {
            if ($field_type == 'text') {
                return '<input type="text" name="lang" value="' . ICL_LANGUAGE_CODE . '" style="display:none;">';
            } else {
                return '<input type="hidden" name="lang" value="' . ICL_LANGUAGE_CODE . '">';
            }
        }
    }

}

function jobsearch_wpml_is_original($post_id = 0, $type = 'post_post') {
    global $post, $sitepress;

    $output = array();

    // use current post if post_id is not provided
    $p_ID = $post_id == 0 ? $post->ID : $post_id;

    $el_trid = $sitepress->get_element_trid($p_ID, $type);
    $el_translations = $sitepress->get_element_translations($el_trid, $type);

    if (!empty($el_translations)) {
        $is_original = FALSE;
        foreach ($el_translations as $lang => $details) {
            if ($details->original == 1 && $details->element_id == $p_ID) {
                $is_original = TRUE;
            }
            if ($details->original == 1) {
                $original_ID = $details->element_id;
            }
        }
        $output['is_original'] = $is_original;
        $output['original_ID'] = $original_ID;
    }
    return $output;
}

function jobsearch_wpml_fix_missing_icl_tables() {
    if (function_exists('icl_sitepress_activate')) {
        icl_sitepress_activate();
    }
}

add_action('wp_footer', 'jobsearch_wpml_fix_missing_icl_tables');

add_action('jobsearch_translate_profile_with_wpml_btn', 'jobsearch_translate_profile_with_wpml_btn', 10, 3);

function jobsearch_translate_profile_with_wpml_btn($member_id = 0, $post_type = 'candidate', $tab = 'dashboard-settings') {
    global $jobsearch_plugin_options, $sitepress;
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $member_id > 0) {
        $page_id = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_id = jobsearch__get_post_id($page_id, 'page');

        $current_lang = $sitepress->get_current_language();
        $languages = icl_get_languages('skip_missing=0&orderby=code');
        if (is_array($languages) && sizeof($languages) > 0) {
            $real_member_id = $member_id;
            $member_id = jobsearch_wpml_is_original($member_id, 'post_' . $post_type);
            $member_id = isset($member_id['original_ID']) && $member_id['original_ID'] > 0 ? $member_id['original_ID'] : $real_member_id;
            foreach ($languages as $lang_code => $language) {
                $sitepress->switch_lang($lang_code);
                $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);
                $page_url = apply_filters('wpml_permalink', $page_url, $lang_code);
                $icl_post_id = icl_object_id($member_id, 'candidate', false, $lang_code);
                $sitepress->switch_lang($current_lang);

                if ($icl_post_id <= 0) {
                    ?>
                    <a class="other-lang-translate-post"
                       href="<?php echo add_query_arg(array('tab' => $tab, 'lang' => $lang_code), $page_url) ?>"><?php printf(esc_html__('Translate in %s', 'wp-jobsearch'), (isset($language['translated_name']) ? $language['translated_name'] : '')) ?></a>
                       <?php
                   }
               }
           }
       }
   }

   add_action('jobsearch_translate_profile_with_wpml_source', 'jobsearch_translate_profile_with_wpml_source', 10, 1);

   function jobsearch_translate_profile_with_wpml_source($user_id = 0) {
       global $jobsearch_plugin_options, $sitepress, $wpdb;

       $tr_lang_allow = true;
       $tr_lang_allow = apply_filters('jobsearch_allowflag_translate_profile_with_wpml', $tr_lang_allow);

       if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $user_id > 0 && $tr_lang_allow === true) {
           $user_is_candidate = jobsearch_user_is_candidate($user_id);
           $user_is_employer = jobsearch_user_is_employer($user_id);

           if ($user_is_employer) {
               $employer_id = jobsearch_get_user_employer_id($user_id);
               $current_lang = $sitepress->get_current_language();
               $args = array(
                   'post_type' => 'employer',
                   'posts_per_page' => '1',
                   'post_status' => 'publish',
                   'post__in' => array($employer_id),
               );
               $res_query = new WP_Query($args);
               $found_res = $res_query->found_posts;
               wp_reset_postdata();

               if ($found_res <= 0) {
                   $tr_employer_obj = get_post($employer_id);
                   $tr_employer_content = $tr_employer_obj->post_content;
                   $tr_employer_content = apply_filters('the_content', $tr_employer_content);
                   $def_trid = $sitepress->get_element_trid($employer_id);
                   $ru_args = array(
                       'post_title' => get_the_title($employer_id),
                       'post_content' => $tr_employer_content,
                       'post_status' => 'publish',
                       'post_type' => 'employer'
                   );

                   $ru_post_id = wp_insert_post($ru_args);
                   $sitepress->set_element_language_details($ru_post_id, 'post_employer', $def_trid, $current_lang);

                   $employer_tr_status = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_employer_approved', $employer_tr_status);

                   $employer_tr_user = get_post_meta($employer_id, 'jobsearch_user_id', true);
                   update_post_meta($ru_post_id, 'jobsearch_user_id', $employer_tr_user);

                   //
                   do_action('jobsearch_dashboard_pass_values_to_duplicate_post', $employer_id, $ru_post_id, 'employer');

                   // Duplicate all the custom fields
                   $custom_fields = get_post_custom($employer_id);
                   foreach ($custom_fields as $key => $value) {
                       if (is_array($value) && count($value) > 0) {
                           foreach ($value as $i => $v) {
                               $result = $wpdb->insert($wpdb->prefix . 'postmeta', array(
                                   'post_id' => $ru_post_id,
                                   'meta_key' => $key,
                                   'meta_value' => $v
                               ));
                           }
                       }
                   }

                   // location
                   $loc1_val = get_post_meta($employer_id, 'jobsearch_field_location_location1', true);
                   if ($loc1_val != '') {
                       $loc_term_obj = get_term_by('slug', $loc1_val, 'job-location');
                       if (is_object($loc_term_obj) && isset($loc_term_obj->term_id)) {
                           $loc_term_id = $loc_term_obj->term_id;
                           $tr_tax_id = icl_object_id($loc_term_id, 'job-location', true, $current_lang);
                           $tr_loc_term_obj = get_term_by('id', $tr_tax_id, 'job-location');
                           update_post_meta($ru_post_id, 'jobsearch_field_location_location1', $tr_loc_term_obj->slug);
                       }
                   }
                   $loc1_val = get_post_meta($employer_id, 'jobsearch_field_location_location2', true);
                   if ($loc1_val != '') {
                       $loc_term_obj = get_term_by('slug', $loc1_val, 'job-location');
                       if (is_object($loc_term_obj) && isset($loc_term_obj->term_id)) {
                           $loc_term_id = $loc_term_obj->term_id;
                           $tr_tax_id = icl_object_id($loc_term_id, 'job-location', true, $current_lang);
                           $tr_loc_term_obj = get_term_by('id', $tr_tax_id, 'job-location');
                           update_post_meta($ru_post_id, 'jobsearch_field_location_location2', $tr_loc_term_obj->slug);
                       }
                   }
                   $loc1_val = get_post_meta($employer_id, 'jobsearch_field_location_location3', true);
                   if ($loc1_val != '') {
                       $loc_term_obj = get_term_by('slug', $loc1_val, 'job-location');
                       if (is_object($loc_term_obj) && isset($loc_term_obj->term_id)) {
                           $loc_term_id = $loc_term_obj->term_id;
                           $tr_tax_id = icl_object_id($loc_term_id, 'job-location', true, $current_lang);
                           $tr_loc_term_obj = get_term_by('id', $tr_tax_id, 'job-location');
                           update_post_meta($ru_post_id, 'jobsearch_field_location_location3', $tr_loc_term_obj->slug);
                       }
                   }
                   $loc1_val = get_post_meta($employer_id, 'jobsearch_field_location_location4', true);
                   if ($loc1_val != '') {
                       $loc_term_obj = get_term_by('slug', $loc1_val, 'job-location');
                       if (is_object($loc_term_obj) && isset($loc_term_obj->term_id)) {
                           $loc_term_id = $loc_term_obj->term_id;
                           $tr_tax_id = icl_object_id($loc_term_id, 'job-location', true, $current_lang);
                           $tr_loc_term_obj = get_term_by('id', $tr_tax_id, 'job-location');
                           update_post_meta($ru_post_id, 'jobsearch_field_location_location4', $tr_loc_term_obj->slug);
                       }
                   }

                   // sector
                   $sector_terms = wp_get_post_terms($employer_id, 'sector');
                   if (!empty($sector_terms)) {
                       $set_to_terms = array();
                       foreach ($sector_terms as $sector_term) {
                           $tr_tax_id = icl_object_id($sector_term->term_id, 'sector', true, $current_lang);
                           $set_to_terms[] = $sector_term->term_id;
                       }
                       wp_set_post_terms($ru_post_id, $set_to_terms, 'sector', false);
                   }

                   //
                   $tr_location_adres = get_post_meta($employer_id, 'jobsearch_field_location_address', true);
                   if ($tr_location_adres != '') {
                       update_post_meta($ru_post_id, 'jobsearch_field_location_address', $tr_location_adres);
                   }
                   $tr_location_lat = get_post_meta($employer_id, 'jobsearch_field_location_lat', true);
                   if ($tr_location_lat != '') {
                       update_post_meta($ru_post_id, 'jobsearch_field_location_lat', $tr_location_lat);
                   }
                   $tr_location_lng = get_post_meta($employer_id, 'jobsearch_field_location_lng', true);
                   if ($tr_location_lng != '') {
                       update_post_meta($ru_post_id, 'jobsearch_field_location_lng', $tr_location_lng);
                   }
                   $tr_location_zoom = get_post_meta($employer_id, 'jobsearch_field_location_zoom', true);
                   if ($tr_location_zoom != '') {
                       update_post_meta($ru_post_id, 'jobsearch_field_location_zoom', $tr_location_zoom);
                   }
                   $tr_location_hieght = get_post_meta($employer_id, 'jobsearch_field_map_height', true);
                   if ($tr_location_hieght != '') {
                       update_post_meta($ru_post_id, 'jobsearch_field_map_height', $tr_location_hieght);
                   }

                   // dob and phone
                   $tr_dob_dd = get_post_meta($employer_id, 'jobsearch_field_user_dob_dd', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_dob_dd', $tr_dob_dd);
                   $tr_dob_mm = get_post_meta($employer_id, 'jobsearch_field_user_dob_mm', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_dob_mm', $tr_dob_mm);
                   $tr_dob_yy = get_post_meta($employer_id, 'jobsearch_field_user_dob_yy', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_dob_yy', $tr_dob_yy);
                   $tr_dob_phone = get_post_meta($employer_id, 'jobsearch_field_user_phone', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_phone', $tr_dob_phone);

                   // gallery imgs
                   $tr_gallery_imgs = get_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_company_gallery_imgs', $tr_gallery_imgs);

                   // social links
                   $tr_facebook = get_post_meta($employer_id, 'jobsearch_field_user_facebook_url', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_facebook_url', $tr_facebook);
                   $tr_twitter = get_post_meta($employer_id, 'jobsearch_field_user_twitter_url', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_twitter_url', $tr_twitter);
                   $tr_google = get_post_meta($employer_id, 'jobsearch_field_user_google_plus_url', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_google_plus_url', $tr_google);
                   $tr_linkedin = get_post_meta($employer_id, 'jobsearch_field_user_linkedin_url', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_linkedin_url', $tr_linkedin);
                   $tr_dribbble = get_post_meta($employer_id, 'jobsearch_field_user_dribbble_url', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_dribbble_url', $tr_dribbble);

                   // employer team
                   $tr_team_title = get_post_meta($employer_id, 'jobsearch_field_team_title', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_team_title', $tr_team_title);
                   $tr_team_img = get_post_meta($employer_id, 'jobsearch_field_team_image', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_team_image', $tr_team_img);
                   $tr_team_desig = get_post_meta($employer_id, 'jobsearch_field_team_designation', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_team_designation', $tr_team_desig);
                   $tr_team_exp = get_post_meta($employer_id, 'jobsearch_field_team_experience', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_team_experience', $tr_team_exp);
                   $tr_team_facebook = get_post_meta($employer_id, 'jobsearch_field_team_facebook', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_team_facebook', $tr_team_facebook);
                   $tr_team_google = get_post_meta($employer_id, 'jobsearch_field_team_google', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_team_google', $tr_team_google);
                   $tr_team_twitter = get_post_meta($employer_id, 'jobsearch_field_team_twitter', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_team_twitter', $tr_team_twitter);
                   $tr_team_linkedin = get_post_meta($employer_id, 'jobsearch_field_team_linkedin', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_team_linkedin', $tr_team_linkedin);
                   $tr_team_desc = get_post_meta($employer_id, 'jobsearch_field_team_description', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_team_description', $tr_team_desc);

                   $tr_team_title_count = 0;
                   if (!empty($tr_team_title)) {
                       $tr_team_title_count = count($tr_team_title);
                   }
                   update_post_meta($ru_post_id, 'jobsearch_field_employer_team_size', $tr_team_title_count);

                   // Feature Img
                   $tr_thumbnail_id = get_post_thumbnail_id($employer_id);
                   if ($tr_thumbnail_id > 0) {
                       set_post_thumbnail($ru_post_id, $tr_thumbnail_id);
                   }

                   // Cover Img
                   if (class_exists('JobSearchMultiPostThumbnails')) {
                       $tr_cover_image_id = JobSearchMultiPostThumbnails::get_post_thumbnail_id('employer', 'cover-image', $employer_id);
                       JobSearchMultiPostThumbnails::set_front_thumbnail($ru_post_id, $tr_cover_image_id, 'cover-image');
                   }
               } else {
                   $_res_posts = $res_query->posts;
                   $ru_post_id = isset($_res_posts[0]->ID) ? $_res_posts[0]->ID : 0;
                   if ($ru_post_id > 0) {

                       $employer_tr_status = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);
                       update_post_meta($ru_post_id, 'jobsearch_field_employer_approved', $employer_tr_status);

                       $employer_tr_user = get_post_meta($employer_id, 'jobsearch_user_id', true);
                       update_post_meta($ru_post_id, 'jobsearch_user_id', $employer_tr_user);
                   }
               }
           }
           if ($user_is_candidate) {
               $candidate_id = jobsearch_get_user_candidate_id($user_id);
               $current_lang = $sitepress->get_current_language();
               $args = array(
                   'post_type' => 'candidate',
                   'posts_per_page' => '1',
                   'post_status' => 'publish',
                   'post__in' => array($candidate_id),
               );
               $res_query = new WP_Query($args);
               $found_res = $res_query->found_posts;
               wp_reset_postdata();

               if ($found_res <= 0) {
                   $tr_candidate_obj = get_post($candidate_id);
                   $tr_candidate_content = $tr_candidate_obj->post_content;
                   $tr_candidate_content = apply_filters('the_content', $tr_candidate_content);
                   $def_trid = $sitepress->get_element_trid($candidate_id);
                   $ru_args = array(
                       'post_title' => get_the_title($candidate_id),
                       'post_content' => $tr_candidate_content,
                       'post_status' => 'publish',
                       'post_type' => 'candidate'
                   );

                   $ru_post_id = wp_insert_post($ru_args);
                   $sitepress->set_element_language_details($ru_post_id, 'post_candidate', $def_trid, $current_lang);

                   $candidate_tr_status = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_candidate_approved', $candidate_tr_status);

                   $candidate_tr_user = get_post_meta($candidate_id, 'jobsearch_user_id', true);
                   update_post_meta($ru_post_id, 'jobsearch_user_id', $candidate_tr_user);

                   //
                   do_action('jobsearch_dashboard_pass_values_to_duplicate_post', $candidate_id, $ru_post_id, 'candidate');

                   // Duplicate all the custom fields
                   $custom_fields = get_post_custom($candidate_id);
                   foreach ($custom_fields as $key => $value) {
                       if (is_array($value) && count($value) > 0) {
                           foreach ($value as $i => $v) {
                               $result = $wpdb->insert($wpdb->prefix . 'postmeta', array(
                                   'post_id' => $ru_post_id,
                                   'meta_key' => $key,
                                   'meta_value' => $v
                               ));
                           }
                       }
                   }

                   // location
                   $loc1_val = get_post_meta($candidate_id, 'jobsearch_field_location_location1', true);
                   if ($loc1_val != '') {
                       $loc_term_obj = get_term_by('slug', $loc1_val, 'job-location');
                       if (is_object($loc_term_obj) && isset($loc_term_obj->term_id)) {
                           $loc_term_id = $loc_term_obj->term_id;
                           $tr_tax_id = icl_object_id($loc_term_id, 'job-location', true, $current_lang);
                           $tr_loc_term_obj = get_term_by('id', $tr_tax_id, 'job-location');
                           update_post_meta($ru_post_id, 'jobsearch_field_location_location1', $tr_loc_term_obj->slug);
                       }
                   }
                   $loc1_val = get_post_meta($candidate_id, 'jobsearch_field_location_location2', true);
                   if ($loc1_val != '') {
                       $loc_term_obj = get_term_by('slug', $loc1_val, 'job-location');
                       if (is_object($loc_term_obj) && isset($loc_term_obj->term_id)) {
                           $loc_term_id = $loc_term_obj->term_id;
                           $tr_tax_id = icl_object_id($loc_term_id, 'job-location', true, $current_lang);
                           $tr_loc_term_obj = get_term_by('id', $tr_tax_id, 'job-location');
                           update_post_meta($ru_post_id, 'jobsearch_field_location_location2', $tr_loc_term_obj->slug);
                       }
                   }
                   $loc1_val = get_post_meta($candidate_id, 'jobsearch_field_location_location3', true);
                   if ($loc1_val != '') {
                       $loc_term_obj = get_term_by('slug', $loc1_val, 'job-location');
                       if (is_object($loc_term_obj) && isset($loc_term_obj->term_id)) {
                           $loc_term_id = $loc_term_obj->term_id;
                           $tr_tax_id = icl_object_id($loc_term_id, 'job-location', true, $current_lang);
                           $tr_loc_term_obj = get_term_by('id', $tr_tax_id, 'job-location');
                           update_post_meta($ru_post_id, 'jobsearch_field_location_location3', $tr_loc_term_obj->slug);
                       }
                   }
                   $loc1_val = get_post_meta($candidate_id, 'jobsearch_field_location_location4', true);
                   if ($loc1_val != '') {
                       $loc_term_obj = get_term_by('slug', $loc1_val, 'job-location');
                       if (is_object($loc_term_obj) && isset($loc_term_obj->term_id)) {
                           $loc_term_id = $loc_term_obj->term_id;
                           $tr_tax_id = icl_object_id($loc_term_id, 'job-location', true, $current_lang);
                           $tr_loc_term_obj = get_term_by('id', $tr_tax_id, 'job-location');
                           update_post_meta($ru_post_id, 'jobsearch_field_location_location4', $tr_loc_term_obj->slug);
                       }
                   }

                   // sector
                   $sector_terms = wp_get_post_terms($candidate_id, 'sector');
                   if (!empty($sector_terms)) {
                       $set_to_terms = array();
                       foreach ($sector_terms as $sector_term) {
                           $tr_tax_id = icl_object_id($sector_term->term_id, 'sector', true, $current_lang);
                           $set_to_terms[] = $sector_term->term_id;
                       }
                       wp_set_post_terms($ru_post_id, $set_to_terms, 'sector', false);
                   }
                   // skill
                   $skill_terms = wp_get_post_terms($candidate_id, 'skill');
                   if (!empty($skill_terms)) {
                       $set_to_terms = array();
                       foreach ($skill_terms as $sector_term) {
                           $tr_tax_id = icl_object_id($sector_term->term_id, 'skill', true, $current_lang);
                           $set_to_terms[] = $sector_term->term_id;
                       }
                       wp_set_post_terms($ru_post_id, $set_to_terms, 'skill', false);
                   }

                   //
                   $tr_location_adres = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);
                   if ($tr_location_adres != '') {
                       update_post_meta($ru_post_id, 'jobsearch_field_location_address', $tr_location_adres);
                   }
                   $tr_location_lat = get_post_meta($candidate_id, 'jobsearch_field_location_lat', true);
                   if ($tr_location_lat != '') {
                       update_post_meta($ru_post_id, 'jobsearch_field_location_lat', $tr_location_lat);
                   }
                   $tr_location_lng = get_post_meta($candidate_id, 'jobsearch_field_location_lng', true);
                   if ($tr_location_lng != '') {
                       update_post_meta($ru_post_id, 'jobsearch_field_location_lng', $tr_location_lng);
                   }
                   $tr_location_zoom = get_post_meta($candidate_id, 'jobsearch_field_location_zoom', true);
                   if ($tr_location_zoom != '') {
                       update_post_meta($ru_post_id, 'jobsearch_field_location_zoom', $tr_location_zoom);
                   }
                   $tr_location_hieght = get_post_meta($candidate_id, 'jobsearch_field_map_height', true);
                   if ($tr_location_hieght != '') {
                       update_post_meta($ru_post_id, 'jobsearch_field_map_height', $tr_location_hieght);
                   }

                   // dob and phone
                   $tr_dob_dd = get_post_meta($candidate_id, 'jobsearch_field_user_dob_dd', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_dob_dd', $tr_dob_dd);
                   $tr_dob_mm = get_post_meta($candidate_id, 'jobsearch_field_user_dob_mm', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_dob_mm', $tr_dob_mm);
                   $tr_dob_yy = get_post_meta($candidate_id, 'jobsearch_field_user_dob_yy', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_dob_yy', $tr_dob_yy);
                   $tr_dob_phone = get_post_meta($candidate_id, 'jobsearch_field_user_phone', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_phone', $tr_dob_phone);

                   // social links
                   $tr_facebook = get_post_meta($candidate_id, 'jobsearch_field_user_facebook_url', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_facebook_url', $tr_facebook);
                   $tr_twitter = get_post_meta($candidate_id, 'jobsearch_field_user_twitter_url', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_twitter_url', $tr_twitter);
                   $tr_google = get_post_meta($candidate_id, 'jobsearch_field_user_google_plus_url', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_google_plus_url', $tr_google);
                   $tr_linkedin = get_post_meta($candidate_id, 'jobsearch_field_user_linkedin_url', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_linkedin_url', $tr_linkedin);
                   $tr_dribbble = get_post_meta($candidate_id, 'jobsearch_field_user_dribbble_url', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_dribbble_url', $tr_dribbble);

                   // job title
                   $tr_job_title = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_candidate_jobtitle', $tr_job_title);

                   // salary
                   $tr_salary_type = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_type', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_candidate_salary_type', $tr_salary_type);
                   $tr_salary = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_candidate_salary', $tr_salary);
                   $tr_salary_currency = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_currency', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_candidate_salary_currency', $tr_salary_currency);
                   $tr_salary_pos = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_pos', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_candidate_salary_pos', $tr_salary_pos);
                   $tr_salary_sep = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_sep', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_candidate_salary_sep', $tr_salary_sep);
                   $tr_salary_deci = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_deci', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_candidate_salary_deci', $tr_salary_deci);

                   // candidate cover letter
                   $tr_candidate_cover_letter = get_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_resume_cover_letter', $tr_candidate_cover_letter);

                   // candidate education
                   $tr_candidate_edu_title = get_post_meta($candidate_id, 'jobsearch_field_education_title', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_education_title', $tr_candidate_edu_title);
                   $tr_candidate_edu_year = get_post_meta($candidate_id, 'jobsearch_field_education_year', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_education_year', $tr_candidate_edu_year);
                   $tr_candidate_edu_acadmy = get_post_meta($candidate_id, 'jobsearch_field_education_academy', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_education_academy', $tr_candidate_edu_acadmy);
                   $tr_candidate_edu_desc = get_post_meta($candidate_id, 'jobsearch_field_education_description', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_education_description', $tr_candidate_edu_desc);

                   // candidate experience
                   $tr_candidate_exp_title = get_post_meta($candidate_id, 'jobsearch_field_experience_title', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_experience_title', $tr_candidate_exp_title);
                   $tr_candidate_exp_sdate = get_post_meta($candidate_id, 'jobsearch_field_experience_start_date', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_experience_start_date', $tr_candidate_exp_sdate);
                   $tr_candidate_exp_edate = get_post_meta($candidate_id, 'jobsearch_field_experience_end_date', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_experience_end_date', $tr_candidate_exp_edate);
                   $tr_candidate_exp_compny = get_post_meta($candidate_id, 'jobsearch_field_experience_company', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_experience_company', $tr_candidate_exp_compny);
                   $tr_candidate_exp_desc = get_post_meta($candidate_id, 'jobsearch_field_experience_description', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_experience_description', $tr_candidate_exp_desc);

                   // candidate skills
                   $tr_candidate_skill_title = get_post_meta($candidate_id, 'jobsearch_field_skill_title', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_skill_title', $tr_candidate_skill_title);
                   $tr_candidate_skill_perc = get_post_meta($candidate_id, 'jobsearch_field_skill_percentage', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_skill_percentage', $tr_candidate_skill_perc);

                   // candidate awards
                   $tr_candidate_award_title = get_post_meta($candidate_id, 'jobsearch_field_award_title', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_award_title', $tr_candidate_award_title);
                   $tr_candidate_award_year = get_post_meta($candidate_id, 'jobsearch_field_award_year', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_award_year', $tr_candidate_award_year);
                   $tr_candidate_award_desc = get_post_meta($candidate_id, 'jobsearch_field_award_description', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_award_description', $tr_candidate_award_desc);

                   // candidate portfolio
                   $tr_candidate_port_title = get_post_meta($candidate_id, 'jobsearch_field_portfolio_title', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_portfolio_title', $tr_candidate_port_title);
                   $tr_candidate_port_img = get_post_meta($candidate_id, 'jobsearch_field_portfolio_image', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_portfolio_image', $tr_candidate_port_img);
                   $tr_candidate_port_url = get_post_meta($candidate_id, 'jobsearch_field_portfolio_url', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_portfolio_url', $tr_candidate_port_url);
                   $tr_candidate_port_vurl = get_post_meta($candidate_id, 'jobsearch_field_portfolio_vurl', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_portfolio_vurl', $tr_candidate_port_vurl);

                   // CV attachment
                   $tr_arg_arr = get_post_meta($candidate_id, 'candidate_cv_file', true);
                   update_post_meta($ru_post_id, 'candidate_cv_file', $tr_arg_arr);
                   $tr_file_url = get_post_meta($candidate_id, 'jobsearch_field_user_cv_attachment', true);
                   update_post_meta($ru_post_id, 'jobsearch_field_user_cv_attachment', $tr_file_url);

                   // Feature Img
                   $tr_thumbnail_id = get_post_thumbnail_id($candidate_id);
                   if ($tr_thumbnail_id > 0) {
                       set_post_thumbnail($ru_post_id, $tr_thumbnail_id);
                   }
               } else {
                   $_res_posts = $res_query->posts;
                   $ru_post_id = isset($_res_posts[0]->ID) ? $_res_posts[0]->ID : 0;
                   if ($ru_post_id > 0) {

                       $candidate_tr_status = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                       update_post_meta($ru_post_id, 'jobsearch_field_candidate_approved', $candidate_tr_status);

                       $candidate_tr_user = get_post_meta($candidate_id, 'jobsearch_user_id', true);
                       update_post_meta($ru_post_id, 'jobsearch_user_id', $candidate_tr_user);
                   }
               }
           }
       }
   }

   add_action('init', 'jobsearch_wpml_table_install');

   function jobsearch_wpml_table_install() {
       global $wpdb;
       if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {

           $table_name = $wpdb->prefix . 'icl_string_packages';
           $charset_collate = $wpdb->get_charset_collate();
           $sql = "SET NAMES utf8;
        SET time_zone = '+00:00';
        SET foreign_key_checks = 0;
        SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

        SET NAMES utf8mb4;

        DROP TABLE IF EXISTS `" . $table_name . "`;
        CREATE TABLE `" . $table_name . "` (
          `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `kind_slug` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
          `kind` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
          `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
          `title` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
          `edit_link` text COLLATE utf8mb4_unicode_ci NOT NULL,
          `view_link` text COLLATE utf8mb4_unicode_ci NOT NULL,
          `post_id` int(11) DEFAULT NULL,
          PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

           require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
           dbDelta($sql);
       }
   }

   add_action('init', 'jobsearch_plugin_options_translation_strings');

   function jobsearch_plugin_options_translation_strings() {
       global $jobsearch_plugin_options;
       $salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';
       if (!empty($salary_types)) {
           foreach ($salary_types as $salary_type) {
               do_action('wpml_register_single_string', 'JobSearch Options', 'Salary Type - ' . $salary_type, $salary_type);
           }
       }

       $job_submit_title = isset($jobsearch_plugin_options['job-submit-title']) ? $jobsearch_plugin_options['job-submit-title'] : '';
       $job_submit_desc = isset($jobsearch_plugin_options['job-submit-msge']) ? $jobsearch_plugin_options['job-submit-msge'] : '';
       do_action('wpml_register_single_string', 'JobSearch Options', 'Job Submit Title - ' . $job_submit_title, $job_submit_title);
       do_action('wpml_register_single_string', 'JobSearch Options', 'Job Submit Message - ' . $job_submit_desc, $job_submit_desc);

       $security_questions = isset($jobsearch_plugin_options['jobsearch-security-questions']) ? $jobsearch_plugin_options['jobsearch-security-questions'] : '';
       if (!empty($security_questions)) {
           foreach ($security_questions as $security_question) {
               do_action('wpml_register_single_string', 'JobSearch Options', 'Security Question - ' . $security_question, $security_question);
           }
       }

       //
       $candidate_unapproved_text = isset($jobsearch_plugin_options['unapproverd_candidate_txt']) ? $jobsearch_plugin_options['unapproverd_candidate_txt'] : '';
       do_action('wpml_register_single_string', 'JobSearch Options', 'Unapproverd Candidate Message - ' . $candidate_unapproved_text, $candidate_unapproved_text);

       $employer_unapproved_text = isset($jobsearch_plugin_options['unapproverd_employer_txt']) ? $jobsearch_plugin_options['unapproverd_employer_txt'] : '';
       do_action('wpml_register_single_string', 'JobSearch Options', 'Unapproverd Employer Message - ' . $employer_unapproved_text, $employer_unapproved_text);
   }

   if (!function_exists('jobsearch_contact_form_submit')) {

       /**
        * User contact form submit
        * @generate mail
        */
       function jobsearch_contact_form_submit() {
           global $jobsearch_plugin_options;

           $uname = isset($_POST['u_name']) ? $_POST['u_name'] : '';
           $uemail = isset($_POST['u_email']) ? $_POST['u_email'] : '';
           $uphone = isset($_POST['u_phone']) ? $_POST['u_phone'] : '';
           $umsg = isset($_POST['u_msg']) ? $_POST['u_msg'] : '';
           $utype = isset($_POST['u_type']) ? $_POST['u_type'] : '';

           if ($utype == 'content') {
               $cnt_email = get_bloginfo('admin_email');
           } else {
               $cnt_email = $utype;
           }

           $error = 0;
           $msg = '';

           if ($umsg != '' && $error == 0) {
               $umsg = esc_html($umsg);
           } else {
               $error = 1;
               $msg = esc_html__('Please type your message.', 'wp-jobsearch');
           }

           if ($uemail != '' && $error == 0 && filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
               $uemail = esc_html($uemail);
           } else {
               $error = 1;
               $msg = esc_html__('Please Enter a valid email.', 'wp-jobsearch');
           }
           if ($uname != '' && $error == 0) {
               $uname = esc_html($uname);
           } else {
               $error = 1;
               $msg = esc_html__('Please Enter your Name.', 'wp-jobsearch');
           }

           if ($msg == '' && $error == 0) {

               $subject = sprintf(__('%s - Contact Form Message', 'wp-jobsearch'), get_bloginfo('name'));

               $headers = "From: " . ($uemail) . "\r\n";
               $headers .= "Reply-To: " . ($uemail) . "\r\n";
               $headers .= "CC: " . get_bloginfo('admin_email') . "\r\n";
               $headers .= "MIME-Version: 1.0\r\n";
               $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

               $email_message = sprintf(esc_html__('Name : %s', 'wp-jobsearch'), $uname) . "<br>";
               $email_message .= sprintf(esc_html__('Email : %s', 'wp-jobsearch'), $uemail) . "<br>";
               $email_message .= sprintf(esc_html__('Phone Number : %s', 'wp-jobsearch'), $uphone) . "<br>";
               $email_message .= sprintf(esc_html__('Message : %s', 'wp-jobsearch'), $umsg) . "<br>";
               if (mail($cnt_email, $subject, $email_message, $headers)) {
                   $msg = esc_html__('Mail sent successfully', 'wp-jobsearch');
               } else {
                   $msg = esc_html__('Error! There is some problem.', 'wp-jobsearch');
               }
           }

           echo json_encode(array('msg' => $msg));
           wp_die();
       }

       add_action('wp_ajax_jobsearch_contact_form_submit', 'jobsearch_contact_form_submit');
       add_action('wp_ajax_nopriv_jobsearch_contact_form_submit', 'jobsearch_contact_form_submit');
   }

   if (!function_exists('jobsearch_admin_gallery')) {

       function jobsearch_admin_gallery($id = 'jobsearch_gallery', $name = '') {
           global $post;
           wp_enqueue_media();
           $jobsearch_field_random_id = rand(10000000, 99999999);
           ?>
        <div id="gallery_container_<?php echo esc_attr($jobsearch_field_random_id); ?>"
             data-ecid="jobsearch_field_<?php echo esc_attr($id) ?>">
                 <?php
                 $jobsearch_inline_script = '
		<script>
                jQuery(document).ready(function () {
                    jQuery("#gallery_sortable_' . esc_attr($jobsearch_field_random_id) . '").sortable({
                        out: function (event, ui) {
                            jobsearch_field_gallery_sorting_list(\'jobsearch_field_' . sanitize_html_class($id) . '\', \'' . esc_attr($jobsearch_field_random_id) . '\');
                        }
                    });

                    jobsearch_field_num_of_items(\'' . esc_attr($id) . '\', \'' . absint($jobsearch_field_random_id) . '\');

                    jQuery(\'#gallery_container_' . esc_attr($jobsearch_field_random_id) . '\').on(\'click\', \'a.delete\', function () {
                        var listItems = jQuery(\'#gallery_sortable_' . esc_attr($jobsearch_field_random_id) . '\').children();
                        var count = listItems.length;
                        jobsearch_field_num_of_items(\'' . esc_attr($id) . '\', \'' . absint($jobsearch_field_random_id) . '\', count);
                        jQuery(this).closest(\'li.image\').remove();
                        jobsearch_field_gallery_sorting_list(\'jobsearch_field_' . sanitize_html_class($id) . '\', \'' . esc_attr($jobsearch_field_random_id) . '\');
                    });
                });
		</script>';
                 echo force_balance_tags($jobsearch_inline_script);
                 ?>
            <ul class="jobsearch-gallery-images"
                id="gallery_sortable_<?php echo esc_attr($jobsearch_field_random_id); ?>">
                    <?php
                    $gallery = get_post_meta($post->ID, 'jobsearch_field_' . $id, true);
                    $gallery_videos = get_post_meta($post->ID, 'jobsearch_field_company_gallery_videos', true);
                    $gallery_titles = get_post_meta($post->ID, 'jobsearch_field_' . $id . '_title', true);
                    $gallery_style = get_post_meta($post->ID, 'jobsearch_field_' . $id . '_style', true);
                    $gallery_description = get_post_meta($post->ID, 'jobsearch_field_' . $id . '_description', true);
                    $gallery_link = get_post_meta($post->ID, 'jobsearch_field_' . $id . '_link', true);
                    $jobsearch_field_gal_counter = 0;
                    if (is_array($gallery) && sizeof($gallery) > 0) {
                        foreach ($gallery as $attach_id) {

                            if ($attach_id != '') {

                                $attach_r_val = $attach_id;
                                if ($attach_id != '' && absint($attach_id) <= 0) {
                                    $attach_id = jobsearch_get_attachment_id_from_url($attach_id);
                                }

                                $post_thumbnail_image = wp_get_attachment_image_src($attach_id, 'thumbnail');
                                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';

                                $jobsearch_field_gal_id = rand(156546, 956546);

                                $jobsearch_field_gallery_video = isset($gallery_videos[$jobsearch_field_gal_counter]) ? $gallery_videos[$jobsearch_field_gal_counter] : '';
                                $jobsearch_field_gallery_title = isset($gallery_titles[$jobsearch_field_gal_counter]) ? $gallery_titles[$jobsearch_field_gal_counter] : '';
                                $jobsearch_field_gallery_style = isset($gallery_style[$jobsearch_field_gal_counter]) ? $gallery_style[$jobsearch_field_gal_counter] : '';
                                $jobsearch_field_gallery_description = isset($gallery_description[$jobsearch_field_gal_counter]) ? $gallery_description[$jobsearch_field_gal_counter] : '';
                                $jobsearch_field_gallery_link = isset($gallery_link[$jobsearch_field_gal_counter]) ? $gallery_link[$jobsearch_field_gal_counter] : '';

                                $grid_selected = '';
                                $medium_selected = '';
                                $large_selected = '';
                                if ($jobsearch_field_gallery_style == 'medium') {
                                    $medium_selected = 'selected="selected"';
                                } elseif ($jobsearch_field_gallery_style == 'large') {
                                    $large_selected = 'selected="selected"';
                                } else {
                                    $grid_selected = 'selected="selected"';
                                }

                                $jobsearch_field_attach_img = '<div class="gal-thumb"><img src="' . $post_thumbnail_src . '" width="150" alt="" /></div>';
                                echo '
                            <li class="image" data-attachment_id="' . esc_attr($jobsearch_field_gal_id) . '">
                                ' . $jobsearch_field_attach_img . '
                                <input type="hidden" value="' . $attach_r_val . '" name="jobsearch_field_' . $id . '[]" />
                                <div class="gal-actions">
                                    <span><a href="javascript:void(0);" class="update-gal" data-id="' . absint($jobsearch_field_gal_id) . '"><i class="fa fa-pencil"></i></a></span>
                                    <span><a href="javascript:void(0);" class="delete" data-tip="' . __('Delete', 'wp-jobsearch') . '"><i class="fa fa-times"></i></a></span>
                                </div>
                                <div id="edit_gal_form' . absint($jobsearch_field_gal_id) . '" style="display: none;" class="gallery-form-elem">
                                    <div class="gallery-form-inner">
                                        <div class="jobsearch-heading-area">
                                                <h3>' . __('Edit', 'wp-jobsearch') . '</h3>
                                                <a href="javascript:void(0);" class="close-gal" data-id="' . absint($jobsearch_field_gal_id) . '"> <i class="fa fa-times"></i></a>
                                        </div>
                                        ' . $jobsearch_field_attach_img . '
                                        <div class="jobsearch-element-field">
                                                <div class="elem-label">
                                                        <label>' . __('Video URL', 'wp-jobsearch') . '</label>
                                                </div>
                                                <div class="elem-field">
                                                        <input type="text" name="jobsearch_field_company_gallery_videos[]" value="' . esc_html($jobsearch_field_gallery_video) . '" />
                                                </div>
                                        </div>

                                        <div class="jobsearch-element-field">
                                                <div class="elem-label">
                                                        <label>' . __('Title', 'wp-jobsearch') . '</label>
                                                </div>
                                                <div class="elem-field">
                                                        <input type="text" name="jobsearch_field_' . $id . '_title[]" value="' . esc_html($jobsearch_field_gallery_title) . '" />
                                                </div>
                                        </div>

                                        <div class="jobsearch-element-field">
                                                <div class="elem-label">
                                                        <label>' . __('Description', 'wp-jobsearch') . '</label>
                                                </div>
                                                <div class="elem-field">
                                                        <textarea type="text" name="jobsearch_field_' . $id . '_description[]" >' . force_balance_tags($jobsearch_field_gallery_description) . '</textarea>
                                                </div>
                                        </div>

                                        <div class="jobsearch-element-field" style="display:none;">
                                                <div class="elem-label">
                                                        <label>' . __('URL', 'wp-jobsearch') . '</label>
                                                </div>
                                                <div class="elem-field">
                                                        <input type="text" name="jobsearch_field_' . $id . '_link[]" value="' . esc_html($jobsearch_field_gallery_link) . '" />
                                                </div>
                                        </div>

                                        <div class="jobsearch-element-field" style="display:none;">
                                                <div class="elem-label">
                                                        <label>' . __('Style', 'wp-jobsearch') . '</label>
                                                </div>
                                                <div class="elem-field">
                                                        <select name="jobsearch_field_' . $id . '_style[]" value="' . esc_html($jobsearch_field_gallery_style) . '">
                                                        <option value="grid" ' . esc_html($grid_selected) . '>Grid</option>
                                                        <option value="medium" ' . esc_html($medium_selected) . '>Medium</option>
                                                        <option value="large" ' . esc_html($large_selected) . '>Large</option>
                                                        </select>
                                                </div>
                                        </div>
                                        <input type="button" class="close-gal" data-id="' . absint($jobsearch_field_gal_id) . '" value="' . __('Update', 'wp-jobsearch') . '" />
                                    </div>
                                </div>
                            </li>';
                            }
                            $jobsearch_field_gal_counter++;
                        }
                    }
                    ?>
            </ul>
        </div>
        <div id="jobsearch_field_<?php echo esc_attr($id) ?>_temp"></div>
        <input type="hidden" value="" name="jobsearch_field_<?php echo esc_attr($id) ?>_num"/>
        <div class="jobsearch-add-gal-btn">
            <label class="browse-icon jobsearch_add_gallery hide-if-no-js"
                   data-id="<?php echo 'jobsearch_field_' . sanitize_html_class($id); ?>"
                   data-rand_id="<?php echo esc_attr($jobsearch_field_random_id); ?>">
                <input type="button" class="left" data-choose="<?php echo esc_attr($name); ?>"
                       data-update="<?php echo esc_attr($name); ?>" data-delete="<?php _e('Delete', 'wp-jobsearch'); ?>"
                       value="<?php echo esc_attr($name); ?>">
            </label>
        </div>
        <?php
    }

}

function jobsearch_get_user_ip_address() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

add_action('wp_ajax_jobsearch_get_location_with_latlng', 'jobsearch_get_location_with_latlng');
add_action('wp_ajax_nopriv_jobsearch_get_location_with_latlng', 'jobsearch_get_location_with_latlng');

function jobsearch_get_location_with_latlng() {
    global $jobsearch_plugin_options;

    $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';

    $response_array = array();
    $response_array['address'] = '';

    if ($location_map_type == 'mapbox') {
        $mapbox_access_token = isset($jobsearch_plugin_options['mapbox_access_token']) ? $jobsearch_plugin_options['mapbox_access_token'] : '';

        $lat = (isset($_POST['lat'])) ? $_POST['lat'] : '';
        $lng = (isset($_POST['lng'])) ? $_POST['lng'] : '';

        $geo_loc_url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . $lng . ',' . $lat . '.json?access_token=' . $mapbox_access_token;
        $location_geo = wp_remote_get($geo_loc_url);
        if (!is_wp_error($location_geo)) {
            if (isset($location_geo['body'])) {
                $cords_info = json_decode($location_geo['body'], true);
                $format_address = isset($cords_info['features'][0]['place_name']) ? $cords_info['features'][0]['place_name'] : '';
                $cords = isset($cords_info['features'][0]['geometry']['coordinates']) ? $cords_info['features'][0]['geometry']['coordinates'] : '';
                $latitude = isset($cords[1]) ? $cords[1] : '';
                $longitude = isset($cords[0]) ? $cords[0] : '';

                $response_array['address'] = $format_address;
            }
        }
    } else {
        $google_api_key = isset($jobsearch_plugin_options['jobsearch-google-api-key']) ? $jobsearch_plugin_options['jobsearch-google-api-key'] : '';

        if ($google_api_key != '') {
            $lat = (isset($_POST['lat'])) ? $_POST['lat'] : '';
            $lng = (isset($_POST['lng'])) ? $_POST['lng'] : '';

            $wp_remote_get_args = array(
                'timeout' => 50,
                'compress' => false,
                'decompress' => true,
            );
            $response = wp_remote_get('https://maps.googleapis.com/maps/api/geocode/json?key=' . $google_api_key . '&latlng=' . $lat . ',' . $lng . '&sensor=true', $wp_remote_get_args);
            if (is_array($response)) {
                $data = json_decode($response['body']);

                $location_data = $data->results[0];
                $response_array['address'] = $location_data->formatted_address;
            }
        }
    }
    echo json_encode($response_array);
    wp_die();
}

if (!function_exists('jobsearch_address_to_cords')) {

    function jobsearch_address_to_cords($address = '') {

        global $jobsearch_plugin_options;
        $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';

        if (empty($address)) {
            return false;
        }

        $cords_array = array();
        if ($location_map_type == 'mapbox') {
            $mapbox_access_token = isset($jobsearch_plugin_options['mapbox_access_token']) ? $jobsearch_plugin_options['mapbox_access_token'] : '';
            $geo_loc_url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($address) . '.json?access_token=' . $mapbox_access_token;
            $location_geo = wp_remote_get($geo_loc_url);
            if (!is_wp_error($location_geo)) {
                if (isset($location_geo['body'])) {
                    $cords_info = json_decode($location_geo['body'], true);
                    $format_address = isset($cords_info['features'][0]['place_name']) ? $cords_info['features'][0]['place_name'] : '';
                    $cords = isset($cords_info['features'][0]['geometry']['coordinates']) ? $cords_info['features'][0]['geometry']['coordinates'] : '';
                    $latitude = isset($cords[1]) ? $cords[1] : '';
                    $longitude = isset($cords[0]) ? $cords[0] : '';
                    if (!empty($latitude) && !empty($longitude)) {
                        $cords_array['lat'] = $latitude;
                        $cords_array['lng'] = $longitude;
                        $cords_array['formatted_address'] = $format_address;
                    }
                }
            }
        } else {
            $google_api_key = isset($jobsearch_plugin_options['jobsearch-google-api-key']) ? $jobsearch_plugin_options['jobsearch-google-api-key'] : '';
            $geo_loc_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false' . ($google_api_key != '' ? '&key=' . $google_api_key : '');
            $location_geo = wp_remote_get($geo_loc_url);
            if (!is_wp_error($location_geo)) {
                if (isset($location_geo['body'])) {
                    $cords_info = json_decode($location_geo['body'], true);
                    if (isset($cords_info['results']) && empty($cords_info['results'])) {
                        $location_geo = wp_remote_get('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address));
                    }
                }

                if (isset($location_geo['body'])) {
                    $cords_info = json_decode($location_geo['body'], true);
                    if (isset($cords_info['status']) && $cords_info['status'] == 'OK') {
                        $latitude = isset($cords_info['results'][0]['geometry']['location']['lat']) ? $cords_info['results'][0]['geometry']['location']['lat'] : '';
                        $longitude = isset($cords_info['results'][0]['geometry']['location']['lng']) ? $cords_info['results'][0]['geometry']['location']['lng'] : '';
                        $formatted_address = isset($cords_info['results'][0]['formatted_address']) ? $cords_info['results'][0]['formatted_address'] : '';
                        if (!empty($latitude) && !empty($longitude)) {
                            $cords_array['lat'] = $latitude;
                            $cords_array['lng'] = $longitude;
                            $cords_array['formatted_address'] = $formatted_address;
                        }
                    }
                }
            }
            if (empty($cords_array)) {
                $mapbox_access_token = isset($jobsearch_plugin_options['mapbox_access_token']) ? $jobsearch_plugin_options['mapbox_access_token'] : '';
                if ($mapbox_access_token != '') {
                    $geo_loc_url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($address) . '.json?access_token=' . $mapbox_access_token;
                    $location_geo = wp_remote_get($geo_loc_url);
                    if (!is_wp_error($location_geo)) {
                        if (isset($location_geo['body'])) {
                            $cords_info = json_decode($location_geo['body'], true);
                            $format_address = isset($cords_info['features'][0]['place_name']) ? $cords_info['features'][0]['place_name'] : '';
                            $cords = isset($cords_info['features'][0]['geometry']['coordinates']) ? $cords_info['features'][0]['geometry']['coordinates'] : '';
                            $latitude = isset($cords[1]) ? $cords[1] : '';
                            $longitude = isset($cords[0]) ? $cords[0] : '';
                            if (!empty($latitude) && !empty($longitude)) {
                                $cords_array['lat'] = $latitude;
                                $cords_array['lng'] = $longitude;
                                $cords_array['formatted_address'] = $format_address;
                            }
                        }
                    }
                }
            }
        }
        return $cords_array;
    }

}

function jobsearch_esc_html($input) {
    if (is_array($input)) {
        return $input;
    } else {
        if ($input != '') {
            $input = wp_kses($input, array());
            $input = str_replace(array('"', "='", '="', 'alert('), array('', '', '', ''), $input);
        }
    }
    return $input;
}

function jobsearch_esc_wp_editor($input) {
    $allowed_tags = array();
    /*
     * Allowed Tags for wp editor
     * */

    $allowed_atts = array(
        'align' => array(),
        'class' => array(),
        'type' => array(),
        'id' => array(),
        'style' => array(),
        'src' => array(),
        'alt' => array(),
        'href' => array(),
        'rel' => array(),
        'target' => array(),
        'width' => array(),
        'height' => array(),
        'title' => array(),
        'data' => array(),
    );
    $allowed_tags['form'] = $allowed_atts;
    $allowed_tags['label'] = $allowed_atts;
    $allowed_tags['input'] = $allowed_atts;
    $allowed_tags['textarea'] = $allowed_atts;
    $allowed_tags['iframe'] = $allowed_atts;
    $allowed_tags['style'] = $allowed_atts;
    $allowed_tags['strong'] = $allowed_atts;
    $allowed_tags['small'] = $allowed_atts;
    $allowed_tags['table'] = $allowed_atts;
    $allowed_tags['span'] = $allowed_atts;
    $allowed_tags['abbr'] = $allowed_atts;
    $allowed_tags['code'] = $allowed_atts;
    $allowed_tags['pre'] = $allowed_atts;
    $allowed_tags['div'] = $allowed_atts;
    $allowed_tags['img'] = $allowed_atts;
    $allowed_tags['h1'] = $allowed_atts;
    $allowed_tags['h2'] = $allowed_atts;
    $allowed_tags['h3'] = $allowed_atts;
    $allowed_tags['h4'] = $allowed_atts;
    $allowed_tags['h5'] = $allowed_atts;
    $allowed_tags['h6'] = $allowed_atts;
    $allowed_tags['ol'] = $allowed_atts;
    $allowed_tags['ul'] = $allowed_atts;
    $allowed_tags['li'] = $allowed_atts;
    $allowed_tags['em'] = $allowed_atts;
    $allowed_tags['hr'] = $allowed_atts;
    $allowed_tags['br'] = $allowed_atts;
    $allowed_tags['tr'] = $allowed_atts;
    $allowed_tags['td'] = $allowed_atts;
    $allowed_tags['p'] = $allowed_atts;
    $allowed_tags['a'] = $allowed_atts;
    $allowed_tags['b'] = $allowed_atts;
    $allowed_tags['i'] = $allowed_atts;

    $input = wp_kses($input, $allowed_tags);
    $input = str_replace(array('alert('), array(''), $input);
    return $input;
}

if (!function_exists('jobsearch_social_share')) {

    /*
     * Social Icons.
     * @return
     */

    function jobsearch_social_share() {
        global $jobsearch_plugin_options;

        wp_enqueue_script('jobsearch-addthis');

        $social_facebook = isset($jobsearch_plugin_options['jobsearch-social-sharing-facebook']) ? $jobsearch_plugin_options['jobsearch-social-sharing-facebook'] : '';
        $social_twitter = isset($jobsearch_plugin_options['jobsearch-social-sharing-twitter']) ? $jobsearch_plugin_options['jobsearch-social-sharing-twitter'] : '';
        $social_google = isset($jobsearch_plugin_options['jobsearch-social-sharing-google']) ? $jobsearch_plugin_options['jobsearch-social-sharing-google'] : '';
        $social_pinterest = isset($jobsearch_plugin_options['jobsearch-social-sharing-pinterest']) ? $jobsearch_plugin_options['jobsearch-social-sharing-pinterest'] : '';
        $social_tumblr = isset($jobsearch_plugin_options['jobsearch-social-sharing-tumblr']) ? $jobsearch_plugin_options['jobsearch-social-sharing-tumblr'] : '';
        $social_dribbble = isset($jobsearch_plugin_options['jobsearch-social-sharing-dribbble']) ? $jobsearch_plugin_options['jobsearch-social-sharing-dribbble'] : '';
        $social_instagram = isset($jobsearch_plugin_options['jobsearch-social-sharing-instagram']) ? $jobsearch_plugin_options['jobsearch-social-sharing-instagram'] : '';
        $social_stumbleupon = isset($jobsearch_plugin_options['jobsearch-social-sharing-stumbleupon']) ? $jobsearch_plugin_options['jobsearch-social-sharing-stumbleupon'] : '';

        $social_youtube = isset($jobsearch_plugin_options['jobsearch-social-sharing-youtube']) ? $jobsearch_plugin_options['jobsearch-social-sharing-youtube'] : '';
        $social_sharemore = isset($jobsearch_plugin_options['jobsearch-social-sharing-more']) ? $jobsearch_plugin_options['jobsearch-social-sharing-more'] : '';
        ?>
        <ul class="jobsearch-blog-social-network">
            <?php
            if ($social_facebook == 'on') {
                ?>
                <li>
                    <a class="addthis_button_facebook">
                        <i class="fa fa-facebook-square"></i>
                    </a>
                </li>
                <?php
            }
            if ($social_twitter == 'on') {
                ?>
                <li>
                    <a class="addthis_button_twitter">
                        <i class="fa fa-twitter-square"></i>
                    </a>
                </li>
                <?php
            }
            if ($social_google == 'on') {
                ?>
                <li>
                    <a class="addthis_button_google">
                        <i class="fa fa-google-plus-square"></i>
                    </a>
                </li>
                <?php
            }
            if ($social_tumblr == 'on') {
                ?>
                <li>
                    <a class="addthis_button_tumblr">
                        <i class="fa fa-tumblr-square"></i>
                    </a>
                </li>
                <?php
            }
            if ($social_dribbble == 'on') {
                ?>
                <li>
                    <a class="addthis_button_dribbble">
                        <i class="fa fa-dribbble"></i>
                    </a>
                </li>
                <?php
            }
            if ($social_instagram == 'on') {
                ?>
                <li>
                    <a class="addthis_button_instagram">
                        <i class="fa fa-instagram"></i>
                    </a>
                </li>
                <?php
            }
            if ($social_stumbleupon == 'on') {
                ?>
                <li>
                    <a class="addthis_button_stumbleupon">
                        <i class="fa fa-stumbleupon"></i>
                    </a>
                </li>
                <?php
            }
            if ($social_youtube == 'on') {
                ?>
                <li>
                    <a class="addthis_button_youtube">
                        <i class="fa fa-youtube-square"></i>
                    </a>
                </li>
                <?php
            }
            if ($social_sharemore == 'on') {
                ?>
                <li>
                    <a class="addthis_button_compact">
                        <i class="fa fa-plus-square"></i>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
    }

}

if (!function_exists('jobsearch_get_image_id')) {

    function jobsearch_get_image_id($image_url) {
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url));
        $attachment = isset($attachment[0]) ? $attachment[0] : '';
        return $attachment;
    }

}

if (!function_exists('jobsearch_get_current_page_url')) {

    /**
     * get current page url
     * @generate url
     */
    function get_current_page_url() {
        global $wp, $wp_rewrite;

        $query_args = $wp_rewrite->using_permalinks() ? array() : $wp->query_string;
        $current_url = esc_url_raw(home_url(add_query_arg($query_args, $wp->request)));
        return $current_url;
    }

}

if (!function_exists('jobsearch_all_users')) {

    /**
     * all users.
     * @return markup
     */
    function jobsearch_all_users($first_element = false, $dropdown = false, $role = '') {
        $args = array(
            'order' => 'ASC',
            'orderby' => 'display_name',
        );
        if ($role != '') {
            $args['role'] = $role;
        }
        $user_query = new WP_User_Query($args);
        // Get the results
        $all_users = $user_query->get_results();
        $users_arr = array();
        if ($first_element == true) {
            $users_arr[''] = esc_html__('Please select user', 'wp-jobsearch');
        }
        if (!empty($all_users)) {
            foreach ($all_users as $alb) {
                $author_info = get_userdata($alb->ID);
                $this_users = $author_info->display_name;
                $this_users = apply_filters('jobsearch_user_display_name', $this_users, $author_info);
                if ($dropdown == false) {
                    $users_arr[$this_users] = $alb->ID;
                } else {
                    $users_arr[$alb->ID] = $this_users;
                }
            }
        }
        return $users_arr;
    }

}

add_filter('cron_schedules', 'jobsearch_comon_cron_recurrence_interval');

function jobsearch_comon_cron_recurrence_interval($schedules) {
    $schedules['every_five_sec'] = array(
        'interval' => 5,
        'display' => __('Every 5 sec', 'wp-jobsearch')
    );
    $schedules['every_five_mins'] = array(
        'interval' => 300,
        'display' => __('Every Five Minutes', 'wp-jobsearch')
    );
    $schedules['every_half_hourly'] = array(
        'interval' => 1800,
        'display' => __('Every Half Hourly', 'wp-jobsearch')
    );
    $schedules['every_hourly'] = array(
        'interval' => 3600,
        'display' => __('Every Hour', 'wp-jobsearch')
    );

    return $schedules;
}

if (!function_exists('jobsearch_get_times_array')) {

    function jobsearch_get_times_array($interval = '+30 minutes', $same_value = false) {
        $output = array();
        $current = strtotime('00:00');
        $end = strtotime('23:59');

        while ($current <= $end) {
            $time = date('H:i', $current);
            if ($same_value == false) {
                $output[$time] = date('h.i A', $current);
            } else {
                $output[$time] = date('H:i', $current);
            }
            $current = strtotime($interval, $current);
        }

        return $output;
    }

}

if (!function_exists('jobsearch_get_user_field')) {

    function jobsearch_get_user_field($selected_user, $role = '') {
        global $jobsearch_form_fields;
        $user_first_element = esc_html__('Please select user', 'wp-jobsearch');
        $users = array(
            '' => $user_first_element,
        );
        if ($selected_user) {
            $author_info = get_userdata($selected_user);
            $this_users = $author_info->display_name;
            $this_users = apply_filters('jobsearch_user_display_name', $this_users, $author_info);
            $users[$selected_user] = $this_users;
        }

        $rand_num = rand(1234, 6867867);
        $field_params = array(
            'classes' => 'user_field',
            'id' => 'user_field_' . $rand_num,
            'name' => 'users',
            'options' => $users,
            'force_std' => $selected_user,
            'ext_attr' => ' data-randid="' . $rand_num . '" data-forcestd="' . $selected_user . '" data-loaded="false" data-role="' . $role . '"',
        );
        $jobsearch_form_fields->select_field($field_params);
        ?><span class="jobsearch-field-loader user_loader_<?php echo absint($rand_num); ?>"></span><?php
    }

}

if (!function_exists('jobsearch_load_all_users_data')) {

    function jobsearch_load_all_users_data() {
        $force_std = $_POST['force_std'];
        $role = $_POST['role'];
        $all_users = jobsearch_all_users(true, true, $role);
        $html .= "";
        if (isset($all_users) && !empty($all_users)) {
            foreach ($all_users as $user_var => $user_val) {
                $selected = $user_var == $force_std ? ' selected="selected"' : '';
                $html .= "<option{$selected} value=\"{$user_var}\">{$user_val}</option>" . "\n";
            }
        }
        echo json_encode(array('html' => $html));

        wp_die();
    }

    add_action('wp_ajax_jobsearch_load_all_users_data', 'jobsearch_load_all_users_data');
    add_action('wp_ajax_nopriv_jobsearch_load_all_users_data', 'jobsearch_load_all_users_data');
}

if (!function_exists('jobsearch_count_custom_post_with_filter')) {

    function jobsearch_count_custom_post_with_filter($posttype, $arg = '') {
        $args = array(
            'posts_per_page' => "1",
            'post_type' => $posttype,
            'fields' => 'ids',
            'meta_query' => $arg,
        );

        $custom_query = new WP_Query($args);
        $all_post_count = $custom_query->found_posts;
        return $all_post_count;
    }

}

if (!function_exists('jobsearch_countin_employers_dash_stats')) {

    function jobsearch_countin_employers_dash_stats() {

        $current_timestamp = current_time('timestamp');
        $total_employers = jobsearch_count_custom_post_with_filter('employer');

        $arg = array(
            array(
                'key' => 'jobsearch_field_employer_approved',
                'value' => 'on',
                'compare' => '=',
            ),
        );
        $total_active_employers = jobsearch_count_custom_post_with_filter('employer', $arg);

        $arg = array(
            array(
                'key' => 'jobsearch_field_employer_approved',
                'value' => 'on',
                'compare' => '!=',
            ),
        );
        $total_pending_employers = jobsearch_count_custom_post_with_filter('employer', $arg);

        echo json_encode(array('tot_counts' => absint($total_employers), 'active_counts' => absint($total_active_employers), 'pending_counts' => absint($total_pending_employers)));
        die;
    }

    add_action('wp_ajax_jobsearch_countin_employers_dash_stats', 'jobsearch_countin_employers_dash_stats');
}

if (!function_exists('jobsearch_countin_candidates_dash_stats')) {

    function jobsearch_countin_candidates_dash_stats() {
        $current_timestamp = current_time('timestamp');
        $total_candidates = jobsearch_count_custom_post_with_filter('candidate');

        $arg = array(
            array(
                'key' => 'jobsearch_field_candidate_approved',
                'value' => 'on',
                'compare' => '=',
            ),
        );

        $total_active_candidates = jobsearch_count_custom_post_with_filter('candidate', $arg);
        $arg = array(
            array(
                'key' => 'jobsearch_field_candidate_approved',
                'value' => 'on',
                'compare' => '!=',
            ),
        );

        $total_pending_candidates = jobsearch_count_custom_post_with_filter('candidate', $arg);
        echo json_encode(array('tot_counts' => absint($total_candidates), 'active_counts' => absint($total_active_candidates), 'pending_counts' => absint($total_pending_candidates)));
        die;
    }

    add_action('wp_ajax_jobsearch_countin_candidates_dash_stats', 'jobsearch_countin_candidates_dash_stats');
}

if (!function_exists('jobsearch_countin_jobs_dash_stats')) {

    function jobsearch_countin_jobs_dash_stats() {
        $current_timestamp = current_time('timestamp');
        $total_jobs = jobsearch_count_custom_post_with_filter('job');
        $arg = array(
            array(
                'key' => 'jobsearch_field_job_publish_date',
                'value' => $current_timestamp,
                'compare' => '<=',
            ),
            array(
                'key' => 'jobsearch_field_job_expiry_date',
                'value' => $current_timestamp,
                'compare' => '>=',
            ),
            array(
                'key' => 'jobsearch_field_job_status',
                'value' => 'approved',
                'compare' => '=',
            ),
        );
        $total_active_jobs = jobsearch_count_custom_post_with_filter('job', $arg);
        $arg = array(
            array(
                'key' => 'jobsearch_field_job_status',
                'value' => 'pending',
                'compare' => '=',
            ),
        );
        $total_pending_jobs = jobsearch_count_custom_post_with_filter('job', $arg);
        $arg = array(
            array(
                'key' => 'jobsearch_field_job_expiry_date',
                'value' => $current_timestamp,
                'compare' => '<=',
            ),
            array(
                'key' => 'jobsearch_field_job_expiry_date',
                'value' => '',
                'compare' => '!=',
            ),
        );
        $total_expired_jobs = jobsearch_count_custom_post_with_filter('job', $arg);

        echo json_encode(array('tot_counts' => absint($total_jobs), 'active_counts' => absint($total_active_jobs), 'pending_counts' => absint($total_pending_jobs), 'expire_counts' => absint($total_expired_jobs)));
        die;
    }

    add_action('wp_ajax_jobsearch_countin_jobs_dash_stats', 'jobsearch_countin_jobs_dash_stats');
}

if (!function_exists('jobsearch_load_all_custom_post_data')) {

    function jobsearch_load_all_custom_post_data() {
        $force_std = $_POST['force_std'];
        $posttype = $_POST['posttype'];
        $placelabel = $_POST['placelabel'];
        $args = array(
            'posts_per_page' => "-1",
            'post_type' => $posttype,
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_query' => array(),
        );
        $args = apply_filters('jobsearch_custom_ajax_postselct_loder_qargs', $args, $posttype);
        $custom_query = new WP_Query($args);
        $all_records = $custom_query->posts;

        $html = "";
        if ($placelabel != '') {
            $html .= "<option value=''>{$placelabel}</option>" . "\n";
        }
        if (isset($all_records) && !empty($all_records)) {
            foreach ($all_records as $user_var) {
                $selected = $user_var == $force_std ? ' selected="selected"' : '';
                $post_title = get_the_title($user_var);
                $html .= "<option{$selected} value=\"{$user_var}\">{$post_title}</option>" . "\n";
            }
        }
        echo json_encode(array('html' => $html));
        wp_die();
    }

    add_action('wp_ajax_jobsearch_load_all_custom_post_data', 'jobsearch_load_all_custom_post_data');
    add_action('wp_ajax_nopriv_jobsearch_load_all_custom_post_data', 'jobsearch_load_all_custom_post_data');
}

if (!function_exists('jobsearch_get_custom_post_field')) {

    function jobsearch_get_custom_post_field($selected_id, $custom_post_slug, $field_label, $field_name, $custom_name = '') {
        global $jobsearch_form_fields;
        $custom_posts = array(
            '' => $field_label,
        );
        if ($selected_id) {
            $this_custom_posts = get_the_title($selected_id);
            $custom_posts[$selected_id] = $this_custom_posts;
        }

        $rand_num = rand(1234568, 6867867);
        $field_params = array(
            'classes' => 'custom_post_field',
            'id' => 'custom_post_field_' . $rand_num,
            'name' => $field_name,
            'options' => $custom_posts,
            'force_std' => $selected_id,
            'ext_attr' => ' data-randid="' . $rand_num . '" data-forcestd="' . $selected_id . '" data-loaded="false" data-placelabel="' . $field_label . '" data-posttype="' . $custom_post_slug . '"',
        );
        if (isset($custom_name) && $custom_name != '') {
            $field_params['cus_name'] = $custom_name;
        }
        $jobsearch_form_fields->select_field($field_params);
        ?>
        <span class="jobsearch-field-loader custom_post_loader_<?php echo absint($rand_num); ?>"></span>
        <?php
    }

}

function jobsearch_get_post_id_bytitle($title, $type = 'post') {
    global $wpdb;
    if ($title != '') {
        $post_query = "SELECT posts.ID FROM $wpdb->posts AS posts";
        $post_query .= " WHERE posts.post_title='{$title}' AND posts.post_type='{$type}'";
        $post_query .= " LIMIT 1";
        $get_db_res = $wpdb->get_col($post_query);
        if (isset($get_db_res[0])) {
            return $get_db_res[0];
        }
    }
    return 0;
}

function jobsearch_get_postmeta_id_byval($meta_key, $meta_value) {
    global $wpdb;
    if ($meta_key != '' && $meta_value != '') {
        $post_query = "SELECT postmeta.meta_id FROM $wpdb->postmeta AS postmeta";
        $post_query .= " WHERE meta_key='{$meta_key}' AND meta_value='{$meta_value}'";
        $post_query .= " LIMIT 1";
        $get_db_res = $wpdb->get_col($post_query);
        if (isset($get_db_res[0])) {
            return $get_db_res[0];
        }
    }
    return 0;
}

if (!function_exists('jobsearch_updated_job_featured_meta')) {

    function jobsearch_updated_job_featured_meta($selected_user) {
        $job_id = $_POST['job_id'];
        $option = $_POST['option'];

        $job_expiry_date = get_post_meta($job_id, 'jobsearch_field_job_expiry_date', true);

        $featured_val = 'on';
        $return_html = esc_html__('Yes', 'wp-jobsearch');
        if ($option == 'un-feature') {
            $featured_val = 'off';
            $return_html = esc_html__('No', 'wp-jobsearch');
        }
        if (isset($_POST['from_f'])) {
            $return_html = esc_html__('Make Featured Job', 'wp-jobsearch');
            if ($option == 'featured') {
                $return_html = esc_html__('Featured', 'wp-jobsearch');
            }
        }
        update_post_meta($job_id, 'jobsearch_field_job_featured', $featured_val);
        if ($job_expiry_date > 0) {
            $job_feature_till = date('d-m-Y H:i:s', $job_expiry_date);
            update_post_meta($job_id, 'jobsearch_field_job_feature_till', $job_feature_till);
            if (isset($_POST['from_f'])) {
                if ($option == 'featured') {
                    $job_feature_until = date_i18n(get_option('date_format'), strtotime($job_feature_till));
                    $return_html = sprintf(esc_html__('Featured Till: %s', 'wp-jobsearch'), $job_feature_until);
                }
            }
        }
        echo json_encode(array('html' => $return_html));

        wp_die();
    }

    add_action('wp_ajax_jobsearch_updated_job_featured_meta', 'jobsearch_updated_job_featured_meta');
    add_action('wp_ajax_nopriv_jobsearch_updated_job_featured_meta', 'jobsearch_updated_job_featured_meta');
}

if (!function_exists('jobsearch_updated_candidate_featured_meta')) {

    function jobsearch_updated_candidate_featured_meta($selected_user) {
        $candidate_id = $_POST['candidate_id'];
        $option = $_POST['option'];
        $featured_val = 'on';
        $return_html = esc_html__('Yes', 'wp-jobsearch');
        if ($option == 'un-feature') {
            $featured_val = 'off';
            $return_html = esc_html__('No', 'wp-jobsearch');
        }
        update_post_meta($candidate_id, 'jobsearch_field_candidate_featured', $featured_val);
        echo json_encode(array('html' => $return_html));

        wp_die();
    }

    add_action('wp_ajax_jobsearch_updated_candidate_featured_meta', 'jobsearch_updated_candidate_featured_meta');
    add_action('wp_ajax_nopriv_jobsearch_updated_candidate_featured_meta', 'jobsearch_updated_candidate_featured_meta');
}

if (!function_exists('jobsearch_updated_employer_featured_meta')) {

    function jobsearch_updated_employer_featured_meta($selected_user) {
        $employer_id = $_POST['employer_id'];
        $option = $_POST['option'];
        $featured_val = 'on';
        $return_html = esc_html__('Yes', 'wp-jobsearch');
        if ($option == 'un-feature') {
            $featured_val = 'off';
            $return_html = esc_html__('No', 'wp-jobsearch');
        }
        update_post_meta($employer_id, 'jobsearch_field_employer_featured', $featured_val);
        echo json_encode(array('html' => $return_html));

        wp_die();
    }

    add_action('wp_ajax_jobsearch_updated_employer_featured_meta', 'jobsearch_updated_employer_featured_meta');
    add_action('wp_ajax_nopriv_jobsearch_updated_employer_featured_meta', 'jobsearch_updated_employer_featured_meta');
}

// Getting user attached candidate id
function jobsearch_get_user_candidate_id($user_id = 0) {
    global $sitepress;
    $user_candidate_id = get_user_meta($user_id, 'jobsearch_candidate_id', true);
    $user_candidate_id = $user_candidate_id > 0 ? $user_candidate_id : 0;
    if ($user_candidate_id > 0) {
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $current_lang = $sitepress->get_current_language();
            $icl_post_id = icl_object_id($user_candidate_id, 'candidate', false, $current_lang);

            if ($icl_post_id > 0) {
                return $user_candidate_id = $icl_post_id;
            }
        }
        $candidate_obj = get_post($user_candidate_id);
        if (isset($candidate_obj->ID)) {
            return $candidate_obj->ID;
        }
    }
    return false;
}

// Getting candidate attached user id
function jobsearch_get_candidate_user_id($candidate_id = 0) {
    $candidate_user_id = get_post_meta($candidate_id, 'jobsearch_user_id', true);
    $candidate_user_id = absint($candidate_user_id);
    $candidate_user_id = $candidate_user_id > 0 ? $candidate_user_id : 0;
    $user_obj = get_user_by('ID', $candidate_user_id);
    if (isset($user_obj->ID)) {
        return $user_obj->ID;
    }
    return false;
}

// Getting user attached employer id
function jobsearch_get_user_employer_id($user_id = 0) {
    global $sitepress;
    $user_employer_id = get_user_meta($user_id, 'jobsearch_employer_id', true);
    $user_employer_id = $user_employer_id > 0 ? $user_employer_id : 0;
    if ($user_employer_id > 0) {
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $current_lang = $sitepress->get_current_language();
            $icl_post_id = icl_object_id($user_employer_id, 'employer', false, $current_lang);

            if ($icl_post_id > 0) {
                return $user_employer_id = $icl_post_id;
            }
        }
        $employer_obj = get_post($user_employer_id);
        if ($employer_obj) {
            return $employer_obj->ID;
        }
    }
    return false;
}

// Getting employer attached user id
function jobsearch_get_employer_user_id($employer_id = 0) {
    $employer_user_id = get_post_meta($employer_id, 'jobsearch_user_id', true);
    $employer_user_id = $employer_user_id > 0 ? $employer_user_id : 0;
    $user_obj = get_user_by('ID', $employer_user_id);
    if (isset($user_obj->ID)) {
        return $user_obj->ID;
    }
    return false;
}

//
function getCountryNamebyCode($code, $array) {
    foreach ($array as $key => $val) {
        if (isset($code[0]) && $val['sortname'] === $code[0]) {
            return $val['name'];
        }
    }
    return null;
}

// Check if user is employer
function jobsearch_user_is_employer($user_id = 0) {
    global $sitepress;
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $user_employer_id = get_user_meta($user_id, 'jobsearch_employer_id', true);
    $user_employer_id = $user_employer_id > 0 ? $user_employer_id : 0;
    if ($user_employer_id > 0) {
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $current_lang = $sitepress->get_current_language();
            $icl_post_id = icl_object_id($user_employer_id, 'employer', false, $current_lang);

            if ($icl_post_id > 0) {
                $user_employer_id = $icl_post_id;
            }
        }
        $employer_obj = get_post($user_employer_id);
        if ($employer_obj && isset($employer_obj->ID)) {
            return true;
        }
    }
    return false;
}

// Check if user is candidate
function jobsearch_user_is_candidate($user_id = 0) {
    global $sitepress;
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $user_candidate_id = get_user_meta($user_id, 'jobsearch_candidate_id', true);
    $user_candidate_id = $user_candidate_id > 0 ? $user_candidate_id : 0;
    if ($user_candidate_id > 0) {
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $current_lang = $sitepress->get_current_language();
            $icl_post_id = icl_object_id($user_candidate_id, 'candidate', false, $current_lang);

            if ($icl_post_id > 0) {
                $user_candidate_id = $icl_post_id;
            }
        }
        $candidate_obj = get_post($user_candidate_id);
        if ($candidate_obj && isset($candidate_obj->ID)) {
            return true;
        }
    }
    return false;
}

if (!function_exists('jobsearch_template_path')) {

    function jobsearch_template_path() {
        return apply_filters('jobsearch_plugin_template_path', 'wp-jobsearch/');
    }

}

if (!function_exists('jobsearch_get_template_part')) {

    function jobsearch_get_template_part($slug = '', $name = '', $ext_template = '') {
        $template = '';

        if ($ext_template != '') {
            $ext_template = trailingslashit($ext_template);
        }
        if ($name) {
            $template = locate_template(array("{$slug}-{$name}.php", jobsearch_template_path() . "{$ext_template}/{$slug}-{$name}.php"));
        }

        if (!$template && $name && file_exists(jobsearch_plugin_get_path() . 'templates/' . "{$ext_template}/{$slug}-{$name}.php")) {
            $template = jobsearch_plugin_get_path() . 'templates/' . "{$ext_template}{$slug}-{$name}.php";
        }

        if (!$template) {
            $template = locate_template(array("{$slug}.php", jobsearch_template_path() . "{$ext_template}/{$slug}.php"));
        }

        if ($template) {
            load_template($template, false);
        }
    }

}
if (!function_exists('jobsearch_get_cached_obj')) {

    function jobsearch_get_cached_obj($cache_variable, $args, $time = 12, $cache = true, $type = 'wp_query', $taxanomy_name = '') {
        $loop_obj = '';
        if ($cache == true) {
            $time_string = $time * HOUR_IN_SECONDS;
            if ($cache_variable != '') {
                if (false === ($loop_obj = wp_cache_get($cache_variable))) {
                    if ($type == 'wp_query') {
                        $loop_obj = new WP_Query($args);
                    } else if ($type == 'get_term') {
                        $loop_obj = array();
                        $terms = get_terms($taxanomy_name, $args);
                        if (sizeof($terms) > 0) {
                            foreach ($terms as $term_data) {
                                $loop_obj[] = $term_data->name;
                            }
                        }
                    }
                    wp_cache_set($cache_variable, $loop_obj, $time_string);
                }
            }
        } else {
            if ($type == 'wp_query') {
                $loop_obj = new WP_Query($args);
            } else if ($type == 'get_term') {
                $loop_obj = array();
                $terms = get_terms($taxanomy_name, $args);
                if (sizeof($terms) > 0) {
                    foreach ($terms as $term_data) {
                        $loop_obj[] = $term_data->name;
                    }
                }
            }
        }

        return $loop_obj;
    }

}
if (!function_exists('jobsearch_remove_transient_obj')) {

    function jobsearch_remove_transient_obj($transient_variable) {
        $identifier = uniqid();
        if (isset($_COOKIE['identifier'])) {
            $identifier = $_COOKIE['identifier'];
        }
        delete_transient($identifier . $transient_variable);
    }

}

if (!function_exists('jobsearch_set_transient_obj')) {

    function jobsearch_set_transient_obj($transient_variable, $data_string, $time = 12) {
        if (!isset($_COOKIE['identifier'])) {
            //setcookie('identifier', uniqid(), time() + (86400 * 30), "/"); // 86400 = 1 day
        }
        $result = '';
        $identifier = isset($_COOKIE['identifier']) ? $_COOKIE['identifier'] : uniqid();
        $time_string = $time * HOUR_IN_SECONDS;
        if ($data_string != '') {
            $result = set_transient($identifier . $transient_variable, $data_string, $time_string);
        }
        return $result;
    }

}

if (!function_exists('jobsearch_get_transient_obj')) {

    function jobsearch_get_transient_obj($transient_variable) {
        $identifier = uniqid();
        if (isset($_COOKIE['identifier'])) {
            $identifier = $_COOKIE['identifier'];
        }
        if (false === ($data_string = get_transient($identifier . $transient_variable))) {
            return false;
        } else {
            return $data_string;
        }
    }

}

if (!function_exists('jobsearch_server_protocol')) {

    function jobsearch_server_protocol() {
        if (is_ssl()) {
            return 'https://';
        }
        return 'http://';
    }

}
if (!function_exists('jobsearch_time_elapsed_string')) {

    function jobsearch_time_elapsed_string($ptime, $before = '', $after = '', $break_time = false) {
        if ($ptime != '') {
            $b_time = human_time_diff($ptime, current_time('timestamp'));
            if ($break_time === true) {
                $b_time = explode(' ', $b_time);
                $b_time = implode('<br>', $b_time);
            }
            $date_string = $before . " " . $b_time . " " . esc_html__('ago', 'wp-jobsearch') . $after;
            return $date_string;
            //return sprintf(esc_html__('%1$s %2$s ago', 'wp-jobsearch'), $before, $b_time) ;
        } else {
            return '';
        }
    }

}

if (!function_exists('jobsearch_get_query_whereclase_by_array')) {

    function jobsearch_get_query_whereclase_by_array($array, $user_meta = false) {
        $id = '';
        $flag_id = 0;

        if (isset($array) && is_array($array)) {
            foreach ($array as $var => $val) {

                $string = ' ';

                $string .= ' AND (';

                if (isset($val['key']) || isset($val['value'])) {
                    $string .= get_meta_condition($val);
                } else {  // if inner array 
                    if (isset($val) && is_array($val)) {
                        foreach ($val as $inner_var => $inner_val) {
                            $inner_relation = isset($inner_val['relation']) ? $inner_val['relation'] : 'and';
                            $second_string = '';
                            if (isset($inner_val) && is_array($inner_val)) {

                                $string .= "( ";

                                $inner_arr_count = is_array($inner_val) ? count($inner_val) : '';

                                $inner_flag = 1;

                                foreach ($inner_val as $inner_val_var => $inner_val_value) {

                                    if (is_array($inner_val_value)) {

                                        $string .= "( ";
                                        $string .= get_meta_condition($inner_val_value);

                                        $string .= ' )';

                                        if ($inner_flag != $inner_arr_count)
                                            $string .= ' ' . $inner_relation . ' ';
                                    }

                                    $inner_flag++;
                                }

                                $string .= ' )';
                            }
                        }
                    }
                }

                $string .= " ) ";

                $id_condtion = '';

                if (isset($id) && $flag_id != 0) {

                    $id = implode(",", $id);

                    if (empty($id)) {

                        $id = 0;
                    }

                    if ($user_meta == true) {

                        $id_condtion = ' AND user_id IN (' . $id . ')';
                    } else {

                        $id_condtion = ' AND post_id IN (' . $id . ')';
                    }
                }

                if ($user_meta == true) {

                    $id = jobsearch_get_user_id_by_whereclase($string . $id_condtion);
                } else {

                    $id = jobsearch_get_post_id_by_whereclase($string . $id_condtion);
                }

                $flag_id = 1;
            }
        }

        return $id;
    }

}
if (!function_exists('jobsearch_get_post_id_by_whereclase')) {

    function jobsearch_get_post_id_by_whereclase($whereclase) {
        global $wpdb;
        $qry = "SELECT post_id FROM $wpdb->postmeta WHERE 1=1 " . $whereclase;
        return $posts = $wpdb->get_col($qry);
    }

}


if (!function_exists('jobsearch_get_user_id_by_whereclase')) {


    function jobsearch_get_user_id_by_whereclase($whereclase) {
        global $wpdb;
        $qry = "SELECT user_id FROM $wpdb->usermeta WHERE 1=1 " . $whereclase;
        return $posts = $wpdb->get_col($qry);
    }

}

if (!function_exists('get_meta_condition')) {

    function get_meta_condition($val) {

        $string = '';

        $meta_key = isset($val['key']) ? $val['key'] : '';

        $compare = isset($val['compare']) ? $val['compare'] : '=';

        $meta_value = isset($val['value']) ? $val['value'] : '';

        $string .= " meta_key='" . $meta_key . "' AND ";

        $type = isset($val['type']) ? $val['type'] : '';

        if ($compare == 'BETWEEN' || $compare == 'between' || $compare == 'Between') {

            $meta_val1 = '';

            $meta_val2 = '';

            if (isset($meta_value) && is_array($meta_value)) {

                $meta_val1 = isset($meta_value[0]) ? $meta_value[0] : '';

                $meta_val2 = isset($meta_value[1]) ? $meta_value[1] : '';
            }

            if ($type != '' && strtolower($type) == 'numeric') {

                $string .= " meta_value BETWEEN '" . $meta_val1 . "' AND " . $meta_val2 . " ";
            } else {

                $string .= " meta_value BETWEEN '" . $meta_val1 . "' AND '" . $meta_val2 . "' ";
            }
        } elseif ($compare == 'like' || $compare == 'LIKE' || $compare == 'Like') {

            $string .= " meta_value LIKE '%" . $meta_value . "%' ";
        } else {

            if ($type != '' && strtolower($type) == 'numeric' && $meta_value != '') {

                $string .= " meta_value" . $compare . " " . $meta_value . " ";
            } else {

                $string .= " meta_value" . $compare . "'" . $meta_value . "' ";
            }
        }

        return $string;
    }

}

if (!function_exists('jobsearch_visibility_query_args')) {

    function jobsearch_visibility_query_args($element_filter_arr = array()) {
        return $element_filter_arr;
    }

}

if (!function_exists('jobsearch_remove_qrystr_extra_var')) {

    function jobsearch_remove_qrystr_extra_var($qStr, $key, $withqury_start = 'yes') {
        $qr_str = preg_replace('/[?&]' . $key . '=[^&]+$|([?&])' . $key . '=[^&]+&/', '$1', $qStr);
        if (!(strpos($qr_str, '?') !== false)) {
            $qr_str = "?" . $qr_str;
        }
        $qr_str = preg_replace('/[?&]' . $key . '=[^&]+$|([?&])' . $key . '=[^&]+&/', '$1', $qr_str);
        $qr_str = str_replace("?&", "?", $qr_str);
        $qr_str = jobsearch_remove_dupplicate_var_val($qr_str);

        if ($withqury_start == 'no') {
            $qr_str = str_replace("?", "", $qr_str);
        }
        return $qr_str;
        die();
    }

}

if (!function_exists('jobsearch_remove_dupplicate_var_val')) {

    function jobsearch_remove_dupplicate_var_val($qry_str) {
        $old_string = $qry_str;
        $qStr = str_replace("?", "", $qry_str);
        $query = explode('&', $qStr);
        $params = array();
        if (isset($query) && !empty($query)) {
            foreach ($query as $param) {
                if (!empty($param)) {
                    $param_array = explode('=', $param);
                    $name = isset($param_array[0]) ? $param_array[0] : '';
                    $value = isset($param_array[1]) ? $param_array[1] : '';
                    $new_str = $name . "=" . $value;
                    // count matches
                    $count_str = substr_count($old_string, $new_str);
                    $count_str = $count_str - 1;
                    if ($count_str > 0) {
                        $old_string = jobsearch_str_replace_limit($new_str, "", $old_string, $count_str);
                    }
                    $old_string = str_replace("&&", "&", $old_string);
                }
            }
        }
        $old_string = str_replace("?&", "?", $old_string);
        return $old_string;
    }

}

if (!function_exists('jobsearch_str_replace_limit')) {

    function jobsearch_str_replace_limit($search, $replace, $string, $limit = 1) {
        if (is_bool($pos = (strpos($string, $search))))
            return $string;
        $search_len = strlen($search);
        for ($i = 0; $i < $limit; $i++) {
            $string = substr_replace($string, $replace, $pos, $search_len);
            if (is_bool($pos = (strpos($string, $search))))
                break;
        }
        return $string;
    }

}
if (!function_exists('getMultipleParameters')) {

    function getMultipleParameters($query_string = '') {
        if ($query_string == '')
            $query_string = $_SERVER['QUERY_STRING'];
        $params = explode('&', $query_string);
        foreach ($params as $param) {

            $k = $param;
            $v = '';
            if (strpos($param, '=')) {
                list($name, $value) = explode('=', $param);
                $k = rawurldecode($name);
                $v = rawurldecode($value);
            }
            if (isset($query[$k])) {
                if (is_array($query[$k])) {
                    $query[$k][] = $v;
                } else {
                    $query[$k][] = array($query[$k], $v);
                }
            } else {
                $query[$k][] = $v;
            }
        }
        return $query;
    }

}

if (!function_exists('jobsearch_get_taxanomy_location_item_count')) {

    function jobsearch_get_taxanomy_location_item_count($left_filter_count_switch, $location_slug, $tax_type, $count_posts_in, $count_post_type = 'job') {
        global $sitepress;
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
        }

        if ($left_filter_count_switch == 'yes') {
            if (!empty($count_posts_in) && is_array($count_posts_in)) {
                $location_condition_arr = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'jobsearch_field_location_location1',
                        'value' => $location_slug,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'jobsearch_field_location_location2',
                        'value' => $location_slug,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'jobsearch_field_location_location3',
                        'value' => $location_slug,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'jobsearch_field_location_location4',
                        'value' => $location_slug,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'jobsearch_field_location_address',
                        'value' => $location_slug,
                        'compare' => 'Like',
                    ),
                );
                $args_count = array(
                    'posts_per_page' => "1",
                    'post_type' => $count_post_type,
                    'post_status' => 'publish',
                    'suppress_filters' => false,
                    'fields' => 'ids', // only load ids
                    'meta_query' => $location_condition_arr,
                    'post__in' => $count_posts_in
                );

                $job_qry = new WP_Query($args_count);
                $wpml_job_totnum = $job_totnum = $job_qry->found_posts;
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $wpml_job_totnum == 0 && isset($trans_able_options['job']) && $trans_able_options['job'] == '2') {
                    $sitepress_def_lang = $sitepress->get_default_language();
                    $sitepress_curr_lang = $sitepress->get_current_language();
                    $sitepress->switch_lang($sitepress_def_lang, true);

                    $job_qry = new WP_Query($args_count);

                    $job_totnum = $job_qry->found_posts;

                    //
                    $sitepress->switch_lang($sitepress_curr_lang, true);
                }
                return $job_totnum;
                wp_reset_postdata();
            }
        }
    }

}

if (!function_exists('jobsearch_get_taxanomy_type_item_count')) {

    function jobsearch_get_taxanomy_type_item_count($left_filter_count_switch, $field_meta_key, $tax_type, $count_posts_in, $count_post_type = 'job') {
        global $sitepress;
        $total_num = 0;
        if ($left_filter_count_switch == 'yes') {
            if (!empty($count_posts_in) && is_array($count_posts_in)) {
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
                }

                $args_count = array(
                    'posts_per_page' => "1",
                    'post_type' => $count_post_type,
                    'post_status' => 'publish',
                    'suppress_filters' => false,
                    'fields' => 'ids', // only load ids
                    'tax_query' => array(
                        array(
                            'taxonomy' => $tax_type,
                            'field' => 'slug',
                            'terms' => $field_meta_key
                        ),
                    ),
                    'post__in' => $count_posts_in
                );

                $jobs_loop_obj = new WP_Query($args_count);
                $wpml_job_totnum = $total_num = $jobs_loop_obj->found_posts;
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $wpml_job_totnum == 0 && isset($trans_able_options[$count_post_type]) && $trans_able_options[$count_post_type] == '2') {
                    $sitepress_def_lang = $sitepress->get_default_language();
                    $sitepress_curr_lang = $sitepress->get_current_language();
                    $sitepress->switch_lang($sitepress_def_lang, true);

                    $job_qry = new WP_Query($args_count);

                    $total_num = $job_qry->found_posts;

                    //
                    $sitepress->switch_lang($sitepress_curr_lang, true);
                }
                wp_reset_postdata();
            }
        }
        return $total_num;
    }

    function jobsearch_get_taxanomy_type_item_count_depricate($left_filter_count_switch, $field_meta_key, $tax_type, $args_filters) {

        global $sitepress;
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
        }

        if ($left_filter_count_switch == 'yes') {
            if (isset($args_filters['tax_query'])) {
                $finded_index = jobsearch_find_in_multiarray($tax_type, $args_filters['tax_query'], 'taxonomy');

                $finded_index = isset($finded_index[0]) ? $finded_index[0] : '-1';
                if ($finded_index >= 0) {
                    $args_filters['tax_query'] = array_splice($args_filters['tax_query'], $finded_index, (count($args_filters['tax_query']) - 1));
                }
            }
            $args_filters['tax_query'][] = array(
                'taxonomy' => $tax_type,
                'field' => 'slug',
                'terms' => $field_meta_key
            );

            if (isset($args_filters['post_type']) && $args_filters['post_type'] == 'candidate') {
                $args_filters = apply_filters('jobsearch_candidates_listing_filter_args', $args_filters);
            }

            if (isset($args_filters['post_type']) && $args_filters['post_type'] == 'employer') {
                $args_filters = apply_filters('jobsearch_employers_listing_filter_args', $args_filters);
            }

            if (isset($args_filters['post_type']) && $args_filters['post_type'] == 'job') {
                $args_filters = apply_filters('jobsearch_jobs_listing_filter_args', $args_filters);
            }

            $job_qry = new WP_Query($args_filters);
            $wpml_job_totnum = $job_totnum = $job_qry->found_posts;
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $wpml_job_totnum == 0 && isset($trans_able_options['job']) && $trans_able_options['job'] == '2') {
                $sitepress_def_lang = $sitepress->get_default_language();
                $sitepress_curr_lang = $sitepress->get_current_language();
                $sitepress->switch_lang($sitepress_def_lang, true);

                $job_qry = new WP_Query($args_filters);

                $job_totnum = $job_qry->found_posts;

                //
                $sitepress->switch_lang($sitepress_curr_lang, true);
            }
            return $job_totnum;
            wp_reset_postdata();
        }
    }

}

if (!function_exists('jobsearch_get_item_count')) {

    function jobsearch_get_item_count($left_filter_count_switch, $count_posts_in, $count_arr, $job_short_counter, $field_meta_key, $post_type = 'job') {
        global $sitepress, $wpdb;

        $total_num = 0;
        if ($left_filter_count_switch == 'yes') {
            if (!empty($count_posts_in) && is_array($count_posts_in)) {
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
                }

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

    function jobsearch_get_item_count_depricate($left_filter_count_switch, $args, $count_arr, $job_short_counter, $field_meta_key, $post_type = 'job') {
        if ($left_filter_count_switch == 'yes') {

            global $jobsearch_shortcode_jobs_frontend, $sitepress, $jobsearch_shortcode_candidates_frontend, $jobsearch_shortcode_employers_frontend;

            if (get_query_var('location') != '' && !isset($_REQUEST['location'])) {
                $get_queryvar_loc = get_query_var('location');
                $_REQUEST['location'] = $get_queryvar_loc;
            }

            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
            }

            // get all arguments from getting flters
            $left_filter_arr = array();
            if ($post_type == 'candidate') {
                $left_filter_arr = $jobsearch_shortcode_candidates_frontend->get_filter_arg($job_short_counter, $field_meta_key);
            } else if ($post_type == 'employer') {
                $left_filter_arr = $jobsearch_shortcode_employers_frontend->get_filter_arg($job_short_counter, $field_meta_key);
            } else {
                $left_filter_arr = $jobsearch_shortcode_jobs_frontend->get_filter_arg($job_short_counter, $field_meta_key);
            }

            if (!empty($count_arr)) {
                // check if count array has multiple condition
                foreach ($count_arr as $count_arr_single) {
                    $left_filter_arr[] = $count_arr_single;
                }
            }

            $post_ids = '';
            $job_post_ids = array();
            $job_post_ids = $jobsearch_shortcode_jobs_frontend->job_general_query_filter($job_post_ids, array());
            if (!empty($left_filter_arr)) {
                // apply all filters and get ids
                if ($post_type == 'candidate') {
                    $post_ids = $jobsearch_shortcode_candidates_frontend->get_candidate_id_by_filter($left_filter_arr);
                } else if ($post_type == 'employer') {
                    $post_ids = $jobsearch_shortcode_employers_frontend->get_employer_id_by_filter($left_filter_arr, $post_type);
                } else {
                    $post_ids = $jobsearch_shortcode_jobs_frontend->get_job_id_by_filter($left_filter_arr, $post_type, $job_post_ids);
                }
            }

            $all_post_ids = $post_ids;
            if (!empty($all_post_ids)) {
                $args['post__in'] = $all_post_ids;
            }

            if ($post_type == 'job') {
                $args = apply_filters('jobsearch_jobs_listing_filter_args', $args);
            }

            if ($post_type == 'candidate') {
                $args = apply_filters('jobsearch_candidates_listing_filter_args', $args);
            }

            if ($post_type == 'employer') {
                $args = apply_filters('jobsearch_employers_listing_filter_args', $args);
            }

            if (isset($_REQUEST['location']) && $_REQUEST['location'] != '' && $post_type == 'job' && !empty($post_ids)) {
                $radius = isset($_REQUEST['radius']) ? $_REQUEST['radius'] : '';
                $post_ids = $jobsearch_shortcode_jobs_frontend->job_location_filter($post_ids);
                if (empty($post_ids)) {
                    $post_ids = array(0);
                }
                $args['post__in'] = $post_ids;
            }

            $jobs_loop_obj = jobsearch_get_cached_obj('job_result_cached_loop_count_obj', $args, 12, false, 'wp_query');
            $wpml_job_totnum = $job_totnum = $jobs_loop_obj->found_posts;
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $wpml_job_totnum == 0 && isset($trans_able_options['job']) && $trans_able_options['job'] == '2') {
                $sitepress_def_lang = $sitepress->get_default_language();
                $sitepress_curr_lang = $sitepress->get_current_language();
                $sitepress->switch_lang($sitepress_def_lang, true);

                $job_qry = jobsearch_get_cached_obj('job_result_cached_loop_count_obj', $args, 12, false, 'wp_query');

                $job_totnum = $job_qry->found_posts;

                //
                $sitepress->switch_lang($sitepress_curr_lang, true);
            }
            return $job_totnum;
        }
    }

}

if (!function_exists('jobsearch_get_cached_obj')) {

    function jobsearch_get_cached_obj($cache_variable, $args, $time = 12, $cache = true, $type = 'wp_query', $taxanomy_name = '') {
        $job_loop_obj = '';
        if ($cache == true) {
            $time_string = $time * HOUR_IN_SECONDS;
            if ($cache_variable != '') {
                if (false === ($job_loop_obj = wp_cache_get($cache_variable))) {
                    if ($type == 'wp_query') {
                        $job_loop_obj = new WP_Query($args);
                    } else if ($type == 'get_term') {
                        $job_loop_obj = array();
                        $terms = get_terms($taxanomy_name, $args);
                        if (sizeof($terms) > 0) {
                            foreach ($terms as $term_data) {
                                $job_loop_obj[] = $term_data->name;
                            }
                        }
                    }
                    wp_cache_set($cache_variable, $job_loop_obj, $time_string);
                }
            }
        } else {
            if ($type == 'wp_query') {
                $job_loop_obj = new WP_Query($args);
            } else if ($type == 'get_term') {
                $job_loop_obj = array();
                $terms = get_terms($taxanomy_name, $args);
                if (sizeof($terms) > 0) {
                    foreach ($terms as $term_data) {
                        $job_loop_obj[] = $term_data->name;
                    }
                }
            }
        }
        return $job_loop_obj;
    }

}

function jobsearch_get_unique_folder_byurl($url) {
    if ($url != '') {
        $get_subpath = substr($url, strpos($url, 'jobsearch-candidates/'), strlen($url));
        $exp_arr = explode('/', $get_subpath);
        $unique_path = isset($exp_arr[1]) ? $exp_arr[1] : '';
        return $unique_path;
    }
}

function jobsearch_remove_cand_photo_foldr($post_id, $type = 'profile_img') {

    if ($type == 'cover_img') {
        $user_avatar_dburl = get_post_meta($post_id, 'jobsearch_user_cover_imge', true);
    } else {
        $user_avatar_dburl = get_post_meta($post_id, 'jobsearch_user_avatar_url', true);
    }

    if (isset($user_avatar_dburl['file_path']) && $user_avatar_dburl['file_path'] != '') {

        $root_folder = $user_avatar_dburl['file_path'];

        if (is_dir($root_folder)) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            WP_Filesystem();
            global $wp_filesystem;
            $wp_filesystem->rmdir($root_folder, true);
        }
        if ($type == 'profile_img') {
            update_post_meta($post_id, 'jobsearch_user_avatar_url', '');
        } else if ($type == 'cover_img') {
            update_post_meta($post_id, 'jobsearch_user_cover_imge', '');
        }
    }
}

function jobsearch_insert_candupload_attach($Fieldname = 'file', $post_id = 0, $img_type = 'profile_img') {
    global $jobsearch_uploding_candimg, $jobsearch_download_locations;
    $jobsearch_download_locations = false;
    $jobsearch_uploding_candimg = true;
    //
    if (isset($_FILES[$Fieldname]) && $_FILES[$Fieldname] != '') {

        add_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        $upload_file = $_FILES[$Fieldname];

        $test_uploaded_file = is_uploaded_file($upload_file['tmp_name']);

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $allowed_image_types = array(
            'jpg|jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        );

        $status_upload = wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_image_types));

        if ($test_uploaded_file && !isset($status_upload['file'])) {
            //$status_upload = jobsearch_wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_image_types));
        }

        if (empty($status_upload['error'])) {

            $file_url = isset($status_upload['url']) ? $status_upload['url'] : '';
            $upload_file_path = $wp_upload_dir['path'] . '/' . basename($file_url);

            $folder_path = $wp_upload_dir['path'];

            $image = wp_get_image_editor($upload_file_path);

            if (!is_wp_error($image)) {
                $file_name = basename($file_url);

                if ($img_type == 'cover_img') {
                    $file_urls = array(
                        'path' => $folder_path,
                        'orig' => $file_url,
                        'crop' => $file_url,
                    );
                } else {
                    $file_ext = end(explode('.', $file_name));
                    $image->resize(150, 150, true);

                    $crop_file_name = $wp_upload_dir['path'] . '/user-img-150.' . $file_ext;
                    $image->save($crop_file_name);

                    //
                    $image_350 = wp_get_image_editor($upload_file_path);
                    $image_350->resize(350, 450, true);
                    $crop_file_name = $wp_upload_dir['path'] . '/user-img-350.' . $file_ext;
                    $image_350->save($crop_file_name);
                    //

                    $crop_file_url = $wp_upload_dir['url'] . '/user-img-150.' . $file_ext;

                    $file_urls = array(
                        'path' => $folder_path,
                        'orig' => $file_url,
                        'crop' => $crop_file_url,
                    );
                }

                do_action('jobsearch_user_upload_avatar_after', $post_id, $file_urls, $wp_upload_dir);

                return $file_urls;
            }
        }

        remove_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);
    }

    return false;
}

function jobsearch_insert_upload_attach($Fieldname = 'file', $post_id = 0, $user_dir_filter = true) {
    if (isset($_FILES[$Fieldname]) && $_FILES[$Fieldname] != '') {
        if ($user_dir_filter === true) {
            add_filter('upload_dir', 'jobsearch_user_upload_files_path');
        }
        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        $upload_file = $_FILES[$Fieldname];

        $test_uploaded_file = is_uploaded_file($upload_file['tmp_name']);

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $allowed_image_types = array(
            'jpg|jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        );

        $status_upload = wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_image_types));

        if ($test_uploaded_file && !isset($status_upload['file'])) {
            $status_upload = jobsearch_wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_image_types));
        }

        if (empty($status_upload['error'])) {

            $image = wp_get_image_editor($status_upload['file']);
            $img_resized_name = $status_upload['file'];

            if (!is_wp_error($image)) {

                $file_url = isset($status_upload['url']) ? $status_upload['url'] : '';

                $upload_file_path = $wp_upload_dir['path'] . '/' . basename($file_url);

                // Check the type of file. We'll use this as the 'post_mime_type'.
                $filetype = wp_check_filetype(basename($file_url), null);

                // Prepare an array of post data for the attachment.
                $attachment = array(
                    'guid' => $wp_upload_dir['url'] . '/' . basename($upload_file_path),
                    'post_mime_type' => $filetype['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', ($upload_file['name'])),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                // Insert the attachment.
                $attach_id = wp_insert_attachment($attachment, $upload_file_path, $post_id);

                // Generate the metadata for the attachment, and update the database record.
                $attach_data = wp_generate_attachment_metadata($attach_id, $upload_file_path);
                wp_update_attachment_metadata($attach_id, $attach_data);

                if ($post_id > 0) {
                    set_post_thumbnail($post_id, $attach_id);
                }

                do_action('jobsearch_user_upload_avatar_after', $post_id, $attach_id);

                return $attach_id;
            }
        }

        if ($user_dir_filter === true) {
            remove_filter('upload_dir', 'jobsearch_user_upload_files_path');
        }
    }

    return false;
}

function jobsearch_upload_attach_with_external_url($image_url, $post_ID = 0) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    WP_Filesystem();
    global $wp_filesystem;
    $image_data = $wp_filesystem->get_contents($image_url);
    if (!$image_data && function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $image_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $image_data = curl_exec($ch);
        curl_close($ch);
    }

    if ($image_data) {
        global $jobsearch_uploding_candimg, $jobsearch_download_locations;
        $jobsearch_download_locations = false;
        $jobsearch_uploding_candimg = true;
        $filename = basename($image_url);
        $wp_filetype = wp_check_filetype($filename, null);
        $file_ext = isset($wp_filetype['ext']) ? $wp_filetype['ext'] : '';

        add_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);

        $wp_upload_dir = wp_upload_dir();

        $uplod_direc_path = $wp_upload_dir['path'];
        $uplod_direc_url = $wp_upload_dir['url'];
        $img_new_path = $uplod_direc_path . '/' . $filename;
        $wp_filesystem->put_contents($img_new_path, $image_data);
        $new_img_url = $uplod_direc_url . '/' . $filename;
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
            'file_name' => $filename,
            'mime_type' => $wp_filetype,
            'time' => $fileuplod_time,
            'orig_file_url' => $new_img_url,
            'file_url' => $crop_file_url,
            'file_path' => $uplod_direc_path,
            'file_id' => $file_uniqid,
        );
        update_post_meta($post_ID, 'jobsearch_user_avatar_url', $arg_arr);

        remove_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);
    }
}

add_action('wp_ajax_jobsearch_empdash_gallery_imgs_url', 'jobsearch_empdash_gall_imgs_url_callback');

function jobsearch_empdash_gall_imgs_url_callback() {
    global $jobsearch_plugin_options;

    $user_id = get_current_user_id();
    $employer_id = jobsearch_get_user_employer_id($user_id);

    $alred_count_imgs = isset($_POST['alred_count']) ? absint($_POST['alred_count']) : 0;

    $max_imgs_allow = isset($jobsearch_plugin_options['max_gal_imgs_allow']) && absint($jobsearch_plugin_options['max_gal_imgs_allow']) > 0 ? $jobsearch_plugin_options['max_gal_imgs_allow'] : 5;

    $number_of_uplod_imgs = $alred_count_imgs > 0 && $max_imgs_allow >= $alred_count_imgs ? ($max_imgs_allow - $alred_count_imgs) : $max_imgs_allow;

    $max_image_size = 2000;

    if (isset($_FILES['gall_imgs']) && $_FILES['gall_imgs'] != '') {
        $multi_files = $_FILES['gall_imgs'];

        $img_files_array = array();
        if (isset($multi_files['name']) && is_array($multi_files['name'])) {
            foreach ($multi_files['name'] as $multi_key => $multi_value) {
                if ($multi_files['name'][$multi_key]) {
                    $upload_file = array(
                        'name' => $multi_files['name'][$multi_key],
                        'type' => $multi_files['type'][$multi_key],
                        'tmp_name' => $multi_files['tmp_name'][$multi_key],
                        'error' => $multi_files['error'][$multi_key],
                        'size' => $multi_files['size'][$multi_key]
                    );
                    $img_files_array[] = $upload_file;
                }
            }
        }
        if (!empty($img_files_array)) {
            $wp_upload_dir = wp_upload_dir();

            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $allowed_image_types = array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            );

            $employer_gal_imges = get_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs', true);
            $employer_gal_imges = !empty($employer_gal_imges) ? $employer_gal_imges : array();

            $uplod_count = 1;
            $html = '';
            $attch_ids_arr = array();
            foreach ($img_files_array as $file_data) {
                $rand_id = rand(100000, 999999);
                if ($uplod_count > $number_of_uplod_imgs) {
                    break;
                }

                $file_name = isset($file_data['name']) ? $file_data['name'] : '';
                $file_ext = end(explode('.', $file_name));
                $allow_ext_arr = array('jpg', 'jpeg', 'png', 'gif');
                if (!in_array($file_ext, $allow_ext_arr)) {
                    echo json_encode(array('html' => $html, 'msg' => esc_html__('Only image files are allowed to upload.', 'wp-jobsearch')));
                    die;
                }

                $file_size = isset($file_data['size']) && $file_data['size'] > 0 ? $file_data['size'] : 1;
                $size_as_kb = round($file_size / 1024);

                if ($size_as_kb > $max_image_size) {
                    echo json_encode(array('html' => $html, 'msg' => esc_html__('The file size is too large.', 'wp-jobsearch')));
                    die;
                }

                $status_upload = wp_handle_upload($file_data, array('test_form' => false, 'mimes' => $allowed_image_types));
                if (empty($status_upload['error'])) {

                    $image = wp_get_image_editor($status_upload['file']);
                    $img_resized_name = $status_upload['file'];

                    if (!is_wp_error($image)) {

                        $file_url = isset($status_upload['url']) ? $status_upload['url'] : '';

                        $upload_file_path = $wp_upload_dir['path'] . '/' . basename($file_url);

                        // Check the type of file. We'll use this as the 'post_mime_type'.
                        $filetype = wp_check_filetype(basename($file_url), null);

                        // Prepare an array of post data for the attachment.
                        $attachment = array(
                            'guid' => $wp_upload_dir['url'] . '/' . basename($upload_file_path),
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', ($upload_file['name'])),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );

                        // Insert the attachment.
                        $attach_id = wp_insert_attachment($attachment, $upload_file_path);

                        // Generate the metadata for the attachment, and update the database record.
                        $attach_data = wp_generate_attachment_metadata($attach_id, $upload_file_path);
                        wp_update_attachment_metadata($attach_id, $attach_data);

                        $attch_ids_arr[] = $attach_id;

                        ob_start();
                        $gal_thumbnail_image = wp_get_attachment_image_src($attach_id, 'thumbnail');
                        $gal_thumb_image_src = isset($gal_thumbnail_image[0]) && esc_url($gal_thumbnail_image[0]) != '' ? $gal_thumbnail_image[0] : '';
                        ?>
                        <li class="jobsearch-column-3 gal-item">
                            <figure>
                                <a><img src="<?php echo esc_url($gal_thumb_image_src) ?>" alt=""></a>
                                <figcaption>
                                    <div class="jobsearch-company-links">
                                        <a href="javascript:void(0);" class="fa fa-arrows el-drag"></a>
                                        <a href="javascript:void(0);" data-rand="<?php echo($rand_id) ?>"
                                           class="fa fa-pencil el-galupdate-btn el-update-btn-<?php echo($rand_id) ?>"></a>

                                        <a href="javascript:void(0);" data-id="<?php echo absint($attach_id) ?>"
                                           class="jobsearch-icon jobsearch-rubbish el-remove"></a>
                                        <input type="hidden" name="company_gallery_imgs[]"
                                               value="<?php echo absint($attach_id) ?>">
                                        <input type="hidden" id="gallery-video-to-put-<?php echo($rand_id) ?>"
                                               name="jobsearch_field_company_gallery_videos[]" value="">
                                        <input type="hidden" id="gallery-title-to-put-<?php echo($rand_id) ?>"
                                               name="jobsearch_field_company_gallery_imgs_title[]" value="">
                                        <textarea id="gallery-desc-to-put-<?php echo($rand_id) ?>"
                                                  name="jobsearch_field_company_gallery_imgs_description[]"
                                                  style="display:none;"></textarea>
                                    </div>
                                </figcaption>
                            </figure>
                        </li>
                        <?php
                        $html .= ob_get_clean();
                    }
                }

                $uplod_count++;
            }
            if (!empty($attch_ids_arr)) {
                $to_updte_arr = array_merge($employer_gal_imges, $attch_ids_arr);
                update_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs', $to_updte_arr);
            }
            echo json_encode(array('html' => $html, 'msg' => ''));
            die;
        }
        echo json_encode(array('html' => '', 'msg' => esc_html__('No Image found.', 'wp-jobsearch')));
        die;
    }
    echo json_encode(array('html' => '', 'msg' => esc_html__('No Image found.', 'wp-jobsearch')));
    die;
}

add_action('wp_ajax_jobsearch_empdash_gallery_imgs_remove', 'jobsearch_empdash_gallery_img_remove');

function jobsearch_empdash_gallery_img_remove() {
    $remove_id = isset($_POST['id']) ? $_POST['id'] : '';
    $user_id = get_current_user_id();
    $employer_id = jobsearch_get_user_employer_id($user_id);
    $employer_gal_imges = get_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs', true);
    $company_gal_videos = get_post_meta($employer_id, 'jobsearch_field_company_gallery_videos', true);

    if (!empty($employer_gal_imges) && in_array($remove_id, $employer_gal_imges)) {
        $rem_key = array_search($remove_id, $employer_gal_imges);
        unset($employer_gal_imges[$rem_key]);
        unset($company_gal_videos[$rem_key]);
        update_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs', $employer_gal_imges);
        update_post_meta($employer_id, 'jobsearch_field_company_gallery_videos', $company_gal_videos);
        wp_delete_attachment($remove_id, true);
        echo json_encode(array('success' => '1'));
    } else {
        echo json_encode(array('success' => '0'));
    }
    die;
}

function jobsearch_gallery_upload_attach($Fieldname = 'file', $img_count = 0, $return_type = 'urls') {

    global $jobsearch_plugin_options;

    if (isset($_FILES[$Fieldname]) && $_FILES[$Fieldname] != '') {

        $max_gal_imgs_allow = isset($jobsearch_plugin_options['max_gal_imgs_allow']) && $jobsearch_plugin_options['max_gal_imgs_allow'] > 0 ? $jobsearch_plugin_options['max_gal_imgs_allow'] : 5;
        $number_of_gal_imgs = $max_gal_imgs_allow;

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        $gall_ids = array();

        $multi_files = $_FILES[$Fieldname];
        if (isset($multi_files['name']) && is_array($multi_files['name'])) {
            $img_name_array = array();
            foreach ($multi_files['name'] as $multi_key => $multi_value) {
                if ($multi_files['name'][$multi_key]) {
                    $upload_file = array(
                        'name' => $multi_files['name'][$multi_key],
                        'type' => $multi_files['type'][$multi_key],
                        'tmp_name' => $multi_files['tmp_name'][$multi_key],
                        'error' => $multi_files['error'][$multi_key],
                        'size' => $multi_files['size'][$multi_key]
                    );

                    $test_uploaded_file = is_uploaded_file($upload_file['tmp_name']);

                    require_once ABSPATH . 'wp-admin/includes/image.php';
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                    require_once ABSPATH . 'wp-admin/includes/media.php';

                    $allowed_image_types = array(
                        'jpg|jpeg|jpe' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                    );

                    $status_upload = wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_image_types));

                    if ($test_uploaded_file && !isset($status_upload['file'])) {
                        $status_upload = jobsearch_wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_image_types));
                    }

                    if (empty($status_upload['error'])) {

                        $image = wp_get_image_editor($status_upload['file']);
                        $img_resized_name = $status_upload['file'];

                        if (!is_wp_error($image)) {

                            $file_url = isset($status_upload['url']) ? $status_upload['url'] : '';

                            $upload_file_path = $wp_upload_dir['path'] . '/' . basename($file_url);

                            // Check the type of file. We'll use this as the 'post_mime_type'.
                            $filetype = wp_check_filetype(basename($file_url), null);

                            // Prepare an array of post data for the attachment.
                            $attachment = array(
                                'guid' => $wp_upload_dir['url'] . '/' . basename($upload_file_path),
                                'post_mime_type' => $filetype['type'],
                                'post_title' => preg_replace('/\.[^.]+$/', '', ($upload_file['name'])),
                                'post_content' => '',
                                'post_status' => 'inherit'
                            );

                            // Insert the attachment.
                            $attach_id = wp_insert_attachment($attachment, $upload_file_path);

                            // Generate the metadata for the attachment, and update the database record.
                            $attach_data = wp_generate_attachment_metadata($attach_id, $upload_file_path);
                            wp_update_attachment_metadata($attach_id, $attach_data);

                            $attach_url = wp_get_attachment_url($attach_id);

                            if ($return_type == 'ids') {
                                $gall_ids[] = $attach_id;
                            } else {
                                $gall_ids[] = $attach_url;
                            }

                            $img_count++;

                            if ($img_count >= $number_of_gal_imgs) {
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $gall_ids;
    }

    return false;
}

function jobsearch_cv_attachment_upload_path($Fieldname = 'file') {

    global $jobsearch_plugin_options, $jobsearch_uploding_resume, $jobsearch_download_locations;
    $jobsearch_download_locations = false;
    if (isset($_FILES[$Fieldname]) && $_FILES[$Fieldname] != '') {

        $jobsearch_uploding_resume = true;

        $jobsearch__options = get_option('jobsearch_plugin_options');

        add_filter('jobsearch_resume_upload_dir', 'jobsearch_upload_cvmod_path', 10, 1);

        $max_attachment_size = 10000;
        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        $gall_ids = array();

        $upload_file = $_FILES[$Fieldname];
        if (!empty($upload_file)) {

            $test_uploaded_file = is_uploaded_file($upload_file['tmp_name']);

            $file_size = isset($upload_file['size']) && $upload_file['size'] > 0 ? $upload_file['size'] : 1;
            $size_as_kb = round($file_size / 1024);

            if ($size_as_kb > $max_attachment_size) {
                return false;
            }

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

            $user_given_filename = '';
            if (isset($_POST['user_fullname']) && $_POST['user_fullname'] != '') {
                $user_firstname = $_POST['user_fullname'];
                $user_firstname = sanitize_title($user_firstname);
                $user_given_filename = $user_firstname;
                if (isset($_POST['user_surname']) && $_POST['user_surname'] != '') {
                    $user_lastname = $_POST['user_surname'];
                    $user_lastname = sanitize_title($user_lastname);
                    $user_given_filename .= '-' . $user_lastname;
                }
            }

            if (isset($upload_file['name'])) {
                $file_name = $upload_file['name'];
                $file_ext = end(explode('.', $file_name));
                if ($file_ext != '') {
                    if ($user_given_filename != '') {
                        $new_file_name = $user_given_filename . '-' . rand(1000000, 9999999) . '.' . $file_ext;
                    } else {
                        $file_before_name = substr($file_name, 0, strpos($file_name, '.' . $file_ext));
                        $new_file_name = $file_before_name . '-' . rand(1000000, 9999999) . '.' . $file_ext;
                    }
                    $upload_file['name'] = $new_file_name;
                }
            }

            $status_upload = wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_file_types));

            if ($test_uploaded_file && !isset($status_upload['file'])) {
                $status_upload = jobsearch_wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_file_types));
            }

            if (empty($status_upload['error'])) {

                $file_url = isset($status_upload['url']) ? $status_upload['url'] : '';

                $upload_file_path = $wp_upload_dir['path'] . '/' . basename($file_url);

                // Check the type of file. We'll use this as the 'post_mime_type'.
                $filetype = wp_check_filetype(basename($file_url), null);

                return $upload_file_path;
            }
        }

        remove_filter('jobsearch_resume_upload_dir', 'jobsearch_upload_cvmod_path', 10, 1);
    }

    return false;
}

function jobsearch_attachments_upload($Fieldname = 'file', $img_count = 0) {

    global $jobsearch_plugin_options;

    if (isset($_FILES[$Fieldname]) && $_FILES[$Fieldname] != '') {

        $max_gal_imgs_allow = isset($jobsearch_plugin_options['number_of_attachments']) && $jobsearch_plugin_options['number_of_attachments'] > 0 ? $jobsearch_plugin_options['number_of_attachments'] : 5;
        $max_attachment_size = isset($jobsearch_plugin_options['attach_file_size']) && $jobsearch_plugin_options['attach_file_size'] > 0 ? $jobsearch_plugin_options['attach_file_size'] : 1024;
        $job_attachment_types = isset($jobsearch_plugin_options['job_attachment_types']) && !empty($jobsearch_plugin_options['job_attachment_types']) ? $jobsearch_plugin_options['job_attachment_types'] : array('application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf');

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        $gall_ids = array();

        $multi_files = $_FILES[$Fieldname];
        if (isset($multi_files['name']) && is_array($multi_files['name'])) {
            $img_name_array = array();
            foreach ($multi_files['name'] as $multi_key => $multi_value) {
                if ($multi_files['name'][$multi_key]) {
                    $upload_file = array(
                        'name' => $multi_files['name'][$multi_key],
                        'type' => $multi_files['type'][$multi_key],
                        'tmp_name' => $multi_files['tmp_name'][$multi_key],
                        'error' => $multi_files['error'][$multi_key],
                        'size' => $multi_files['size'][$multi_key]
                    );

                    $test_uploaded_file = is_uploaded_file($upload_file['tmp_name']);

                    $file_size = isset($upload_file['size']) && $upload_file['size'] > 0 ? $upload_file['size'] : 1;
                    $size_as_kb = round($file_size / 1024);

                    if ($size_as_kb > $max_attachment_size) {
                        continue;
                    }

                    require_once ABSPATH . 'wp-admin/includes/image.php';
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                    require_once ABSPATH . 'wp-admin/includes/media.php';

                    $allowed_image_types = array();
                    if (in_array('image/jpeg', $job_attachment_types)) {
                        $allowed_image_types['jpg|jpeg|jpe'] = 'image/jpeg';
                        $allowed_image_types['png'] = 'image/png';
                    }
                    if (in_array('image/png', $job_attachment_types)) {
                        $allowed_image_types['jpg|jpeg|jpe'] = 'image/jpeg';
                        $allowed_image_types['png'] = 'image/png';
                    }
                    if (in_array('text/plain', $job_attachment_types)) {
                        $allowed_image_types['txt|asc|c|cc|h'] = 'text/plain';
                    }
                    if (in_array('application/msword', $job_attachment_types)) {
                        $allowed_image_types['doc'] = 'application/msword';
                    }
                    if (in_array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $job_attachment_types)) {
                        $allowed_image_types['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    }
                    if (in_array('application/pdf', $job_attachment_types)) {
                        $allowed_image_types['pdf'] = 'application/pdf';
                    }
                    if (in_array('application/vnd.ms-excel', $job_attachment_types)) {
                        $allowed_image_types['xla|xls|xlt|xlw'] = 'application/vnd.ms-excel';
                    }
                    if (in_array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $job_attachment_types)) {
                        $allowed_image_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                    }

                    $status_upload = wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_image_types));

                    if ($test_uploaded_file && !isset($status_upload['file'])) {
                        $status_upload = jobsearch_wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_image_types));
                    }

                    if (empty($status_upload['error'])) {

                        $file_url = isset($status_upload['url']) ? $status_upload['url'] : '';

                        $upload_file_path = $wp_upload_dir['path'] . '/' . basename($file_url);

                        // Check the type of file. We'll use this as the 'post_mime_type'.
                        $filetype = wp_check_filetype(basename($file_url), null);

                        // Prepare an array of post data for the attachment.
                        $attachment = array(
                            'guid' => $wp_upload_dir['url'] . '/' . basename($upload_file_path),
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', ($upload_file['name'])),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );

                        // Insert the attachment.
                        $attach_id = wp_insert_attachment($attachment, $upload_file_path);

                        // Generate the metadata for the attachment, and update the database record.
                        $attach_data = wp_generate_attachment_metadata($attach_id, $upload_file_path);
                        wp_update_attachment_metadata($attach_id, $attach_data);

                        $attach_url = wp_get_attachment_url($attach_id);
                        $gall_ids[] = $attach_url;

                        $img_count++;

                        if ($img_count >= $max_gal_imgs_allow) {
                            break;
                        }
                    }
                }
            }
        }

        return $gall_ids;
    }

    return false;
}

function jobsearch_cus_fields_attachments_upload($Fieldname = 'file', $img_count = 0, $max_gal_imgs_allow = 5, $job_attachment_types = array(), $max_attachment_size = '5MB') {

    global $jobsearch_plugin_options;

    if (isset($_FILES[$Fieldname]) && $_FILES[$Fieldname] != '') {

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        $uplod_file_size_num = abs((int) filter_var($max_attachment_size, FILTER_SANITIZE_NUMBER_INT));
        $uplod_file_size_num = $uplod_file_size_num > 0 ? $uplod_file_size_num : 5;
        $uplod_file_size = $uplod_file_size_num * 1024;

        $gall_ids = array();

        $multi_files = $_FILES[$Fieldname];
        if (isset($multi_files['name']) && is_array($multi_files['name'])) {
            $img_name_array = array();
            foreach ($multi_files['name'] as $multi_key => $multi_value) {

                $img_count++;

                if ($img_count > $max_gal_imgs_allow) {
                    break;
                }

                if ($multi_files['name'][$multi_key]) {
                    $upload_file = array(
                        'name' => $multi_files['name'][$multi_key],
                        'type' => $multi_files['type'][$multi_key],
                        'tmp_name' => $multi_files['tmp_name'][$multi_key],
                        'error' => $multi_files['error'][$multi_key],
                        'size' => $multi_files['size'][$multi_key]
                    );

                    $test_uploaded_file = is_uploaded_file($upload_file['tmp_name']);

                    $file_size = isset($upload_file['size']) && $upload_file['size'] > 0 ? $upload_file['size'] : 1;
                    $size_as_kb = round($file_size / 1024);

                    if ($size_as_kb > $uplod_file_size) {
                        continue;
                    }

                    require_once ABSPATH . 'wp-admin/includes/image.php';
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                    require_once ABSPATH . 'wp-admin/includes/media.php';

                    $allowed_image_types = array();
                    if (in_array('image/jpeg', $job_attachment_types)) {
                        $allowed_image_types['jpg|jpeg|jpe'] = 'image/jpeg';
                        $allowed_image_types['png'] = 'image/png';
                    }
                    if (in_array('image/png', $job_attachment_types)) {
                        $allowed_image_types['jpg|jpeg|jpe'] = 'image/jpeg';
                        $allowed_image_types['png'] = 'image/png';
                    }
                    if (in_array('text/plain', $job_attachment_types)) {
                        $allowed_image_types['txt|asc|c|cc|h'] = 'text/plain';
                    }
                    if (in_array('application/msword', $job_attachment_types)) {
                        $allowed_image_types['doc'] = 'application/msword';
                    }
                    if (in_array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $job_attachment_types)) {
                        $allowed_image_types['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    }
                    if (in_array('application/pdf', $job_attachment_types)) {
                        $allowed_image_types['pdf'] = 'application/pdf';
                    }
                    if (in_array('application/vnd.ms-excel', $job_attachment_types)) {
                        $allowed_image_types['xla|xls|xlt|xlw'] = 'application/vnd.ms-excel';
                    }
                    if (in_array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $job_attachment_types)) {
                        $allowed_image_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                    }

                    $status_upload = wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_image_types));

                    if ($test_uploaded_file && !isset($status_upload['file'])) {
                        $status_upload = jobsearch_wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_image_types));
                    }

                    if (empty($status_upload['error'])) {

                        $file_url = isset($status_upload['url']) ? $status_upload['url'] : '';
                        $upload_file_path = $wp_upload_dir['path'] . '/' . basename($file_url);
                        // Check the type of file. We'll use this as the 'post_mime_type'.
                        $filetype = wp_check_filetype(basename($file_url), null);

                        // Prepare an array of post data for the attachment.
                        $attachment = array(
                            'guid' => $wp_upload_dir['url'] . '/' . basename($upload_file_path),
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', ($upload_file['name'])),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );

                        // Insert the attachment.
                        $attach_id = wp_insert_attachment($attachment, $upload_file_path);

                        // Generate the metadata for the attachment, and update the database record.
                        $attach_data = wp_generate_attachment_metadata($attach_id, $upload_file_path);
                        wp_update_attachment_metadata($attach_id, $attach_data);

                        $attach_url = wp_get_attachment_url($attach_id);
                        $gall_ids[] = $attach_url;
                    }
                }
            }
        }

        return $gall_ids;
    }

    return false;
}

function jobsearch_get_currency_symbol() {
    global $woocommerce;

    $currency_sign = '$';
    if (function_exists('get_woocommerce_currency_symbol')) {
        $currency_sign = get_woocommerce_currency_symbol();
    }

    return $currency_sign;
}

function jobsearch_get_price_format($price = 0, $cur_tag = '') {

    $price = preg_replace("/[^0-9.]+/iu", "", $price);
    $price = $price > 0 ? $price : 0;
    if (function_exists('wc_price')) {
        $ret_price = wc_price($price);
        $ret_price = wp_kses($ret_price, array());
    } else {
        $ret_price = ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . jobsearch_get_currency_symbol() . ($cur_tag != '' ? '</' . $cur_tag . '>' : '') . number_format($price, 2, ".", ",");
    }

    return $ret_price;
}

function jobsearch_get_price_only_format($price = 0) {

    $ret_price = number_format($price, 2, ".", ",");

    return $ret_price;
}

function jobsearch_get_duration_unit_str($dur = '') {

    if ($dur == 'weeks') {
        $str = esc_html__('Weeks', 'wp-jobsearch');
    } else if ($dur == 'months') {
        $str = esc_html__('Months', 'wp-jobsearch');
    } else if ($dur == 'years') {
        $str = esc_html__('Years', 'wp-jobsearch');
    } else {
        $str = esc_html__('Days', 'wp-jobsearch');
    }
    return $str;
}

function jobsearch_google_map_with_directions($post_id = '', $map_height = '407', $ajax_flag = false, $cus_data = array()) {
    global $jobsearch_plugin_options;
    $cnt_counter = $rand_num = rand(1000000, 99999999);
    if (isset($cus_data['address']) && $cus_data['address'] != '') {
        $map_address = $cus_data['address'];
        $map_latitude = isset($cus_data['lat']) ? $cus_data['lat'] : '';
        $map_longitude = isset($cus_data['lng']) ? $cus_data['lng'] : '';
        $map_zoom = isset($cus_data['zoom']) ? $cus_data['zoom'] : '';
    } else {
        $map_address = get_post_meta($post_id, 'jobsearch_field_location_address', true);
        $map_latitude = get_post_meta($post_id, 'jobsearch_field_location_lat', true);
        $map_longitude = get_post_meta($post_id, 'jobsearch_field_location_lng', true);
        $map_zoom = get_post_meta($post_id, 'jobsearch_field_location_zoom', true);
    }

    $map_address = jobsearch_esc_html($map_address);
    $map_latitude = jobsearch_esc_html($map_latitude);
    $map_longitude = jobsearch_esc_html($map_longitude);
    $map_zoom = jobsearch_esc_html($map_zoom);

    $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';

    $_post_type = get_post_type($post_id);
    if ($_post_type == 'employer') {
        $marker_key_name = 'elistin_map_marker_img';
    } else if ($_post_type == 'candidate') {
        $marker_key_name = 'clistin_map_marker_img';
    } else {
        $marker_key_name = 'listin_map_marker_img';
    }

    $map_marker_icon = get_post_meta($post_id, 'jobsearch_field_marker_image', true);
    if ($map_marker_icon == '') {
        $map_marker_icon = isset($jobsearch_plugin_options[$marker_key_name]['url']) ? $jobsearch_plugin_options[$marker_key_name]['url'] : '';
    }
    if ($map_marker_icon == '') {
        if ($_post_type == 'candidate') {
            $map_marker_icon = jobsearch_plugin_get_url('images/candidate_map_marker.png');
        } else {
            $map_marker_icon = jobsearch_plugin_get_url('images/job_map_marker.png');
        }
    }

    $map_style = isset($jobsearch_plugin_options['jobsearch-location-map-style']) ? $jobsearch_plugin_options['jobsearch-location-map-style'] : '';


    if ($map_latitude != '' && $map_longitude != '' && $map_zoom > 0) {

        if ($location_map_type == 'mapbox') {

            wp_enqueue_script('jobsearch-mapbox');
            wp_enqueue_script('jobsearch-mapbox-geocoder');
            wp_enqueue_script('mapbox-geocoder-polyfill');
            wp_enqueue_script('mapbox-geocoder-polyfillauto');
            wp_enqueue_script('mapbox-directions');
        } else {
            wp_enqueue_script('jobsearch-google-map');
        }
        ?>
        <div class="jobsearch-map">
            <?php if ($location_map_type == 'mapbox') { ?>
                <div class="directions-main-con mapbox-directions">
                    <div class="directions-input-con">
                        <div id="go-to-<?php echo absint($cnt_counter) ?>" class="directon-goto-inpbox"></div>
                        <input id="go-to-hidden-<?php echo absint($cnt_counter) ?>" type="hidden">
                        <ul>
                            <li>
                                <span><i class="fa fa-search"></i></span>
                            </li>
                            <li>
                                <a id="get-direction-<?php echo absint($cnt_counter) ?>" href="javascript:void(0);"><i
                                        class="fa fa-mail-forward"></i></a>
                            </li>
                        </ul>
                    </div>
                    <div class="directions-modes-con" style="display: none;">
                        <a id="dir-close-<?php echo absint($cnt_counter) ?>" class="close-direc-panel"
                           href="javascript:void(0);"><i class="fa fa-times"></i></a>
                        <ul>
                            <li><input id="driving-mode-<?php echo absint($cnt_counter) ?>" type="radio"
                                       class="mode-radio-select" name="mode_radio_select" value="mapbox/driving"
                                       checked="checked"><label
                                       for="driving-mode-<?php echo absint($cnt_counter) ?>"><i
                                        class="fa fa-automobile"></i></label></li>
                            <li><input id="drivetraffic-mode-<?php echo absint($cnt_counter) ?>" type="radio"
                                       class="mode-radio-select" name="mode_radio_select"
                                       value="mapbox/driving-traffic"><label
                                       for="drivetraffic-mode-<?php echo absint($cnt_counter) ?>"><i
                                        class="fa fa-bus"></i></label>
                            </li>
                            <li><input id="walking-mode-<?php echo absint($cnt_counter) ?>" type="radio"
                                       class="mode-radio-select" name="mode_radio_select" value="mapbox/walking"><label
                                       for="walking-mode-<?php echo absint($cnt_counter) ?>"><i
                                        class="fa fa-blind"></i></label></li>
                            <li><input id="bycycle-mode-<?php echo absint($cnt_counter) ?>" type="radio"
                                       class="mode-radio-select" name="mode_radio_select" value="mapbox/cycling"><label
                                       for="bycycle-mode-<?php echo absint($cnt_counter) ?>"><i
                                        class="fa fa-bicycle"></i></label>
                            </li>
                        </ul>
                        <input id="direction-type-<?php echo absint($cnt_counter) ?>" type="hidden" value="DRIVING">
                        <div class="desti-to-orig">
                            <input id="go-desti-<?php echo absint($cnt_counter) ?>" value="<?php echo($map_address) ?>">
                        </div>
                    </div>
                </div>
                <div id="map-<?php echo absint($cnt_counter) ?>"
                     style="width:100%; height:<?php echo absint($map_height) ?>px;"></div>
                <div id="direc-in-map<?php echo($rand_num) ?>"></div>
                <?php
            } else {
                ?>
                <div class="directions-main-con">
                    <div class="directions-input-con">
                        <input id="go-to-<?php echo absint($cnt_counter) ?>">
                        <ul>
                            <li>
                                <span><i class="fa fa-search"></i></span>
                            </li>
                            <li>
                                <a id="get-direction-<?php echo absint($cnt_counter) ?>" href="javascript:void(0);"><i
                                        class="fa fa-mail-forward"></i></a>
                            </li>
                        </ul>
                    </div>
                    <div class="directions-modes-con" style="display: none;">
                        <a id="dir-close-<?php echo absint($cnt_counter) ?>" class="close-direc-panel"
                           href="javascript:void(0);"><i class="fa fa-times"></i></a>
                        <ul>
                            <li><input id="driving-mode-<?php echo absint($cnt_counter) ?>" type="radio"
                                       class="mode-radio-select" name="mode_radio_select" value="DRIVING"
                                       checked="checked"><label
                                       for="driving-mode-<?php echo absint($cnt_counter) ?>"><i
                                        class="fa fa-automobile"></i></label></li>
                            <li><input id="bus-mode-<?php echo absint($cnt_counter) ?>" type="radio"
                                       class="mode-radio-select" name="mode_radio_select" value="TRANSIT"><label
                                       for="bus-mode-<?php echo absint($cnt_counter) ?>"><i
                                        class="fa fa-bus"></i></label>
                            </li>
                            <li><input id="walking-mode-<?php echo absint($cnt_counter) ?>" type="radio"
                                       class="mode-radio-select" name="mode_radio_select" value="WALKING"><label
                                       for="walking-mode-<?php echo absint($cnt_counter) ?>"><i
                                        class="fa fa-blind"></i></label></li>
                            <li><input id="bycycle-mode-<?php echo absint($cnt_counter) ?>" type="radio"
                                       class="mode-radio-select" name="mode_radio_select" value="BICYCLING"><label
                                       for="bycycle-mode-<?php echo absint($cnt_counter) ?>"><i
                                        class="fa fa-bicycle"></i></label>
                            </li>
                            <li><input id="plane-mode-<?php echo absint($cnt_counter) ?>" type="radio"
                                       class="mode-radio-select" name="mode_radio_select" value="TRANSIT"><label
                                       for="plane-mode-<?php echo absint($cnt_counter) ?>"><i
                                        class="fa fa-plane"></i></label></li>
                        </ul>
                        <input id="direction-type-<?php echo absint($cnt_counter) ?>" type="hidden" value="DRIVING">
                        <div class="desti-to-orig">
                            <input id="go-orig-<?php echo absint($cnt_counter) ?>">
                            <input id="go-desti-<?php echo absint($cnt_counter) ?>" value="<?php echo($map_address) ?>">
                            <input id="go-to-hiden-<?php echo absint($cnt_counter) ?>" type="hidden">
                        </div>
                    </div>
                </div>
                <div id="map-<?php echo absint($cnt_counter) ?>"
                     style="height:<?php echo absint($map_height) ?>px;"></div>
                <div id="panel-<?php echo absint($cnt_counter) ?>" class="map-directions-container"></div>
                <div id="panel-no-<?php echo absint($cnt_counter) ?>"></div>
                <?php
            }
            ?>
        </div>
        <script>
        <?php
        if ($location_map_type == 'mapbox') {
            $mapbox_access_token = isset($jobsearch_plugin_options['mapbox_access_token']) ? $jobsearch_plugin_options['mapbox_access_token'] : '';
            $mapbox_style_url = isset($jobsearch_plugin_options['mapbox_style_url']) ? $jobsearch_plugin_options['mapbox_style_url'] : '';
            if ($mapbox_access_token != '' && $mapbox_style_url != '') {
                if ($ajax_flag != true) {
                    ?>
                        jQuery(document).ready(function () {
                <?php } ?>
                        document.getElementById('go-to-<?php echo absint($cnt_counter) ?>').addEventListener('focusin', function () {
                            jQuery('.directions-modes-con').slideDown('fast');
                        });
                        jQuery('#dir-close-<?php echo absint($cnt_counter) ?>').on('click', function () {
                            jQuery('.directions-modes-con').slideUp('fast');
                        });

                        mapboxgl.accessToken = '<?php echo($mapbox_access_token) ?>';
                <?php
                if (is_rtl()) {
                    ?>
                            mapboxgl.setRTLTextPlugin(
                                    'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.3/mapbox-gl-rtl-text.js',
                                    null,
                                    true // Lazy load the plugin
                                    );
                    <?php
                }
                ?>
                        var map = new mapboxgl.Map({
                            container: 'map-<?php echo absint($rand_num); ?>',
                            style: '<?php echo($mapbox_style_url) ?>',
                            center: [<?php echo esc_js($map_longitude) ?>, <?php echo esc_js($map_latitude) ?>],
                            scrollZoom: false,
                            zoom: <?php echo esc_js($map_zoom) ?>
                        });
                        map.addControl(new mapboxgl.NavigationControl({
                            showCompass: false
                        }), 'bottom-right');
                        var el = new Image();
                        el.src = '<?php echo($map_marker_icon) ?>';
                        el.classList.add('mapMarker');
                        el.dataset.type = 'point';
                        var marker = new mapboxgl.Marker({
                            element: el,
                            draggable: false
                        }).setLngLat([<?php echo esc_js($map_longitude) ?>, <?php echo esc_js($map_latitude) ?>]).addTo(map);
                        var mapboxDir = new MapboxDirections({
                            accessToken: mapboxgl.accessToken,
                            controls: {
                                inputs: false,
                                //instructions: false,
                            }
                        });
                        document.getElementById('direc-in-map<?php echo($rand_num) ?>').appendChild(mapboxDir.onAdd(map));
                        var geocodParams = {
                            accessToken: mapboxgl.accessToken,
                            marker: false,
                            flyTo: false,
                            mapboxgl: mapboxgl
                        };
                        var selected_contries = jobsearch_plugin_vars.sel_countries_json;
                        if (selected_contries != '') {
                            var selected_contries_tojs = jQuery.parseJSON(selected_contries);
                            var sel_countries_str = selected_contries_tojs.join();
                            geocodParams['countries'] = sel_countries_str;
                        }
                        var geocoder<?php echo($rand_num) ?> = new MapboxGeocoder(geocodParams);
                        document.getElementById('go-to-<?php echo($rand_num) ?>').appendChild(geocoder<?php echo($rand_num) ?>.onAdd(map));
                        geocoder<?php echo($rand_num) ?>.on('result', function (obj) {
                            var place_name = obj.result.place_name;
                            document.getElementById("go-to-hidden-<?php echo absint($rand_num); ?>").value = place_name;
                        });
                        jQuery('#get-direction-<?php echo absint($cnt_counter) ?>').on('click', function () {
                            var origPlace = jQuery('#go-desti-<?php echo absint($cnt_counter) ?>').val();
                            var destiPlace = jQuery('#go-to-hidden-<?php echo absint($cnt_counter) ?>').val();
                            if (origPlace != '') {
                                mapboxDir.setOrigin(origPlace);
                            } else {
                                mapboxDir.setOrigin([<?php echo esc_js($map_longitude) ?>, <?php echo esc_js($map_latitude) ?>]);
                            }
                            mapboxDir.setDestination(destiPlace);
                            mapboxDir.getWaypoints();
                        });
                <?php if ($ajax_flag != true) { ?>
                        });
                <?php } ?>
                <?php
            }
        } else {
            ?>
                var det_map;

                jQuery(document).on('click', 'input[type="radio"][class="mode-radio-select"]', function () {
                    var sel_mode_val = jQuery('input[type="radio"][class="mode-radio-select"]:checked').val();
                    jQuery('#direction-type-<?php echo absint($cnt_counter) ?>').val(sel_mode_val);
                });

                jQuery(document).ready(function () {

                    document.getElementById('go-to-<?php echo absint($cnt_counter) ?>').addEventListener('focusin', function () {
                        jQuery('.directions-modes-con').slideDown('fast');
                    });
                    document.getElementById('go-to-<?php echo absint($cnt_counter) ?>').addEventListener('focusout', function () {
                        //jQuery('.directions-modes-con').slideUp('fast');
                    });
                    jQuery('#dir-close-<?php echo absint($cnt_counter) ?>').on('click', function () {
                        jQuery('.directions-modes-con').slideUp('fast');
                    });

                    function initMap() {
                        var directionsService = new google.maps.DirectionsService();
                        var directionsDisplay = new google.maps.DirectionsRenderer();

                        var myLatLng = {
                            lat: <?php echo esc_js($map_latitude) ?>,
                            lng: <?php echo esc_js($map_longitude) ?>
                        };
                        det_map = new google.maps.Map(document.getElementById('map-<?php echo absint($cnt_counter) ?>'), {
                            zoom: <?php echo esc_js($map_zoom) ?>,
                            center: myLatLng,
                            streetViewControl: false,
                            scrollwheel: false,
                            mapTypeControl: false,
                        });

            <?php
            if ($map_style != '') {
                $map_style = stripslashes($map_style);
                $map_style = preg_replace('/\s+/', ' ', trim($map_style));
                ?>
                            var styles = '<?php echo($map_style) ?>';
                            if (styles != '') {
                                styles = jQuery.parseJSON(styles);
                                var styledMap = new google.maps.StyledMapType(
                                        styles,
                                        {name: 'Styled Map'}
                                );
                                det_map.mapTypes.set('map_style', styledMap);
                                det_map.setMapTypeId('map_style');
                            }
                <?php
            }
            ?>

                        var marker = new google.maps.Marker({
                            position: myLatLng,
                            map: det_map,
                            title: '',
                            icon: '<?php echo($map_marker_icon) ?>',
                        });

                        directionsDisplay.setMap(det_map);
                        directionsDisplay.setPanel(document.getElementById('panel-<?php echo absint($cnt_counter) ?>'));

                        google.maps.event.addDomListener(document.getElementById('get-direction-<?php echo absint($cnt_counter) ?>'), 'click', function () {

                            var desti = jQuery('#go-desti-<?php echo absint($cnt_counter) ?>').val();
                            var orig = jQuery('#go-to-hiden-<?php echo absint($cnt_counter) ?>').val();
                            var selectedMode = jQuery('#direction-type-<?php echo absint($cnt_counter) ?>').val();

                            //if (jQuery('#go-orig-<?php echo absint($cnt_counter) ?>').val() != '') {
                            //    orig = jQuery('#go-orig-<?php echo absint($cnt_counter) ?>').val();
                            //}

                            if (desti != '' && orig != '') {

                                var request = {
                                    origin: orig,
                                    destination: desti,
                                    travelMode: google.maps.TravelMode[selectedMode]
                                };

                                directionsService.route(request, function (response, status) {
                                    if (status == google.maps.DirectionsStatus.OK) {
                                        directionsDisplay.setDirections(response);
                                        jQuery('#panel-<?php echo absint($cnt_counter) ?>').show();
                                        //
                                        jQuery('#panel-no-<?php echo absint($cnt_counter) ?>').html('');
                                        jQuery('#panel-no-<?php echo absint($cnt_counter) ?>').hide();
                                    } else {
                                        jQuery('#panel-no-<?php echo absint($cnt_counter) ?>').html('<?php esc_html_e("No direction found.", "wp-jobsearch") ?>');
                                        jQuery('#panel-no-<?php echo absint($cnt_counter) ?>').show();
                                    }
                                });
                                jQuery('.directions-modes-con').slideUp('fast');
                            }
                        });

                        var ac_goto_input = document.getElementById('go-to-<?php echo absint($cnt_counter) ?>');
                        var autocomplete_goto = new google.maps.places.Autocomplete(ac_goto_input);

                        var ac_orig_input = document.getElementById('go-orig-<?php echo absint($cnt_counter) ?>');
                        var autocomplete_orig = new google.maps.places.Autocomplete(ac_orig_input);

                        var ac_desti_input = document.getElementById('go-desti-<?php echo absint($cnt_counter) ?>');
                        var autocomplete_desti = new google.maps.places.Autocomplete(ac_desti_input);

                        //
                        google.maps.event.addListener(autocomplete_goto, 'place_changed', function () {
                            var gplace_val = jQuery('#go-to-<?php echo absint($cnt_counter) ?>').val();
                            jQuery('#go-to-hiden-<?php echo absint($cnt_counter) ?>').val(gplace_val);
                            jQuery('#go-orig-<?php echo absint($cnt_counter) ?>').val(gplace_val);
                            return false;
                        });
                        //
                        google.maps.event.addListener(autocomplete_orig, 'place_changed', function () {
                            var gplace_val = jQuery('#go-orig-<?php echo absint($cnt_counter) ?>').val();
                            jQuery('#go-to-hiden-<?php echo absint($cnt_counter) ?>').val(gplace_val);
                            jQuery('#go-to-<?php echo absint($cnt_counter) ?>').val(gplace_val);
                            return false;
                        });

                    }

                    //google.maps.event.addDomListener(window, 'load', initMap);
                    initMap();
                });
            <?php
        }
        ?>
        </script>
        <?php
    }
}

if (!function_exists('jobsearch_find_in_multiarray')) {

    function jobsearch_find_in_multiarray($elem, $array, $field) {

        $top = sizeof($array);
        $k = 0;
        $new_array = array();
        for ($i = 0; $i <= $top; $i++) {
            if (isset($array[$i])) {
                $new_array[$k] = $array[$i];
                $k++;
            }
        }
        $array = $new_array;
        $top = sizeof($array) - 1;
        $bottom = 0;

        $finded_index = array();
        if (is_array($array)) {
            while ($bottom <= $top) {
                if (isset($array[$bottom][$field]) && $array[$bottom][$field] == $elem)
                    $finded_index[] = $bottom;
                else
                if (isset($array[$bottom][$field]) && is_array($array[$bottom][$field]))
                    if (jobsearch_find_in_multiarray($elem, ($array[$bottom][$field])))
                        $finded_index[] = $bottom;
                $bottom++;
            }
        }
        return $finded_index;
    }

}

if (!function_exists('jobsearch_filter_querystring_variables')) {

    function jobsearch_filter_querystring_variables($qrystr) {

        $qrystr;
        return $qrystr;
    }

}

if (!function_exists('jobsearch_user_profile_before')) {

    add_action('jobsearch_user_profile_before', 'jobsearch_user_profile_before', 10, 1);

    function jobsearch_user_profile_before($id) {

        $job_id = isset($_GET['job_id']) ? $_GET['job_id'] : '';
        $employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : '';
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        if ($action == 'preview_profile' && $employer_id > 0) {
            $user_id = jobsearch_get_employer_user_id($employer_id);

            if ($job_id > 0) {
                $viewed_candidates = get_post_meta($job_id, 'jobsearch_viewed_candidates', true);
                if (jobsearch_is_employer_job($job_id, $user_id)) {
                    if (empty($viewed_candidates)) {
                        $viewed_candidates = array();
                    }
                    if (!in_array($id, $viewed_candidates)) {
                        $viewed_candidates[] = $id;
                        update_post_meta($job_id, 'jobsearch_viewed_candidates', $viewed_candidates);

                        do_action('jobsearch_after_cand_preview_as_applicant', $id, $employer_id);
                    }
                }
            } else {
                $viewed_candidates = get_post_meta($employer_id, 'jobsearch_viewed_candidates', true);
                if (empty($viewed_candidates)) {
                    $viewed_candidates = array();
                }
                if (!in_array($id, $viewed_candidates)) {
                    $viewed_candidates[] = $id;
                    update_post_meta($employer_id, 'jobsearch_viewed_candidates', $viewed_candidates);

                    do_action('jobsearch_after_cand_preview_as_applicant', $id, $employer_id);
                }
            }
        }
    }

}
if (!function_exists('jobsearch_get_user_id')) {

    function jobsearch_get_user_id() {
        global $current_user;
        wp_get_current_user();
        return $current_user->ID;
    }

}

if (!function_exists('jobsearch_get_user_jobapply_meta')) {

    function jobsearch_get_user_jobapply_meta($user = "") {
        if (!empty($user)) {
            $userdata = get_user_by('login', $user);
            $user_id = $userdata->ID;
            return get_user_meta($user_id, 'jobsearch-jobs-applied', true);
        } else {
            return get_user_meta(jobsearch_get_user_id(), 'jobsearch-jobs-applied', true);
        }
    }

}

if (!function_exists('jobsearch_update_user_jobapply_meta')) {

    function jobsearch_update_user_jobapply_meta($arr) {
        return update_user_meta(jobsearch_get_user_id(), 'jobsearch-jobs-applied', $arr);
    }

}

if (!function_exists('jobsearch_create_user_meta_list')) {

    function jobsearch_create_user_meta_list($post_id, $list_name, $user_id) {
        $current_timestamp = strtotime(current_time('d-m-Y H:i:s'));
        $existing_list_data = array();
        $existing_list_data = get_user_meta($user_id, $list_name, true);
        if (!is_array($existing_list_data)) {
            $existing_list_data = array();
        }

        if (is_array($existing_list_data)) {
            // search duplicat and remove it then arrange new ordering
            $finded = jobsearch_find_in_multiarray($post_id, $existing_list_data, 'post_id');
            $existing_list_data = remove_index_from_array($existing_list_data, $finded);
            // adding one more entry
            $existing_list_data[] = array('post_id' => $post_id, 'date_time' => $current_timestamp);
            update_user_meta($user_id, $list_name, $existing_list_data);
        }
    }

}

if (!function_exists('jobsearch_remove_user_meta_list')) {

    function jobsearch_remove_user_meta_list($post_id, $list_name, $user_id) {

        $existing_list_data = array();
        $existing_list_data = get_user_meta($user_id, $list_name, true);
        if (!is_array($existing_list_data)) {
            $existing_list_data = array();
        }

        if (is_array($existing_list_data)) {
            // search duplicat and remove it then arrange new ordering
            $finded = jobsearch_find_in_multiarray($post_id, $existing_list_data, 'post_id');
            $existing_list_data = remove_index_from_array($existing_list_data, $finded);

            update_user_meta($user_id, $list_name, $existing_list_data);
        }
    }

}

if (!function_exists('remove_index_from_array')) {

    function remove_index_from_array($array, $index_array) {
        $top = sizeof($index_array) - 1;
        $bottom = 0;
        if (is_array($index_array)) {
            while ($bottom <= $top) {
                unset($array[$index_array[$bottom]]);
                $bottom++;
            }
        }
        if (!empty($array))
            return array_values($array);
        else
            return $array;
    }

}

if (!function_exists('jobsearch_find_index_user_meta_list')) {

    function jobsearch_find_index_user_meta_list($post_id, $list_name, $need_find, $user_id) {
        $existing_list_data = get_user_meta($user_id, $list_name, true);
        if (empty($existing_list_data)) {
            $existing_list_data = array();
        }
        $finded = array();
        if (is_array($existing_list_data) && !empty($existing_list_data)) {
            $finded = find_in_multiarray($post_id, $existing_list_data, $need_find);
        }
        return $finded;
    }

}

if (!function_exists('find_in_multiarray')) {

    function find_in_multiarray($elem, $array, $field) {
        $top = sizeof($array);
        $k = 0;
        $new_array = array();
        for ($i = 0; $i <= $top; $i++) {
            if (isset($array[$i])) {
                $new_array[$k] = $array[$i];
                $k++;
            }
        }
        $array = $new_array;
        $top = sizeof($array) - 1;
        $bottom = 0;
        $finded_index = array();
        if (is_array($array)) {
            while ($bottom <= $top) {
                if ($array[$bottom][$field] == $elem)
                    $finded_index[] = $bottom;
                else
                if (is_array($array[$bottom][$field]))
                    if (find_in_multiarray($elem, ($array[$bottom][$field])))
                        $finded_index[] = $bottom;
                $bottom++;
            }
        }
        return $finded_index;
    }

}

function jobsearch_get_attachment_id_from_url($attachment_url = '') {

    global $wpdb;
    $attachment_id = false;

    // If there is no url, return.
    if ('' == $attachment_url)
        return;

    if (strpos($attachment_url, 'jobsearch-user-files')) {
        add_filter('upload_dir', 'jobsearch_user_upload_files_path');
    }
    // Get the upload directory paths
    $upload_dir_paths = wp_upload_dir();

    // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
    if (false !== strpos($attachment_url, $upload_dir_paths['baseurl'])) {

        // If this is the URL of an auto-generated thumbnail, get the URL of the original image
        $attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);

        // Remove the upload path base directory from the attachment URL
        $attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);

        // Finally, run a custom database query to get the attachment ID from the modified attachment URL
        $perepare_query = $wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url);

        $attachment_id = $wpdb->get_var($perepare_query);
    }
    if (strpos($attachment_url, 'jobsearch-user-files')) {
        remove_filter('upload_dir', 'jobsearch_user_upload_files_path');
    }

    return $attachment_id;
}

if (!function_exists('jobsearch_recaptcha')) {

    function jobsearch_recaptcha($id = '') {
        global $jobsearch_plugin_options;
        $captcha_switch = isset($jobsearch_plugin_options['captcha_switch']) ? $jobsearch_plugin_options['captcha_switch'] : '';
        $sitekey = isset($jobsearch_plugin_options['captcha_sitekey']) ? $jobsearch_plugin_options['captcha_sitekey'] : '';
        $secretkey = isset($jobsearch_plugin_options['captcha_secretkey']) ? $jobsearch_plugin_options['captcha_secretkey'] : '';
        $output = '';
        if ($captcha_switch == 'on') {
            wp_enqueue_script('jobsearch_google_recaptcha');
            if ($sitekey != '' && $secretkey != '') {
                $output .= '<div class="g-recaptcha" data-theme="light" id="' . $id . '" data-sitekey="' . $sitekey . '">'
                        . '</div> <a class="recaptcha-reload-a" href="javascript:void(0);" onclick="jobsearch_captcha_reload(\'' . admin_url('admin-ajax.php') . '\', \'' . $id . '\');">'
                        . '<i class="fa fa-refresh"></i> ' . esc_html__('Reload', 'wp-jobsearch') . '</a>';
            } else {
                $output = '<p>' . esc_html__('Please provide google captcha API keys', 'wp-jobsearch') . '</p>';
            }
        }
        return $output;
    }

}

/*
 * Start Function for create form validation/verify captcha
 */
if (!function_exists('jobsearch_captcha_verify')) {

    function jobsearch_captcha_verify($page = '') {
        global $jobsearch_plugin_options;
        $jobsearch_captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
        $captcha_switch = isset($jobsearch_plugin_options['captcha_switch']) ? $jobsearch_plugin_options['captcha_switch'] : '';
        if ($captcha_switch == 'on') {
            if ($page == true) {
                if (empty($jobsearch_captcha)) {
                    return true;
                }
            } else {
                $json = array();
                if (empty($jobsearch_captcha)) {
                    $json['error'] = '1';
                    $json['msg'] = '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . esc_html__('Please fill the captcha field.', 'wp-jobsearch') . '</div>';
                    $json['message'] = '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . esc_html__('Please fill the captcha field.', 'wp-jobsearch') . '</div>';
                    echo json_encode($json);
                    exit();
                }
            }
        }
    }

}

/*
 * Start Function for create captcha reload
 */
if (!function_exists('jobsearch_captcha_reload')) {

    function jobsearch_captcha_reload($atts = '') {
        global $jobsearch_plugin_options;
        $captcha_id = isset($_REQUEST['captcha_id']) ? $_REQUEST['captcha_id'] : '';
        $sitekey = isset($jobsearch_plugin_options['captcha_sitekey']) ? $jobsearch_plugin_options['captcha_sitekey'] : '';
        $html = "<script>
        var " . $captcha_id . ";
            " . $captcha_id . " = grecaptcha.render('" . $captcha_id . "', {
                'sitekey': '" . $sitekey . "', //Replace this with your Site key
                'theme': 'light'
            });"
                . "</script>";
        $html .= jobsearch_recaptcha($captcha_id);
        echo force_balance_tags($html);
        die();
    }

    add_action('wp_ajax_jobsearch_captcha_reload', 'jobsearch_captcha_reload');
    add_action('wp_ajax_nopriv_jobsearch_captcha_reload', 'jobsearch_captcha_reload');
}

function jobsearch_keywords_to_translate_arr() {
    $trans_array = array(
        'recent' => esc_html__('Recent', 'wp-jobsearch'),
        'all' => esc_html__('All', 'wp-jobsearch'),
        'featured' => esc_html__('Featured', 'wp-jobsearch'),
        'alphabetical' => esc_html__('Alphabetical', 'wp-jobsearch'),
        'most_viewed' => esc_html__('Most Viewed', 'wp-jobsearch'),
    );

    return apply_filters('jobsearch_keywords_to_translate_arr', $trans_array);
}

if (!function_exists('jobsearch__get_post_id')) {

    function jobsearch__get_post_id($id_slug, $type = 'post') {
        if ($id_slug != '' && absint($id_slug) <= 0) {
            $post_obj = $id_slug != '' ? get_page_by_path($id_slug, 'OBJECT', $type) : '';
            if (is_object($post_obj) && isset($post_obj->ID)) {
                return $post_obj->ID;
            }
        } else if ($id_slug > 0) {
            return $id_slug;
        }
        return 0;
    }

}

if (!function_exists('jobsearch_get_post_id_by_guid')) {

    function jobsearch_get_post_id_by_guid($guid) {
        global $wpdb;
        if ($guid != '') {
            $post_query = "SELECT posts.ID FROM $wpdb->posts AS posts";
            $post_query .= " WHERE posts.guid='{$guid}'";
            $post_query .= " LIMIT 1";
            $get_db_res = $wpdb->get_col($post_query);
            if (!empty($get_db_res) && isset($get_db_res[0])) {
                return $get_db_res[0];
            }
        }
        return 0;
    }

}

add_filter('term_link', 'jobsearch_modify_sector_tax_link', 1, 2);

function jobsearch_modify_sector_tax_link($content, $term_obj) {
    global $jobsearch_plugin_options;
    $search_list_page = isset($jobsearch_plugin_options['jobsearch_search_list_page']) ? $jobsearch_plugin_options['jobsearch_search_list_page'] : '';
    if (is_object($term_obj) && isset($term_obj->taxonomy) && $term_obj->taxonomy == 'sector' && $search_list_page != '') {
        $page_obj = get_page_by_path($search_list_page, 'OBJECT', 'page');
        if (isset($page_obj->ID)) {
            $term_slug = $term_obj->slug;
            $content = add_query_arg(array('sector_cat' => $term_slug), get_permalink($page_obj->ID));
        }
    }
    if (is_object($term_obj) && isset($term_obj->taxonomy) && $term_obj->taxonomy == 'skill' && $search_list_page != '') {
        $page_obj = get_page_by_path($search_list_page, 'OBJECT', 'page');
        if (isset($page_obj->ID)) {
            $term_slug = $term_obj->slug;
            $content = add_query_arg(array('skill' => $term_slug), get_permalink($page_obj->ID));
        }
    }
    if (is_object($term_obj) && isset($term_obj->taxonomy) && $term_obj->taxonomy == 'jobtype' && $search_list_page != '') {
        $page_obj = get_page_by_path($search_list_page, 'OBJECT', 'page');
        if (isset($page_obj->ID)) {
            $term_slug = $term_obj->slug;
            $content = add_query_arg(array('job_type' => $term_slug), get_permalink($page_obj->ID));
        }
    }
    if (is_object($term_obj) && isset($term_obj->taxonomy) && $term_obj->taxonomy == 'job-location' && $search_list_page != '') {
        $page_obj = get_page_by_path($search_list_page, 'OBJECT', 'page');
        if (isset($page_obj->ID)) {
            $term_slug = $term_obj->slug;
            $content = add_query_arg(array('location' => $term_slug), get_permalink($page_obj->ID));
        }
    }
    return $content;
}

function jobsearch_is_post_ids_array($array, $post_type = 'job') {
    $retrn_array = array();
    if (!empty($array) && !is_array($array)) {
        $array = explode(',', $array);
    }
    if (!empty($array)) {
        foreach ($array as $ret_arr) {
            if (get_post_type($ret_arr) == 'candidate') {
                $retrn_array[] = $ret_arr;
            }
        }
        return $retrn_array;
    }
    return $array;
}

function jobsearch_is_valid_phone_number($phone_num = '') {
    if ($phone_num != '') {
        $pattern = '/^[0-9\-\(\)\/\+\s]*$/';
        preg_match($pattern, $phone_num, $match_num);
        return $match_num;
    }
}

add_filter('careerfy_header_button_html', 'jobsearch_header_post_job_button_html', 10, 2);

function jobsearch_header_post_job_button_html($html) {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $is_employer = jobsearch_user_is_employer($user_id);
        if (!$is_employer) {
            $html = '';
        }
    }
    return $html;
}

add_filter('careerfy_header_button_url', 'jobsearch_post_job_button_url', 10, 2);

function jobsearch_post_job_button_url($url, $id = '') {
    global $jobsearch_plugin_options;
    $post_job_without_reg = isset($jobsearch_plugin_options['job-post-wout-reg']) ? $jobsearch_plugin_options['job-post-wout-reg'] : '';
    $page_ide = jobsearch_wpml_lang_page_id($id, 'page');
    $url = get_permalink($page_ide);
    if (is_user_logged_in() && $post_job_without_reg != 'on') {
        $user_id = get_current_user_id();
        $is_employer = jobsearch_user_is_employer($user_id);
        if ($is_employer) {
            $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
            $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');
            $url = add_query_arg(array('tab' => 'user-job'), $page_url);
        }
    }
    return $url;
}

add_filter('careerfy_header_button_text', 'jobsearch_post_job_button_text', 10, 2);

function jobsearch_post_job_button_text($text, $id = '') {
    global $jobsearch_plugin_options;
    $post_job_without_reg = isset($jobsearch_plugin_options['job-post-wout-reg']) ? $jobsearch_plugin_options['job-post-wout-reg'] : '';
    $page_ide = jobsearch_wpml_lang_page_id($id, 'page');
    $text = get_the_title($page_ide);
    if (is_user_logged_in() && $post_job_without_reg != 'on') {
        $user_id = get_current_user_id();
        $is_employer = jobsearch_user_is_employer($user_id);
        if ($is_employer) {
            $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
            $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');
            $url = add_query_arg(array('tab' => 'user-job'), $page_url);
        }
    }
    return $text;
}

function jobsearch_no_image_placeholder($size = 'thumbnail') {
    global $jobsearch_plugin_options;
    $no_img_url = isset($jobsearch_plugin_options['default_no_img']['url']) && $jobsearch_plugin_options['default_no_img']['url'] != '' ? $jobsearch_plugin_options['default_no_img']['url'] : '';
    if ($no_img_url != '') {
        $no_img_id = jobsearch_get_attachment_id_from_url($no_img_url);
        if ($no_img_id > 0) {
            $no_img_src = wp_get_attachment_image_src($no_img_id, $size);
            $no_img_url = isset($no_img_src[0]) && esc_url($no_img_src[0]) != '' ? $no_img_src[0] : $no_img_url;
        }
        return $no_img_url;
    }
    return jobsearch_plugin_get_url('images/no-image.jpg');
}

function jobsearch_candidate_image_placeholder($size = 'thumbnail') {
    global $jobsearch_plugin_options;
    $no_img_url = isset($jobsearch_plugin_options['candidate_no_img']['url']) && $jobsearch_plugin_options['candidate_no_img']['url'] != '' ? $jobsearch_plugin_options['candidate_no_img']['url'] : '';
    if ($no_img_url != '') {
        $no_img_id = jobsearch_get_attachment_id_from_url($no_img_url);
        if ($no_img_id > 0) {
            $no_img_src = wp_get_attachment_image_src($no_img_id, $size);
            $no_img_url = isset($no_img_src[0]) && esc_url($no_img_src[0]) != '' ? $no_img_src[0] : $no_img_url;
        }
        return $no_img_url;
    }
    return jobsearch_plugin_get_url('images/no-image.jpg');
}

function jobsearch_employer_image_placeholder($size = 'thumbnail') {
    global $jobsearch_plugin_options;
    $no_img_url = isset($jobsearch_plugin_options['employer_no_img']['url']) && $jobsearch_plugin_options['employer_no_img']['url'] != '' ? $jobsearch_plugin_options['employer_no_img']['url'] : '';
    if ($no_img_url != '') {
        $no_img_id = jobsearch_get_attachment_id_from_url($no_img_url);
        if ($no_img_id > 0) {
            $no_img_src = wp_get_attachment_image_src($no_img_id, $size);
            $no_img_url = isset($no_img_src[0]) && esc_url($no_img_src[0]) != '' ? $no_img_src[0] : $no_img_url;
        }
        return $no_img_url;
    }
    return jobsearch_plugin_get_url('images/no-image.jpg');
}

function jobsearch_get_user_roles_by_user_id($user_id) {
    $user = get_userdata($user_id);
    return empty($user) ? array() : $user->roles;
}

if (!function_exists('jobsearch_get_ajax_users_list')) {
    add_action('wp_ajax_jobsearch_get_ajax_users_list', 'jobsearch_get_ajax_users_list');

    function jobsearch_get_ajax_users_list() {
        $sel_user = isset($_POST['sel_value']) ? $_POST['sel_value'] : '';
        $users_role = isset($_POST['users_role']) ? $_POST['users_role'] : '';
        $user_role_array = array('jobsearch_candidate', 'jobsearch_employer');
        if (!in_array($users_role, $user_role_array)) {
            $users_role = 'jobsearch_candidate';
        }
        $users_list = '';
        $users = get_users('orderby=nicename&role=' . $users_role);
        if (!empty($users)) {
            foreach ($users as $_user) {
                $user_name = $_user->display_name;
                $user_name = apply_filters('jobsearch_user_display_name', $user_name, $_user);
                if ($sel_user != $_user->user_login) {
                    $users_list .= '<option value="' . $_user->user_login . '">' . $user_name . '</option>' . "\n";
                }
            }
        }
        //
        echo json_encode(array('list' => $users_list));
        die;
    }

}

if (!function_exists('jobsearch_get_all_db_locations')) {

    function jobsearch_get_all_db_locations() {
        global $jobsearch_plugin_options;
        $location_name = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
        $country_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
        );
        //$locations_objs = get_terms('job-location', $country_args);
        $locations_objs = jobsearch_get_terms_woutparnt('job-location');
        $location_list = array();
        $selectedkey = '';
        if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
            $selectedkey = $_REQUEST['location'];
        }

        if (isset($locations_objs) && !empty($locations_objs)) {
            foreach ($locations_objs as $key => $country) {
                $selected = '';
                if (isset($selectedkey) && $selectedkey == $country->slug) {
                    $selected = 'selected';
                }
                if (preg_match("/^$location_name/i", $country->name)) {
                    $location_list[] = array('slug' => $country->slug, 'value' => $country->name);
                }
            }
        }
        echo json_encode($location_list);
        die();
    }

    add_action("wp_ajax_jobsearch_get_all_db_locations", "jobsearch_get_all_db_locations");
    add_action("wp_ajax_nopriv_jobsearch_get_all_db_locations", "jobsearch_get_all_db_locations");
}

function jobsearch_get_wp_date_simple_format() {
    $date_format = get_option('date_format');
    $date_format = str_replace(array('/', ', ', ' ', ','), array('-', '-', '-', '-'), $date_format);
    $dformt_arr = explode('-', $date_format);
    $ret_format = 'd-m-y';
    if (!empty($dformt_arr) && sizeof($dformt_arr) == 3) {
        $fory_formt = array();

        $day_vars = array('d', 'D', 'j', 'l');
        $month_vars = array('F', 'm', 'M', 'n');
        $year_vars = array('y', 'Y');

        if (in_array($dformt_arr[0], $day_vars)) {
            $fory_formt[] = 'd';
        } else if (in_array($dformt_arr[0], $month_vars)) {
            $fory_formt[] = 'm';
        } else if (in_array($dformt_arr[0], $year_vars)) {
            $fory_formt[] = 'y';
        }

        if (in_array($dformt_arr[1], $day_vars)) {
            $fory_formt[] = 'd';
        } else if (in_array($dformt_arr[1], $month_vars)) {
            $fory_formt[] = 'm';
        } else if (in_array($dformt_arr[1], $year_vars)) {
            $fory_formt[] = 'y';
        }

        if (in_array($dformt_arr[2], $day_vars)) {
            $fory_formt[] = 'd';
        } else if (in_array($dformt_arr[2], $month_vars)) {
            $fory_formt[] = 'm';
        } else if (in_array($dformt_arr[2], $year_vars)) {
            $fory_formt[] = 'y';
        }

        //
        if (!empty($fory_formt) && sizeof($fory_formt) == 3) {
            $ret_format = implode('-', $fory_formt);
        }
    }

    return $ret_format;
}

function jobsearch_get_delte_all_locs() {
    global $wpdb;
    $wpdb->query("DELETE $wpdb->terms FROM $wpdb->terms LEFT JOIN $wpdb->term_taxonomy ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id) WHERE $wpdb->term_taxonomy.taxonomy ='job-location'");
    $wpdb->query("DELETE FROM $wpdb->term_taxonomy WHERE $wpdb->term_taxonomy.taxonomy = 'job-location'");
}

if (!function_exists('jobsearch_get_search_box_posts_results')) {

    function jobsearch_get_search_box_posts_results() {
        global $jobsearch_plugin_options;
        $emporler_approval = isset($jobsearch_plugin_options['job_listwith_emp_aprov']) ? $jobsearch_plugin_options['job_listwith_emp_aprov'] : '';
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
        $post_type = isset($_POST['post_type']) ? $_POST['post_type'] : '';

        $types_array = array('job', 'employer', 'candidate');
        if (!in_array($post_type, $types_array)) {
            $post_type = 'job';
        }

        $jobs_listin_class = new Jobsearch_Shortcode_Jobs_Frontend;

        $results_list = array();
        if ($keyword != '') {
            if ($post_type == 'job') {
                $default_date_time_formate = 'd-m-Y H:i:s';
                $element_filter_arr = array();

                $post_ids = array();
                $post_ids = $jobs_listin_class->job_general_query_filter($post_ids, array());
                $all_post_ids = $post_ids;

                if ($emporler_approval != 'off') {
                    $element_filter_arr[] = array(
                        'key' => 'jobsearch_job_employer_status',
                        'value' => 'approved',
                        'compare' => '=',
                    );
                }
                $args = array(
                    'posts_per_page' => '5',
                    'post_type' => 'job',
                    'post_status' => 'publish',
                    'order' => 'DESC',
                    'orderby' => 'meta_value_num',
                    'meta_key' => 'jobsearch_field_job_publish_date',
                    'fields' => 'ids', // only load ids
                    //'s' => $keyword,
                    'meta_query' => array(
                        $element_filter_arr,
                    ),
                );
                if (!empty($all_post_ids)) {
                    $all_post_ids = $post_ids;
                } else {
                    $all_post_ids = array(0);
                }
                $args['post__in'] = $all_post_ids;
            } else if ($post_type == 'employer') {
                $element_filter_arr = array();
                $element_filter_arr[] = array(
                    'key' => 'jobsearch_field_employer_approved',
                    'value' => 'on',
                    'compare' => '=',
                );
                $args = array(
                    'posts_per_page' => '5',
                    'post_type' => 'employer',
                    'post_status' => 'publish',
                    'order' => 'DESC',
                    'orderby' => 'ID',
                    'fields' => 'ids', // only load ids
                    //'s' => $keyword,
                    'meta_query' => array(
                        $element_filter_arr,
                    ),
                );
            } else if ($post_type == 'candidate') {
                $element_filter_arr = array();
                $element_filter_arr[] = array(
                    'key' => 'jobsearch_field_candidate_approved',
                    'value' => 'on',
                    'compare' => '=',
                );
                $args = array(
                    'posts_per_page' => '5',
                    'post_type' => 'candidate',
                    'post_status' => 'publish',
                    'order' => 'DESC',
                    'orderby' => 'ID',
                    'fields' => 'ids', // only load ids
                    //'s' => $keyword,
                    'meta_query' => array(
                        $element_filter_arr,
                    ),
                );
            }

            add_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);
            $args_query = new WP_Query($args);
            remove_filter('posts_where', 'jobsearch_search_query_results_filter', 10);
            wp_reset_postdata();

            //var_dump($args_query->request);

            $total_posts = $args_query->found_posts;
            $post_ids = $args_query->posts;
            //var_dump($post_ids);
            
            $post_ids = apply_filters('jobsearch_ajaxsrchbox_post_mainadd_ids', $post_ids, $post_type, $keyword);
            //
            if (!empty($post_ids)) {
                foreach ($post_ids as $post_id) {
                    //
                    $srch_posadres = jobsearch_job_item_address($post_id);
                    //
                    if ($post_type == 'job') {
                        $srch_pos_thum_id = jobsearch_job_get_profile_image($post_id);
                    } else {
                        $srch_pos_thum_id = get_post_thumbnail_id($post_id);
                    }
                    $srch_pos_thumb_image = wp_get_attachment_image_src($srch_pos_thum_id, 'thumbnail');
                    $srch_pos_thumb_src = isset($srch_pos_thumb_image[0]) && esc_url($srch_pos_thumb_image[0]) != '' ? $srch_pos_thumb_image[0] : '';

                    $img_placeholder = jobsearch_no_image_placeholder();
                    if ($post_type == 'candidate') {
                        $img_placeholder = jobsearch_candidate_image_placeholder();
                    } else if ($post_type == 'employer') {
                        $img_placeholder = jobsearch_employer_image_placeholder();
                    }
                    $srch_pos_thumb_src = $srch_pos_thumb_src == '' ? $img_placeholder : $srch_pos_thumb_src;
                    $thi_html = '<div class="img-holder"><a href="' . (get_permalink($post_id)) . '"><img src="' . $srch_pos_thumb_src . '" alt=""></a></div>';
                    $thi_html .= '<div class="text-holder">';
                    $thi_html .= '<div class="post-title"><a href="' . (get_permalink($post_id)) . '">' . (get_the_title($post_id)) . '</a></div>';
                    if ($srch_posadres != '') {
                        $thi_html .= '<span class="post-adress"><i class="jobsearch-icon jobsearch-maps-and-flags"></i> ' . $srch_posadres . '</span>';
                    }
                    $thi_html .= '</div>';
                    $results_list[] = array('item' => $thi_html);
                }
                if ($total_posts > 5) {
                    $show_all_htmal = '<div class="show-all-results"><a href="javascript:void(0);">' . esc_html__('View All', 'wp-jobsearch') . '</a></div>';
                    $results_list[] = array('item_all' => $show_all_htmal);
                }
            }
        }

        echo json_encode($results_list);
        die();
    }

    add_action("wp_ajax_jobsearch_get_search_box_posts_results", "jobsearch_get_search_box_posts_results");
    add_action("wp_ajax_nopriv_jobsearch_get_search_box_posts_results", "jobsearch_get_search_box_posts_results");
}

function jobsearch_search_query_results_filter($where, \WP_Query $q) {
    global $wpdb;
    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
        $serch_keyword = $_REQUEST['keyword'];
    }
    if (isset($_REQUEST['search_title']) && $_REQUEST['search_title'] != '') {
        $serch_keyword = $_REQUEST['search_title'];
    }
    if (isset($serch_keyword) && $serch_keyword != '') {
        $get_ids_arr = array();
        $q_post_type = isset($q->query['post_type']) ? $q->query['post_type'] : '';
        if ($q_post_type == 'candidate') {
            $get_ids_arr = jobsearch_get_serchable_keywrd_candidate_ids($serch_keyword);
        } else if ($q_post_type == 'employer') {
            $get_ids_arr = jobsearch_get_serchable_keywrd_employer_ids($serch_keyword);
        } else if ($q_post_type == 'job') {
            $get_ids_arr = jobsearch_get_serchable_keywrd_job_ids($serch_keyword);
        }
        //
        if (!empty($get_ids_arr)) {
            $get_ids_implod = implode(',', $get_ids_arr);

            if (strpos($where, "AND {$wpdb->posts}.ID IN (") !== false) {
                $alredy_post_ids = isset($q->query['post__in']) ? $q->query['post__in'] : array();

                if (!empty($alredy_post_ids)) {

                    //
                    $alredy_post_idsimpld = implode(',', $alredy_post_ids);

                    $getsameres_ids_arr = array_intersect($get_ids_arr, $alredy_post_ids);
                    $get_ids_implod = !empty($getsameres_ids_arr) ? implode(',', $getsameres_ids_arr) : '0';
                    $where = str_replace("AND {$wpdb->posts}.ID IN ({$alredy_post_idsimpld}", "AND {$wpdb->posts}.ID IN ({$get_ids_implod}", $where);
                }
            } else {
                $where .= " AND ({$wpdb->posts}.ID IN ({$get_ids_implod})) ";
            }
        }
    }

    return $where;
}

function jobsearch_get_serchable_keywrd_job_ids($serch_keyword) {
    global $wpdb, $jobseacrh_jobsh_attslist;

    $all_post_ids = array();

    $employer_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type='employer' AND post_status='publish' AND post_title LIKE '%{$serch_keyword}%';");
    $employer_ids = array_unique($employer_ids);
    $employer_ids = apply_filters('jobsearch_ajaxsrchbox_job_empsrch_ids', $employer_ids, $serch_keyword);

    $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
    if (!empty($employer_ids)) {
        $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
        $post_ids_query .= " ON postmeta.post_id = posts.ID";
    }
    $post_ids_query .= " WHERE post_type='job' AND post_status='publish'";
    if (!empty($employer_ids)) {
        $employer_ids = implode(',', $employer_ids);
        $post_ids_query .= " AND ((postmeta.meta_key='jobsearch_field_job_posted_by' AND postmeta.meta_value IN ({$employer_ids})) OR (posts.post_title LIKE '%{$serch_keyword}%'));";
    } else {
        $post_ids_query .= " AND posts.post_title LIKE '" . apply_filters('jobsearch_ajaxsrchbox_job_title_kword', "%{$serch_keyword}%", $serch_keyword) . "';";
    }
    $post_ids = $wpdb->get_col($post_ids_query);
    $post_ids = apply_filters('jobsearch_ajaxsrchbox_job_mainpost_ids', $post_ids, $serch_keyword);
    if (!empty($post_ids)) {
        $post_ids = array_unique($post_ids);
        $all_post_ids = array_merge($all_post_ids, $post_ids);
    }

    //var_dump($post_ids);
    //die;
    // for skills
    $post_ids_query = "SELECT ID FROM $wpdb->posts";
    $post_ids_query .= " LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)";
    $post_ids_query .= " LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)";
    $post_ids_query .= " LEFT JOIN $wpdb->terms ON($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)";
    $post_ids_query .= " WHERE $wpdb->posts.post_type='job' AND $wpdb->posts.post_status='publish'";
    $post_ids_query .= " AND $wpdb->terms.name LIKE '%{$serch_keyword}%'";
    $post_ids_query .= " AND $wpdb->term_taxonomy.taxonomy = 'skill'";

    $skpost_ids = $wpdb->get_col($post_ids_query);
    $skpost_ids = apply_filters('jobsearch_ajaxsrchbox_job_skillsrch_ids', $skpost_ids, $serch_keyword);
    if (!empty($skpost_ids)) {
        $skpost_ids = array_unique($skpost_ids);
        $all_post_ids = array_merge($all_post_ids, $skpost_ids);
    }

    // for custom fields
    $field_db_slug = "jobsearch_custom_field_job";
    $custom_all_fields_saved_data = get_option($field_db_slug);
    if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
        $cus_fields_arr = array();
        foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {
            $cfield_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
            if ($cfield_name != '') {
                $cus_fields_arr[$cfield_name] = '';
            }
        }
        if (!empty($cus_fields_arr)) {
            $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
            $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
            $post_ids_query .= " ON postmeta.post_id = posts.ID";
            $post_ids_query .= " WHERE post_type='job' AND post_status='publish' AND (";
            $cus_countr = 1;
            foreach ($cus_fields_arr as $cus_fields_key => $cus_field_val) {
                if ($cus_countr == 1) {
                    $post_ids_query .= " (postmeta.meta_key='{$cus_fields_key}' AND postmeta.meta_value LIKE '%{$serch_keyword}%')";
                } else {
                    $post_ids_query .= " OR (postmeta.meta_key='{$cus_fields_key}' AND postmeta.meta_value LIKE '%{$serch_keyword}%')";
                }
                $cus_countr++;
            }
            $post_ids_query .= ")";

            $cuspost_ids = $wpdb->get_col($post_ids_query);
            $cuspost_ids = apply_filters('jobsearch_ajaxsrchbox_job_cfsrch_ids', $cuspost_ids, $serch_keyword);
            if (!empty($cuspost_ids)) {
                $cuspost_ids = array_unique($cuspost_ids);
                $all_post_ids = array_merge($all_post_ids, $cuspost_ids);
            }
        }
    }

    if (empty($all_post_ids)) {
        $all_post_ids = array(0);
    } else {
        $all_post_ids = array_unique($all_post_ids);
    }

    return $all_post_ids;
}

function jobsearch_get_serchable_keywrd_candidate_ids($serch_keyword) {
    global $wpdb;

    $all_post_ids = array();

    $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
    $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
    $post_ids_query .= " ON postmeta.post_id = posts.ID";
    $post_ids_query .= " WHERE post_type='candidate' AND post_status='publish'";
    $post_ids_query .= " AND ((posts.ID='{$serch_keyword}') OR (postmeta.meta_key='jobsearch_field_candidate_jobtitle' AND postmeta.meta_value LIKE '%{$serch_keyword}%') OR (posts.post_title LIKE '%{$serch_keyword}%') OR (posts.post_content LIKE '%{$serch_keyword}%'));";
    $post_ids_query = apply_filters('jobsearch_ajaxsrchbox_cand_titlesrch_query', $post_ids_query, $serch_keyword);

    $post_ids = $wpdb->get_col($post_ids_query);
    if (!empty($post_ids)) {
        $post_ids = array_unique($post_ids);
        $all_post_ids = array_merge($all_post_ids, $post_ids);
    }

    // for skills
    $post_ids_query = "SELECT ID FROM $wpdb->posts";
    $post_ids_query .= " LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)";
    $post_ids_query .= " LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)";
    $post_ids_query .= " LEFT JOIN $wpdb->terms ON($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)";
    $post_ids_query .= " WHERE $wpdb->posts.post_type='candidate' AND $wpdb->posts.post_status='publish'";
    $post_ids_query .= " AND $wpdb->terms.name LIKE '%{$serch_keyword}%'";
    $post_ids_query .= " AND $wpdb->term_taxonomy.taxonomy = 'skill'";

    $skpost_ids = $wpdb->get_col($post_ids_query);
    $skpost_ids = apply_filters('jobsearch_ajaxsrchbox_cand_skillsrch_ids', $skpost_ids, $serch_keyword);
    if (!empty($skpost_ids)) {
        $skpost_ids = array_unique($skpost_ids);
        $all_post_ids = array_merge($all_post_ids, $skpost_ids);
    }

    // for custom fields
    $field_db_slug = "jobsearch_custom_field_candidate";
    $custom_all_fields_saved_data = get_option($field_db_slug);
    if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
        $cus_fields_arr = array();
        foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {
            $cfield_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
            if ($cfield_name != '') {
                $cus_fields_arr[$cfield_name] = '';
            }
        }
        if (!empty($cus_fields_arr)) {
            $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
            $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
            $post_ids_query .= " ON postmeta.post_id = posts.ID";
            $post_ids_query .= " WHERE post_type='candidate' AND post_status='publish' AND (";
            $cus_countr = 1;
            foreach ($cus_fields_arr as $cus_fields_key => $cus_field_val) {
                if ($cus_countr == 1) {
                    $post_ids_query .= " (postmeta.meta_key='{$cus_fields_key}' AND postmeta.meta_value LIKE '%{$serch_keyword}%')";
                } else {
                    $post_ids_query .= " OR (postmeta.meta_key='{$cus_fields_key}' AND postmeta.meta_value LIKE '%{$serch_keyword}%')";
                }
                $cus_countr++;
            }
            $post_ids_query .= ")";

            $cuspost_ids = $wpdb->get_col($post_ids_query);
            $cuspost_ids = apply_filters('jobsearch_ajaxsrchbox_cand_cfsrch_ids', $cuspost_ids, $serch_keyword);
            if (!empty($cuspost_ids)) {
                $cuspost_ids = array_unique($cuspost_ids);
                $all_post_ids = array_merge($all_post_ids, $cuspost_ids);
            }
        }
    }

    if (empty($all_post_ids)) {
        $all_post_ids = array(0);
    } else {
        $all_post_ids = array_unique($all_post_ids);
    }

    return $all_post_ids;
}

function jobsearch_get_serchable_keywrd_employer_ids($serch_keyword) {
    global $wpdb;

    $all_post_ids = array();

    $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
    $post_ids_query .= " WHERE post_type='employer' AND post_status='publish'";
    $post_ids_query .= " AND posts.post_title LIKE '" . apply_filters('jobsearch_ajaxsrchbox_emp_title_kword', "%{$serch_keyword}%", $serch_keyword) . "';";
    $post_ids = $wpdb->get_col($post_ids_query);
    if (!empty($post_ids)) {
        $post_ids = array_unique($post_ids);
        $all_post_ids = array_merge($all_post_ids, $post_ids);
    }

    // for custom fields
    $field_db_slug = "jobsearch_custom_field_employer";
    $custom_all_fields_saved_data = get_option($field_db_slug);
    if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
        $cus_fields_arr = array();
        foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {
            $cfield_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
            if ($cfield_name != '') {
                $cus_fields_arr[$cfield_name] = '';
            }
        }
        if (!empty($cus_fields_arr)) {
            $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
            $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
            $post_ids_query .= " ON postmeta.post_id = posts.ID";
            $post_ids_query .= " WHERE post_type='employer' AND post_status='publish' AND (";
            $cus_countr = 1;
            foreach ($cus_fields_arr as $cus_fields_key => $cus_field_val) {
                if ($cus_countr == 1) {
                    $post_ids_query .= " (postmeta.meta_key='{$cus_fields_key}' AND postmeta.meta_value LIKE '%{$serch_keyword}%')";
                } else {
                    $post_ids_query .= " OR (postmeta.meta_key='{$cus_fields_key}' AND postmeta.meta_value LIKE '%{$serch_keyword}%')";
                }
                $cus_countr++;
            }
            $post_ids_query .= ")";

            $cuspost_ids = $wpdb->get_col($post_ids_query);
            $cuspost_ids = apply_filters('jobsearch_ajaxsrchbox_emp_cfsrch_ids', $cuspost_ids, $serch_keyword);
            if (!empty($cuspost_ids)) {
                $cuspost_ids = array_unique($cuspost_ids);
                $all_post_ids = array_merge($all_post_ids, $cuspost_ids);
            }
        }
    }

    if (empty($all_post_ids)) {
        $all_post_ids = array(0);
    } else {
        $all_post_ids = array_unique($all_post_ids);
    }

    return $all_post_ids;
}

if (!function_exists('jobsearch_job_item_address')) {

    function jobsearch_job_item_address($post_id) {
        $get_job_location = get_post_meta($post_id, 'jobsearch_field_location_address', true);

        $job_city_title = '';
        $get_job_city = get_post_meta($post_id, 'jobsearch_field_location_location3', true);
        if ($get_job_city == '') {
            $get_job_city = get_post_meta($post_id, 'jobsearch_field_location_location2', true);
        }
        if ($get_job_city != '') {
            $get_job_country = get_post_meta($post_id, 'jobsearch_field_location_location1', true);
        }

        $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
        if (is_object($job_city_tax)) {
            $job_city_title = isset($job_city_tax->name) ? $job_city_tax->name : '';

            $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
            if (is_object($job_country_tax)) {
                $job_city_title .= isset($job_country_tax->name) ? ', ' . $job_country_tax->name : '';
            }
        } else if ($job_city_title == '') {
            $get_job_country = get_post_meta($post_id, 'jobsearch_field_location_location1', true);
            $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
            if (is_object($job_country_tax)) {
                $job_city_title .= isset($job_country_tax->name) ? $job_country_tax->name : '';
            }
        }

        if ($job_city_title != '') {
            $get_job_location = $job_city_title;
        }
        return $get_job_location;
    }

}

function jobsearch_dependent_dropdown_showval_html($dropdown_field_options, $custom_field_saved_data, $dropdown_field_name_db_val, $parent_id = '0', $prefix = 'jobsearch', $dependent_drop_col) {

    global $sitepress;

    $lang_code = '';
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $lang_code = $sitepress->get_current_language();
    }

    $field_main_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
    $field__icon = isset($custom_field_saved_data['icon']) ? $custom_field_saved_data['icon'] : '';

    $field_main_label = apply_filters('wpml_translate_single_string', $field_main_label, 'Custom Fields', 'Dropdown Field Label - ' . $field_main_label, $lang_code);

    $icon_con_html = '';
    $no_icon_class = ' has-no-icon';
    if (isset($cus_field_icon_arr) && $cus_field_icon_arr <> '') {
        $no_icon_class = '';
        $icon_con_html = '<i class="' . ($cus_field_icon_arr) . '"></i>';
    }

    $drpdwns_dbval_arr = array();
    if ($dropdown_field_name_db_val != '') {
        $drpdwns_dbval_arr = explode('|', $dropdown_field_name_db_val);
    }
    if (isset($dropdown_field_options[$parent_id]['label']) && !empty($dropdown_field_options[$parent_id]['label']) && is_array($dropdown_field_options[$parent_id]['label']) && sizeof($dropdown_field_options[$parent_id]['label']) > 0) {

        $val_arr = '';
        $field_count = 0;
        foreach ($dropdown_field_options as $opt_field_key => $opt_field_list) {
            $field_group_label = isset($opt_field_list['group_label']) ? $opt_field_list['group_label'] : '';
            $field_labels = isset($opt_field_list['label']) ? $opt_field_list['label'] : '';
            $field_vals = isset($opt_field_list['value']) ? $opt_field_list['value'] : '';

            $field_group_label = apply_filters('wpml_translate_single_string', $field_group_label, 'Custom Fields', 'Dependent Dropdown Field Group Label - ' . $field_group_label, $lang_code);

            if (!empty($field_labels) && is_array($field_labels)) {
                $sin_field_count = 0;
                foreach ($field_labels as $sin_field_label) {
                    $sin_field_val = isset($field_vals[$sin_field_count]) ? $field_vals[$sin_field_count] : '';

                    $sin_field_label = apply_filters('wpml_translate_single_string', $sin_field_label, 'Custom Fields', 'Dependent Dropdown Field Label - ' . $sin_field_label, $lang_code);

                    if (!empty($drpdwns_dbval_arr) && in_array($sin_field_val, $drpdwns_dbval_arr)) {
                        $val_arr .= '<li class="' . $dependent_drop_col . '">';
                        $val_arr .= $icon_con_html;
                        $val_arr .= '<div class="' . $prefix . '-services-text' . $no_icon_class . '">';
                        if ($field_group_label != '') {
                            $val_arr .= $field_group_label;
                        } else if ($field_main_label != '') {
                            $val_arr .= $field_main_label;
                        }
                        $val_arr .= '<small>' . $sin_field_label . '</small>';
                        $val_arr .= '</div>';
                        $val_arr .= '</li>';
                    }

                    $sin_field_count++;
                }
            }
            $field_count++;
        }

        if ($val_arr != '') {
            $val_arr = '<ul class="jobsearch-row">' . $val_arr . '</ul>';
        }

        return $val_arr;
    }
}

function jobsearch_dependent_dropdown_list_html($dropdown_field_options, $field_counter, $custom_field_saved_data, $dropdown_field_name_db_val, $parent_id = '0', $field_place = '', $field_main_label = '') {
    global $sitepress;

    $lang_code = '';
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $lang_code = $sitepress->get_current_language();
    }

    $field_rand_num = rand(10000000, 99999999);
    $dropdown_field_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
    $dropdown_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
    $dropdown_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
    if ($dropdown_field_placeholder == '') {
        $dropdown_field_placeholder = esc_html__('Select Option', 'wp-jobsearch');
    }
    $dropdown_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';

    $drpdwns_dbval_arr = array();
    if ($dropdown_field_name_db_val != '') {
        $drpdwns_dbval_arr = explode('|', $dropdown_field_name_db_val);
    }

    if (isset($dropdown_field_options[$parent_id]['label']) && !empty($dropdown_field_options[$parent_id]['label']) && is_array($dropdown_field_options[$parent_id]['label']) && sizeof($dropdown_field_options[$parent_id]['label']) > 0) {
        ob_start();
        $to_parnt_ids = $opt_field_keyids = array();
        $field_count = 0;
        foreach ($dropdown_field_options as $opt_field_key => $opt_field_list) {
            $field_group_label = isset($opt_field_list['group_label']) ? $opt_field_list['group_label'] : '';
            $field_labels = isset($opt_field_list['label']) ? $opt_field_list['label'] : '';
            $field_vals = isset($opt_field_list['value']) ? $opt_field_list['value'] : '';
            $field_ids = isset($opt_field_list['id']) ? $opt_field_list['id'] : '';
            $field_par_ids = isset($opt_field_list['par_id']) ? $opt_field_list['par_id'] : '';

            $field_group_label = apply_filters('wpml_translate_single_string', $field_group_label, 'Custom Fields', 'Dependent Dropdown Field Group Label - ' . $field_group_label, $lang_code);

            $display_str = 'none';

            if (!empty($field_vals)) {
                $chfi_countr = 0;
                foreach ($field_vals as $chfield_val) {
                    $chfield_val = urldecode(sanitize_title($chfield_val));
                    if (!empty($drpdwns_dbval_arr) && in_array($chfield_val, $drpdwns_dbval_arr)) {
                        $chfi_parent_child = isset($field_ids[$chfi_countr]) ? $field_ids[$chfi_countr] : '';
                        $to_parnt_ids[] = $chfi_parent_child;
                    }
                    $chfi_countr++;
                }
            }
            if (!empty($to_parnt_ids) && in_array($opt_field_key, $to_parnt_ids)) {
                $display_str = 'inline-block';
            }

            if ($field_place == 'dashboard') {
                echo '
                <li ' . ($opt_field_key > 0 ? 'id="drp-field-' . $opt_field_key . '"' : '') . ' class="jobsearch-column-6" style="display: ' . ($opt_field_key > 0 ? $display_str : 'inline-block') . ';">';
                if ($opt_field_key == 0 && $field_main_label != '') {
                    echo '<label>' . $field_main_label . '</label>';
                }
                if ($field_group_label != '') {
                    echo '<label>' . $field_group_label . '</label>';
                }
                echo '
                <div class="jobsearch-profile-select">';
            } else {
                echo '
                <div ' . ($opt_field_key > 0 ? 'id="drp-field-' . $opt_field_key . '"' : '') . ' class="jobsearch-profile-select" style="display: ' . ($opt_field_key > 0 ? $display_str : 'inline-block') . ';">';
            }
            ?>
            <select class="selectize-select111 jobsearch-mdep-drpdwn jobsearch-mdep-drpdwn-<?php echo($field_rand_num) ?>">
                <?php
                if (!empty($field_labels) && is_array($field_labels)) {
                    $sin_field_count = 0;
                    if ($opt_field_key == 0) {
                        ?>
                        <option data-id="0" data-parid="0" value=""><?php echo($dropdown_field_placeholder) ?></option>
                        <?php
                    }
                    foreach ($field_labels as $sin_field_label) {
                        $sin_field_val = isset($field_vals[$sin_field_count]) ? urldecode(sanitize_title($field_vals[$sin_field_count])) : '';
                        $sin_field_id = isset($field_ids[$sin_field_count]) ? $field_ids[$sin_field_count] : '';
                        $sin_field_par_id = isset($field_par_ids[$sin_field_count]) ? $field_par_ids[$sin_field_count] : '';

                        $sin_field_label = apply_filters('wpml_translate_single_string', $sin_field_label, 'Custom Fields', 'Dependent Dropdown Field Label - ' . $sin_field_label, $lang_code);
                        ?>
                        <option data-id="<?php echo($sin_field_id) ?>"
                                data-parid="<?php echo($sin_field_par_id) ?>" <?php echo(!empty($drpdwns_dbval_arr) && in_array($sin_field_val, $drpdwns_dbval_arr) ? 'selected="selected"' : '') ?>
                                value="<?php echo($sin_field_val) ?>"><?php echo($sin_field_label); ?></option>
                                <?php
                                $sin_field_count++;
                            }
                        }
                        ?>
            </select>
            <?php
            if ($field_place == 'dashboard') {
                echo '
                </div>
                </li>';
            } else {
                echo '</div>';
            }
            $field_count++;

            if ($opt_field_key > 0) {
                $opt_field_keyids[] = $opt_field_key;
            }
        }

        //
        if ($field_place == 'dashboard') {
            echo '
            <li>';
        }
        ?>
        <input id="depdrpdwn_fields_val_<?php echo($field_rand_num) ?>" type="hidden"
               name="<?php echo($dropdown_field_name) ?>" value="<?php echo($dropdown_field_name_db_val) ?>">
        <script>
            function jobsearch_depdrpdwn_fields_acts_<?php echo($field_rand_num) ?>(this_main_opt_id) {
                var all_opts_list = jQuery('#drp-field-' + this_main_opt_id).find('select > option');
                jQuery(all_opts_list).each(function () {
                    var this__opt = jQuery(this);
                    var this__opt_id = this__opt.attr('data-id');
                    var this__opt_parid = this__opt.attr('data-parid');
                    if (jQuery('#drp-field-' + this__opt_id).length > 0) {
                        jQuery('#drp-field-' + this__opt_id).hide();
                    }
                    jobsearch_depdrpdwn_fields_acts_<?php echo($field_rand_num) ?>(this__opt_id);
                });
            }

            function jobsearch_depdrpdwn_fields_allval_<?php echo($field_rand_num) ?>(this_dpar_con, to_actuel_val, this_par_id) {
                var overall_opts_list = this_dpar_con.find('option');
                jQuery(overall_opts_list).each(function () {
                    var this__oopt = jQuery(this);
                    var this__oopt_id = this__oopt.attr('data-id');
                    var this__oopt_parid = this__oopt.attr('data-parid');
                    if (this__oopt_id == this_par_id) {
                        if (this__oopt_parid != '0') {
                            to_actuel_val += jobsearch_depdrpdwn_fields_allval_<?php echo($field_rand_num) ?>(this_dpar_con, to_actuel_val, this__oopt_parid);
                        }
                        to_actuel_val += this__oopt.val() + '|';
                    }
                });

                return to_actuel_val;
            }

            jQuery(document).on('change', '.jobsearch-mdep-drpdwn-<?php echo($field_rand_num) ?>', function () {
                var this_drpdwn = jQuery(this);
                var this_drpdwn_val = this_drpdwn.val();
                var allover_val = jQuery('#depdrpdwn_fields_val_<?php echo($field_rand_num) ?>');
                var this_dpar_con = this_drpdwn.parents('.jobsearch-depdrpdwn-fields');
                var this_id = this_drpdwn.find('option:selected').attr('data-id');

                var this_par_id = this_drpdwn.find('option:selected').attr('data-parid');
                if (jQuery('#drp-field-' + this_id).length > 0) {
                    var this_all_opts_list = jQuery('#drp-field-' + this_id).find('select > option');
                    var this_opts_counter = 1;
                    jQuery(this_all_opts_list).each(function () {
                        var this_w_opt = jQuery(this);
                        this_w_opt.removeAttr('selected');
                        if (this_opts_counter == 1) {
                            this_w_opt.attr('selected', 'selected');
                            this_w_opt.prop('selected', true);
                        }
                        this_opts_counter++;
                    });
                    jQuery('#drp-field-' + this_id).css({'display': 'inline-block'});
                }
                if (allover_val.val() == '') {
                    allover_val.val(this_drpdwn_val);
                } else {
                    var to_actuel_val = '';
                    if (this_par_id != '0') {
                        to_actuel_val += jobsearch_depdrpdwn_fields_allval_<?php echo($field_rand_num) ?>(this_dpar_con, to_actuel_val, this_par_id);
                    }
                    to_actuel_val += this_drpdwn_val;
                    allover_val.val(to_actuel_val);
                }

                //
                var all_opts_list = this_drpdwn.find('option');
                jQuery(all_opts_list).each(function () {
                    var this__opt = jQuery(this);
                    var this__opt_id = this__opt.attr('data-id');
                    var this__opt_parid = this__opt.attr('data-parid');
                    if (!this__opt.is(':selected')) {
                        if (jQuery('#drp-field-' + this__opt_id).length > 0) {
                            jQuery('#drp-field-' + this__opt_id).hide();
                        }
                        jobsearch_depdrpdwn_fields_acts_<?php echo($field_rand_num) ?>(this__opt_id);
                    }
                });
            });
        </script>
        <?php
        //
        if ($field_place == 'dashboard') {
            echo '
            </li>';
        }
        $html = ob_get_clean();

        return $html;
    }
}

if (!function_exists('jobsearch_terms_and_con_link_txt')) {

    function jobsearch_terms_and_con_link_txt($saved_val = '') {
        global $jobsearch_plugin_options;
        $terms_page = isset($jobsearch_plugin_options['terms_conditions_page']) ? $jobsearch_plugin_options['terms_conditions_page'] : '';
        $terms_page = jobsearch__get_post_id($terms_page, 'page');
        if ($terms_page != '') {
            $privcy_page_id = get_option('wp_page_for_privacy_policy');
            $terms_page_url = jobsearch_wpml_lang_page_permalink($terms_page, 'page');

            ob_start();
            if ($privcy_page_id != '') {
                ?>
                <div class="terms-priv-chek-con">
                    <p><input type="checkbox"
                              name="terms_cond_check" <?php echo($saved_val ? 'checked' : '') ?>> <?php echo wp_kses(sprintf(__('By clicking checkbox, you agree to our <a href="%s">Terms and Conditions</a> and <a href="%s">Privacy Policy</a>', 'wp-jobsearch'), $terms_page_url, get_permalink($privcy_page_id)), array('a' => array('href' => array(), 'target' => array(), 'title' => array()))) ?>
                    </p>
                </div>
                <?php
            } else {
                ?>
                <div class="terms-priv-chek-con">
                    <p><input type="checkbox"
                              name="terms_cond_check" <?php echo($saved_val ? 'checked' : '') ?>> <?php echo wp_kses(sprintf(__('By clicking checkbox, you agree to our <a href="%s">Terms and Conditions</a>', 'wp-jobsearch'), $terms_page_url), array('a' => array('href' => array(), 'target' => array(), 'title' => array()))) ?>
                    </p>
                </div>
                <?php
            }
            $terms_html = ob_get_clean();
            echo apply_filters('jobsearch_terms_nd_policy_txthtml', $terms_html, $terms_page_url, $privcy_page_id);
        }
    }

}

function jobsearch_get_page_by_slug($page_slug, $output = OBJECT, $post_type = 'page') {
    global $wpdb;
    $page = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s AND post_status = 'publish'", $page_slug, $post_type));
    if ($page) {
        return get_post($page, $output);
    }
    return null;
}

add_filter('jobsearch_after_registr_in_before_msg', 'jobsearch_userlogin_beforemsg_hook_callback', 10, 2);
add_filter('jobsearch_after_logged_in_before_msg', 'jobsearch_userlogin_beforemsg_hook_callback', 10, 2);

function jobsearch_userlogin_beforemsg_hook_callback($json, $args) {
    $user_obj = isset($args['login_user']) ? $args['login_user'] : '';
    $user_id = $user_obj->ID;
    $extra_params = isset($args['extra_params']) ? $args['extra_params'] : '';
    $wredirct_url = isset($args['wredirct_url']) ? $args['wredirct_url'] : '';
    //
    $user_is_candidate = jobsearch_user_is_candidate($user_id);
    $user_is_employer = jobsearch_user_is_employer($user_id);

    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
    } else {
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
    }

    $extra_params = explode('|', $extra_params);

    if (isset($extra_params[0]) && $extra_params[0] == 'buying_pkg') {
        $pkg_id = isset($extra_params[1]) ? $extra_params[1] : '';

        if ($pkg_id > 0) {

            $candidate_pkgs_typs = apply_filters('jobsearch_candidate_pkgs_typs_tochek', array('candidate'));
            $employer_pkgs_typs = apply_filters('jobsearch_employer_pkgs_typs_tochek', array('job', 'cv', 'feature_job'));

            $pkg_type = get_post_meta($pkg_id, 'jobsearch_field_package_type', true);
            $pkg_chrg_type = get_post_meta($pkg_id, 'jobsearch_field_charges_type', true);
            $pkg_price = get_post_meta($pkg_id, 'jobsearch_field_package_price', true);

            if ($pkg_chrg_type == 'paid' && $pkg_price > 0 && class_exists('WooCommerce')) {

                if (in_array($pkg_type, $employer_pkgs_typs) && $user_is_employer) {
                    ob_start();
                    do_action('jobsearch_woocommerce_payment_checkout', $pkg_id, 'checkout_url');
                    $tochkot_url = ob_get_clean();
                    echo json_encode(array('error' => false, 'redirect' => $tochkot_url, 'message' => '<div class="alert alert-success"><i class="fa fa-check"></i> ' . __('reloading page...', 'wp-jobsearch') . '</div>'));
                    die;
                }
                if (in_array($pkg_type, $candidate_pkgs_typs) && $user_is_candidate) {
                    ob_start();
                    do_action('jobsearch_woocommerce_payment_checkout', $pkg_id, 'checkout_url');
                    $tochkot_url = ob_get_clean();
                    echo json_encode(array('error' => false, 'redirect' => $tochkot_url, 'message' => '<div class="alert alert-success"><i class="fa fa-check"></i> ' . __('reloading page...', 'wp-jobsearch') . '</div>'));
                    die;
                }
            }
            //
        }
    }
    return $json;
}

function jobsearch_remove_url_extattr_list($remove_item_list = array()) {
    $qrystr = http_build_query($_REQUEST);
    if (!empty($remove_item_list)) {
        $remove_def_list = array(
            'job_arg',
            'action',
            'job_page',
        );
        $remove_item_list = array_merge($remove_def_list, $remove_item_list);
        foreach ($remove_item_list as $remove_item_list_single) {
            $qrystr = jobsearch_remove_qrystr_extra_var($qrystr, $remove_item_list_single, true);
        }
    }

    return $qrystr;
}

function jobsearch_self_assign_packge_to_user($user_id, $pckg_id) {
    $user_obj = get_user_by('ID', $user_id);

    if ($pckg_id > 0 && $user_id > 0 && isset($user_obj->ID)) {
        //
        $user_displayname = $user_obj->display_name;
        $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
        $user_bio = $user_obj->description;
        $user_website = $user_obj->user_url;
        $user_email = $user_obj->user_email;
        $user_fname = $user_obj->first_name;
        $user_lname = $user_obj->last_name;

        $first_name = $user_fname;
        $last_name = $user_lname;
        if ($user_fname == '' && $user_lname == '') {
            $first_name = $user_displayname;
            $last_name = '';
        }

        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);
        if ($user_is_employer) {
            $member_id = jobsearch_get_user_employer_id($user_id);
        } else {
            $member_id = jobsearch_get_user_candidate_id($user_id);
        }

        $user_phone = get_post_meta($member_id, 'jobsearch_field_user_phone', true);
        $user_address = get_post_meta($member_id, 'jobsearch_field_location_address', true);
        $user_city = get_post_meta($member_id, 'jobsearch_field_location_location3', true);
        $user_state = get_post_meta($member_id, 'jobsearch_field_location_location2', true);
        $user_country = get_post_meta($member_id, 'jobsearch_field_location_location1', true);

        $product_id = 0;
        $package_product = get_post_meta($pckg_id, 'jobsearch_package_product', true);
        $package_product_obj = $package_product != '' ? get_page_by_path($package_product, 'OBJECT', 'product') : '';
        if ($package_product != '' && is_object($package_product_obj)) {
            $product_id = $package_product_obj->ID;
        }

        if ($product_id > 0 && get_post_type($product_id) == 'product') {

            $address = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'company' => '',
                'email' => $user_email,
                'phone' => $user_phone,
                'address_1' => $user_address,
                'address_2' => '',
                'city' => $user_city,
                'state' => $user_state,
                'postcode' => '',
                'country' => $user_country
            );

            // pckge type
            $pckg_chrge_type = get_post_meta($pckg_id, 'jobsearch_field_charges_type', true);
            $pckg_main_type = get_post_meta($pckg_id, 'jobsearch_field_package_type', true);

            // Now we create the order
            $order = wc_create_order();

            $order->add_product(wc_get_product($product_id), 1);
            $order->set_address($address, 'billing');
            //
            $order->calculate_totals();
            $order_id = $order->get_ID();

            $order->update_status('processing');
            //
            update_post_meta($order_id, 'jobsearch_order_attach_with', 'package');
            update_post_meta($order_id, 'jobsearch_order_package', $pckg_id);
            update_post_meta($order_id, 'jobsearch_order_user', $user_id);
            //
            // For free package
            if ($pckg_chrge_type == 'free') {
                update_post_meta($order_id, 'jobsearch_order_transaction_type', 'free');
            }
            //
            $order->update_status('completed');

            return $order_id;
        }
        //
    }
    return false;
}

add_filter('wp_ajax_jobsearch_admin_assign_packge_to_user', 'jobsearch_admin_assign_packge_to_user');

function jobsearch_admin_assign_packge_to_user() {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $pckg_id = isset($_POST['pkg_id']) ? $_POST['pkg_id'] : '';

    $user_obj = get_user_by('ID', $user_id);

    $assing_pkg = jobsearch_self_assign_packge_to_user($user_id, $pckg_id);
    if ($assing_pkg) {
        echo json_encode(array('success' => '1', 'msg' => 'Package assign successfully.'));
        die;
    }

    echo json_encode(array('success' => '0', 'msg' => 'Package assign fail.'));
    die;
}

function jobsearch_get_order_product_name($order_id) {
    $trans_order_obj = wc_get_order($order_id);
    $trans_prod_items = $trans_order_obj->get_items();
    if (!empty($trans_prod_items)) {
        foreach ($trans_prod_items as $oitem_id => $oitem_product) {
            //Get the WC_Product object
            $oproduct = $oitem_product->get_product();

            if (is_object($oproduct)) {
                return get_the_title($oproduct->get_ID());
            }
        }
    }
}

// Menu functions
//add_action('careerfy_mega_menu_cus_items_before', 'jobsearch_mega_menu_cus_items_before', 10, 2);

function jobsearch_mega_menu_cus_items_before($item, $item_id) {
    ?>
    <p class="field-view description description-wide">
        <label for="edit-menu-item-visifor-<?php echo absint($item_id); ?>">
            <?php _e('Visible for', 'wp-jobsearch'); ?><br/>
            <select id="edit-menu-item-visifor-<?php echo absint($item_id); ?>" class="widefat edit-menu-item-visifor"
                    name="menu-item-visifor[<?php echo absint($item_id); ?>]">
                <option<?php echo esc_attr($item->visifor) == 'all' ? ' selected="selected"' : '' ?>
                    value="all"><?php _e('For All', 'wp-jobsearch'); ?></option>
                <option<?php echo esc_attr($item->visifor) == 'candidate' ? ' selected="selected"' : '' ?>
                    value="candidate"><?php _e('For Candidates', 'wp-jobsearch'); ?></option>
                <option<?php echo esc_attr($item->visifor) == 'employer' ? ' selected="selected"' : '' ?>
                    value="employer"><?php _e('For Employers', 'wp-jobsearch'); ?></option>
            </select>
        </label>
    </p>
    <?php
}

//add_filter('careerfy_mega_add_custom_nav_fields_filtr', 'jobsearch_mega_add_custom_nav_fields_filtr', 10, 1);

function jobsearch_mega_add_custom_nav_fields_filtr($menu_item) {
    $menu_item->visifor = get_post_meta($menu_item->ID, '_menu_item_visifor', true);
    return $menu_item;
}

//add_action('careerfy_mega_menu_items_save', 'jobsearch_mega_menu_items_save', 10, 1);

function jobsearch_mega_menu_items_save($menu_item_db_id) {
    if (isset($_REQUEST['menu-item-visifor'][$menu_item_db_id])) {
        $item_value = $_REQUEST['menu-item-visifor'][$menu_item_db_id];
    } else {
        $item_value = 'all';
    }

    update_post_meta($menu_item_db_id, '_menu_item_visifor', $item_value);
}

add_filter('walker_nav_menu_start_el', 'jobsearch_walker_nav_menu_start_el', 10, 5);

function jobsearch_walker_nav_menu_start_el($item_output, $item, $depth = 0, $args = array(), $id = '') {
    $item_id = isset($item->ID) ? $item->ID : '';
    $visifor = get_post_meta($item_id, '_menu_item_visifor', true);

    if ($visifor != '' && in_array($visifor, array('candidate', 'employer'))) {
        $view_item = false;

        if (is_user_logged_in()) {
            $cur_user_id = get_current_user_id();
            $cur_user_obj = wp_get_current_user();
            $employer_id = jobsearch_get_user_employer_id($cur_user_id);
            $candidate_id = jobsearch_get_user_candidate_id($cur_user_id);
            if ($candidate_id > 0 && !in_array('administrator', (array) $cur_user_obj->roles) && $visifor == 'candidate') {
                $view_item = true;
            } else if ($employer_id > 0 && !in_array('administrator', (array) $cur_user_obj->roles) && $visifor == 'employer') {
                $view_item = true;
            } else if (in_array('administrator', (array) $cur_user_obj->roles)) {
                $view_item = true;
            }
        }
        if (!$view_item) {
            $item_output = '';
        }
    }

    return $item_output;
}

function jobsearch_filter_keyword_str_replcewith($str) {
    $output_str = '';
    if ($str == 'sort-by') {
        
    }
    return $output_str;
}

if (!function_exists('jobsearch_job_listing_custom_fields_callback')) {

    function jobsearch_job_listing_custom_fields_callback($atts = array(), $job_id = '', $job_cus_field_arr = array(), $view = 'view-1') {
        $job_custom_fields_switch = isset($atts['job_custom_fields_switch']) ? $atts['job_custom_fields_switch'] : 'no';
        if ($job_custom_fields_switch == 'yes' && !empty($job_cus_field_arr)) {
            $cus_fields = array('content' => '');
            $cus_fields = apply_filters('jobsearch_custom_fields_list', 'job', $job_id, $cus_fields, '<li>', '</li>', '', true, true, true, 'jobsearch', $job_cus_field_arr);
            if (isset($cus_fields['content']) && $cus_fields['content'] != '') {
                if ($view == 'style9') {
                    echo ' <ul class="jobsearch-style9-custom-fields"> ' . force_balance_tags($cus_fields['content']) . ' </ul> ';
                } else {
                    echo ' <ul class="jobsearch-custom-field"> ' . force_balance_tags($cus_fields['content']) . ' </ul> ';
                }
            }
        }
    }

    add_action('jobsearch_job_listing_custom_fields', 'jobsearch_job_listing_custom_fields_callback', 10, 4);
}

if (!function_exists('jobsearch_job_listing_deadline_callback')) {

    function jobsearch_job_listing_deadline_callback($atts = array(), $job_id = '') {

        $job_deadline_date = get_post_meta($job_id, 'jobsearch_field_job_application_deadline_date', true);
        $job_deadline_switch = isset($atts['job_deadline_switch']) ? $atts['job_deadline_switch'] : '';
        $job_view = isset($atts['job_view']) ? $atts['job_view'] : '';

        $current_date = strtotime(current_time('d-m-Y H:i:s'));

        $dead_line_flag = false;
        if ($job_deadline_date != '' && ($job_deadline_date) <= $current_date) {
            $dead_line_flag = true;
        }

        if ($job_deadline_switch == 'Yes') {
            $jobsearch_last_date_formated = '';

            if (!empty($job_deadline_date)) {
                $jobsearch_last_date_formated = date_i18n(get_option('date_format'), ($job_deadline_date));

                if ($job_view == 'view-medium') {
                    if ($dead_line_flag) {
                        echo '<time class="deadline-closed" datetime="' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $current_date) . '">' . esc_html__('Application closed.', 'wp-jobsearch') . '</time>';
                    } else {
                        echo '<time datetime="' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $job_deadline_date) . '">' . esc_html__("Deadline ", "wp-jobsearch") . $jobsearch_last_date_formated . '</time>';
                    }
                } elseif ($job_view == 'view-medium2') {
                    if ($dead_line_flag) {
                        echo '<time class="deadline-closed" datetime="' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $current_date) . '">' . esc_html__('Application closed.', 'wp-jobsearch') . '</time>';
                    } else {
                        echo '<time datetime="' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $job_deadline_date) . '">' . esc_html__("Deadline ", "wp-jobsearch") . $jobsearch_last_date_formated . '</time>';
                    }
                } elseif ($job_view == 'view-grid2') {
                    if ($dead_line_flag) {
                        echo '<li class="deadline-closed"> ' . esc_html__('Application closed.', 'wp-jobsearch') . '</li>';
                    } else {
                        echo '<li>' . esc_html__("Deadline ", "wp-jobsearch") . '' . $jobsearch_last_date_formated . '</li>';
                    }
                } elseif ($job_view == 'view-grid') {
                    if ($dead_line_flag) {
                        echo '<li class="deadline-closed"> ' . esc_html__('Application closed.', 'wp-jobsearch') . '</li>';
                    } else {
                        echo '<li>' . esc_html__("Deadline ", "wp-jobsearch") . '' . $jobsearch_last_date_formated . '</li>';
                    }
                } elseif ($job_view == 'view-medium3') {
                    if ($dead_line_flag) {
                        echo '<small class="deadline-closed"><i class="jobsearch-icon jobsearch-calendar"></i> ' . esc_html__('Application closed.', 'wp-jobsearch') . '</small>';
                    } else {
                        echo ' <small><i class="jobsearch-icon jobsearch-calendar"></i> ' . esc_html__("Deadline ", "wp-jobsearch") . '' . $jobsearch_last_date_formated . '</small>';
                    }
                } else {

                    if ($job_deadline_date != '' && ($job_deadline_date) <= $current_date) {
                        echo '<li class="deadline-closed"><i class="jobsearch-icon jobsearch-calendar"></i> ' . esc_html__('Application closed.', 'wp-jobsearch') . '</li>';
                    } else {
                        echo '<li><i class="jobsearch-icon jobsearch-calendar"></i> ' . esc_html__("Deadline ", "wp-jobsearch") . '' . $jobsearch_last_date_formated . '</li>';
                    }
                }
            }
        }
    }

    add_action('jobsearch_job_listing_deadline', 'jobsearch_job_listing_deadline_callback', 10, 2);
}

function jobsearch_is_mobile() {
    static $is_mobile;

    if (isset($is_mobile))
        return $is_mobile;

    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        $is_mobile = false;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false) {
        $is_mobile = true;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false) {
        $is_mobile = true;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) {
        $is_mobile = false;
    } else {
        $is_mobile = false;
    }

    return $is_mobile;
}

add_action('admin_menu', 'jobsearch_notification_count_in_admin_menu');

function jobsearch_notification_count_in_admin_menu() {
    global $menu;
    /*
     * pending jobs query 
     */

    $jobs_pending_count = 0;
    $emp_pending_count = 0;
    $cand_pending_count = 0;

    $cachetime = 600;
    $transient = 'jobsearch_admin_menuconuts_cache';

    $check_transient = get_transient($transient);
    if (!empty($check_transient)) {
        $jobs_pending_count = get_option('jobsearch_admin_menu_pjobscount');
        $emp_pending_count = get_option('jobsearch_admin_menu_pempcount');
        $cand_pending_count = get_option('jobsearch_admin_menu_pcandcount');
    } else {
        $jobs_args = array(
            'post_type' => 'job',
            'posts_per_page' => '1',
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_job_status',
                    'value' => 'approved',
                    'compare' => '!=',
                ),
            ),
        );
        $jobs_query = new WP_Query($jobs_args);
        wp_reset_postdata();

        $jobs_pending_count = $jobs_query->found_posts;
        update_option('jobsearch_admin_menu_pjobscount', $jobs_pending_count);

        $emp_args = array(
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
        $emplo_query = new WP_Query($emp_args);
        wp_reset_postdata();

        $emp_pending_count = $emplo_query->found_posts;
        update_option('jobsearch_admin_menu_pempcount', $emp_pending_count);

        $cand_args = array(
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
        $cand_query = new WP_Query($cand_args);
        wp_reset_postdata();

        $cand_pending_count = $cand_query->found_posts;
        update_option('jobsearch_admin_menu_pcandcount', $cand_pending_count);

        //
        set_transient($transient, true, $cachetime);
    }
    $jobs_pending_status = $emp_pending_status = $cand_pending_status = '';
    if ($jobs_pending_count > 0) {
        $pending_title = esc_html__('Pending Job(s)', 'wp-jobsearch');
        $jobs_pending_status = '<span class="update-plugins"><span class="plugin-count" title="' . $pending_title . '">' . $jobs_pending_count . '</span></span>';
    }
    if ($emp_pending_count > 0) {
        $pending_title = esc_html__('Pending Employer(s)', 'wp-jobsearch');
        $emp_pending_status = '<span class="update-plugins"><span class="plugin-count" title="' . $pending_title . '">' . $emp_pending_count . '</span></span>';
    }
    if ($cand_pending_count > 0) {
        $pending_title = esc_html__('Pending Candidate(s)', 'wp-jobsearch');
        $cand_pending_status = '<span class="update-plugins"><span class="plugin-count" title="' . $pending_title . '">' . $cand_pending_count . '</span></span>';
    }

    if (isset($menu) && !empty($menu) && is_array($menu)) {
        foreach ($menu as $key => $parent_menu) {

            if ($parent_menu[2] == 'edit.php?post_type=job') {
                $menu[$key][0] = $menu[$key][0] . '' . $jobs_pending_status . '';
            }
            if ($parent_menu[2] == 'edit.php?post_type=candidate') {
                $menu[$key][0] = $menu[$key][0] . '' . $cand_pending_status . '';
            }
            if ($parent_menu[2] == 'edit.php?post_type=employer') {
                $menu[$key][0] = $menu[$key][0] . '' . $emp_pending_status . '';
            }
        }
    }
}

function jobsearch_locations_upload_dir($dir = '') {
    global $jobsearch_download_locations;

    if ($jobsearch_download_locations == true) {
        $cus_dir = 'jobsearch-locations';
        $dir_path = array(
            'path' => $dir['basedir'] . '/' . $cus_dir,
            'url' => $dir['baseurl'] . '/' . $cus_dir,
            'subdir' => $cus_dir,
        );
        return $dir_path + $dir;
    }
    return $dir;
}

function read_cities_file($filename) {
    global $wp_filesystem, $jobsearch_download_locations;
    $jobsearch_download_locations = true;
    add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
    $wp_upload_dir = wp_upload_dir();

    $upload_file_path = $wp_upload_dir['path'];
    if (!file_exists($upload_file_path . "/countries")) {
        $upload_file_path = $wp_upload_dir['basedir'] . '/jobsearch-locations';
    }
    $url = wp_nonce_url("options-general.php?page=demo", "filesystem-nonce");
    remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
    $jobsearch_download_locations = false;

    if (connect_fs($url, "", $upload_file_path)) {
        $dir = $wp_filesystem->find_folder($upload_file_path);
        $file = trailingslashit($dir) . $filename;
        if ($wp_filesystem->exists($file)) {
            $text = $wp_filesystem->get_contents($file);
            if (!$text) {
                return "";
            } else {
                return $text;
            }
        } else {
            return new WP_Error("filesystem_error", "File doesn't exist");
        }
    } else {
        return new WP_Error("filesystem_error", "Cannot initialize filesystem");
    }
}

function read_state_cities($filename) {
    global $wp_filesystem, $jobsearch_download_locations;
    $jobsearch_download_locations = true;
    add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
    $wp_upload_dir = wp_upload_dir();
    $upload_file_path = $wp_upload_dir['path'];
    if (!file_exists($upload_file_path . "/countries")) {
        $upload_file_path = $wp_upload_dir['basedir'] . '/jobsearch-locations';
    }
    $url = wp_nonce_url("options-general.php?page=demo", "filesystem-nonce");
    remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
    $jobsearch_download_locations = false;

    if (connect_fs($url, "", $upload_file_path)) {
        $dir = $wp_filesystem->find_folder($upload_file_path);
        $file = trailingslashit($dir) . $filename;

        if ($wp_filesystem->exists($file)) {
            $text = $wp_filesystem->get_contents($file);
            if (!$text) {
                return "";
            } else {
                return $text;
            }
        } else {
            return new WP_Error("filesystem_error", "File doesn't exist");
        }
    } else {
        return new WP_Error("filesystem_error", "Cannot initialize filesystem");
    }
}

function read_state_file($filename) {
    global $wp_filesystem, $jobsearch_download_locations;
    $jobsearch_download_locations = true;
    add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
    $wp_upload_dir = wp_upload_dir();
    $upload_file_path = $wp_upload_dir['path'];
    if (!file_exists($upload_file_path . "/countries")) {
        $upload_file_path = $wp_upload_dir['basedir'] . '/jobsearch-locations';
    }
    $url = wp_nonce_url("options-general.php?page=demo", "filesystem-nonce");
    remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
    $jobsearch_download_locations = false;

    if (connect_fs($url, "", $upload_file_path)) {
        $dir = $wp_filesystem->find_folder($upload_file_path);
        $file = trailingslashit($dir) . $filename;

        if ($wp_filesystem->exists($file)) {
            $text = $wp_filesystem->get_contents($file);
            if (!$text) {
                return "";
            } else {
                return $text;
            }
        } else {
            return new WP_Error("filesystem_error", "File doesn't exist");
        }
    } else {
        return new WP_Error("filesystem_error", "Cannot initialize filesystem");
    }
}

function read_location_file($filename) {
    global $wp_filesystem, $jobsearch_download_locations;
    $jobsearch_download_locations = true;
    add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
    $wp_upload_dir = wp_upload_dir();
    $upload_file_path = $wp_upload_dir['path'];
    if (!file_exists($upload_file_path . "/countries")) {
        $upload_file_path = $wp_upload_dir['basedir'] . '/jobsearch-locations';
    }
    $url = wp_nonce_url("options-general.php?page=demo", "filesystem-nonce");
    remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
    $jobsearch_download_locations = false;

    if (connect_fs($url, "", $upload_file_path)) {
        $dir = $wp_filesystem->find_folder($upload_file_path);
        $file = trailingslashit($dir) . $filename;

        if ($wp_filesystem->exists($file)) {
            $text = $wp_filesystem->get_contents($file);
            if (!$text) {
                return "";
            } else {
                return $text;
            }
        } else {
            return new WP_Error("filesystem_error", "File doesn't exist");
        }
    } else {
        return new WP_Error("filesystem_error", "Cannot initialize filesystem");
    }
}

function connect_fs($url, $method, $context, $fields = null) {
    global $wp_filesystem;

    require_once ABSPATH . '/wp-admin/includes/file.php';

    if (false === ($credentials = request_filesystem_credentials($url, $method, false, $context, $fields))) {
        return false;
    }

    //check if credentials are correct or not.
    if (!WP_Filesystem($credentials)) {
        request_filesystem_credentials($url, $method, true, $context);
        return false;
    }

    return true;
}

//Remove JQuery migrate

function jobsearch_remove_jquery_migrate($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            $script->deps = array_diff($script->deps, array('jquery-migrate'));
        }
    }
}

//add_action('wp_default_scripts', 'jobsearch_remove_jquery_migrate');

function cand_calculate_experience($candidate_id) {
    $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_title', true);
    $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_experience_description', true);
    $experience_start_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_start_date', true);
    $experience_end_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_end_date', true);
    $experience_prsnt_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_date_prsnt', true);
    $experience_company_field_list = get_post_meta($candidate_id, 'jobsearch_field_experience_company', true);
    //
    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
        ?>
        <?php
        $start_date_exp = '';
        $end_date_exp = '';
        $numItems = count($exfield_list);
        $counter = 1;
        foreach ($exfield_list as $exfield_counter => $exfield) {
            $experience_start_datefield_val = isset($experience_start_datefield_list[$exfield_counter]) ? $experience_start_datefield_list[$exfield_counter] : '';
            $experience_end_datefield_val = isset($experience_end_datefield_list[$exfield_counter]) ? $experience_end_datefield_list[$exfield_counter] : '';

            if ($exfield_counter == 0) {
                $start_date_exp = $experience_start_datefield_val;
            }
            if ($counter === $numItems) {
                $end_date_exp = $experience_end_datefield_val;
            }
            $counter++;
        }
        $datetime1 = new DateTime($start_date_exp);
        $datetime2 = new DateTime($end_date_exp);
        $interval = $datetime1->diff($datetime2);
        $year_text = $interval->y == 1 ? 'year' : 'years';
        return $interval->format('%y ' . $year_text . '');
    }
}

function jobsearch_candidate_id_elementor() {
    $cand_args = array(
        'post_type' => 'candidate',
        'posts_per_page' => '1',
        'post_status' => 'publish',
        'fields' => 'ids',
    );
    $cand_query = new WP_Query($cand_args);
    $cand_posts = $cand_query->posts;
    return count($cand_posts) > 0 ? $cand_posts[0] : '';
}

function jobsearch_employer_id_elementor() {
    $emp_args = array(
        'post_type' => 'employer',
        'posts_per_page' => '1',
        'post_status' => 'publish',
        'fields' => 'ids',
    );
    $emp_query = new WP_Query($emp_args);
    $emp_posts = $emp_query->posts;

    return count($emp_posts) > 0 ? $emp_posts[0] : '';
}

function jobsearch_job_id_elementor() {
    $emp_args = array(
        'post_type' => 'job',
        'posts_per_page' => '1',
        'post_status' => 'publish',
        'fields' => 'ids',
    );
    $emp_query = new WP_Query($emp_args);
    $emp_posts = $emp_query->posts;

    return count($emp_posts) > 0 ? $emp_posts[0] : '';
}
