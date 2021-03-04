<?php
/**
 * Define Meta boxes for plugin
 * and theme.
 *
 */
add_action('save_post', 'jobsearch_candidates_time_save');
function jobsearch_candidates_time_save($post_id)
{
    global $pagenow;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    $post_type = '';
    if ($pagenow == 'post.php') {
        $post_type = get_post_type();
    }
    if (isset($_POST)) {
        if ($post_type == 'candidate') {
            $candidate_user_id = get_post_meta($post_id, 'jobsearch_user_id', true);
            // extra save
            if (isset($_POST['jobsearch_field_user_cv_attachment']) && $_POST['jobsearch_field_user_cv_attachment'] != '') {
                $cv_file_url = $_POST['jobsearch_field_user_cv_attachment'];
                $cv_file_id = jobsearch_get_attachment_id_from_url($cv_file_url);
                if ($cv_file_id) {
                    $arg_arr = array(
                        'file_id' => $cv_file_id,
                        'file_url' => $cv_file_url,
                    );
                    update_post_meta($post_id, 'candidate_cv_file', $arg_arr);
                }
            } else {
                update_post_meta($post_id, 'candidate_cv_file', '');
            }

            // Cus Fields Upload Files /////
            do_action('jobsearch_custom_field_upload_files_save', $post_id, 'candidate');
            //

            do_action('jobsearch_cand_bk_meta_fields_save_after', $post_id);

            // urgent cand from bckend
            if (isset($_POST['cuscand_urgent_fbckend'])) {
                $cuscand_urgent_fbckend = $_POST['cuscand_urgent_fbckend'];
                if ($cuscand_urgent_fbckend == 'on') {
                    update_post_meta($post_id, '_urgent_cand_frmadmin', 'yes');
                } else if ($cuscand_urgent_fbckend == 'off') {
                    update_post_meta($post_id, '_urgent_cand_frmadmin', 'no');
                }
            }

            // feature cand from bckend
            if (isset($_POST['cuscand_feature_fbckend'])) {
                $promote_pckg_subtime = get_post_meta($post_id, 'promote_profile_substime', true);
                //
                $cuscand_feature_fbckend = $_POST['cuscand_feature_fbckend'];
                if ($cuscand_feature_fbckend == 'on') {
                    update_post_meta($post_id, '_feature_mber_frmadmin', 'yes');
                    if ($promote_pckg_subtime <= 0) {
                        update_post_meta($post_id, 'promote_profile_substime', current_time('timestamp'));
                    }
                } else if ($cuscand_feature_fbckend == 'off') {
                    update_post_meta($post_id, '_feature_mber_frmadmin', 'no');
                }
            }

            if (isset($_POST['prev_candidate_approved'])) {

                $post_on_status = $_POST['prev_candidate_approved'];

                update_post_meta($post_id, 'jobsearch_prev_candidate_approved', $post_on_status);
            }

            jobsearch_onuser_update_wc_update($candidate_user_id);
        }
    }
}

//if (class_exists('JobSearchMultiPostThumbnails')) {
//    new JobSearchMultiPostThumbnails(array(
//        'label' => 'Cover Image',
//        'id' => 'cover-image',
//        'post_type' => 'candidate',
//            )
//    );
//}

add_action('wp_ajax_jobsearch_cand_bkprofile_meta_delete_cover', 'jobsearch_cand_bkprofile_meta_delete_cover');

function jobsearch_cand_bkprofile_meta_delete_cover()
{

    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    if ($user_id > 0) {
        $candidate_id = jobsearch_get_user_candidate_id($user_id);

        jobsearch_remove_cand_photo_foldr($candidate_id, 'cover_img');

        echo json_encode(array('success' => '1'));
        wp_die();
    }
    echo json_encode(array('success' => '0'));
    wp_die();
}

add_action('wp_ajax_jobsearch_bkmeta_updating_cand_cover_img', 'jobsearch_bkmeta_updating_cand_cover_img');

function jobsearch_bkmeta_updating_cand_cover_img()
{
    $candidate_id = isset($_POST['cand_id']) ? $_POST['cand_id'] : '';
    if ($candidate_id > 0) {
        $user_id = jobsearch_get_candidate_user_id($candidate_id);
        $atach_urls = jobsearch_insert_candupload_attach('profile_img', $candidate_id, 'cover_img');
        if (!empty($atach_urls)) {

            jobsearch_remove_cand_photo_foldr($candidate_id, 'cover_img');

            $folder_path = $atach_urls['path'];
            $img_url = $atach_urls['orig'];

            $file_uniqid = jobsearch_get_unique_folder_byurl($img_url);

            $filename = basename($img_url);
            $filetype = wp_check_filetype($filename, null);
            $fileuplod_time = current_time('timestamp');

            $arg_arr = array(
                'file_name' => $filename,
                'mime_type' => $filetype,
                'time' => $fileuplod_time,
                'file_url' => $img_url,
                'file_path' => $folder_path,
                'file_id' => $file_uniqid,
            );
            update_post_meta($candidate_id, 'jobsearch_user_cover_imge', $arg_arr);

            $img_url = apply_filters('wp_jobsearch_cand_ccovr_img_url', $img_url, $candidate_id);

            echo json_encode(array('imgUrl' => $img_url, 'err_msg' => ''));
            die;
        }
    }
    echo json_encode(array('imgUrl' => '', 'err_msg' => ''));
    die;
}

add_action('wp_ajax_jobsearch_cand_bkprofile_avatar_delete_pthumb', 'jobsearch_cand_bkprofile_avatar_delete_pthumb');

function jobsearch_cand_bkprofile_avatar_delete_pthumb()
{

    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    if ($user_id > 0) {
        $candidate_id = jobsearch_get_user_candidate_id($user_id);

        //
        $def_img_url = get_avatar_url($user_id, array('size' => 132));
        $def_img_url = $def_img_url == '' ? jobsearch_candidate_image_placeholder() : $def_img_url;

        jobsearch_remove_cand_photo_foldr($candidate_id);

        echo json_encode(array('success' => '1', 'img_url' => $def_img_url));
        wp_die();
    }
    echo json_encode(array('success' => '0', 'img_url' => ''));
    wp_die();
}

add_action('wp_ajax_jobsearch_bkmeta_updating_cand_avatar_img', 'jobsearch_bkmeta_updating_cand_avatar_img');

function jobsearch_bkmeta_updating_cand_avatar_img()
{
    $candidate_id = isset($_POST['cand_id']) ? $_POST['cand_id'] : '';
    if ($candidate_id > 0) {
        $user_id = jobsearch_get_candidate_user_id($candidate_id);
        $file_urls = jobsearch_insert_candupload_attach('profile_img', $candidate_id);
        if (!empty($file_urls)) {

            do_action('jobsearch_aftr_user_uploaded_profile_pic', $file_urls, $user_id);

            jobsearch_remove_cand_photo_foldr($candidate_id);

            $folder_path = $file_urls['path'];
            $img_url = $file_urls['crop'];
            $orig_img_url = $file_urls['orig'];

            $file_uniqid = jobsearch_get_unique_folder_byurl($img_url);

            $filename = basename($orig_img_url);
            $filetype = wp_check_filetype($filename, null);
            $fileuplod_time = current_time('timestamp');

            $arg_arr = array(
                'file_name' => $filename,
                'mime_type' => $filetype,
                'time' => $fileuplod_time,
                'orig_file_url' => $orig_img_url,
                'file_url' => $img_url,
                'file_path' => $folder_path,
                'file_id' => $file_uniqid,
            );
            update_post_meta($candidate_id, 'jobsearch_user_avatar_url', $arg_arr);

            $img_url = apply_filters('wp_jobsearch_cand_profile_img_url', $img_url, $candidate_id, '150');

            echo json_encode(array('imgUrl' => $img_url, 'err_msg' => ''));
            die;
        }
    }
    echo json_encode(array('imgUrl' => '', 'err_msg' => ''));
    die;
}

add_action('add_meta_boxes', 'jobsearch_candidate_profilephoto_meta_box');

function jobsearch_candidate_profilephoto_meta_box()
{
    add_meta_box('jobsearch-cand-profilephoto', esc_html__('Profile Photo', 'wp-jobsearch'), 'jobsearch_candidate_bkmeta_profilephoto', 'candidate', 'side');
}

function jobsearch_candidate_bkmeta_profilephoto()
{
    global $post;
    $rand_num = rand(1000000, 99999999);
    $_post_id = $post->ID;
    
    $user_id = jobsearch_get_candidate_user_id($_post_id);
    $user_avatar_dburl = get_post_meta($_post_id, 'jobsearch_user_avatar_url', true);
    $user_def_avatar_url = '';
    $user_has_cimg = false;
    if (isset($user_avatar_dburl['file_url']) && $user_avatar_dburl['file_url'] != '') {
        $user_has_cimg = true;
    }
    $user_def_avatar_url = jobsearch_candidate_img_url_comn($_post_id);
    ob_start();
    ?>
    <div class="jobsearch-post-settings">
        <div class="jobsearch-bkimg-holdr">
            <a href="javascript:void(0);" class="user-bkdashthumb-remove"
               title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"
               data-uid="<?php echo($user_id) ?>" <?php echo($user_has_cimg ? '' : 'style="display: none;"') ?>><i
                        class="dashicons dashicons-no-alt"></i></a>
            <a id="candbk-profileimg-holder" href="javascript:void(0);" class="metabk-uplodr-thumb">
                <img src="<?php echo($user_def_avatar_url) ?>" alt="" style="max-width: 150px;">
            </a>
        </div>
        <div class="jobsearch-bkimg-uploadrcon">
            <input type="file" id="candidate_profile_img" name="candidate_profile_img" style="display: none;">
            <a href="javascript:void(0);" class="button button-primary jobsearch-candbk-uplodimgbtn"
               data-id="<?php echo($_post_id) ?>"><?php esc_html_e('Upload Profile Photo', 'wp-jobsearch') ?></a>
            <span class="file-img-uploadr"></span>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    echo apply_filters('jobsearch_cand_backend_profile_img', $html, $user_id, $user_has_cimg, $user_def_avatar_url);
}

add_action('add_meta_boxes', 'jobsearch_candidate_coverimg_meta_box');

function jobsearch_candidate_coverimg_meta_box()
{
    add_meta_box('jobsearch-cand-coverimge', esc_html__('Cover Image', 'wp-jobsearch'), 'jobsearch_candidate_bkmeta_coverimge', 'candidate', 'side');
}

function jobsearch_candidate_bkmeta_coverimge()
{
    global $post;
    $rand_num = rand(1000000, 99999999);
    $_post_id = $post->ID;
    
    $user_id = jobsearch_get_candidate_user_id($_post_id);
    $user_cover_img_url = '';
    if ($_post_id != '') {
        $user_cover_img_url = jobsearch_candidate_covr_url_comn($_post_id);
    }

    $candidate_cover_image_src_style_str = '';
    if ($user_cover_img_url != '') {
        $candidate_cover_image_src_style_str = ' style="background:url(\'' . ($user_cover_img_url) . '\'")"';
    }
    ?>
    <div class="jobsearch-post-settings">
        <div class="jobsearch-bkimg-holdr">
            <a href="javascript:void(0);" class="user-bkdashcover-remove"
               title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"
               data-uid="<?php echo($user_id) ?>" <?php echo($user_cover_img_url == '' ? 'style="display: none;"' : '') ?>><i
                        class="dashicons dashicons-no-alt"></i></a>
            <a id="candbk-covrimg-holder" href="javascript:void(0);" class="metabk-uplodr-cvr">
                <span<?php echo($candidate_cover_image_src_style_str) ?>></span>
            </a>
        </div>
        <div class="jobsearch-bkimg-uploadrcon">
            <input type="file" id="candidate_cover_img" name="candidate_cover_img" style="display: none;">
            <a href="javascript:void(0);" class="button button-primary jobsearch-candbk-uplodimgbtn"
               data-id="<?php echo($_post_id) ?>"><?php esc_html_e('Upload Cover Image', 'wp-jobsearch') ?></a>
            <span class="file-img-uploadr"></span>
        </div>
    </div>
    <?php
}

/**
 * Candidate settings meta box.
 */
function jobsearch_candidates_settings_meta_boxes()
{
    add_meta_box('jobsearch-candidates-settings', esc_html__('Candidate Settings', 'wp-jobsearch'), 'jobsearch_candidates_meta_settings', 'candidate', 'normal');
}

/**
 * Candidate settings meta box callback.
 */
function jobsearch_candidates_meta_settings()
{
    global $post, $pagenow, $jobsearch_form_fields, $jobsearch_plugin_options, $jobsearch_currencies_list;
    $rand_num = rand(1000000, 99999999);
    $_post_id = $post->ID;

    $job_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';

    $job_custom_currency_switch = isset($jobsearch_plugin_options['job_custom_currency']) ? $jobsearch_plugin_options['job_custom_currency'] : '';

    $is_candidate_approved = get_post_meta($_post_id, 'jobsearch_field_candidate_approved', true);
    $prev_candidate_approved = get_post_meta($_post_id, 'jobsearch_prev_candidate_approved', true);

    $candidate_posted_by = get_post_meta($_post_id, 'jobsearch_field_users', true);

    $candidate_user_id = get_post_meta($_post_id, 'jobsearch_user_id', true);

    //$candidasasdby = get_user_meta($candidate_user_id, 'att_profpckg_orderid', true);
    //var_dump($candidasasdby);

    $salar_cur_list = array('default' => esc_html__('Default', 'wp-jobsearch'));
    if (!empty($jobsearch_currencies_list)) {
        foreach ($jobsearch_currencies_list as $jobsearch_curr_key => $jobsearch_curr_item) {
            $cus_cur_name = isset($jobsearch_curr_item['name']) ? $jobsearch_curr_item['name'] : '';
            $cus_cur_symbol = isset($jobsearch_curr_item['symbol']) ? $jobsearch_curr_item['symbol'] : '';
            $salar_cur_list[$jobsearch_curr_key] = $cus_cur_name . ' - ' . $cus_cur_symbol;
        }
    }
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery('#jobsearch_candidate_publish_date').datetimepicker({
                timepicker: true,
                format: 'd-m-Y H:i:s'
            });
            jQuery('#jobsearch_candidate_expiry_date').datetimepicker({
                timepicker: true,
                format: 'd-m-Y H:i:s'
            });
        });
    </script>
    <div class="jobsearch-post-settings">
        <?php
        //
        do_action('jobsearch_candidate_meta_box_inbefore', $_post_id, $candidate_user_id);
        //
        if ($pagenow == 'post-new.php') {
            ?>
            <br>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('User Email', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <input type="email" name="user_reg_with_email" required="required">
                </div>
            </div>
            <br><br>
            <?php
        }

        $get_user_cand_id = get_user_meta($candidate_user_id, 'jobsearch_candidate_id', true);
        if ($get_user_cand_id != '' && $post->ID == $get_user_cand_id) {
            $user_obj = get_user_by('ID', $candidate_user_id);

            if (is_object($user_obj)) {
                ?>
                <br><br>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Attached User', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        echo '<strong>' . ($user_obj->user_login) . '</strong>';
                        //
                        $user_phone = get_post_meta($_post_id, 'jobsearch_field_user_phone', true);
                        echo '<p>' . sprintf(esc_html__('User email : %s', 'wp-jobsearch'), $user_obj->user_email) . '</p>';
                        if ($user_phone != '') {
                            echo '<p>' . sprintf(esc_html__('User Phone : %s', 'wp-jobsearch'), $user_phone) . '</p>';
                        }
                        ?>
                    </div>
                </div>
                <br><br>
                <?php
            }
        }
        if (class_exists('w357LoginAsUser')) {
            $w357LoginAsUser = new w357LoginAsUser;
            $user_obj = get_user_by('ID', $candidate_user_id);
            if (isset($user_obj->ID)) {
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Login as', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $the_user_obj = new WP_User($candidate_user_id);
                        $login_as_user_url = $w357LoginAsUser->build_the_login_as_user_url($the_user_obj);
                        $login_as_link = '<a class="button w357-login-as-user-btn" href="' . esc_url($login_as_user_url) . '" title="' . esc_html__('Login as', 'login-as-user') . ': ' . $w357LoginAsUser->login_as_type($the_user_obj, false) . '"><span class="dashicons dashicons-admin-users"></span> ' . esc_html__('Login as', 'login-as-user') . ': <strong>' . $w357LoginAsUser->login_as_type($the_user_obj) . '</strong></a>';
                        echo($login_as_link);
                        ?>
                    </div>
                </div>
                <br><br><br>
                <?php
            }
        }

        do_action('jobsearch_candidate_admin_meta_fields_before', $post->ID);
        $sdate_format = jobsearch_get_wp_date_simple_format();

        $days = array();
        for ($day = 1; $day <= 31; $day++) {
            $days[$day] = $day;
        }
        $months = array();
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = $month;
        }
        $years = array();
        for ($year = 1900; $year <= date('Y'); $year++) {
            $years[$year] = $year;
        }

        $cand_dob_switch = isset($jobsearch_plugin_options['cand_dob_switch']) ? $jobsearch_plugin_options['cand_dob_switch'] : 'on';
        if ($cand_dob_switch != 'off') { ?>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Date of Birth', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    ob_start();
                    ?>
                    <div style="float:left; margin-right: 4px; width: 80px;">
                        <?php
                        $field_params = array(
                            'std' => date('d'),
                            'name' => 'user_dob_dd',
                            'options' => $days,
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                    <?php
                    $dob_dd_html = ob_get_clean();
                    ob_start();
                    ?>
                    <div style="float:left; margin-right: 4px; width: 80px;">
                        <?php
                        $field_params = array(
                            'std' => date('m'),
                            'name' => 'user_dob_mm',
                            'options' => $months,
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                    <?php
                    $dob_mm_html = ob_get_clean();
                    ob_start();
                    ?>
                    <div style="float:left; margin-right: 4px; width: 80px;">
                        <?php
                        $field_params = array(
                            'std' => date('Y'),
                            'name' => 'user_dob_yy',
                            'options' => $years,
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                    <?php
                    $dob_yy_html = ob_get_clean();

                    if ($sdate_format == 'm-d-y') {
                        echo($dob_mm_html);
                        echo($dob_dd_html);
                        echo($dob_yy_html);
                    } else if ($sdate_format == 'y-m-d') {
                        echo($dob_yy_html);
                        echo($dob_mm_html);
                        echo($dob_dd_html);
                    } else {
                        echo($dob_dd_html);
                        echo($dob_mm_html);
                        echo($dob_yy_html);
                    }
                    ?>
                </div>
            </div>
        <?php } ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Phone', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'user_phone',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>

        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Urgent Candidate', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $cand_urgnt_val = get_post_meta($_post_id, 'cuscand_urgent_fbckend', true);

                if ($cand_urgnt_val != 'on' && $cand_urgnt_val != 'off') {
                    $urgnt_att_pckg = get_post_meta($_post_id, 'att_urgent_pkg_orderid', true);
                    if (!jobsearch_promote_profile_pkg_is_expired($urgnt_att_pckg)) {
                        $cand_urgnt_val = 'on';
                    }
                }

                $field_params = array(
                    'force_std' => $cand_urgnt_val,
                    'name' => 'cand_urgent',
                    'cus_name' => 'cuscand_urgent_fbckend',
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>
        <?php
        do_action('jobsearch_cand_postbk_meta_after_urgent_field', $_post_id);
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Featured Candidate', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $cand_feature_val = get_post_meta($_post_id, 'cuscand_feature_fbckend', true);

                if ($cand_feature_val != 'on' && $cand_feature_val != 'off') {
                    $feature_att_pckg = get_post_meta($_post_id, 'att_promote_profile_pkgorder', true);
                    if (!jobsearch_promote_profile_pkg_is_expired($feature_att_pckg)) {
                        $cand_feature_val = 'on';
                    }
                }

                $field_params = array(
                    'force_std' => $cand_feature_val,
                    'name' => 'cand_feature',
                    'cus_name' => 'cuscand_feature_fbckend',
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>

        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Approved', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'std' => 'on',
                    'name' => 'candidate_approved',
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Job Title', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'candidate_jobtitle',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <?php
        do_action('jobsearch_cand_postbk_meta_after_jobtitle_field', $_post_id);
        
        $salary_onoff_switch = isset($jobsearch_plugin_options['cand_salary_switch']) ? $jobsearch_plugin_options['cand_salary_switch'] : 'on';
        if ($salary_onoff_switch == 'on') {
            ?>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Salary', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'candidate_salary',
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <?php
            if (!empty($job_salary_types)) {
                $salar_types = array();
                $slar_type_count = 1;
                foreach ($job_salary_types as $job_salary_type) {
                    $salar_types['type_' . $slar_type_count] = $job_salary_type;
                    $slar_type_count++;
                }
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Salary Type', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'candidate_salary_type',
                            'options' => $salar_types,
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
            }
            if ($job_custom_currency_switch == 'on') {
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Salary Currency', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'candidate_salary_currency',
                            'options' => $salar_cur_list,
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Currency position', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'candidate_salary_pos',
                            'options' => array(
                                'left' => esc_html__('Left', 'wp-jobsearch'),
                                'right' => esc_html__('Right', 'wp-jobsearch'),
                                'left_space' => esc_html__('Left with space', 'wp-jobsearch'),
                                'right_space' => esc_html__('Right with space', 'wp-jobsearch'),
                            ),
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Thousand separator', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'std' => ',',
                            'name' => 'candidate_salary_sep',
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of decimals', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'std' => '2',
                            'name' => 'candidate_salary_deci',
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
            }
        }

        // load custom fields which is configured in candidate custom fields
        do_action('jobsearch_custom_fields_load', $post->ID, 'candidate');
        ?>
        <div class="jobsearch-elem-heading">
            <h2><?php esc_html_e('Social Links', 'wp-jobsearch') ?></h2>
        </div>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Facebook', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'user_facebook_url',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Twitter', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'user_twitter_url',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Linkedin', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'user_linkedin_url',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Dribbble', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'user_dribbble_url',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <?php
        $candidate_social_mlinks = isset($jobsearch_plugin_options['candidate_social_mlinks']) ? $jobsearch_plugin_options['candidate_social_mlinks'] : '';
        if (!empty($candidate_social_mlinks)) {
            if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
                $field_counter = 0;
                foreach ($candidate_social_mlinks['title'] as $field_title_val) {
                    $field_random = rand(10000000, 99999999);
                    $field_icon = isset($candidate_social_mlinks['icon'][$field_counter]) ? $candidate_social_mlinks['icon'][$field_counter] : '';
                    $field_icon_group = isset($candidate_social_mlinks['icon_group'][$field_counter]) ? $candidate_social_mlinks['icon_group'][$field_counter] : '';
                    if ($field_icon_group == '') {
                        $field_icon_group = 'default';
                    }
                    if ($field_title_val != '') { ?>
                        <div class="jobsearch-element-field">
                            <div class="elem-label">
                                <label><?php echo($field_title_val) ?></label>
                            </div>
                            <div class="elem-field">
                                <?php
                                $field_params = array(
                                    'name' => 'dynm_social' . $field_counter,
                                );
                                $jobsearch_form_fields->input_field($field_params);
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    $field_counter++;
                }
            }
        }

        //
        do_action('jobsearch_cand_admin_meta_after_social', $post->ID);

        ?>
        <input type="hidden" name="prev_candidate_approved" value="<?php echo($is_candidate_approved) ?>">
        <?php

        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

        if ($all_location_allow == 'on') {
            ?>
            <div class="jobsearch-elem-heading">
                <h2><?php esc_html_e('Location', 'wp-jobsearch') ?></h2>
            </div>
            <?php
        }
        do_action('jobsearch_admin_location_map', $post->ID);
        // candidate multi meta fields
        do_action('candidate_multi_fields_meta', $post);
        $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
        if ($multiple_cv_files_allow == 'on') {
            ?>
            <div class="jobsearch-elem-heading">
                <h2><?php esc_html_e('CV Files', 'wp-jobsearch') ?></h2>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-field">
                    <?php
                    $ca_at_cv_files = get_post_meta($_post_id, 'candidate_cv_files', true);
                    if (!empty($ca_at_cv_files)) {
                        ?>
                        <div class="cancom-cvfiles-holder">
                            <?php
                            $cv_files_count = count($ca_at_cv_files);
                            foreach ($ca_at_cv_files as $cv_file_key => $cv_file_val) {
                                $file_attach_id = isset($cv_file_val['file_id']) ? $cv_file_val['file_id'] : '';
                                $file_url = isset($cv_file_val['file_url']) ? $cv_file_val['file_url'] : '';
                                $filename = isset($cv_file_val['file_name']) ? $cv_file_val['file_name'] : '';
                                $filetype = isset($cv_file_val['mime_type']) ? $cv_file_val['mime_type'] : '';
                                if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment') {
                                    $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                                    $filetype = array('type' => $attach_mime);
                                }
                                $fileuplod_time = isset($cv_file_val['time']) ? $cv_file_val['time'] : '';
                                $file_attach_id = isset($cv_file_val['file_id']) ? $cv_file_val['file_id'] : '';
                                $cv_primary = isset($cv_file_val['primary']) ? $cv_file_val['primary'] : '';

                                $cv_file_title = $filename;

                                $attach_date = $fileuplod_time;
                                $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';

                                if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment') {
                                    $cv_file_title = get_the_title($file_attach_id);
                                    $attach_post = get_post($file_attach_id);
                                    $file_path = get_attached_file($file_attach_id);

                                    //
                                    $filename = basename($file_path);

                                    $attach_date = isset($attach_post->post_date) ? $attach_post->post_date : '';
                                    $attach_date = strtotime($attach_date);
                                    $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                                }

                                if ($attach_mime == 'application/pdf') {
                                    $attach_icon = 'fa fa-file-pdf-o';
                                } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                    $attach_icon = 'fa fa-file-word-o';
                                } else {
                                    $attach_icon = 'fa fa-file-word-o';
                                }

                                $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_attach_id, $_post_id);

                                if (!empty($filetype)) {
                                    ?>
                                    <div class="jobsearch-cv-manager-list">
                                        <ul class="jobsearch-row">
                                            <li class="jobsearch-column-12">
                                                <div class="jobsearch-cv-manager-wrap">
                                                    <a class="jobsearch-cv-manager-thumb"><i
                                                                class="<?php echo($attach_icon) ?>"></i></a>
                                                    <div class="jobsearch-cv-manager-text">
                                                        <div class="jobsearch-cv-manager-left">
                                                            <h2><a href="<?php echo($file_url) ?>"
                                                                   oncontextmenu="javascript: return false;"
                                                                   onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                                   download="<?php echo($filename) ?>"><?php echo(strlen($filename) > 40 ? substr($filename, 0, 40) . '...' : $filename) ?></a>
                                                            </h2>
                                                            <?php
                                                            if ($attach_date != '') {
                                                                ?>
                                                                <ul>
                                                                    <li>
                                                                        <i class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), ($attach_date)) . ' ' . date_i18n(get_option('time_format'), ($attach_date)) ?>
                                                                    </li>
                                                                </ul>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <a href="javascript:void(0);"
                                                           class="jobsearch-cv-manager-link jobsearch-del-user-cv"
                                                           data-id="<?php echo($file_attach_id) ?>"><i
                                                                    class="jobsearch-icon jobsearch-rubbish"></i></a>
                                                        <a href="<?php echo($file_url) ?>"
                                                           class="jobsearch-cv-manager-link jobsearch-cv-manager-download"
                                                           oncontextmenu="javascript: return false;"
                                                           onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                           download="<?php echo($filename) ?>"><i
                                                                    class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php
                    } else {
                        ?>
                        <p><?php esc_html_e('No File attached.', 'wp-jobsearch') ?></p>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('CV Attachment', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'user_cv_attachment' . rand(10000000, 999999999),
                        'name' => 'user_cv_attachment',
                    );
                    $jobsearch_form_fields->file_upload_field($field_params);
                    ?>
                </div>
            </div>
            <?php
        }
        echo apply_filters('jobsearch_cand_backend_meta_after_cv_field', '', $_post_id);
        //
        $security_questions = isset($jobsearch_plugin_options['jobsearch-security-questions']) ? $jobsearch_plugin_options['jobsearch-security-questions'] : '';
        if (!empty($security_questions) && sizeof($security_questions) >= 3) {

            $sec_questions = get_post_meta($post->ID, 'user_security_questions', true);
            if (!empty($sec_questions)) {
                ?>
                <div class="jobsearch-elem-heading"><h2><?php esc_html_e('Security Questions', 'wp-jobsearch') ?></h2>
                </div>
                <?php
                $answer_to_ques = isset($sec_questions['answers']) ? $sec_questions['answers'] : '';
                $qcount = 0;
                $qcount_num = 1;
                if (!empty($answer_to_ques)) {
                    foreach ($answer_to_ques as $sec_ans) {
                        $_ques = isset($sec_questions['questions'][$qcount]) ? $sec_questions['questions'][$qcount] : '';
                        $_answer_to_ques = $sec_ans;
                        ?>
                        <div class="jobsearch-element-field">
                            <div class="elem-label">
                                <label><?php printf(esc_html__('Question No %s :', 'wp-jobsearch'), $qcount_num) ?>
                                    <span><?php echo($_ques) ?></span></label>
                            </div>
                            <div class="elem-field">
                                <input type="hidden" name="user_security_questions[questions][]"
                                       value="<?php echo($_ques) ?>">
                                <input type="text" name="user_security_questions[answers][]" disabled="disabled"
                                       value="<?php echo($_answer_to_ques) ?>">
                            </div>
                        </div>
                        <?php
                        $qcount_num++;
                        $qcount++;
                    }
                }
            }
        }
        ?>
        <div class="jobsearch-elem-heading">
            <h2><?php esc_html_e('User Assign Packages', 'wp-jobsearch') ?></h2>
        </div>

        <?php
        $args = array(
            'post_type' => 'package',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'order' => 'ASC',
            'orderby' => 'title',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_package_type',
                    'value' => array('candidate', 'urgent_pkg', 'promote_profile', 'candidate_profile'),
                    'compare' => 'IN',
                ),
            ),
        );
        $pkgs_query = new WP_Query($args);

        if (!empty($pkgs_query->posts)) {
            ?>
            <div class="packge-asignbtn-holder">
                <label><?php esc_html_e('Select Package and assign to user:', 'wp-jobsearch') ?></label>
                <select id="jobsearch-assign-pck-slect" class="user_asign_pckg_drpdown">
                    <?php
                    $firts_pkg_id = 0;
                    $pck_countre = 1;
                    foreach ($pkgs_query->posts as $pkg_id) {
                        $pkg_rand = rand(10000000, 99999999);
                        $pkg_attach_product = get_post_meta($pkg_id, 'jobsearch_package_product', true);

                        if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                            //$pkg_id = get_the_ID();
                            if ($pck_countre == 1) {
                                $firts_pkg_id = $pkg_id;
                            }
                            ?>
                            <option value="<?php echo($pkg_id) ?>"><?php echo get_the_title($pkg_id) ?></option>
                            <?php
                            $pck_countre++;
                        }
                    }
                    wp_reset_postdata();
                    ?>
                </select>
                <a href="javascript:void(0);" data-uid="<?php echo($candidate_user_id) ?>"
                   data-id="<?php echo($firts_pkg_id) ?>"
                   class="button button-primary button-large admin-packge-asignbtn"><?php esc_html_e('Assign new package to this User', 'wp-jobsearch') ?></a>
                <span class="assign-loder"></span>
            </div>
            <script>
                jQuery(document).on('change', '#jobsearch-assign-pck-slect', function () {
                    jQuery('.admin-packge-asignbtn').attr('data-id', jQuery(this).val());
                });
                jQuery(document).on('click', '.admin-packge-asignbtn', function () {

                    var loader_con = jQuery(this).parent('.packge-asignbtn-holder').find('.assign-loder');

                    var pkg_id = jQuery(this).attr('data-id');
                    var user_id = jQuery(this).attr('data-uid');

                    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                    var request = $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        method: "POST",
                        data: {
                            'user_id': user_id,
                            'pkg_id': pkg_id,
                            'action': 'jobsearch_admin_assign_packge_to_user'
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        loader_con.html('');
                        if (typeof response.success !== 'undefined' && response.success == '1') {
                            loader_con.html(response.msg);
                        }
                    });

                    request.fail(function (jqXHR, textStatus) {
                        loader_con.html('');
                    });
                });
            </script>
            <?php
        }

        $args = array(
            'post_type' => 'shop_order',
            'posts_per_page' => -1,
            'post_status' => 'wc-completed',
            'order' => 'DESC',
            'fields' => 'ids',
            'orderby' => 'ID',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_order_attach_with',
                    'value' => 'package',
                    'compare' => '=',
                ),
                array(
                    'key' => 'package_type',
                    'value' => array('candidate', 'urgent_pkg', 'promote_profile', 'candidate_profile'),
                    'compare' => 'IN',
                ),
                array(
                    'key' => 'jobsearch_order_user',
                    'value' => $candidate_user_id,
                    'compare' => '=',
                ),
            ),
        );
        $pkgs_query = new WP_Query($args);

        if (!empty($pkgs_query->posts)) {
            ?>

            <div class="jobsearch-jobs-list-holder">
                <div class="jobsearch-managejobs-list">
                    <div class="jobsearch-table-layer jobsearch-managejobs-thead">
                        <div class="jobsearch-table-row">
                            <div class="jobsearch-table-cell"
                                 style="width: 20%;"><?php esc_html_e('Order ID', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Package', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Total Applications', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Used', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Remaining', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Package Expiry', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Status', 'wp-jobsearch') ?></div>
                        </div>
                    </div>
                    <?php
                    foreach ($pkgs_query->posts as $pkg_order_id) {
                        $pkg_rand = rand(10000000, 99999999);
                        //$pkg_order_id = get_the_ID();
                        $pkg_order_name = get_post_meta($pkg_order_id, 'package_name', true);

                        $unlimited_pkg = get_post_meta($pkg_order_id, 'unlimited_pkg', true);
                        //
                        $pkg_type = get_post_meta($pkg_order_id, 'package_type', true);

                        $total_apps = get_post_meta($pkg_order_id, 'num_of_apps', true);

                        $used_apps = jobsearch_pckg_order_used_apps($pkg_order_id);
                        $remaining_apps = jobsearch_pckg_order_remaining_apps($pkg_order_id);

                        $unlimited_numcapps = get_post_meta($pkg_order_id, 'unlimited_numcapps', true);
                        if ($unlimited_numcapps == 'yes') {
                            $total_apps = esc_html__('Unlimited', 'wp-jobsearch');
                            $used_apps = '-';
                            $remaining_apps = '-';
                        }

                        $pkg_exp_dur = get_post_meta($pkg_order_id, 'package_expiry_time', true);
                        $pkg_exp_dur_unit = get_post_meta($pkg_order_id, 'package_expiry_time_unit', true);

                        $status_txt = esc_html__('Active', 'wp-jobsearch');
                        $status_class = ' style="color: green;"';

                        if (jobsearch_app_pckg_order_is_expired($pkg_order_id)) {
                            $status_txt = esc_html__('Expired', 'wp-jobsearch');
                            $status_class = ' style="color: red;"';
                        }
                        if ($pkg_type == 'promote_profile') {
                            $status_txt = esc_html__('Active', 'wp-jobsearch');
                            $status_class = ' style="color: green;"';

                            if (jobsearch_promote_profile_pkg_is_expired($pkg_order_id)) {
                                $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                $status_class = ' style="color: red;"';
                            }
                        }
                        if ($pkg_type == 'urgent_pkg') {
                            $status_txt = esc_html__('Active', 'wp-jobsearch');
                            $status_class = ' style="color: green;"';

                            if (jobsearch_member_urgent_pkg_is_expired($pkg_order_id)) {
                                $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                $status_class = ' style="color: red;"';
                            }
                        }
                        if ($pkg_type == 'candidate_profile') {
                            $status_txt = esc_html__('Active', 'wp-jobsearch');
                            $status_class = ' style="color: green;"';

                            if (jobsearch_cand_profile_pkg_is_expired($pkg_order_id)) {
                                $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                $status_class = ' style="color: red;"';
                            }
                        }
                        ?>
                        <div class="jobsearch-table-layer jobsearch-packages-tbody">
                            <div class="jobsearch-table-row">
                                <div class="jobsearch-table-cell" style="width: 20%;">
                                    #<?php echo($pkg_order_id) ?></div>
                                <div class="jobsearch-table-cell"><span><?php echo($pkg_order_name) ?></span></div>

                                <div class="jobsearch-table-cell"><?php echo($total_apps) ?></div>
                                <div class="jobsearch-table-cell"><?php echo($used_apps) ?></div>
                                <div class="jobsearch-table-cell"><?php echo($remaining_apps) ?></div>

                                <?php
                                if ($unlimited_pkg == 'yes') {
                                    ?>
                                    <div class="jobsearch-table-cell"><?php esc_html_e('Never', 'wp-jobsearch'); ?></div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="jobsearch-table-cell"><?php echo absint($pkg_exp_dur) . ' ' . jobsearch_get_duration_unit_str($pkg_exp_dur_unit) ?></div>
                                    <?php
                                }
                                ?>
                                <div class="jobsearch-table-cell"<?php echo($status_class) ?>><?php echo($status_txt) ?></div>
                            </div>
                        </div>
                    <?php
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            <?php
        }
        ?>

    </div>
    <?php
    if ($candidate_user_id > 0 && $prev_candidate_approved != 'on' && $is_candidate_approved == 'on') {
        $user_obj = get_user_by('ID', $candidate_user_id);
        if (isset($user_obj->ID)) {
            //do_action('jobsearch_profile_approval_to_candidate', $user_obj);
        }
    }
    //
    do_action('jobsearch_user_data_save_onprofile', $candidate_user_id, $_post_id, 'candidate');
}
