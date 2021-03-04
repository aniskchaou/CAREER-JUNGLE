<?php
if (!class_exists('jobsearch_candidate_pdf_resume_default_template')) {

    class jobsearch_candidate_pdf_resume_default_template
    {
        public function __construct()
        {
            add_action('init', array($this, 'jobsearch_single_candidate_resume_export_callback'));
            add_action('wp_footer', array($this, 'jobsearch_single_candidate_resume_form'), 10);
            add_action('admin_footer', array($this, 'jobsearch_single_candidate_resume_form'), 10);
        }

        public function jobsearch_single_candidate_resume_form()
        {
            global $jobsearch_plugin_options, $sitepress;
            //
            $flag = false;
            $page_id = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $page_id = jobsearch__get_post_id($page_id, 'page');
            $lang_code = '';
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
            }
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $page_id = icl_object_id($page_id, 'page', false, $lang_code);
            }
            if (is_page($page_id)) {
                $flag = true;
            }
            if (is_admin()) {
                $flag = true;
            }

            if ($flag == false) {
                return;
            }
            ?>
            <form id="pdf_cand_generate_form" method="post" enctype="multipart/form-data" style="display: none">
                <input type="text" name="jobsearch_single_pdf_cand_id" value="">
                <input type="submit" class="btn btn-default" name="pdf_cand_generate_form_submit"
                       value="Generate PDF">
            </form>
            <script type="text/javascript">
                jQuery(document).on('click', '.jobsearch-get-cand-id', function () {

                    var _this = jQuery(this), _template = _this.attr('data-template'), _loader_html,
                        _template_class = _this.attr('data-class'), _cand_id = jQuery(this).attr('data-cand-id');
                    _loader_html = '<div class="jobsearch-candidate-pdf-locked pdf-loader"><a href="javascript:void(0)" class="fa fa-refresh fa-spin"></a></div>';
                    jQuery(document).find('.' + _template_class).after(_loader_html);
                    jQuery(".jobsearch-candidate-pdf-list").find("figcaption").remove();

                    var request = jQuery.ajax({
                        url: jobsearch_plugin_vars.ajax_url,
                        method: "POST",
                        data: {
                            template_name: _template,
                            action: 'jobsearch_user_pdf_type_save',
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if (typeof response.res !== 'undefined' && response.res == true) {
                            jQuery(document).find('.' + _template_class).after('<figcaption>' + jobsearch_export_vars.active + '</figcaption>');
                            jQuery(document).find(".pdf-loader").remove();
                            //
                            jQuery("input[name=jobsearch_single_pdf_cand_id]").val(_cand_id);
                            jQuery("input[name=pdf_cand_generate_form_submit]").trigger('click');
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        console.info(textStatus);
                    });
                });
            </script>
        <?php }

        public function jobsearch_single_candidate_resume_export_callback()
        {
            global $jobsearch_resume_export, $jobsearch_plugin_options;

            if (isset($_POST['pdf_cand_generate_form_submit'])) {

                $candidate_id = $_POST['jobsearch_single_pdf_cand_id'];
                $stylesheet = file_get_contents($jobsearch_resume_export->jobsearch_resume_export_get_path('css/jobsearch-mpdf-style.css'));
                $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];
                //
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => '+aCJK',
                    "autoScriptToLang" => true,
                    "autoLangToFont" => true,
                    'format' => 'A4',
                    'margin_left' => 5,
                    'margin_right' => 5,
                    'margin_top' => 13,
                    'margin_bottom' => 13,
                    'border' => '2px solid #000',
                    'mirrorMargins' => true,
                    'tempDir' => __DIR__ . '/upload',
                    'fontDir' => array_merge($fontDirs, [
                        __DIR__ . '/fonts'
                    ]),
                    'fontdata' => $fontData + [
                            "proximanova" => [
                                'R' => "ProximaNova-Regular.ttf",
                            ],
                            "jobsearch" => [
                                'R' => "icomoon.ttf",
                            ],
                            "careerfy" => [
                                'R' => "careerfy.ttf",
                            ],
                        ],
                    'default_font' => 'proximanova'
                ]);

                $mpdf->allow_charset_conversion = true;
                //$mpdf->charset_in = 'iso-8859-4';
                //$mpdf->charset_in = 'iso-8859-4';

                $user_id = jobsearch_get_candidate_user_id($candidate_id);
                $user_obj = get_user_by('ID', $user_id);
                $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
                $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
                $candidate_obj = get_post($candidate_id);
                $candidate_content = $candidate_obj->post_content;
                $candidate_content = apply_filters('the_content', $candidate_content);

                $user_website = isset($user_obj->user_url) ? $user_obj->user_url : '';
                $user_email = isset($user_obj->user_email) ? $user_obj->user_email : '';
                //
                $jobsearch_candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                $candidate_company_str = '';
                if ($jobsearch_candidate_jobtitle != '') {
                    $candidate_company_str .= $jobsearch_candidate_jobtitle;
                }
                //
                $cand_det_full_address_switch = true;
                $locations_view_type = isset($jobsearch_plugin_options['cand_det_loc_listing']) ? $jobsearch_plugin_options['cand_det_loc_listing'] : '';
                $loc_view_country = $loc_view_state = $loc_view_city = false;
                if (!empty($locations_view_type)) {
                    if (is_array($locations_view_type) && in_array('country', $locations_view_type)) {
                        $loc_view_country = true;
                    }

                    if (is_array($locations_view_type) && in_array('state', $locations_view_type)) {
                        $loc_view_state = true;
                    }
                    if (is_array($locations_view_type) && in_array('city', $locations_view_type)) {
                        $loc_view_city = true;
                    }
                }
                $candidate_address = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);

                if (function_exists('jobsearch_post_city_contry_txtstr')) {
                    $candidate_address = jobsearch_post_city_contry_txtstr($candidate_id, $loc_view_country, $loc_view_state, $loc_view_city, $cand_det_full_address_switch);
                }

                // Extra Fields
                $user_def_avatar_url = jobsearch_candidate_img_url_comn($candidate_id);
                $profile_image = $user_def_avatar_url;
                $user_id = jobsearch_get_candidate_user_id($candidate_id);
                $user_obj = get_user_by('ID', $user_id);
                $cand_email = $user_obj->user_email;
                $user_firstname = isset($user_obj->first_name) ? $user_obj->first_name : '';
                $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
                //
                $phone_number = get_post_meta($candidate_id, 'jobsearch_field_user_phone', true);
                ob_start();
                ?>
                <div class="cndt-body">
                    <div class="cndt-user-section">
                        <?php if (!empty($profile_image)) { ?>
                            <div class="cndt-user-thumb">
                                <img src="<?php echo($profile_image) ?>">
                            </div>
                        <?php } ?>
                        <div class="cndt-user-text">
                            <div class="cndt-name"><?php echo($user_displayname) ?></div>
                            <div class="cndt-jobtitle"><?php echo jobsearch_esc_html($candidate_company_str) ?></div>
                            <div class="cndt-contact-info">

                                <span><?php echo esc_html__('PHONE:', 'jobsearch-resume-export') ?></span>
                                <a href="tel:<?php echo($phone_number) ?>"><?php echo($phone_number) ?></a>
                                <span><?php echo esc_html__('EMAIL:', 'jobsearch-resume-export') ?></span>
                                <a href="mailto:<?php echo($cand_email) ?>"><?php echo($cand_email) ?></a>
                                <br>
                                <span><?php echo esc_html__('Address:', 'jobsearch-resume-export') ?></span>
                                <a href="#"><?php echo($candidate_address) ?></a>
                            </div>
                        </div>
                    </div>

                    <div class="cndt-left-content">
                        <!--Candidate Custom Fields-->
                        <?php echo self::jobsearch_resume_candidate_custom_fields($candidate_id) ?>
                        <!--Candidate Experience-->
                        <?php echo self::jobsearch_resume_candidate_experience($candidate_id) ?>
                        <!--Candidate Education-->
                        <?php echo self::jobsearch_resume_candidate_education($candidate_id) ?>
                        <!--Candidate Awards-->
                        <?php echo self::jobsearch_resume_candidate_awards($candidate_id) ?>
                        <!--Portfolio-->
                        <?php echo self::jobsearch_resume_cand_portfolio($candidate_id) ?>
                    </div>

                    <div class="cndt-right-content">
                        <div class="content-icon-wrap">
                            <div class="content-icon">
                                <div style="font-family: jobsearch">&#xe943</div>
                            </div>
                            <div class="cndt-content-title">
                                <span><?php echo esc_html__('About Me', 'jobsearch-resume-export') ?></span>
                            </div>
                        </div>

                        <div class="cndt-right-pera"><?php echo($candidate_content) ?></div>
                        <!--Candidate Expertise-->
                        <?php echo self::jobsearch_resume_candidate_expertise($candidate_id) ?>
                        <div class="sidebar-spacer"></div>
                        <!--Candidate Languages-->
                        <?php echo self::jobsearch_resume_candidate_languages($candidate_id) ?>
                        <div class="sidebar-spacer"></div>
                        <!--Candidate Skills-->
                        <?php echo self::jobsearch_resume_candidate_skills($candidate_id) ?>
                    </div>
                </div>
                <?php
                $pdf_html = ob_get_clean();
                $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
                $mpdf->WriteHTML($pdf_html, \Mpdf\HTMLParserMode::HTML_BODY);
                $mpdf->Output($user_firstname . '-' . date('dmy') . "-" . $candidate_id . '.pdf', 'D');
            }
        }

        public function jobsearch_candidate_resume_bulk_export_template_default($candidate_id)
        {
            global $jobsearch_resume_export, $jobsearch_plugin_options, $jobsearch_pdf_temp_upload_file;
            $stylesheet = file_get_contents($jobsearch_resume_export->jobsearch_resume_export_get_path('css/jobsearch-mpdf-style.css'));
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
            //
            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];
            //
            $char_set = get_bloginfo('charset');

            $mpdf = new \Mpdf\Mpdf([
                'mode' => '+aCJK',
                "autoScriptToLang" => true,
                "autoLangToFont" => true,
                'format' => 'A4',
                'margin_left' => 5,
                'margin_right' => 5,
                'margin_top' => 13,
                'autoArabic' => true,
                'margin_bottom' => 13,
                'border' => '2px solid #000',
                'mirrorMargins' => true,
                'tempDir' => __DIR__ . '/upload',
                'fontDir' => array_merge($fontDirs, [
                    __DIR__ . '/fonts'
                ]),
                'fontdata' => $fontData + [
                        "proximanova" => [
                            'R' => "ProximaNova-Regular.ttf",
                        ],
                        "fontawesome" => [
                            'R' => "fontawesome.ttf",
                        ],
                        "jobsearch" => [
                            'R' => "icomoon.ttf",
                        ],
                        "careerfy" => [
                            'R' => "careerfy.ttf",
                        ],
                    ],
                'default_font' => 'proximanova'
            ]);
            $user_id = jobsearch_get_candidate_user_id($candidate_id);
            $user_obj = get_user_by('ID', $user_id);

            $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
            $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
            $candidate_obj = get_post($candidate_id);
            $candidate_content = $candidate_obj->post_content;
            $candidate_content = apply_filters('the_content', $candidate_content);

            $user_website = isset($user_obj->user_url) ? $user_obj->user_url : '';
            $cand_email = isset($user_obj->user_email) ? $user_obj->user_email : '';
            //
            $jobsearch_candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
            $candidate_company_str = '';
            if ($jobsearch_candidate_jobtitle != '') {
                $candidate_company_str .= $jobsearch_candidate_jobtitle;
            }
            $cand_det_full_address_switch = true;
            $locations_view_type = isset($jobsearch_plugin_options['cand_det_loc_listing']) ? $jobsearch_plugin_options['cand_det_loc_listing'] : '';
            $loc_view_country = $loc_view_state = $loc_view_city = false;
            if (!empty($locations_view_type)) {
                if (is_array($locations_view_type) && in_array('country', $locations_view_type)) {
                    $loc_view_country = true;
                }

                if (is_array($locations_view_type) && in_array('state', $locations_view_type)) {
                    $loc_view_state = true;
                }
                if (is_array($locations_view_type) && in_array('city', $locations_view_type)) {
                    $loc_view_city = true;
                }
            }
            $candidate_address = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);

            if (function_exists('jobsearch_post_city_contry_txtstr')) {
                $candidate_address = jobsearch_post_city_contry_txtstr($candidate_id, $loc_view_country, $loc_view_state, $loc_view_city, $cand_det_full_address_switch);
            }

            $user_dob_dd = get_post_meta($candidate_id, 'jobsearch_field_user_dob_dd', true);
            $user_dob_mm = get_post_meta($candidate_id, 'jobsearch_field_user_dob_mm', true);
            $user_dob_yy = get_post_meta($candidate_id, 'jobsearch_field_user_dob_yy', true);

            $user_dob_whole = get_post_meta($candidate_id, 'jobsearch_field_user_dob_whole', true);

            if ($user_dob_whole == '' && $user_dob_dd != '' && $user_dob_mm != '' && $user_dob_yy != '') {
                $user_dob_whole = $user_dob_dd . '-' . $user_dob_mm . '-' . $user_dob_yy;
            }

            $user_dob_whole = get_post_meta($candidate_id, 'jobsearch_field_user_dob_whole', true);
            // Extra Fields
            $user_def_avatar_url = jobsearch_candidate_img_url_comn($candidate_id);
            $profile_image = $user_def_avatar_url;
            $user_firstname = isset($user_obj->first_name) ? $user_obj->first_name : '';
            $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
            //
            $phone_number = get_post_meta($candidate_id, 'jobsearch_field_user_phone', true);
            $sectors = wp_get_post_terms($candidate_id, 'sector');
            $candidate_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';
            //
            ob_start();
            ?>
            <div class="cndt-body">
                <div class="cndt-user-section">
                    <?php if (!empty($profile_image)) { ?>
                        <div class="cndt-user-thumb"><img src="<?php echo($profile_image) ?>" alt=""></div>
                    <?php } ?>
                    <div class="cndt-user-text">
                        <div class="cndt-name"><?php echo($user_displayname) ?></div>
                        <div class="cndt-jobtitle"><?php echo jobsearch_esc_html($candidate_company_str) ?></div>
                        <div class="cndt-contact-info">
                            <span><?php echo __('PHONE:', 'jobsearch-resume-export') ?></span>
                            <a href="tel:<?php echo($phone_number) ?>"><?php echo($phone_number) ?></a>
                            <span><?php echo esc_html__('EMAIL:', 'jobsearch-resume-export') ?></span>
                            <a href="mailto:<?php echo($cand_email) ?>"><?php echo($cand_email) ?></a>
                            <span><?php echo esc_html__('Address:', 'jobsearch-resume-export') ?></span>
                            <a href="#"><?php echo($candidate_address) ?></a>
                            <span><?php echo esc_html__('DOB:', 'jobsearch-resume-export') ?></span>
                            <a href="#"><?php echo($user_dob_whole) ?></a>
                            <?php if (!empty($candidate_sector)) { ?>
                                <span><?php echo esc_html__('Sector:', 'jobsearch-resume-export') ?></span>
                                <a href="#"><?php echo($candidate_sector) ?></a>
                            <?php } ?>
                            <br>
                            <?php echo apply_filters('jobsearch_user_fields_for_pdf', $candidate_id, 10, 1) ?>

                        </div>
                    </div>
                </div>

                <div class="cndt-left-content">
                    <!--Candidate Custom Fields-->
                    <?php echo self::jobsearch_resume_candidate_custom_fields($candidate_id) ?>
                    <?php echo apply_filters('jobsearch_default_pdf_template_questionnaire', $candidate_id, 'candidate', 10, 2) ?>
                    <!--Candidate Experience-->
                    <?php echo self::jobsearch_resume_candidate_experience($candidate_id) ?>
                    <!--Candidate Education-->
                    <?php echo self::jobsearch_resume_candidate_education($candidate_id) ?>
                    <!--Candidate Awards-->
                    <?php echo self::jobsearch_resume_candidate_awards($candidate_id) ?>
                    <!--Portfolio-->
                    <?php echo self::jobsearch_resume_cand_portfolio($candidate_id) ?>
                </div>

                <div class="cndt-right-content">
                    <div class="content-icon-wrap">
                        <div class="content-icon">
                            <div style="font-family: jobsearch">&#xe943</div>
                        </div>
                        <div class="cndt-content-title">
                            <span><?php echo esc_html__('About Me', 'jobsearch-resume-export') ?></span>
                        </div>
                    </div>
                    <div class="cndt-right-pera"><?php echo($candidate_content) ?></div>
                    <!--Candidate Expertise-->
                    <?php echo self::jobsearch_resume_candidate_expertise($candidate_id) ?>
                    <div class="sidebar-spacer"></div>
                    <!--Candidate Languages-->
                    <?php echo self::jobsearch_resume_candidate_languages($candidate_id) ?>
                    <div class="sidebar-spacer"></div>
                    <!--Candidate Skills-->
                    <?php echo self::jobsearch_resume_candidate_skills($candidate_id) ?>
                </div>
            </div>

            <?php
            if (file_exists(JOBSEARCH_RESUME_PDF_TEMP_DIR_PATH)) {
                $location = JOBSEARCH_RESUME_PDF_TEMP_DIR_PATH;
            } else {
                $jobsearch_pdf_temp_upload_file = true;
                add_filter('upload_dir', 'jobsearch_resume_export_files_upload_dir', 10, 1);
                $wp_upload_dir = wp_upload_dir();
                $location = $wp_upload_dir['path'] . "/";
                remove_filter('upload_dir', 'jobsearch_resume_export_files_upload_dir', 10, 1);
                $jobsearch_pdf_temp_upload_file = false;
            }

            $pdf_html = ob_get_clean();

            $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
            $mpdf->WriteHTML($pdf_html, \Mpdf\HTMLParserMode::HTML_BODY);
            $mpdf->Output($location . $user_firstname . '-' . date('dmy') . "-" . $candidate_id . '.pdf', 'F');
        }

        public static function jobsearch_resume_candidate_custom_fields($candidate_id)
        {
            global $sitepress;
            $custom_all_fields = get_option('jobsearch_custom_field_candidate');

            if (!empty($custom_all_fields)) { ?>

                <div class="content-icon-wrap">
                    <div class="content-icon">
                        <div>&#xe940</div>
                    </div>
                    <div class="cndt-content-title">
                        <span><?php echo esc_html__('About Me', 'jobsearch-resume-export') ?></span>
                    </div>
                </div>
                <div class="cndt-custom-field">
                <?php

                $fields_data = array();
                $lang_code = '';
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $lang_code = $sitepress->get_current_language();
                }

                foreach ($custom_all_fields as $info) {

                    $field_name = isset($info['name']) ? $info['name'] : '';
                    $field_label = isset($info['label']) ? $info['label'] : '';
                    $type = isset($info['type']) ? $info['type'] : '';
                    $icon = isset($info['icon']) ? $info['icon'] : '';
                    $field_put_val = get_post_meta($candidate_id, $field_name, true);
                    $icon_type = strpos($icon, 'careerfy') !== false ? 'careerfy' : 'jobsearch';

                     if ($type == 'heading') { ?>
                        </div>
                        <div class="content-icon-wrap">
                            <?php if (!empty($fields['icon'])) { ?>
                                <div class="content-icon">
                                    <div><?php echo jobsearch_get_font_code($icon) ?></div>
                                </div>
                            <?php } ?>
                            <div class="cndt-content-title">
                                <span><?php echo($field_label) ?></span>
                            </div>
                        </div>
                        <div class="cndt-custom-field">

                    <?php } else if ($type == 'checkbox') {
                        $drop_down_arr = array();
                        $cut_field_flag = 0;
                        foreach ($info['options']['value'] as $key => $cus_field_options_value) {
                            $drop_down_arr[$cus_field_options_value] = (apply_filters('wpml_translate_single_string', $info['options']['label'][$cut_field_flag], 'Custom Fields', 'Checkbox Option Label - ' . $info['options']['label'][$cut_field_flag], $lang_code));
                            $cut_field_flag++;
                        }

                        if (is_array($field_put_val) && !empty($field_put_val)) {
                            $field_put_valarr = array();
                            foreach ($field_put_val as $fil_putval) {
                                if (isset($drop_down_arr[$fil_putval]) && $drop_down_arr[$fil_putval] != '') {
                                    $field_put_valarr[] = $drop_down_arr[$fil_putval];
                                }
                            }
                            $field_put_val = implode(', ', $field_put_valarr);
                        } else {
                            if (isset($drop_down_arr[$field_put_val]) && $drop_down_arr[$field_put_val] != '') {
                                $field_put_val = $drop_down_arr[$field_put_val];
                            }
                        }
                        $fields_data[] = array(
                            'icon' => jobsearch_get_font_code($icon),
                            'label' => $field_label,
                            'value' => $field_put_val,
                        );

                    } else if (!empty($field_name)) {

                        $field_name = $type == 'upload_file' ? 'jobsearch_cfupfiles_' . $field_name : $field_name;
                        $field_value = get_post_meta($candidate_id, $field_name, true);
                        //
                        if ($type == 'upload_file') { ?>
                            <div class="cndt-custom-field-inner cndt-custom-field-inner-full">
                                <?php if (!empty($icon)) { ?>
                                    <div class="cndt-custom-field-icon-wrap">
                                        <div class="cndt-custom-field-icon">
                                            <div style="font-family: <?php echo($icon_type) ?>"><?php echo jobsearch_get_font_code($icon) ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="cndt-custom-field-text">
                                    <div class="cndt-custom-field-text-left">
                                        <div class="cndt-custom-field-title"><?php echo($field_label) ?></div>
                                    </div>
                                    <?php
                                    if (is_array($field_value) && count($field_value) > 0) {
                                        foreach ($field_value as $val) {
                                            $img_path = str_replace(get_site_url(), ABSPATH, $val); ?>
                                            <div class="cndt-custom-field-image">
                                                <img src="<?php echo($img_path) ?>">
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="cndt-custom-field-inner">
                                <?php if (!empty($icon)) { ?>
                                    <div class="cndt-custom-field-icon-wrap">
                                        <div class="cndt-custom-field-icon">
                                            <div style="font-family: <?php echo($icon_type) ?>"><?php echo jobsearch_get_font_code($icon) ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="cndt-custom-field-text">
                                    <div class="cndt-custom-field-title"><?php echo($field_label) ?></div>
                                    <?php if (is_array($field_value) && count($field_value) > 0) {
                                        foreach ($field_value as $val) { ?>
                                            <div class="cndt-custom-field-sub"><?php echo($val) ?></div>
                                        <?php }
                                    } else {
                                        $field_value = $type == 'date' ? date_i18n($info['date-format'], $field_value) : $field_value;
                                        ?>
                                        <div class="cndt-custom-field-sub"><?php echo($field_value) ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                <!--if the field is checkbox-->
                <?php foreach ($fields_data as $fields) { ?>
                    <div class="cndt-custom-field-inner">
                        <?php if (!empty($fields['icon'])) { ?>
                            <div class="cndt-custom-field-icon-wrap">
                                <div class="cndt-custom-field-icon">
                                    <div><?php echo($fields['icon']) ?></div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="cndt-custom-field-text">
                            <div class="cndt-custom-field-title"><?php echo($fields['label']) ?></div>
                            <div class="cndt-custom-field-sub"><?php echo($fields['value']) ?></div>
                        </div>
                    </div>
                <?php } ?>
                </div>
            <?php }
        }

        public function jobsearch_resume_candidate_experience($candidate_id)
        {
            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_title', true);
            $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_experience_description', true);
            $experience_start_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_start_date', true);
            $experience_end_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_end_date', true);
            $experience_prsnt_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_date_prsnt', true);
            $experience_company_field_list = get_post_meta($candidate_id, 'jobsearch_field_experience_company', true);

            if (is_array($exfield_list) && sizeof($exfield_list) > 0) { ?>
                <div class="content-icon-wrap">
                    <div class="content-icon">
                        <div style="font-family: jobsearch">&#xe940</div>
                    </div>
                    <div class="cndt-content-title">
                        <span><?php echo esc_html__('Work Experience', 'jobsearch-resume-export'); ?></span>
                    </div>
                </div>
                <?php
                $exfield_counter = 0;
                foreach ($exfield_list as $exfield) {
                    $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                    $experience_start_datefield_val = isset($experience_start_datefield_list[$exfield_counter]) ? $experience_start_datefield_list[$exfield_counter] : '';
                    $experience_end_datefield_val = isset($experience_end_datefield_list[$exfield_counter]) ? $experience_end_datefield_list[$exfield_counter] : '';
                    $experience_prsnt_datefield_val = isset($experience_prsnt_datefield_list[$exfield_counter]) ? $experience_prsnt_datefield_list[$exfield_counter] : '';
                    $experience_end_companyfield_val = isset($experience_company_field_list[$exfield_counter]) ? $experience_company_field_list[$exfield_counter] : '';
                    ?>
                    <div class="cndt-contant-article">
                        <span class="cndt-contant-article-sub"><?php echo($experience_end_companyfield_val) ?></span>
                        <div class="cndt-contant-article-min"><?php echo jobsearch_esc_html($exfield) ?></div>
                        <?php if ($experience_prsnt_datefield_val == 'on') { ?>
                            <div class="cndt-contant-article-date"><?php echo ($experience_start_datefield_val != '' ? date('Y', strtotime($experience_start_datefield_val)) : '') . (' - ') . esc_html__('Present', 'jobsearch-resume-export') ?></div>
                        <?php } else { ?>
                            <div class="cndt-contant-article-date"><?php echo ($experience_start_datefield_val != '' ? date('Y', strtotime($experience_start_datefield_val)) : '') . ($experience_end_datefield_val != '' ? ' - ' . date('Y', strtotime($experience_end_datefield_val)) : '') ?></div>
                        <?php } ?>
                        <div class="cndt-contant-article-pera">
                            <?php echo jobsearch_esc_html($exfield_val) ?>
                        </div>
                    </div>
                    <?php $exfield_counter++;
                } ?>
            <?php }
        }

        public static function jobsearch_resume_candidate_education($candidate_id)
        {
            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_title', true);
            $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_education_description', true);
            $education_academyfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_academy', true);
            $education_yearfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_year', true);
            if (is_array($exfield_list) && sizeof($exfield_list) > 0) { ?>
                <div class="content-icon-wrap">
                    <div class="content-icon">
                        <div style="font-family: jobsearch ;">&#xe944</div>
                    </div>
                    <div class="cndt-content-title">
                        <span><?php echo esc_html__('EDUCATION', 'jobsearch-resume-export') ?></span>
                    </div>
                </div>
                <?php
                $exfield_counter = 0;
                foreach ($exfield_list as $exfield) {
                    $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                    $education_academyfield_val = isset($education_academyfield_list[$exfield_counter]) ? $education_academyfield_list[$exfield_counter] : '';
                    $education_yearfield_val = isset($education_yearfield_list[$exfield_counter]) ? $education_yearfield_list[$exfield_counter] : ''; ?>
                    <div class="cndt-contant-article">
                        <span class="cndt-contant-article-sub"><?php echo($exfield) ?></span>
                        <div class="cndt-contant-article-min"><?php echo($exfield_val) ?></div>
                        <div class="cndt-contant-article-date"><?php echo($education_yearfield_val) ?></div>
                    </div>
                    <?php $exfield_counter++;
                } ?>
            <?php }
        }

        public static function jobsearch_resume_candidate_awards($candidate_id)
        {
            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_title', true);
            $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_award_description', true);
            $award_yearfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_year', true);
            if (is_array($exfield_list) && sizeof($exfield_list) > 0) { ?>
                <div class="content-icon-wrap">
                    <div class="content-icon">
                        <div style="font-family: jobsearch">&#xe940</div>
                    </div>
                    <div class="cndt-content-title">
                        <span><?php echo esc_html__('HONORS & AWARDS', 'jobsearch-resume-export'); ?></span>
                    </div>
                </div>
                <?php
                $rand_num = rand(1000000, 99999999);
                $exfield_counter = 0;
                foreach ($exfield_list as $exfield) {
                    $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                    $award_yearfield_val = isset($award_yearfield_list[$exfield_counter]) ? $award_yearfield_list[$exfield_counter] : '';
                    ?>
                    <div class="cndt-contant-article">
                        <span class="cndt-contant-article-sub"><?php echo jobsearch_esc_html($exfield) ?></span>
                        <div class="cndt-contant-article-min"><?php echo jobsearch_esc_html($exfield_val) ?></div>
                        <div class="cndt-contant-article-date"><?php echo jobsearch_esc_html($award_yearfield_val) ?></div>
                    </div>
                    <?php $exfield_counter++;
                }
            }
        }

        public static function jobsearch_resume_candidate_skills($candidate_id)
        {
            $skills_list = jobsearch_resume_export_job_get_all_skills($candidate_id, '', '', '', '', '<div class="cndt-skills-inner"><div class="cndt-skills-list-item">', '</div></div>', 'candidate');
            $skills_list = apply_filters('jobsearch_cand_detail_skills_list_html', $skills_list, $candidate_id);
            if (!empty($skills_list)) { ?>
                <div class="content-icon-wrap">
                    <div class="content-icon">
                        <div style="font-family: jobsearch">&#xe93f</div>
                    </div>
                    <div class="cndt-content-title">
                        <span><?php echo esc_html__('Skills', 'jobsearch-resume-export') ?></span>
                    </div>
                </div>
                <div class="cndt-skills">
                    <?php if ($skills_list != '') { ?>
                        <?php echo($skills_list); ?>
                    <?php } ?>
                </div>
            <?php }
        }

        public static function jobsearch_resume_candidate_languages($candidate_id)
        {
            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_lang_title', true);
            $lang_percentagefield_list = get_post_meta($candidate_id, 'jobsearch_field_lang_percentage', true);
            $lang_level_list = get_post_meta($candidate_id, 'jobsearch_field_lang_level', true);

            if (is_array($exfield_list) && sizeof($exfield_list) > 0) { ?>
                <div class="content-icon-wrap">
                    <div class="content-icon">
                        <div style="font-family: jobsearch">&#xe93f</div>
                    </div>
                    <div class="cndt-content-title">
                        <span><?php echo esc_html__('Languages', 'jobsearch-resume-export') ?></span>
                    </div>
                </div>
                <?php
                $exfield_counter = 0;
                foreach ($exfield_list as $exfield) {
                    $rand_num = rand(1000000, 99999999);
                    $lang_percentagefield_val = isset($lang_percentagefield_list[$exfield_counter]) ? absint($lang_percentagefield_list[$exfield_counter]) : '';
                    $lang_percentagefield_val = $lang_percentagefield_val > 100 ? 100 : $lang_percentagefield_val;
                    $lang_level_val = isset($lang_level_list[$exfield_counter]) ? ($lang_level_list[$exfield_counter]) : '';

                    $lang_level_str = esc_html__('Beginner', 'wp-jobsearch');
                    if ($lang_level_val == 'proficient') {
                        $lang_level_str = esc_html__('Proficient', 'wp-jobsearch');
                    } else if ($lang_level_val == 'intermediate') {
                        $lang_level_str = esc_html__('Intermediate', 'wp-jobsearch');
                    }
                    ?>
                    <div class="cndt-right-links-wrap">
                        <div class="cndt-right-links">
                            <strong><?php echo($exfield) ?></strong> <?php echo($lang_level_str) ?>
                        </div>
                        <div class="cndt-expertise-lines">
                            <div class="cndt-expertise-lines-inn"
                                 style="width: <?php echo($lang_percentagefield_val) ?>%;"></div>
                        </div>
                    </div>
                    <?php
                    $exfield_counter++;
                }

            }
        }

        public static function jobsearch_resume_candidate_expertise($candidate_id)
        {
            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_skill_title', true);
            $skill_percentagefield_list = get_post_meta($candidate_id, 'jobsearch_field_skill_percentage', true);
            if (is_array($exfield_list) && sizeof($exfield_list) > 0) { ?>
                <div class="content-icon-wrap">
                    <div class="content-icon">
                        <div style="font-family: jobsearch">&#xe93f</div>
                    </div>
                    <div class="cndt-content-title">
                        <span><?php echo esc_html__('EXPERTISE', 'jobsearch-resume-export') ?></span>
                    </div>
                </div>
                <?php
                $exfield_counter = 0;
                foreach ($exfield_list as $exfield) {
                    $rand_num = rand(1000000, 99999999);
                    $skill_percentagefield_val = isset($skill_percentagefield_list[$exfield_counter]) ? absint($skill_percentagefield_list[$exfield_counter]) : '';
                    $skill_percentagefield_val = $skill_percentagefield_val > 100 ? 100 : $skill_percentagefield_val;
                    ?>
                    <div class="cndt-right-links-wrap">
                        <div class="cndt-right-links">
                            <?php echo($exfield) ?> <?php echo($skill_percentagefield_val) ?>%
                        </div>
                        <div class="cndt-expertise-lines">
                            <div class="cndt-expertise-lines-inn"
                                 style="width: <?php echo($skill_percentagefield_val) ?>%;"></div>
                        </div>
                    </div>
                    <?php $exfield_counter++;
                } ?>
            <?php }
        }

        public static function jobsearch_resume_cand_portfolio($candidate_id)
        {
            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_portfolio_title', true);
            $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_portfolio_image', true);
            $exfield_portfolio_url = get_post_meta($candidate_id, 'jobsearch_field_portfolio_url', true);
            $exfield_portfolio_vurl = get_post_meta($candidate_id, 'jobsearch_field_portfolio_vurl', true);

            if (is_array($exfield_list) && sizeof($exfield_list) > 0) { ?>
                <div class="jobsearch-pdf-main-list">
                    <h2><?php echo esc_html__('Portfolio', 'jobsearch-resume-export') ?></h2>
                    <?php
                    $exfield_counter = 0;
                    foreach ($exfield_list as $exfield) {
                        $portfolio_img = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                        $portfolio_url = isset($exfield_portfolio_url[$exfield_counter]) ? $exfield_portfolio_url[$exfield_counter] : '';
                        $portfolio_vurl = isset($exfield_portfolio_vurl[$exfield_counter]) ? $exfield_portfolio_vurl[$exfield_counter] : '';
                        $file_path = jobsearch_get_cand_portimg_path($candidate_id, $portfolio_img);
                        ?>
                        <div class="jobsearch-pdf-porfolio-img">
                            <?php if (!empty($file_path)) { ?>
                                <img src="<?php echo($file_path) ?>">
                            <?php } ?>
                            <br>
                            <div class="jobsearch-pdf-porfolio-link">
                                <?php if (!empty($exfield)) {
                                    echo $exfield . "<br>";
                                } ?>
                                <?php if (!empty($portfolio_url)) { ?>
                                    <?php echo esc_html__('Portfolio URL: ', 'jobsearch-resume-export'); ?><br>
                                    <a
                                            href="<?php echo($portfolio_url) ?>"><?php echo($portfolio_url); ?></a><br>
                                <?php } ?>
                                <?php if (!empty($portfolio_vurl)) { ?>
                                    <?php echo esc_html__('Video URL: ', 'jobsearch-resume-export'); ?><br>
                                    <a href="<?php echo($portfolio_vurl) ?>"><?php echo($portfolio_vurl); ?></a><br>
                                <?php } ?>
                            </div>
                        </div>
                        <?php
                        $exfield_counter++;
                    }
                    ?>
                </div>
            <?php }
        }
    }
}
global $jobsearch_resume_pdf_default_template;
$jobsearch_resume_pdf_default_template = new jobsearch_candidate_pdf_resume_default_template();