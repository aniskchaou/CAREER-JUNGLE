<?php

use WP_Jobsearch\Package_Limits;

global $jobsearch_plugin_options, $diff_form_errs;

$user_pkg_limits = new Package_Limits;

$get_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$employer_id = jobsearch_get_user_employer_id($user_id);

$user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
$user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
$user_bio = isset($user_obj->description) ? $user_obj->description : '';
$user_website = isset($user_obj->user_url) ? $user_obj->user_url : '';
$user_email = isset($user_obj->user_email) ? $user_obj->user_email : '';
$user_firstname = isset($user_obj->first_name) ? $user_obj->first_name : '';
$user_lastname = isset($user_obj->last_name) ? $user_obj->last_name : '';

//
$user_dob_dd = get_post_meta($employer_id, 'jobsearch_field_user_dob_dd', true);
$user_dob_mm = get_post_meta($employer_id, 'jobsearch_field_user_dob_mm', true);
$user_dob_yy = get_post_meta($employer_id, 'jobsearch_field_user_dob_yy', true);

$user_dob_dd = jobsearch_esc_html($user_dob_dd);
$user_dob_mm = jobsearch_esc_html($user_dob_mm);
$user_dob_yy = jobsearch_esc_html($user_dob_yy);

$user_phone = get_post_meta($employer_id, 'jobsearch_field_user_phone', true);
$user_justphone = get_post_meta($employer_id, 'jobsearch_field_user_justphone', true);
$user_dial_code = get_post_meta($employer_id, 'jobsearch_field_user_dial_code', true);
$contry_iso_code = get_post_meta($employer_id, 'jobsearch_field_contry_iso_code', true);

$user_justphone = jobsearch_esc_html($user_justphone);
$user_dial_code = jobsearch_esc_html($user_dial_code);
$contry_iso_code = jobsearch_esc_html($contry_iso_code);
//

$emp_post_obj = get_post($employer_id);
$employer_content = isset($emp_post_obj->post_content) ? $emp_post_obj->post_content : '';
$employer_content = apply_filters('the_content', $employer_content);

$employer_content = jobsearch_esc_wp_editor($employer_content);

$user_profile_url = isset($emp_post_obj->post_name) ? $emp_post_obj->post_name : '';

$user_public_pview = isset($emp_post_obj->post_status) && $emp_post_obj->post_status == 'publish' ? 'yes' : 'no';

$user_facebook_url = get_post_meta($employer_id, 'jobsearch_field_user_facebook_url', true);
$user_facebook_url = esc_url($user_facebook_url);
$user_twitter_url = get_post_meta($employer_id, 'jobsearch_field_user_twitter_url', true);
$user_twitter_url = esc_url($user_twitter_url);
$user_google_plus_url = get_post_meta($employer_id, 'jobsearch_field_user_google_plus_url', true);
$user_youtube_url = get_post_meta($employer_id, 'jobsearch_field_user_youtube_url', true);
$user_youtube_url = esc_url($user_youtube_url);
$user_dribbble_url = get_post_meta($employer_id, 'jobsearch_field_user_dribbble_url', true);
$user_dribbble_url = esc_url($user_dribbble_url);
$user_linkedin_url = get_post_meta($employer_id, 'jobsearch_field_user_linkedin_url', true);
$user_linkedin_url = esc_url($user_linkedin_url);
//

$sectors = wp_get_post_terms($employer_id, 'sector');
$employer_sector = isset($sectors[0]->term_id) ? $sectors[0]->term_id : '';

$user_def_avatar_url = get_avatar_url($user_id, array('size' => 128));

$user_avatar_id = get_user_meta($user_id, 'jobsearch_user_avatar_id', true);

$emp_phone_switch = isset($jobsearch_plugin_options['employer_phone_field']) ? $jobsearch_plugin_options['employer_phone_field'] : '';
$emp_web_switch = isset($jobsearch_plugin_options['employer_web_field']) ? $jobsearch_plugin_options['employer_web_field'] : '';
$emp_foundate_switch = isset($jobsearch_plugin_options['employer_founded_date']) ? $jobsearch_plugin_options['employer_founded_date'] : '';

if ($user_avatar_id > 0) {
    $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
    $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
}
?>
<div class="jobsearch-typo-wrap">
    <form id="employer-profilesetings-form" class="jobsearch-employer-dasboard" method="post" action="<?php echo add_query_arg(array('tab' => 'dashboard-settings'), $page_url) ?>" enctype="multipart/form-data">
        <div class="jobsearch-employer-box-section">
            <?php
            ob_start();
            ?>
            <div class="jobsearch-profile-title"><h2><?php esc_html_e('Basic Information', 'wp-jobsearch') ?></h2></div>
            <?php
            $title_html = ob_get_clean();
            echo apply_filters('jobsearch_empdash_profile_tab_maintitle', $title_html);
            if (isset($_POST['user_settings_form']) && $_POST['user_settings_form'] == '1') {
                if (empty($diff_form_errs)) {
                    ?>
                    <div class="jobsearch-alert jobsearch-success-alert">
                        <p><?php echo wp_kses(__('<strong>Success!</strong> All changes updated.', 'wp-jobsearch'), array('strong' => array())) ?></p>
                    </div>
                    <?php
                } else if (isset($diff_form_errs['user_not_allow_mod']) && $diff_form_errs['user_not_allow_mod'] == true) {
                    ?>
                    <div class="jobsearch-alert jobsearch-error-alert">
                        <p><?php echo wp_kses(__('<strong>Error!</strong> You are not allowed to modify settings.', 'wp-jobsearch'), array('strong' => array())) ?></p>
                    </div>
                    <?php
                } else if (isset($diff_form_errs['user_email_error']) && $diff_form_errs['user_email_error'] != '') {
                    ?>
                    <div class="jobsearch-alert jobsearch-error-alert">
                        <p><?php echo ($diff_form_errs['user_email_error']) ?></p>
                    </div>
                    <?php
                }
            }

            //
            $cover_img_switch = isset($jobsearch_plugin_options['employer_cover_img_switch']) ? $jobsearch_plugin_options['employer_cover_img_switch'] : '';
            
            if ($cover_img_switch !== 'off') {
                $user_cover_img_url = '';
                if ($employer_id != '') {
                    if (class_exists('JobSearchMultiPostThumbnails')) {
                        $employer_cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url('employer', 'cover-image', $employer_id);
                        if ($employer_cover_image_src != '') {
                            $user_cover_img_url = $employer_cover_image_src;
                        }
                    }
                }
                $employer_cover_image_src_style_str = ' style="background:url(' . jobsearch_esc_html(esc_url($user_cover_img_url)) . ') no-repeat center/cover;"';

                if ($user_pkg_limits::emp_field_is_locked('profile_fields|jobs_cover_img')) {
                    $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                    echo ($lock_field_html);
                } else {
                    ob_start();
                    ?>
                    <div class="jobsearch-employer-cvr-img">

                        <figure>
                            <div class="img-cont-sec" style="display: <?php echo ($user_cover_img_url == '' ? 'none' : 'block') ?>;">
                                <a href="javascript:void(0);" class="employer-remove-coverimg"><i class="fa fa-times"></i> <?php esc_html_e('Delete Cover', 'wp-jobsearch') ?></a>    
                                <a id="com-cvrimg-holder" class="employer-dashboard-cvr">

                                    <span<?php echo ($employer_cover_image_src_style_str) ?>></span>
                                </a>
                            </div>
                            <figcaption>
                                <span class="file-loader"></span>
                                <div class="jobsearch-fileUpload">
                                    <span><i class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Upload Jobs Cover Photo', 'wp-jobsearch') ?></span>
                                    <input type="file" id="user_cvr_photo" name="user_cvr_photo" class="jobsearch-upload">
                                </div>
                            </figcaption>
                        </figure>
                    </div>
                    <?php
                    $cvr_img_html = ob_get_clean();
                    echo apply_filters('jobsearch_empdash_profile_tab_cvrimg_html', $cvr_img_html, $user_cover_img_url, $employer_cover_image_src_style_str);
                }
            }
            ?>
            <ul class="jobsearch-row jobsearch-employer-profile-form">
                <?php
                ob_start();
                ?>
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('First Name *', 'wp-jobsearch') ?></label>
                    <input type="text" name="u_firstname" value="<?php echo jobsearch_esc_html($user_firstname) ?>" required>
                </li>
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('Last Name *', 'wp-jobsearch') ?></label>
                    <input type="text" name="u_lastname" value="<?php echo jobsearch_esc_html($user_lastname) ?>" required>
                </li>
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('Company Name *', 'wp-jobsearch') ?></label>
                    <input type="text" name="display_name" value="<?php echo jobsearch_esc_html($user_displayname) ?>">
                </li>
                <?php
                $field_html = ob_get_clean();
                echo apply_filters('jobsearch_empdash_profile_tab_namefield_html', $field_html, $user_displayname);

                //
                ob_start();
                ?>
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('Email *', 'wp-jobsearch') ?> <span class="chk-loder"></span></label>
                    <input value="<?php echo jobsearch_esc_html($user_email) ?>" name="user_email_field" class="user-email-field" type="text">
                    <div class="email-chek-msg" style="display: none;"></div>
                </li>
                <?php
                $field_html = ob_get_clean();
                echo apply_filters('jobsearch_empdash_profile_tab_emailfield_html', $field_html, $employer_id);

                //
                $profile_url_switch = isset($jobsearch_plugin_options['emp_profile_url_switch']) ? $jobsearch_plugin_options['emp_profile_url_switch'] : '';
                if ($profile_url_switch == 'on') {
                    $employer_site_slug = isset($jobsearch_plugin_options['employer_rewrite_slug']) && $jobsearch_plugin_options['employer_rewrite_slug'] != '' ? $jobsearch_plugin_options['employer_rewrite_slug'] : 'employer';
                    ob_start();
                    ?>
                    <li class="jobsearch-column-12">
                        <label><?php esc_html_e('Profile URL', 'wp-jobsearch') ?></label>
                        <?php
                        if ($user_pkg_limits::emp_field_is_locked('profile_fields|profile_url')) {
                            $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                            echo ($lock_field_html);
                        } else {
                            ?>
                            <div class="jobsearch-userprofile-url">
                                <a href="<?php echo get_permalink($employer_id) ?>" target="_blank"><span><?php echo home_url('/' . $employer_site_slug . '/') ?></span><strong><?php echo urldecode($user_profile_url) ?></strong></a>
                                <input type="text" class="profile-slug-field" style="display: none;" name="user_profile_slug" value="<?php echo urldecode($user_profile_url) ?>">
                                <a href="javascript:void(0);" class="updte-profile-slugbtn"><?php esc_html_e('Edit', 'wp-jobsearch') ?></a>
                                <a href="javascript:void(0);" class="ok-profile-slugbtn" style="display: none;"><?php esc_html_e('Ok', 'wp-jobsearch') ?></a>
                                <span class="slugchng-loder"></span>
                            </div>
                            <?php
                        }
                        ?>
                    </li>
                    <?php
                    $profileurl_html = ob_get_clean();
                    echo apply_filters('jobsearch_emp_dash_profileurl_html', $profileurl_html, $employer_id);
                }

                //
                $public_pview_switch = isset($jobsearch_plugin_options['public_pview_switch']) ? $jobsearch_plugin_options['public_pview_switch'] : '';
                if ($public_pview_switch == 'on') {
                    ?>
                    <li class="jobsearch-column-6">
                        <label><?php esc_html_e('Profile for Public View', 'wp-jobsearch') ?></label>
                        <?php
                        if ($user_pkg_limits::emp_field_is_locked('profile_fields|public_view')) {
                            $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                            echo ($lock_field_html);
                        } else {
                            ?>
                            <div class="jobsearch-profile-select">
                                <select name="jobsearch_field_user_public_pview" class="selectize-select" placeholder="<?php _e('Visible in Listing and Detail', 'wp-jobsearch') ?>">
                                    <option <?php echo ($user_public_pview == 'yes' ? 'selected="selected"' : '') ?> value="yes"><?php esc_html_e('Yes', 'wp-jobsearch') ?></option>
                                    <option <?php echo ($user_public_pview == 'no' ? 'selected="selected"' : '') ?> value="no"><?php esc_html_e('No', 'wp-jobsearch') ?></option>
                                </select>
                            </div>
                            <?php
                        }
                        ?>
                    </li>
                    <?php
                }
                ob_start();
                if ($emp_phone_switch != 'off') {
                    $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';
                    ?>
                    <li class="jobsearch-column-6">
                        <label><?php esc_html_e('Phone', 'wp-jobsearch') ?><?php echo ($emp_phone_switch == 'on_req' ? ' *' : '') ?></label>
                        <?php
                        if ($user_pkg_limits::emp_field_is_locked('profile_fields|phone')) {
                            $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                            echo ($lock_field_html);
                        } else {
                            if ($phone_validation_type == 'on') {
                                wp_enqueue_script('jobsearch-intlTelInput');
                                $rand_numb = rand(100000000, 999999999);
                                //
                                $phone_intl_args = array();
                                if ($emp_phone_switch == 'on_req') {
                                    $phone_intl_args['is_required'] = true;
                                }
                                jobsearch_phonenum_itltell_input('user_phone', $rand_numb, $user_justphone, $phone_intl_args);
                                //
                            } else {
                                ?>
                                <input value="<?php echo jobsearch_esc_html($user_phone) ?>"<?php echo ($emp_phone_switch == 'on_req' ? ' required' : '') ?> onkeyup="javascript:jobsearch_is_valid_phone_number(this)" type="tel" name="user_phone">
                                <?php
                            }
                        }
                        ?>
                    </li>
                    <?php
                }
                $fieldphn_html = ob_get_clean();
                echo apply_filters('jobsearch_empdash_profile_tab_phonefield_html', $fieldphn_html, $employer_id);
                ob_start();
                if ($emp_web_switch == 'on') {
                    ?>
                    <li class="jobsearch-column-6">
                        <label><?php esc_html_e('Website', 'wp-jobsearch') ?></label>
                        <?php
                        if ($user_pkg_limits::emp_field_is_locked('profile_fields|website')) {
                            $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                            echo ($lock_field_html);
                        } else {
                            ?>
                            <input value="<?php echo jobsearch_esc_html($user_website) ?>" type="text" name="user_website">
                            <?php
                        }
                        ?>
                    </li>
                    <?php
                }
                $web_field_html = ob_get_clean();
                echo apply_filters('jobsearch_emp_dash_website_field_html', $web_field_html);

                ob_start();
                $sectors_enable_switch = isset($jobsearch_plugin_options['usersector_onoff_switch']) ? $jobsearch_plugin_options['usersector_onoff_switch'] : '';
                if ($sectors_enable_switch == 'on_emp' || $sectors_enable_switch == 'on_both') {
                    $sector_selct_method = isset($jobsearch_plugin_options['emp_sector_selct_method']) ? $jobsearch_plugin_options['emp_sector_selct_method'] : '';
                    if ($sector_selct_method == 'multi' || $sector_selct_method == 'multi_req') {
                        $selct_sector_title = esc_html__('Sector', 'wp-jobsearch');
                        $selct_sector_class = 'selectize-select';
                        if ($sector_selct_method == 'multi_req') {
                            $selct_sector_title = esc_html__('Sector *', 'wp-jobsearch');
                            $selct_sector_class = 'profile-req-field multiselect-req selectize-select';
                        }
                    } else {
                        $selct_sector_title = esc_html__('Sector', 'wp-jobsearch');
                        $selct_sector_class = 'selectize-select';
                        if ($sector_selct_method == 'single_req') {
                            $selct_sector_title = esc_html__('Sector *', 'wp-jobsearch');
                            $selct_sector_class = 'profile-req-field selectize-select';
                        }
                    }
                    ?>
                    <li class="jobsearch-column-6">
                        <label><?php echo ($selct_sector_title) ?></label>
                        <?php
                        if ($user_pkg_limits::emp_field_is_locked('profile_fields|sector')) {
                            $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                            echo ($lock_field_html);
                        } else {
                            ?>
                            <div class="jobsearch-profile-select">
                                <?php
                                if ($sector_selct_method == 'multi' || $sector_selct_method == 'multi_req') {
                                    $jobsector_args = array(
                                        'orderby' => 'name',
                                        'order' => 'ASC',
                                        'fields' => 'all',
                                        'slug' => '',
                                        'hide_empty' => false,
                                    );
                                    $all_sectors = get_terms('sector', $jobsector_args);

                                    $employer_sector = wp_get_post_terms($employer_id, 'sector');

                                    $saved_sectors = array();
                                    if (!empty($employer_sector)) {
                                        foreach ($employer_sector as $jobsector_obj) {
                                            $saved_sectors[] = $jobsector_obj->term_id;
                                        }
                                    }
                                    ob_start();
                                    if (!empty($all_sectors)) {
                                        ?>
                                        <select id="user-sector-multi" name="user_sector[]" class="<?php echo ($selct_sector_class) ?>" multiple="" placeholder="<?php esc_html_e('Select Sectors', 'wp-jobsearch') ?>">
                                            <?php
                                            foreach ($all_sectors as $sector_obj) {
                                                $term_id = $sector_obj->term_id;
                                                //var_dump($term_id);
                                                ?>
                                                <option value="<?php echo (string)($term_id) ?>" <?php echo (in_array($term_id, $saved_sectors) ? ' selected' : '') ?>><?php echo ($sector_obj->name) ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <?php
                                    }
                                    $sector_sel_html = ob_get_clean();
                                    echo apply_filters('jobsearch_emp_profile_sector_select', $sector_sel_html, $employer_id);
                                } else {
                                    $sector_args = array(
                                        'show_option_all' => esc_html__('Select Sector', 'wp-jobsearch'),
                                        'show_option_none' => '',
                                        'option_none_value' => '',
                                        'orderby' => 'title',
                                        'order' => 'ASC',
                                        'show_count' => 0,
                                        'hide_empty' => 0,
                                        'echo' => 0,
                                        'selected' => $employer_sector,
                                        'hierarchical' => 1,
                                        'id' => 'user-sector',
                                        'class' => $selct_sector_class,
                                        'name' => 'user_sector',
                                        'depth' => 0,
                                        'taxonomy' => 'sector',
                                        'hide_if_empty' => false,
                                        'value_field' => 'term_id',
                                    );
                                    $sector_sel_html = wp_dropdown_categories($sector_args);
                                    echo apply_filters('jobsearch_emp_profile_sector_select', $sector_sel_html, $employer_id);
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </li>
                    <?php
                }
                $sec_field_html = ob_get_clean();
                echo apply_filters('jobsearch_empdash_profile_sector_field_html', $sec_field_html, $employer_sector);

                //
                $sdate_format = jobsearch_get_wp_date_simple_format();
                ob_start();
                if ($emp_foundate_switch == 'on') {
                    ?>
                    <li class="jobsearch-column-6">
                        <label><?php esc_html_e('Founded Date', 'wp-jobsearch') ?></label>
                        <?php
                        if ($user_pkg_limits::emp_field_is_locked('profile_fields|founded_date')) {
                            $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                            echo ($lock_field_html);
                        } else {
                            ?>
                            <div class="jobsearch-three-column-row">
                                <?php
                                ob_start();
                                ?>
                                <div class="jobsearch-profile-select jobsearch-three-column">
                                    <select name="user_dob_dd" class="selectize-select" placeholder="<?php esc_html_e('Day', 'wp-jobsearch') ?>">
                                        <?php
                                        for ($dd = 1; $dd <= 31; $dd++) {
                                            $db_val = $user_dob_dd != '' ? $user_dob_dd : date('d');
                                            ?>
                                            <option <?php echo ($db_val == $dd ? 'selected="selected"' : '') ?> value="<?php echo ($dd) ?>"><?php echo ($dd) ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php
                                $dob_dd_html = ob_get_clean();
                                ob_start();
                                ?>
                                <div class="jobsearch-profile-select jobsearch-three-column">
                                    <select name="user_dob_mm" class="selectize-select" placeholder="<?php esc_html_e('Month', 'wp-jobsearch') ?>">
                                        <?php
                                        for ($mm = 1; $mm <= 12; $mm++) {
                                            $db_val = $user_dob_mm != '' ? $user_dob_mm : date('m');
                                            ?>
                                            <option <?php echo ($db_val == $mm ? 'selected="selected"' : '') ?> value="<?php echo ($mm) ?>"><?php echo ($mm) ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php
                                $dob_mm_html = ob_get_clean();
                                ob_start();
                                ?>
                                <div class="jobsearch-profile-select jobsearch-three-column">
                                    <select name="user_dob_yy" class="selectize-select" placeholder="<?php esc_html_e('Year', 'wp-jobsearch') ?>">
                                        <?php
                                        for ($yy = 1900; $yy <= date('Y'); $yy++) {
                                            $db_val = $user_dob_yy != '' ? $user_dob_yy : date('Y');
                                            ?>
                                            <option <?php echo ($db_val == $yy ? 'selected="selected"' : '') ?> value="<?php echo ($yy) ?>"><?php echo ($yy) ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php
                                $dob_yy_html = ob_get_clean();
                                //
                                if ($sdate_format == 'm-d-y') {
                                    echo ($dob_mm_html);
                                    echo ($dob_dd_html);
                                    echo ($dob_yy_html);
                                } else if ($sdate_format == 'y-m-d') {
                                    echo ($dob_yy_html);
                                    echo ($dob_mm_html);
                                    echo ($dob_dd_html);
                                } else {
                                    echo ($dob_dd_html);
                                    echo ($dob_mm_html);
                                    echo ($dob_yy_html);
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </li>
                    <?php
                }
                $found_date_html = ob_get_clean();
                echo apply_filters('jobsearch_emp_dash_found_date_html', $found_date_html);
                ?>
                <li class="jobsearch-column-12">
                    <?php
                    ob_start();
                    ?>
                    <label class="about-compny-label"><?php esc_html_e('About the Company', 'wp-jobsearch') ?></label>
                    <?php
                    $abtcmp_html = ob_get_clean();
                    echo apply_filters('jobsearch_empdash_abut_cmpny_ftitle_html', $abtcmp_html);

                    if ($user_pkg_limits::emp_field_is_locked('profile_fields|about_company')) {
                        $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                        echo ($lock_field_html);
                    } else {
                        $emp_desc_with_media = isset($jobsearch_plugin_options['emp_desc_with_media']) ? $jobsearch_plugin_options['emp_desc_with_media'] : '';
                        ob_start();
                        $settings = array(
                            'media_buttons' => ($emp_desc_with_media == 'on' ? true : false),
                            'quicktags' => array('buttons' => 'strong,em,del,ul,ol,li,close'),
                            'tinymce' => array(
                                'toolbar1' => 'wdm_mce_button,bold,bullist,numlist,italic,underline,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                                'toolbar2' => '',
                                'toolbar3' => '',
                            ),
                        );
                        wp_editor($employer_content, 'user_bio', $settings);
                        $empeditor_html = ob_get_clean();
                        echo apply_filters('jobsearch_empdash_abut_cmpny_editor_html', $empeditor_html, $employer_content);
                        do_action('jobsearch_empdash_after_abut_cmpny_editor', $employer_id, $employer_content);
                    }
                    ?>
                </li>
                <?php echo apply_filters('jobsearch_emp_dashbord_after_desc_content', '', $employer_id); ?>
            </ul>
        </div>
        <?php echo apply_filters('jobsearch_emp_dash_after_generl_info', '', $employer_id); ?>

        <?php do_action('jobsearch_dashboard_custom_fields_load', $employer_id, 'employer'); ?>

        <?php
        ob_start();

        $emp_alow_fb_smm = isset($jobsearch_plugin_options['emp_alow_fb_smm']) ? $jobsearch_plugin_options['emp_alow_fb_smm'] : '';
        $emp_alow_twt_smm = isset($jobsearch_plugin_options['emp_alow_twt_smm']) ? $jobsearch_plugin_options['emp_alow_twt_smm'] : '';
        $emp_alow_gplus_smm = isset($jobsearch_plugin_options['emp_alow_gplus_smm']) ? $jobsearch_plugin_options['emp_alow_gplus_smm'] : '';
        $emp_alow_linkd_smm = isset($jobsearch_plugin_options['emp_alow_linkd_smm']) ? $jobsearch_plugin_options['emp_alow_linkd_smm'] : '';
        $emp_alow_dribbb_smm = isset($jobsearch_plugin_options['emp_alow_dribbb_smm']) ? $jobsearch_plugin_options['emp_alow_dribbb_smm'] : '';
        $employer_social_mlinks = isset($jobsearch_plugin_options['employer_social_mlinks']) ? $jobsearch_plugin_options['employer_social_mlinks'] : '';

        $employer_social_mlinks_cond = false;
        if (!empty($employer_social_mlinks)) {
            foreach ($employer_social_mlinks['title'] as $field_title_val) {
                if ($field_title_val != '') {
                    $employer_social_mlinks_cond = true;
                }
                break;
            }
        }
        if (($employer_social_mlinks_cond) || ($emp_alow_fb_smm == 'on' || $emp_alow_twt_smm == 'on' || $emp_alow_gplus_smm == 'on' || $emp_alow_linkd_smm == 'on' || $emp_alow_dribbb_smm == 'on')) {
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title"><h2><?php esc_html_e('Social Links', 'wp-jobsearch') ?></h2></div>
                <ul class="jobsearch-row jobsearch-employer-profile-form">
                    <?php
                    if ($emp_alow_fb_smm == 'on') {
                        ?>
                        <li class="jobsearch-column-6">
                            <label><?php esc_html_e('Facebook', 'wp-jobsearch') ?></label>
                            <?php
                            if ($user_pkg_limits::emp_field_is_locked('social_links|facebook')) {
                                $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                                echo ($lock_field_html);
                            } else {
                                ?>
                                <input value="<?php echo jobsearch_esc_html($user_facebook_url) ?>" name="emp_user_facebook_url" type="text">
                                <?php
                            }
                            ?>
                        </li>
                        <?php
                    }
                    if ($emp_alow_twt_smm == 'on') {
                        ?>
                        <li class="jobsearch-column-6">
                            <label><?php esc_html_e('Twitter', 'wp-jobsearch') ?></label>
                            <?php
                            if ($user_pkg_limits::emp_field_is_locked('social_links|twitter')) {
                                $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                                echo ($lock_field_html);
                            } else {
                                ?>
                                <input value="<?php echo jobsearch_esc_html($user_twitter_url) ?>" name="emp_user_twitter_url" type="text">
                                <?php
                            }
                            ?>
                        </li>
                        <?php
                    }
                    if ($emp_alow_linkd_smm == 'on') {
                        ?>
                        <li class="jobsearch-column-6">
                            <label><?php esc_html_e('Linkedin', 'wp-jobsearch') ?></label>
                            <?php
                            if ($user_pkg_limits::emp_field_is_locked('social_links|linkedin')) {
                                $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                                echo ($lock_field_html);
                            } else {
                                ?>
                                <input value="<?php echo jobsearch_esc_html($user_linkedin_url) ?>" name="emp_user_linkedin_url" type="text">
                                <?php
                            }
                            ?>
                        </li>
                        <?php
                    }
                    if ($emp_alow_dribbb_smm == 'on') {
                        ?>
                        <li class="jobsearch-column-6">
                            <label><?php esc_html_e('Dribbble', 'wp-jobsearch') ?></label>
                            <?php
                            if ($user_pkg_limits::emp_field_is_locked('social_links|dribbble')) {
                                $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                                echo ($lock_field_html);
                            } else {
                                ?>
                                <input value="<?php echo jobsearch_esc_html($user_dribbble_url) ?>" name="emp_user_dribbble_url" type="text">
                                <?php
                            }
                            ?>
                        </li>
                        <?php
                    }
                    if (!empty($employer_social_mlinks)) {
                        if (isset($employer_social_mlinks['title']) && is_array($employer_social_mlinks['title'])) {
                            $field_counter = 0;
                            foreach ($employer_social_mlinks['title'] as $field_title_val) {
                                $field_random = rand(10000000, 99999999);
                                $field_icon = isset($employer_social_mlinks['icon'][$field_counter]) ? $employer_social_mlinks['icon'][$field_counter] : '';
                                $field_icon_group = isset($employer_social_mlinks['icon_group'][$field_counter]) ? $employer_social_mlinks['icon_group'][$field_counter] : '';
                                if ($field_icon_group == '') {
                                    $field_icon_group = 'default';
                                }
                                if ($field_title_val != '') {
                                    $emp_dynm_social = get_post_meta($employer_id, 'jobsearch_field_dynm_social' . $field_counter, true);
                                    ?>
                                    <li class="jobsearch-column-6">
                                        <label><?php echo ($field_title_val) ?></label>
                                        <?php
                                        if ($user_pkg_limits::emp_field_is_locked('social_links|dynm_social' . $field_counter)) {
                                            echo ($user_pkg_limits::emp_gen_locked_html());
                                        } else {
                                            ?>
                                            <input value="<?php echo jobsearch_esc_html($emp_dynm_social) ?>" name="employer_dynm_social<?php echo ($field_counter) ?>" type="text">
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                }
                                $field_counter++;
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
            <?php
        }
        $socilinks_html = ob_get_clean();
        echo apply_filters('jobsearch_empdash_profilesett_socilinks', $socilinks_html, $employer_id);

        // Location
        if ($user_pkg_limits::emp_field_is_locked('location_defields')) {
            ob_start();
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title"><h2><?php esc_html_e('Address / Location', 'wp-jobsearch') ?></h2></div>
                <?php echo ($user_pkg_limits::emp_gen_locked_html()) ?>
            </div>
            <?php
            $lock_field_cushtml = ob_get_clean();
            $lock_field_html = $user_pkg_limits->emp_field_locked_html($lock_field_cushtml);
            echo ($lock_field_html);
        } else {
            do_action('jobsearch_dashboard_location_map', $employer_id);
        }

        //
        $emp_account_membs = isset($jobsearch_plugin_options['emp_account_membs']) ? $jobsearch_plugin_options['emp_account_membs'] : '';

        if ($emp_account_membs == 'on') {

            $accmembs_is_locked = $user_pkg_limits::emp_field_is_locked('accmembs_defields');
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-candidate-resume-wrap">
                    <?php
                    if (!$accmembs_is_locked) {
                        $popup_args = array(
                            'employer_id' => $employer_id,
                            'employer_user_id' => $user_id,
                        );
                        add_action('wp_footer', function () use ($popup_args) {

                            global $jobsearch_plugin_options;

                            extract(shortcode_atts(array(
                                'employer_id' => '',
                                'employer_user_id' => '',
                                            ), $popup_args));
                            ?>
                            <div class="jobsearch-modal fade" id="JobSearchModalEmpAccMembAdd">
                                <div class="modal-inner-area">&nbsp;</div>
                                <div class="modal-content-area">
                                    <div class="modal-box-area">
                                        <div class="jobsearch-modal-title-box">
                                            <h2><?php esc_html_e('Add Account Member', 'wp-jobsearch') ?></h2>
                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                        </div>
                                        <div class="jobsearch-addempacount-membcon jobsearch-typo-wrap">
                                            <?php
                                            echo '<form id="addempmemb-account-form" method="post">';
                                            ?>
                                            <div class="jobsearch-user-form jobsearch-user-form-coltwo">
                                                <ul class="addempmemb-fields-list">
                                                    <li> 
                                                        <label><?php esc_html_e('Member First Name:', 'wp-jobsearch') ?></label>
                                                        <input class="required" name="u_firstname" type="text" placeholder="<?php esc_html_e('First Name', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <li> 
                                                        <label><?php esc_html_e('Member Last Name:', 'wp-jobsearch') ?></label>
                                                        <input class="required" name="u_lastname" type="text" placeholder="<?php esc_html_e('Last Name', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <li> 
                                                        <label><?php esc_html_e('Member Username:', 'wp-jobsearch') ?></label>
                                                        <input class="required" name="u_username" type="text" placeholder="<?php esc_html_e('Username', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <li> 
                                                        <label><?php esc_html_e('Member Email:', 'wp-jobsearch') ?></label>
                                                        <input class="required" name="u_emailadres" type="text" placeholder="<?php esc_html_e('Email Address', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <?php
                                                    echo apply_filters('jobsearch_addacc_member_form_aftr_email', '', $employer_id, $employer_user_id);
                                                    ?>
                                                    <li> 
                                                        <label><?php esc_html_e('Password:', 'wp-jobsearch') ?></label>
                                                        <input name="u_password" type="password" placeholder="<?php esc_html_e('Password', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <li> 
                                                        <label><?php esc_html_e('Confirm Password:', 'wp-jobsearch') ?></label>
                                                        <input class="required" name="u_confpass" type="password" placeholder="<?php esc_html_e('Confirm Password', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <li class="jobsearch-user-form-coltwo-full"> 
                                                        <div class="jobsearch-adingmem-permisons">
                                                            <h3><?php esc_html_e('Member Permissions', 'wp-jobsearch') ?></h3>
                                                            <?php
                                                            ob_start();
                                                            ?>
                                                            <ul>
                                                                <li> 
                                                                    <input id="u-post-job-btn" name="u_memb_perms[]" type="checkbox" value="u_post_job" checked="checked">
                                                                    <label for="u-post-job-btn"><?php esc_html_e('Post New Job', 'wp-jobsearch') ?></label>
                                                                </li>
                                                                <li> 
                                                                    <input id="u-mange-jobs-btn" name="u_memb_perms[]" type="checkbox" value="u_manage_jobs" checked="checked">
                                                                    <label for="u-mange-jobs-btn"><?php esc_html_e('Manage Jobs', 'wp-jobsearch') ?></label>
                                                                </li>
                                                                <li> 
                                                                    <input id="u-saved-cands-btn" name="u_memb_perms[]" type="checkbox" value="u_saved_cands" checked="checked">
                                                                    <label for="u-saved-cands-btn"><?php esc_html_e('Saved Candidates', 'wp-jobsearch') ?></label>
                                                                </li>
                                                                <li> 
                                                                    <input id="u-pkgs-perms-btn" name="u_memb_perms[]" type="checkbox" value="u_packages" checked="checked">
                                                                    <label for="u-pkgs-perms-btn"><?php esc_html_e('Packages', 'wp-jobsearch') ?></label>
                                                                </li>
                                                                <li> 
                                                                    <input id="u-trans-perms-btn" name="u_memb_perms[]" type="checkbox" value="u_transactions" checked="checked">
                                                                    <label for="u-trans-perms-btn"><?php esc_html_e('Transactions', 'wp-jobsearch') ?></label>
                                                                </li>
                                                            </ul>
                                                            <?php
                                                            $perms_html = ob_get_clean();
                                                            echo apply_filters('jobsearch_emp_dash_empmembs_perms_html', $perms_html, $employer_id);
                                                            ?>
                                                        </div>
                                                    </li>
                                                    <li class="jobsearch-user-form-coltwo-full">
                                                        <input type="hidden" name="action" value="jobsearch_employer_ading_member_account">
                                                        <input class="jobsearch-empmember-add-btn" type="submit" value="<?php esc_html_e('Add Member', 'wp-jobsearch') ?>">
                                                        <div class="form-loader"></div>
                                                    </li>
                                                </ul>
                                                <div class="form-msg"></div>
                                            </div>
                                            <?php
                                            echo '</form>';
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }, 11, 1);
                    }
                    ?>
                    <div class="jobsearch-candidate-title"> 
                        <h2>
                            <?php
                            esc_html_e('Account Members', 'wp-jobsearch');

                            if (!$accmembs_is_locked) {
                                ?>
                                <a href="javascript:void(0)" class="jobsearch-empmember-add-popup"><span class="fa fa-plus"></span> <?php esc_html_e('Add Account Member', 'wp-jobsearch') ?> </a>
                                <?php
                            }
                            ?>
                        </h2>
                    </div>
                    <?php
                    if ($accmembs_is_locked) {
                        $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                        echo ($lock_field_html);
                    } else {
                        ?>
                        <div class="empacc-menbers-list">
                            <?php
                            $emp_accmembers = get_post_meta($employer_id, 'emp_acount_member_acounts', true);
                            
                            if (!empty($emp_accmembers)) {
                                ?>
                                <ul class="accmem-head">
                                    <li><?php esc_html_e('Account Member', 'wp-jobsearch') ?></li>
                                    <li><?php esc_html_e('Actions', 'wp-jobsearch') ?></li>
                                </ul>
                                <?php
                                foreach ($emp_accmembers as $emp_accmemb_uid) {

                                    $get_acuser_obj = get_user_by('ID', $emp_accmemb_uid);
                                    //
                                    $att_user_pperms = get_user_meta($emp_accmemb_uid, 'jobsearch_attchprof_perms', true);

                                    if (isset($get_acuser_obj->display_name)) {
                                        ?>
                                        <ul class="accmem-head">
                                            <li><?php echo ($get_acuser_obj->display_name) ?></li>
                                            <li>
                                                <a href="javascript:void(0);" class="emp-memb-updatebtn" data-id="<?php echo ($emp_accmemb_uid) ?>"><i class="jobsearch-icon jobsearch-edit"></i></a>
                                                <a href="javascript:void(0);" class="emp-memb-removebtn" data-id="<?php echo ($emp_accmemb_uid) ?>"><i class="jobsearch-icon jobsearch-rubbish"></i></a>
                                            </li>
                                        </ul>
                                        <?php
                                        $popup_args = array(
                                            'employer_id' => $employer_id,
                                            'employer_user_id' => $user_id,
                                            'memb_acc_uid' => $emp_accmemb_uid,
                                        );
                                        add_action('wp_footer', function () use ($popup_args) {

                                            global $jobsearch_plugin_options;

                                            extract(shortcode_atts(array(
                                                'employer_id' => '',
                                                'employer_user_id' => '',
                                                'memb_acc_uid' => '',
                                                            ), $popup_args));

                                            $get_acuser_obj = get_user_by('ID', $memb_acc_uid);
                                            $att_user_pperms = get_user_meta($memb_acc_uid, 'jobsearch_attchprof_perms', true);
                                            ?>
                                            <div class="jobsearch-modal fade" id="JobSearchModalEmpAccMembUpdate<?php echo ($memb_acc_uid) ?>">
                                                <div class="modal-inner-area">&nbsp;</div>
                                                <div class="modal-content-area">
                                                    <div class="modal-box-area">
                                                        <div class="jobsearch-modal-title-box">
                                                            <h2><?php esc_html_e('Update Account Member', 'wp-jobsearch') ?></h2>
                                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                                        </div>
                                                        <div class="jobsearch-addempacount-membcon jobsearch-typo-wrap">
                                                            <?php
                                                            echo '<form id="editempmemb-account-form-' . ($memb_acc_uid) . '" method="post">';
                                                            ?>
                                                            <div class="jobsearch-user-form jobsearch-user-form-coltwo">
                                                                <ul class="addempmemb-fields-list">
                                                                    <li> 
                                                                        <label><?php esc_html_e('Member First Name:', 'wp-jobsearch') ?></label>
                                                                        <input class="required" name="u_firstname" type="text" placeholder="<?php esc_html_e('First Name', 'wp-jobsearch') ?>" value="<?php echo ($get_acuser_obj->first_name) ?>">
                                                                    </li>
                                                                    <li> 
                                                                        <label><?php esc_html_e('Member Last Name:', 'wp-jobsearch') ?></label>
                                                                        <input class="required" name="u_lastname" type="text" placeholder="<?php esc_html_e('Last Name', 'wp-jobsearch') ?>" value="<?php echo ($get_acuser_obj->last_name) ?>">
                                                                    </li>
                                                                    <li> 
                                                                        <label><?php esc_html_e('Member Username:', 'wp-jobsearch') ?></label>
                                                                        <input class="required" type="text" readonly="readonly" value="<?php echo ($get_acuser_obj->user_login) ?>">
                                                                    </li>
                                                                    <li> 
                                                                        <label><?php esc_html_e('Member Email:', 'wp-jobsearch') ?></label>
                                                                        <input class="required" type="text" readonly="readonly" value="<?php echo ($get_acuser_obj->user_email) ?>">
                                                                    </li>
                                                                    <?php
                                                                    echo apply_filters('jobsearch_updtacc_member_form_aftr_email', '', $employer_id, $employer_user_id, $memb_acc_uid);
                                                                    ?>
                                                                    <li class="jobsearch-user-form-coltwo-full"> 
                                                                        <div class="jobsearch-adingmem-permisons">
                                                                            <h3><?php esc_html_e('Member Permissions', 'wp-jobsearch') ?></h3>
                                                                            <?php
                                                                            ob_start();
                                                                            ?>
                                                                            <ul>
                                                                                <li>
                                                                                    <input id="u-post-job-btn-<?php echo ($memb_acc_uid) ?>" name="u_memb_perms[]" type="checkbox" value="u_post_job" <?php echo (!empty($att_user_pperms) && in_array('u_post_job', $att_user_pperms) ? 'checked="checked"' : '') ?>>
                                                                                    <label for="u-post-job-btn-<?php echo ($memb_acc_uid) ?>"><?php esc_html_e('Post New Job', 'wp-jobsearch') ?></label>
                                                                                </li>
                                                                                <li>
                                                                                    <input id="u-mange-jobs-btn-<?php echo ($memb_acc_uid) ?>" name="u_memb_perms[]" type="checkbox" value="u_manage_jobs" <?php echo (!empty($att_user_pperms) && in_array('u_manage_jobs', $att_user_pperms) ? 'checked="checked"' : '') ?>>
                                                                                    <label for="u-mange-jobs-btn-<?php echo ($memb_acc_uid) ?>"><?php esc_html_e('Manage Jobs', 'wp-jobsearch') ?></label>
                                                                                </li>
                                                                                <li>
                                                                                    <input id="u-saved-cands-btn-<?php echo ($memb_acc_uid) ?>" name="u_memb_perms[]" type="checkbox" value="u_saved_cands" <?php echo (!empty($att_user_pperms) && in_array('u_saved_cands', $att_user_pperms) ? 'checked="checked"' : '') ?>>
                                                                                    <label for="u-saved-cands-btn-<?php echo ($memb_acc_uid) ?>"><?php esc_html_e('Saved Candidates', 'wp-jobsearch') ?></label>
                                                                                </li>
                                                                                <li>
                                                                                    <input id="u-pkgs-perms-btn-<?php echo ($memb_acc_uid) ?>" name="u_memb_perms[]" type="checkbox" value="u_packages" <?php echo (!empty($att_user_pperms) && in_array('u_packages', $att_user_pperms) ? 'checked="checked"' : '') ?>>
                                                                                    <label for="u-pkgs-perms-btn-<?php echo ($memb_acc_uid) ?>"><?php esc_html_e('Packages', 'wp-jobsearch') ?></label>
                                                                                </li>
                                                                                <li>
                                                                                    <input id="u-trans-perms-btn-<?php echo ($memb_acc_uid) ?>" name="u_memb_perms[]" type="checkbox" value="u_transactions" <?php echo (!empty($att_user_pperms) && in_array('u_transactions', $att_user_pperms) ? 'checked="checked"' : '') ?>>
                                                                                    <label for="u-trans-perms-btn-<?php echo ($memb_acc_uid) ?>"><?php esc_html_e('Transactions', 'wp-jobsearch') ?></label>
                                                                                </li>
                                                                            </ul>
                                                                            <?php
                                                                            $perms_html = ob_get_clean();
                                                                            echo apply_filters('jobsearch_emp_dash_empmembs_updt_perms_html', $perms_html, $memb_acc_uid, $att_user_pperms);
                                                                            ?>
                                                                        </div>
                                                                    </li>
                                                                    <li class="jobsearch-user-form-coltwo-full">
                                                                        <input type="hidden" name="action" value="jobsearch_employer_update_member_account">
                                                                        <input type="hidden" name="member_uid" value="<?php echo ($memb_acc_uid) ?>">
                                                                        <input class="jobsearch-empmember-updte-btn" data-id="<?php echo ($memb_acc_uid) ?>" type="submit" value="<?php esc_html_e('Update Member', 'wp-jobsearch') ?>">
                                                                        <div class="form-loader"></div>
                                                                    </li>
                                                                </ul>
                                                                <div class="form-msg"></div>
                                                            </div>
                                                            <?php
                                                            echo '</form>';
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }, 11, 1);
                                    }
                                }
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        $_allow_team_add = isset($jobsearch_plugin_options['allow_team_members']) ? $jobsearch_plugin_options['allow_team_members'] : '';
        if ($_allow_team_add == 'on') {

            $empteam_is_locked = $user_pkg_limits::emp_field_is_locked('team_defields');
            ob_start();
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-candidate-resume-wrap">    
                    <div class="jobsearch-candidate-title"> 
                        <h2>
                            <i class="jobsearch-icon jobsearch-group"></i> <?php esc_html_e('Team Members', 'wp-jobsearch') ?>
                            <?php
                            if (!$empteam_is_locked) {
                                ?>
                                <a href="javascript:void(0)" class="jobsearch-resume-addbtn jobsearch-portfolio-add-btn"><span class="fa fa-plus"></span> <?php esc_html_e('Add Team Member', 'wp-jobsearch') ?> </a>
                                <?php
                            }
                            ?>
                        </h2> 
                    </div>
                    <?php
                    if ($empteam_is_locked) {
                        $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                        echo ($lock_field_html);
                    } else {
                        ?>
                        <div class="jobsearch-add-popup jobsearch-add-resume-item-popup">
                            <span class="close-popup-item"><i class="fa fa-times"></i></span>
                            <ul class="jobsearch-row jobsearch-employer-profile-form">
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Member Title *', 'wp-jobsearch') ?></label>
                                    <input id="team_title" class="jobsearch-req-field" type="text">
                                </li>
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Designation *', 'wp-jobsearch') ?></label>
                                    <input id="team_designation" class="jobsearch-req-field" type="text">
                                </li>
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Experience *', 'wp-jobsearch') ?></label>
                                    <input id="team_experience" class="jobsearch-req-field" type="text">
                                </li>
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Image *', 'wp-jobsearch') ?></label>
                                    <div class="upload-img-holder-sec">
                                        <span class="file-loader"></span>
                                        <img src="" alt="">
                                        <input name="team_image" type="file" style="display: none;">
                                        <input type="hidden" id="team_image_input" class="jobsearch-req-field">
                                        <a href="javascript:void(0)" class="upload-port-img-btn"><i class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Upload Photo', 'wp-jobsearch') ?></a>
                                    </div>
                                </li>
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Facebook URL', 'wp-jobsearch') ?></label>
                                    <input id="team_facebook" type="text">
                                </li>
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Google+ URL', 'wp-jobsearch') ?></label>
                                    <input id="team_google" type="text">
                                </li>
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Twitter URL', 'wp-jobsearch') ?></label>
                                    <input id="team_twitter" type="text">
                                </li>
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('LinkedIn URL', 'wp-jobsearch') ?></label>
                                    <input id="team_linkedin" type="text">
                                </li>
                                <li class="jobsearch-column-12">
                                    <label><?php esc_html_e('Description', 'wp-jobsearch') ?></label>
                                    <textarea id="team_description"></textarea>
                                </li>
                                <li class="jobsearch-column-12">
                                    <input type="submit" id="add-team-member-btn" value="<?php esc_html_e('Add Team Member', 'wp-jobsearch') ?>">
                                    <span class="portfolio-loding-msg edu-loding-msg"></span>
                                </li>
                            </ul>
                        </div>

                        <div id="jobsearch-team-members-con" class="jobsearch-company-gallery">
                            <ul class="jobsearch-row jobsearch-team-list-con">
                                <?php
                                $exfield_list = get_post_meta($employer_id, 'jobsearch_field_team_title', true);
                                $exfield_list_val = get_post_meta($employer_id, 'jobsearch_field_team_description', true);
                                $team_designationfield_list = get_post_meta($employer_id, 'jobsearch_field_team_designation', true);
                                $team_experiencefield_list = get_post_meta($employer_id, 'jobsearch_field_team_experience', true);
                                $team_imagefield_list = get_post_meta($employer_id, 'jobsearch_field_team_image', true);
                                $team_facebookfield_list = get_post_meta($employer_id, 'jobsearch_field_team_facebook', true);
                                $team_googlefield_list = get_post_meta($employer_id, 'jobsearch_field_team_google', true);
                                $team_twitterfield_list = get_post_meta($employer_id, 'jobsearch_field_team_twitter', true);
                                $team_linkedinfield_list = get_post_meta($employer_id, 'jobsearch_field_team_linkedin', true);
                                if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

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
                                        
                                        $exfield = jobsearch_esc_html($exfield);
                                        $exfield_val = jobsearch_esc_html($exfield_val);
                                        $team_designationfield_val = jobsearch_esc_html($team_designationfield_val);
                                        $team_experiencefield_val = jobsearch_esc_html($team_experiencefield_val);
                                        $team_imagefield_val = jobsearch_esc_html($team_imagefield_val);
                                        $team_facebookfield_val = jobsearch_esc_html($team_facebookfield_val);
                                        $team_googlefield_val = jobsearch_esc_html($team_googlefield_val);
                                        $team_twitterfield_val = jobsearch_esc_html($team_twitterfield_val);
                                        $team_linkedinfield_val = jobsearch_esc_html($team_linkedinfield_val);
                                        ?>
                                        <li class="jobsearch-column-3">
                                            <figure>
                                                <a class="portfolio-img-holder"><span style="background-image: url('<?php echo ($team_imagefield_val) ?>');"></span></a>
                                                <figcaption>
                                                    <span><?php echo ($exfield) ?></span>
                                                    <div class="jobsearch-company-links">
                                                        <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                                                        <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish del-resume-item"></a>
                                                    </div>
                                                </figcaption>
                                            </figure>
                                            <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                                                <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                                <ul class="jobsearch-row jobsearch-employer-profile-form">
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Member Title *', 'wp-jobsearch') ?></label>
                                                        <input name="jobsearch_field_team_title[]" type="text" value="<?php echo ($exfield) ?>">
                                                    </li>
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Designation *', 'wp-jobsearch') ?></label>
                                                        <input name="jobsearch_field_team_designation[]" type="text" value="<?php echo ($team_designationfield_val) ?>">
                                                    </li>
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Experience *', 'wp-jobsearch') ?></label>
                                                        <input name="jobsearch_field_team_experience[]" type="text" value="<?php echo ($team_experiencefield_val) ?>">
                                                    </li>
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Image *', 'wp-jobsearch') ?></label>
                                                        <div class="upload-img-holder-sec">
                                                            <span class="file-loader"></span>
                                                            <img src="<?php echo ($team_imagefield_val) ?>" alt="">
                                                            <br>
                                                            <input name="team_image" type="file" style="display: none;">
                                                            <input type="hidden" class="img-upload-save-field" name="jobsearch_field_team_image[]" value="<?php echo ($team_imagefield_val) ?>">
                                                            <a href="javascript:void(0)" class="upload-port-img-btn"><i class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Upload Photo', 'wp-jobsearch') ?></a>
                                                        </div>
                                                    </li>
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Facebook URL', 'wp-jobsearch') ?></label>
                                                        <input name="jobsearch_field_team_facebook[]" type="text" value="<?php echo ($team_facebookfield_val) ?>">
                                                    </li>
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Google+ URL', 'wp-jobsearch') ?></label>
                                                        <input name="jobsearch_field_team_google[]" type="text" value="<?php echo ($team_googlefield_val) ?>">
                                                    </li>
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Twitter URL', 'wp-jobsearch') ?></label>
                                                        <input name="jobsearch_field_team_twitter[]" type="text" value="<?php echo ($team_twitterfield_val) ?>">
                                                    </li>
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('LinkedIn URL', 'wp-jobsearch') ?></label>
                                                        <input name="jobsearch_field_team_linkedin[]" type="text" value="<?php echo ($team_linkedinfield_val) ?>">
                                                    </li>
                                                    <li class="jobsearch-column-12">
                                                        <label><?php esc_html_e('Description', 'wp-jobsearch') ?></label>
                                                        <textarea name="jobsearch_field_team_description[]"><?php echo ($exfield_val) ?></textarea>
                                                    </li>
                                                    <li class="jobsearch-column-12">
                                                        <input class="update-resume-list-btn" type="submit" value="<?php esc_html_e('Update', 'wp-jobsearch') ?>">
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <?php
                                        $exfield_counter++;
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            $team_html = ob_get_clean();
            echo apply_filters('jobsearch_emp_dash_profile_team_html', $team_html);
        }
        echo apply_filters('jobsearch_emp_dash_profile_after_team', '');

        //
        $_allow_award_add = isset($jobsearch_plugin_options['allow_empl_awards']) ? $jobsearch_plugin_options['allow_empl_awards'] : '';
        if ($_allow_award_add == 'on') {

            $empaward_is_locked = $user_pkg_limits::emp_field_is_locked('award_defields');

            ob_start();
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-candidate-resume-wrap">    
                    <div class="jobsearch-candidate-title"> 
                        <h2>
                            <i class="jobsearch-icon jobsearch-group"></i> <?php esc_html_e('Awards', 'wp-jobsearch') ?>
                            <?php
                            if (!$empaward_is_locked) {
                                ?>
                                <a href="javascript:void(0)" class="jobsearch-resume-addbtn jobsearch-portfolio-add-btn"><span class="fa fa-plus"></span> <?php esc_html_e('Add Award', 'wp-jobsearch') ?> </a>
                                <?php
                            }
                            ?>
                        </h2> 
                    </div>
                    <?php
                    if ($empaward_is_locked) {
                        $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                        echo ($lock_field_html);
                    } else {
                        ?>
                        <div class="jobsearch-add-popup jobsearch-add-resume-item-popup">
                            <span class="close-popup-item"><i class="fa fa-times"></i></span>
                            <ul class="jobsearch-row jobsearch-employer-profile-form">
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Award Title *', 'wp-jobsearch') ?></label>
                                    <input id="award_title" class="jobsearch-req-field" type="text">
                                </li>
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Image *', 'wp-jobsearch') ?></label>
                                    <div class="upload-img-holder-sec">
                                        <span class="file-loader"></span>
                                        <img src="" alt="">
                                        <input name="award_image" type="file" style="display: none;">
                                        <input type="hidden" id="award_image_input" class="jobsearch-req-field">
                                        <a href="javascript:void(0)" class="upload-port-img-btn"><i class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Upload Photo', 'wp-jobsearch') ?></a>
                                    </div>
                                </li>
                                <li class="jobsearch-column-12">
                                    <input type="submit" id="add-emp-award-btn" value="<?php esc_html_e('Add award', 'wp-jobsearch') ?>">
                                    <span class="portfolio-loding-msg edu-loding-msg"></span>
                                </li>
                            </ul>
                        </div>

                        <div id="jobsearch-emp-awards-con" class="jobsearch-company-gallery">
                            <ul class="jobsearch-row jobsearch-award-list-con">
                                <?php
                                $exfield_list = get_post_meta($employer_id, 'jobsearch_field_award_title', true);
                                $award_imagefield_list = get_post_meta($employer_id, 'jobsearch_field_award_image', true);
                                if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                                    $exfield_counter = 0;
                                    foreach ($exfield_list as $exfield) {
                                        $rand_num = rand(1000000, 99999999);

                                        $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                        $award_imagefield_val = isset($award_imagefield_list[$exfield_counter]) ? $award_imagefield_list[$exfield_counter] : '';
                                        
                                        $exfield = jobsearch_esc_html($exfield);
                                        $exfield_val = jobsearch_esc_html($exfield_val);
                                        $award_imagefield_val = jobsearch_esc_html($award_imagefield_val);
                                        ?>
                                        <li class="jobsearch-column-3">
                                            <figure>
                                                <a class="portfolio-img-holder"><span style="background-image: url('<?php echo ($award_imagefield_val) ?>');"></span></a>
                                                <figcaption>
                                                    <span><?php echo ($exfield) ?></span>
                                                    <div class="jobsearch-company-links">
                                                        <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                                                        <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish del-resume-item"></a>
                                                    </div>
                                                </figcaption>
                                            </figure>
                                            <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                                                <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                                <ul class="jobsearch-row jobsearch-employer-profile-form">
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Award Title *', 'wp-jobsearch') ?></label>
                                                        <input name="jobsearch_field_award_title[]" type="text" value="<?php echo ($exfield) ?>">
                                                    </li>
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Image *', 'wp-jobsearch') ?></label>
                                                        <div class="upload-img-holder-sec">
                                                            <span class="file-loader"></span>
                                                            <img src="<?php echo ($award_imagefield_val) ?>" alt="">
                                                            <br>
                                                            <input name="award_image" type="file" style="display: none;">
                                                            <input type="hidden" class="img-upload-save-field" name="jobsearch_field_award_image[]" value="<?php echo ($award_imagefield_val) ?>">
                                                            <a href="javascript:void(0)" class="upload-port-img-btn"><i class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Upload Photo', 'wp-jobsearch') ?></a>
                                                        </div>
                                                    </li>
                                                    <li class="jobsearch-column-12">
                                                        <input class="update-resume-list-btn" type="submit" value="<?php esc_html_e('Update', 'wp-jobsearch') ?>">
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <?php
                                        $exfield_counter++;
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            $awards_html = ob_get_clean();
            echo apply_filters('jobsearch_emp_dashbord_bx_awards_html', $awards_html);
        }

        //
        $_allow_affiliation_add = isset($jobsearch_plugin_options['allow_empl_affiliations']) ? $jobsearch_plugin_options['allow_empl_affiliations'] : '';
        if ($_allow_affiliation_add == 'on') {

            $empaffiliation_is_locked = $user_pkg_limits::emp_field_is_locked('affiliation_defields');

            ob_start();
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-candidate-resume-wrap">    
                    <div class="jobsearch-candidate-title"> 
                        <h2>
                            <i class="jobsearch-icon jobsearch-group"></i> <?php esc_html_e('Affiliations', 'wp-jobsearch') ?>
                            <?php
                            if (!$empaffiliation_is_locked) {
                                ?>
                                <a href="javascript:void(0)" class="jobsearch-resume-addbtn jobsearch-portfolio-add-btn"><span class="fa fa-plus"></span> <?php esc_html_e('Add Affiliation', 'wp-jobsearch') ?> </a>
                                <?php } ?>
                        </h2> 
                    </div>
                    <?php
                    if ($empaffiliation_is_locked) {
                        $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                        echo ($lock_field_html);
                    } else { ?>
                        <div class="jobsearch-add-popup jobsearch-add-resume-item-popup">
                            <span class="close-popup-item"><i class="fa fa-times"></i></span>
                            <ul class="jobsearch-row jobsearch-employer-profile-form">
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Affiliation Title *', 'wp-jobsearch') ?></label>
                                    <input id="affiliation_title" class="jobsearch-req-field" type="text">
                                </li>
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Image *', 'wp-jobsearch') ?></label>
                                    <div class="upload-img-holder-sec">
                                        <span class="file-loader"></span>
                                        <img src="" alt="">
                                        <input name="affiliation_image" type="file" style="display: none;">
                                        <input type="hidden" id="affiliation_image_input" class="jobsearch-req-field">
                                        <a href="javascript:void(0)" class="upload-port-img-btn"><i class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Upload Photo', 'wp-jobsearch') ?></a>
                                    </div>
                                </li>
                                <li class="jobsearch-column-12">
                                    <input type="submit" id="add-emp-affiliation-btn" value="<?php esc_html_e('Add affiliation', 'wp-jobsearch') ?>">
                                    <span class="portfolio-loding-msg edu-loding-msg"></span>
                                </li>
                            </ul>
                        </div>

                        <div id="jobsearch-emp-affiliations-con" class="jobsearch-company-gallery">
                            <ul class="jobsearch-row jobsearch-affiliation-list-con">
                                <?php
                                $exfield_list = get_post_meta($employer_id, 'jobsearch_field_affiliation_title', true);
                                $affiliation_imagefield_list = get_post_meta($employer_id, 'jobsearch_field_affiliation_image', true);
                                if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                                    $exfield_counter = 0;
                                    foreach ($exfield_list as $exfield) {
                                        $rand_num = rand(1000000, 99999999);

                                        $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                        $affiliation_imagefield_val = isset($affiliation_imagefield_list[$exfield_counter]) ? $affiliation_imagefield_list[$exfield_counter] : '';
                                        
                                        $exfield = jobsearch_esc_html($exfield);
                                        $exfield_val = jobsearch_esc_html($exfield_val);
                                        $affiliation_imagefield_val = jobsearch_esc_html($affiliation_imagefield_val);
                                        ?>
                                        <li class="jobsearch-column-3">
                                            <figure>
                                                <a class="portfolio-img-holder"><span style="background-image: url('<?php echo ($affiliation_imagefield_val) ?>');"></span></a>
                                                <figcaption>
                                                    <span><?php echo ($exfield) ?></span>
                                                    <div class="jobsearch-company-links">
                                                        <a href="javascript:void(0);" class="jobsearch-icon jobsearch-edit update-resume-item"></a>
                                                        <a href="javascript:void(0);" class="jobsearch-icon jobsearch-rubbish del-resume-item"></a>
                                                    </div>
                                                </figcaption>
                                            </figure>
                                            <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                                                <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                                <ul class="jobsearch-row jobsearch-employer-profile-form">
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Affiliation Title *', 'wp-jobsearch') ?></label>
                                                        <input name="jobsearch_field_affiliation_title[]" type="text" value="<?php echo ($exfield) ?>">
                                                    </li>
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Image *', 'wp-jobsearch') ?></label>
                                                        <div class="upload-img-holder-sec">
                                                            <span class="file-loader"></span>
                                                            <img src="<?php echo ($affiliation_imagefield_val) ?>" alt="">
                                                            <br>
                                                            <input name="affiliation_image" type="file" style="display: none;">
                                                            <input type="hidden" class="img-upload-save-field" name="jobsearch_field_affiliation_image[]" value="<?php echo ($affiliation_imagefield_val) ?>">
                                                            <a href="javascript:void(0)" class="upload-port-img-btn"><i class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Upload Photo', 'wp-jobsearch') ?></a>
                                                        </div>
                                                    </li>
                                                    <li class="jobsearch-column-12">
                                                        <input class="update-resume-list-btn" type="submit" value="<?php esc_html_e('Update', 'wp-jobsearch') ?>">
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <?php
                                        $exfield_counter++;
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            $awards_html = ob_get_clean();
            echo apply_filters('jobsearch_emp_dash_bx_affiliations_html', $awards_html);
        }

        $compny_gal_allow = isset($jobsearch_plugin_options['allow_compny_galery']) ? $jobsearch_plugin_options['allow_compny_galery'] : '';
        $max_gal_imgs_allow = isset($jobsearch_plugin_options['max_gal_imgs_allow']) && $jobsearch_plugin_options['max_gal_imgs_allow'] > 0 ? $jobsearch_plugin_options['max_gal_imgs_allow'] : 5;
        $number_of_gal_imgs = $max_gal_imgs_allow;
        $company_gal_imgs = get_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs', true);
        $company_gal_videos = get_post_meta($employer_id, 'jobsearch_field_company_gallery_videos', true);
        $company_gal_titles = get_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs_title', true);
        $company_gal_descs = get_post_meta($employer_id, 'jobsearch_field_company_gallery_imgs_description', true);

        if ($compny_gal_allow == 'on') {
            ob_start();
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title"><h2><?php esc_html_e('Company Photos/Videos', 'wp-jobsearch') ?></h2></div>
                <?php
                $gphotos_is_locked = $user_pkg_limits::emp_field_is_locked('gphotos_defields');
                if ($gphotos_is_locked) {
                    $lock_field_html = $user_pkg_limits->emp_field_locked_html();
                    echo ($lock_field_html);
                } else {
                    ?>
                    <div class="jobsearch-company-photo jobsearch-company-gal-photo" style="display: <?php echo (!empty($company_gal_imgs) ? 'none' : 'block') ?>;">
                        <img src="<?php echo jobsearch_plugin_get_url('images/employer-profile-nonphoto.png') ?>" alt="">
                        <h2><?php esc_html_e('Upload profile photos here.', 'wp-jobsearch') ?></h2>
                        <small><?php printf(esc_html__('You can upload up to %s images under your profile.', 'wp-jobsearch'), $number_of_gal_imgs) ?></small>
                        <div class="jobsearch-fileUpload">
                            <span><i class="jobsearch-icon jobsearch-upload"></i> <?php esc_html_e('Upload Images', 'wp-jobsearch') ?></span>
                            <input id="company_gallery_imgs" name="user_profile_gallery_imgs[]" type="file" class="upload jobsearch-upload" multiple="multiple" onchange="jobsearch_gallry_read_file_url(event)" />
                        </div>
                    </div>
                    <div class="jobsearch-gallery-main">
                        <div id="gallery-imgs-holder" class="gallery-imgs-holder jobsearch-company-gallery">
                            <ul class="jobsearch-row gal-all-imgs">
                                <?php
                                $company_gal_imgs_count = 0;
                                if (!empty($company_gal_imgs)) {
                                    $company_gal_imgs_count = count($company_gal_imgs);

                                    $gal_counter = 0;
                                    foreach ($company_gal_imgs as $company_gal_img) {
                                        $rand_id = rand(100000, 9999999);
                                        if ($company_gal_img != '' && absint($company_gal_img) <= 0) {
                                            $company_gal_img = jobsearch_get_attachment_id_from_url($company_gal_img);
                                        }
                                        $gal_thumbnail_image = wp_get_attachment_image_src($company_gal_img, 'thumbnail');
                                        $gal_thumb_image_src = isset($gal_thumbnail_image[0]) && esc_url($gal_thumbnail_image[0]) != '' ? $gal_thumbnail_image[0] : '';

                                        $gal_video_url = isset($company_gal_videos[$gal_counter]) && esc_url($company_gal_videos[$gal_counter]) != '' ? $company_gal_videos[$gal_counter] : '';
                                        $gal_img_title = isset($company_gal_titles[$gal_counter]) && esc_url($company_gal_titles[$gal_counter]) != '' ? $company_gal_titles[$gal_counter] : '';
                                        $gal_img_desc = isset($company_gal_descs[$gal_counter]) && esc_url($company_gal_descs[$gal_counter]) != '' ? $company_gal_descs[$gal_counter] : '';
                                        
                                        $gal_video_url = jobsearch_esc_html($gal_video_url);
                                        $gal_img_title = jobsearch_esc_html($gal_img_title);
                                        $gal_img_desc = jobsearch_esc_html($gal_img_desc);
                                        ?>
                                        <li class="jobsearch-column-3 gal-item">
                                            <figure>
                                                <a><img src="<?php echo esc_url($gal_thumb_image_src) ?>" alt=""></a>
                                                <figcaption>
                                                    <div class="jobsearch-company-links">
                                                        <a href="javascript:void(0);" class="fa fa-arrows el-drag"></a>
                                                        <a href="javascript:void(0);" data-rand="<?php echo ($rand_id) ?>" class="fa fa-pencil el-galupdate-btn el-update-btn-<?php echo ($rand_id) ?>"></a>

                                                        <a href="javascript:void(0);" data-id="<?php echo absint($company_gal_img) ?>" class="jobsearch-icon jobsearch-rubbish el-remove"></a>
                                                        <input type="hidden" name="company_gallery_imgs[]" value="<?php echo absint($company_gal_img) ?>">
                                                        <input type="hidden" id="gallery-video-to-put-<?php echo ($rand_id) ?>" name="jobsearch_field_company_gallery_videos[]" value="<?php echo ($gal_video_url) ?>">
                                                        <input type="hidden" id="gallery-title-to-put-<?php echo ($rand_id) ?>" name="jobsearch_field_company_gallery_imgs_title[]" value="<?php echo ($gal_img_title) ?>">
                                                        <textarea id="gallery-desc-to-put-<?php echo ($rand_id) ?>" name="jobsearch_field_company_gallery_imgs_description[]" style="display:none;"><?php echo ($gal_img_desc) ?></textarea>
                                                    </div>
                                                </figcaption>
                                            </figure>
                                        </li>
                                        <?php
                                        $gal_counter ++;
                                    }
                                }
                                ?>
                            </ul>
                            <?php
                            $popup_args = array();
                            add_action('wp_footer', function () use ($popup_args) {
                                ?>
                                <div class="jobsearch-modal fade" id="JobSearchModalEmployerGallery">
                                    <div class="modal-inner-area">&nbsp;</div>
                                    <div class="modal-content-area">
                                        <div class="modal-box-area">
                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                            <div class="jobsearch-send-message-form">
                                                <div class="jobsearch-user-form">
                                                    <ul class="email-fields-list">
                                                        <li>
                                                            <label>
                                                                <?php echo esc_html__('Video URL', 'wp-jobsearch'); ?>:
                                                            </label>
                                                            <div class="input-field">
                                                                <input type="text" class="gallery-video-to-get-0" />
                                                                <em style="display: inline-block; margin-top: 8px;"><?php esc_html_e('Add video URL of youtube, Vimeo.', 'wp-jobsearch') ?></em>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <label>
                                                                <?php echo esc_html__('Title', 'wp-jobsearch'); ?>:
                                                            </label>
                                                            <div class="input-field">
                                                                <input type="text" class="gallery-title-to-get-0" />
                                                                <em style="display: inline-block; margin-top: 8px;"><?php esc_html_e('Add the image title here.', 'wp-jobsearch') ?></em>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <label>
                                                                <?php echo esc_html__('Description', 'wp-jobsearch'); ?>:
                                                            </label>
                                                            <div class="input-field">
                                                                <textarea class="gallery-desc-to-get-0"></textarea>
                                                                <em style="display: inline-block; margin-top: 8px;"><?php esc_html_e('Add the image description here.', 'wp-jobsearch') ?></em>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="input-field-submit">
                                                                <a href="javascript:void(0);" data-id="0" class="careerfy-classic-btn gallery-update-0 jobsearch-bgcolor"><?php echo esc_html__('Update', 'wp-jobsearch'); ?></a>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    jQuery(document).on('click', '.el-galupdate-btn', function () {
                                        var rand_id = jQuery(this).attr('data-rand');
                                        jQuery('#JobSearchModalEmployerGallery').find('.gallery-update-0').attr('data-id', rand_id);
                                        jQuery('#JobSearchModalEmployerGallery').find('.gallery-video-to-get-0').attr('id', 'gallery-video-to-get-' + rand_id);
                                        jQuery('#JobSearchModalEmployerGallery').find('.gallery-video-to-get-0').val(jQuery('#gallery-video-to-put-' + rand_id).val());
                                        //
                                        jQuery('#JobSearchModalEmployerGallery').find('.gallery-title-to-get-0').attr('id', 'gallery-title-to-get-' + rand_id);
                                        jQuery('#JobSearchModalEmployerGallery').find('.gallery-title-to-get-0').val(jQuery('#gallery-title-to-put-' + rand_id).val());
                                        //
                                        jQuery('#JobSearchModalEmployerGallery').find('.gallery-desc-to-get-0').attr('id', 'gallery-desc-to-get-' + rand_id);
                                        jQuery('#JobSearchModalEmployerGallery').find('.gallery-desc-to-get-0').val(jQuery('#gallery-desc-to-put-' + rand_id).val());
                                        jQuery('#JobSearchModalEmployerGallery').find('.gallery-desc-to-get-0').html(jQuery('#gallery-desc-to-put-' + rand_id).val());
                                        //
                                        jobsearch_modal_popup_open('JobSearchModalEmployerGallery');
                                    });
                                    jQuery(document).on('click', '.gallery-update-0', function () {
                                        var _this = jQuery(this);
                                        var this_id = _this.attr('data-id');
                                        var galery_video_val = jQuery('#gallery-video-to-get-' + this_id).val();
                                        jQuery('#gallery-video-to-put-' + this_id).val(galery_video_val);
                                        //
                                        var galery_title_val = jQuery('#gallery-title-to-get-' + this_id).val();
                                        jQuery('#gallery-title-to-put-' + this_id).val(galery_title_val);
                                        //
                                        var galery_desc_val = jQuery('#gallery-desc-to-get-' + this_id).val();
                                        jQuery('#gallery-desc-to-put-' + this_id).val(galery_desc_val);
                                        jQuery('#gallery-desc-to-put-' + this_id).html(galery_desc_val);
                                        //
                                        _this.parents('.jobsearch-modal').find('.modal-close').trigger('click');
                                    });
                                </script>
                                <?php
                            }, 11, 1);
                            //
                            ?>
                        </div>
                    </div>
                    <?php
                    $display_val = 'none;';
                    if (!empty($company_gal_imgs) && $company_gal_imgs_count < $number_of_gal_imgs) {
                        $display_val = 'inline-block;';
                    }
                    ?>
                    <a id="upload-more-gal-imgs" href="javascript:void(0)" class="jobsearch-add-more-imgs jobsearch-employer-profile-submit" style="display: <?php echo ($display_val) ?>;"> <?php esc_html_e('Upload More Images', 'wp-jobsearch') ?> </a>
                    <span class="galery-uplod-lodr"></span>
                    <div class="galery-uplod-msg"></div>
                    <?php
                }
                ?>
            </div>
            <?php
            $galery_html = ob_get_clean();
            echo apply_filters('jobsearch_empdash_profile_gallery_html', $galery_html);
        }
        ?>

        <input type="hidden" name="user_settings_form" value="1">
        <?php
        $termscon_chek = get_post_meta($employer_id, 'terms_cond_check', true);
        ob_start();
        jobsearch_terms_and_con_link_txt($termscon_chek);
        ?>
        <input type="submit" class="jobsearch-employer-profile-submit" value="<?php esc_html_e('Save Settings', 'wp-jobsearch') ?>">
        <?php
        $subbtn_html = ob_get_clean();
        echo apply_filters('jobsearch_empdash_profile_savbtncond_html', $subbtn_html);

        ob_start();
        do_action('jobsearch_translate_profile_with_wpml_btn', $employer_id, 'employer', 'dashboard-settings');
        $btns_html = ob_get_clean();
        echo apply_filters('jobsearch_translate_eprofile_with_wpml_btn_html', $btns_html);
        ?>
    </form>
</div>
