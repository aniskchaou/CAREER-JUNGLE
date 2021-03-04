<?php

require_once dirname(__FILE__) . '/mpdf/autoload.php';

if (!class_exists('addon_jobsearch_export_resume_admin_hooks')) {

    class addon_jobsearch_export_resume_admin_hooks
    {
        public function __construct()
        {
            //add_action('add_meta_boxes', array($this, 'jobsearch_candidate_profile_pdf_meta_box'));
            add_action('admin_footer', array($this, 'jobsearch_admin_resume_export_functions_js'), 100);
            add_action('admin_init', array($this, 'jobsearch_all_admin_candidates_resume_export_submit'), 1);
            add_action('admin_init', array($this, 'jobsearch_all_admin_candidates_resume_export_excel_submit'), 1);
            add_action('admin_footer', array($this, 'jobsearch_single_candpdf_form'), 100);
            add_action('admin_init', array($this, 'jobsearch_resume_export_single_pdf_submit_callback'), 100);
            add_action('admin_head', array($this, 'jobsearch_resume_export_get_post_type_callback'), 100);
            add_action('jobsearch_export_btns_list_admin', array($this, 'jobsearch_export_btns_list_admin_callback'), 10, 2);
            add_action('jobsearch_export_selection_aplicnts_admin', array($this, 'jobsearch_export_selection_aplicnts_admin_callback'), 10, 2);
            add_action('jobsearch_export_select_all_applicnts_admin', array($this, 'jobsearch_export_select_all_applicnts_admin'), 10, 2);
            add_filter('jobsearch_admin_change_package_types', array($this, 'jobsearch_admin_change_package_types_callback'), 10, 1);
            add_filter('jobsearch_pkg_admin_resume_meta_fields', array($this, 'jobsearch_pkg_admin_resume_meta_fields_callback'), 10, 2);
            add_action('jobsearch_pkg_admin_descriptions_after', array($this, 'jobsearch_pkg_admin_descriptions_after_callback'), 10);
        }

        public function jobsearch_resume_export_get_post_type_callback()
        {
            global $post, $post_type;
            if ($post != '') {
                $post_type = $post->post_type;
            }
        }

        public function jobsearch_resume_export_single_pdf_submit_callback()
        {
            global $jobsearch_resume_export;

            $flag = apply_filters('jobsearch_disable_resume_export', true);
            if ($flag == false) {
                return false;
            }
            //cand_default_resume
            $stylesheet = file_get_contents($jobsearch_resume_export->jobsearch_resume_export_get_path('css/jobsearch-mpdf-style.css'));

            if (isset($_POST['jobsearch_cand_profile_btn'])) {
                $candidate_id = $_POST['jobsearch_cand_id'];
                $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
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
                $user_email = isset($user_obj->user_email) ? $user_obj->user_email : '';
                //
                $jobsearch_candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                $candidate_company_str = '';
                if ($jobsearch_candidate_jobtitle != '') {
                    $candidate_company_str .= $jobsearch_candidate_jobtitle;
                }
                // Extra Fields
                $user_def_avatar_url = jobsearch_candidate_img_url_comn($candidate_id);
                $profile_image = $user_def_avatar_url;
                $user_firstname = isset($user_obj->first_name) ? $user_obj->first_name : '';
                $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
                //
                $phone_number = get_post_meta($candidate_id, 'jobsearch_field_user_phone', true);
                $cand_pdf_package = get_post_meta($candidate_id, 'jobsearch_field_user_pdf_package', true);
                if (empty($cand_pdf_package)) {
                    update_post_meta($candidate_id, 'jobsearch_field_user_pdf_package', 'pdf-package-default');
                }
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
                                <span><?php echo esc_html__('PHONE:', 'jobsearch-resume-export') ?></span>
                                <a href="tel:<?php echo($phone_number) ?>"><?php echo($phone_number) ?></a>
                                <span><?php echo esc_html__('EMAIL:', 'jobsearch-resume-export') ?></span>
                                <a href="mailto:<?php echo($user_email) ?>"><?php echo($user_email) ?></a>
                            </div>
                        </div>
                    </div>

                    <div class="cndt-left-content">
                        <!--Candidate Custom Fields-->
                        <?php echo self::jobsearch_resume_candidate_custom_fields($candidate_id) ?>
                        <?php
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
                        <?php } ?>

                        <?php
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
                        <?php } ?>
                        <?php
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
                        ?>
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

                        <?php
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
                        <?php } ?>

                        <div class="sidebar-spacer"></div>

                        <?php
                        $skills_list = jobsearch_resume_export_job_get_all_skills($candidate_id, '', '', '', '', '<div>', '</div>', 'candidate');
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
                        <?php } ?>

                    </div>
                </div>
                <?php
                $pdf_html = ob_get_clean();
                $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
                $mpdf->WriteHTML($pdf_html, \Mpdf\HTMLParserMode::HTML_BODY);
                $mpdf->Output($user_firstname . '-' . date('dmy') . "-" . $candidate_id . '.pdf', 'D');
                exit();
            }
        }

        public function jobsearch_single_candpdf_form()
        {
            global $post, $post_type;
            $flag = apply_filters('jobsearch_disable_resume_export', true);
            //
            if ($flag == false) {
                return false;
            }

            if ($post_type != "" && $post_type == 'candidate') {
                ob_start();
                ?>
                <form class="jobsearch-cand-pdf-form" style="display: none;"
                      action="<?php echo esc_url(admin_url('post.php?post=' . $post->ID . '&action=edit')); ?>"
                      method="post">
                    <input type="text" name="jobsearch_cand_id" value="<?php echo($post->ID) ?>">
                    <input type="submit" name="jobsearch_cand_profile_btn" value="submit">
                </form>
                <?php
                $html = ob_get_clean();
                echo $html;
            }
        }

        public function jobsearch_candidate_profile_pdf_meta_box()
        {
            add_meta_box('jobsearch-cand-profile-pdf', esc_html__('Profile PDF', 'jobsearch-resume-export'), array($this, 'jobsearch_candidate_bkmeta_profilePDF'), 'candidate', 'side');
        }

        public function jobsearch_candidate_bkmeta_profilePDF()
        {
            $flag = apply_filters('jobsearch_disable_resume_export', true);
            if ($flag == false) {
                return false;
            } ?>
            <a href="javascript:void(0)"
               class="button button-primary jobsearch-cand-profile-pdf"><?php echo esc_html__('Show in PDF', 'jobsearch-resume-export') ?></a>
            <?php
            $html = ob_get_clean();
            echo $html;
        }

        public function jobsearch_pkg_admin_descriptions_after_callback()
        {
            ob_start(); ?>
            <li>
                <strong>
                    <?php esc_html_e('Candidate PDF Resume', 'jobsearch-resume-export') ?>
                    :</strong> <?php esc_html_e('This package is useful for candidates to have multiple resume designs.', 'jobsearch-resume-export') ?>
            </li>
            <?php
            $html = ob_get_clean();
            echo $html;
        }

        public function jobsearch_pkg_admin_resume_meta_fields_callback($package_type, $_post_id)
        {
            global $jobsearch_form_fields, $jobsearch_resume_export, $jobsearch_plugin_options;
            $cand_default_resume = isset($jobsearch_plugin_options['cand_default_resume']) ? $jobsearch_plugin_options['cand_default_resume'] : '';


            ob_start();
            wp_enqueue_style('jobsearch-admin-pdf-export-style');
            $args = array(
                'post_type' => 'package',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'order' => 'ASC',
                'orderby' => 'title',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_package_type',
                        'value' => 'cand_resume',
                        'compare' => '=',
                    ),
                ),
            );
            $pkgs_query = new WP_Query($args);
            $pdfs_posts = $pkgs_query->posts;

            $saved_templates = [];
            foreach ($pdfs_posts as $pdf_info) {
                $saved_templates[] = get_post_meta($pdf_info->ID, 'jobsearch_field_cand_pbase_pdfs', true);
            }
            /*
             * All PDF Templates
             * */
            $templates_arr_list = [
                'default' => 'default',
                'Template 1' => 'Template 1',
                'Template 2' => 'Template 2',
                'Template 3' => 'Template 3',
                'Template 4' => 'Template 4',
                'Template 5' => 'Template 5',
                'Template 6' => 'Template 6',
                'Template 7' => 'Template 7',
                'Template 8' => 'Template 8',
                'Template 9' => 'Template 9',
                'Template 10' => 'Template 10',
                'Template 11' => 'Template 11',
                'Template 12' => 'Template 12',
                'Template 13' => 'Template 13',
                'Template 14' => 'Template 14',
                'Template 15' => 'Template 15',
                'Template 16' => 'Template 16',
                'Template 17' => 'Template 17',
                'Template 18' => 'Template 18',
                'Template 19' => 'Template 19',
                'Template 20' => 'Template 20',
            ];

            /*
             * Remaining Templates (which are not assigned)
             * */
            $unassigned_pdfs = array_diff($templates_arr_list, $saved_templates);
            ?>
            <div id="cand_resume_package_fields" class="job-package-fields specific-pkges-fields"
                 style="display: <?php echo($package_type == 'cand_resume' ? 'block' : 'none') ?>;">
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Select PDF Templates', 'jobsearch-resume-export') ?></label>
                    </div>
                    <div class="jobsearch-candidate-pdf-list">
                        <ul>
                            <?php
                            foreach ($templates_arr_list as $templte_info) {
                                $temp_img_thumb = '';
                                $temp_img_large = '';

                                if ($templte_info == 'default') {
                                    $temp_img_thumb = 'cv-resume-thumb-default.jpg';
                                    $temp_img_large = 'cv-resume-large-default.jpg';
                                } else if ($templte_info == 'Template 1') {
                                    $temp_img_thumb = 'cv-resume-thumb-1.jpg';
                                    $temp_img_large = 'cv-resume-large-1.jpg';
                                } else if ($templte_info == 'Template 2') {
                                    $temp_img_thumb = 'cv-resume-thumb-2.jpg';
                                    $temp_img_large = 'cv-resume-large-2.jpg';
                                } else if ($templte_info == 'Template 3') {
                                    $temp_img_thumb = 'cv-resume-thumb-3.jpg';
                                    $temp_img_large = 'cv-resume-large-3.jpg';
                                } else if ($templte_info == 'Template 4') {
                                    $temp_img_thumb = 'cv-resume-thumb-4.jpg';
                                    $temp_img_large = 'cv-resume-large-4.jpg';
                                } else if ($templte_info == 'Template 5') {
                                    $temp_img_thumb = 'cv-resume-thumb-5.jpg';
                                    $temp_img_large = 'cv-resume-large-5.jpg';
                                } else if ($templte_info == 'Template 6') {
                                    $temp_img_thumb = 'cv-resume-thumb-6.jpg';
                                    $temp_img_large = 'cv-resume-large-6.jpg';
                                } else if ($templte_info == 'Template 7') {
                                    $temp_img_thumb = 'cv-resume-thumb-7.jpg';
                                    $temp_img_large = 'cv-resume-large-7.jpg';
                                } else if ($templte_info == 'Template 8') {
                                    $temp_img_thumb = 'cv-resume-thumb-8.jpg';
                                    $temp_img_large = 'cv-resume-large-8.jpg';
                                } else if ($templte_info == 'Template 9') {
                                    $temp_img_thumb = 'cv-resume-thumb-9.jpg';
                                    $temp_img_large = 'cv-resume-large-9.jpg';
                                } else if ($templte_info == 'Template 10') {
                                    $temp_img_thumb = 'cv-resume-thumb-10.jpg';
                                    $temp_img_large = 'cv-resume-large-10.jpg';
                                } else if ($templte_info == 'Template 11') {
                                    $temp_img_thumb = 'cv-resume-thumb-11.jpg';
                                    $temp_img_large = 'cv-resume-large-11.jpg';
                                } else if ($templte_info == 'Template 12') {
                                    $temp_img_thumb = 'cv-resume-thumb-12.jpg';
                                    $temp_img_large = 'cv-resume-large-12.jpg';
                                } else if ($templte_info == 'Template 13') {
                                    $temp_img_thumb = 'cv-resume-thumb-13.jpg';
                                    $temp_img_large = 'cv-resume-large-13.jpg';
                                } else if ($templte_info == 'Template 14') {
                                    $temp_img_thumb = 'cv-resume-thumb-14.jpg';
                                    $temp_img_large = 'cv-resume-large-14.jpg';
                                } else if ($templte_info == 'Template 15') {
                                    $temp_img_thumb = 'cv-resume-thumb-15.jpg';
                                    $temp_img_large = 'cv-resume-large-15.jpg';
                                } else if ($templte_info == 'Template 16') {
                                    $temp_img_thumb = 'cv-resume-thumb-16.jpg';
                                    $temp_img_large = 'cv-resume-large-16.jpg';
                                } else if ($templte_info == 'Template 17') {
                                    $temp_img_thumb = 'cv-resume-thumb-17.jpg';
                                    $temp_img_large = 'cv-resume-large-17.jpg';
                                } else if ($templte_info == 'Template 18') {
                                    $temp_img_thumb = 'cv-resume-thumb-18.jpg';
                                    $temp_img_large = 'cv-resume-large-18.jpg';
                                } else if ($templte_info == 'Template 19') {
                                    $temp_img_thumb = 'cv-resume-thumb-19.jpg';
                                    $temp_img_large = 'cv-resume-large-19.jpg';
                                } else if ($templte_info == 'Template 20') {
                                    $temp_img_thumb = 'cv-resume-thumb-20.jpg';
                                    $temp_img_large = 'cv-resume-large-20.jpg';
                                }


                                if ($cand_default_resume != $templte_info) { ?>
                                    <li class="col-md-3">
                                        <figure>
                                            <a href="javascript:void(0)" data-template="<?php echo($templte_info) ?>"
                                               class="jobsearch-activate-pdf-template">
                                                <img src="<?php echo $jobsearch_resume_export->jobsearch_pdf_resume_get_url('/cv-resume-thumb/' . $temp_img_thumb) ?>">
                                            </a>
                                            <?php if (jobsearch_pdf_pckg_pdf_templates($unassigned_pdfs, $templte_info) != true) { ?>
                                                <div class="jobsearch-candidate-pdf-locked">
                                                    <a href="javascript:void(0)"
                                                       class="fa fa-lock jobsearch-show-pdf-template-pckgs"></a>
                                                </div>
                                            <?php } ?>
                                        </figure>
                                        <div class="jobsearch-candidate-pdf-list-inner">
                                            <a target="_blank"
                                               href="<?php echo $jobsearch_resume_export->jobsearch_pdf_resume_get_url('/cv-resume-large/' . $temp_img_large) ?>"
                                               class="jobsearch-candidate-pdf-preview jobsearch-tooltipcon"
                                               title="Preview"><i
                                                        class="fa fa-eye"></i></a>

                                            <a href="javascript:void(0)" class="jobsearch-candidate-pdf-buy">
                                                <i class="fa fa-dollar"></i><?php echo jobsearch_pdf_pckg_pdf_templates($unassigned_pdfs, $templte_info) != true ? esc_html__('Package Already used', 'jobsearch-resume-export') : esc_html__('Click image to select Template', 'jobsearch-resume-export') ?>
                                            </a>
                                        </div>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
            $opt_array = array(
                'id' => 'cand-pbase-pdfs',
                'name' => "cand_pbase_pdfs",
                'std' => '',
            );
            $jobsearch_form_fields->input_hidden_field($opt_array);
            $html = ob_get_clean();
            echo $html;
        }

        public function jobsearch_admin_change_package_types_callback($pckg_types_options)
        {
            $pckg_types_options['cand_resume'] = esc_html__('Candidate PDF Resume', 'jobsearch-resume-export');
            return $pckg_types_options;
        }

        public function jobsearch_export_select_all_applicnts_admin($_job_id, $job_applicants_list)
        {
            ob_start();
            wp_enqueue_style('jobsearch-admin-pdf-export-style');
            ?>
            <div class="sort-select-all-aplicnt-opts">
                <input type="checkbox" data-job-id="<?php echo($_job_id) ?>"
                       class="select-all-job-applicnts" id="select-all-job-applicnts-<?php echo($_job_id) ?>">
                <label for="select-all-job-applicnts-<?php echo($_job_id) ?>"><?php echo esc_html__('Select All', 'jobsearch-resume-export') ?></label>
            </div>
            <?php
            $html = ob_get_clean();
            echo $html;
        }

        public function jobsearch_export_selection_aplicnts_admin_callback($_candidate_id, $_job_id)
        {
            ob_start();
            wp_enqueue_style('jobsearch-admin-pdf-export-style');
            ?>
            <input type="checkbox" class="jobsearch-applicant-id" name="jobsearch_applicant_id[]"
                   data-job-id="<?php echo($_job_id) ?>" value="<?php echo($_candidate_id) ?>">
            <?php
            $html = ob_get_clean();
            echo $html;
        }

        public function jobsearch_export_btns_list_admin_callback($_job_id, $job_applicants_list)
        {
            wp_enqueue_style('jobsearch-admin-pdf-export-style');
            ob_start(); ?>
            <li class="admin-applicants-export-options" style="display: none">
                <div class="applicnt-count-box excel-export">
                    <a href="javascript:void(0)" data-job-id="<?php echo($_job_id) ?>"
                       class="jobsearch-cand-export-excel"><?php esc_html_e('Export to Excel', 'jobsearch-resume-export') ?></a>
                </div>
            </li>
            <li class="admin-applicants-export-options" style="display: none">
                <div class="applicnt-count-box pdf-export">
                    <a href="javascript:void(0)" data-job-id="<?php echo($_job_id) ?>"
                       class="jobsearch-cand-export-pdf"><?php esc_html_e('Export to PDF', 'jobsearch-resume-export') ?></a>
                </div>
            </li>
            <?php
            $html = ob_get_clean();
            echo apply_filters('jobsearch_allaplics_admin_export_cv_btns', $html, $_job_id);
        }

        /*
         * Export to excel zip (admin side)
         * */
        public function jobsearch_all_admin_candidates_resume_export_excel_submit()
        {
            global $rand_num, $jobsearch_plugin_options, $sitepress;
            $candidate_site_slug = isset($jobsearch_plugin_options['candidate_rewrite_slug']) && $jobsearch_plugin_options['candidate_rewrite_slug'] != '' ? $jobsearch_plugin_options['candidate_rewrite_slug'] : 'candidate';

            if (isset($_POST['excel_generate_admin_form_submit'])) {

                header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                header("Content-Disposition: attachment; filename=export-candidate.xlsx");
                header('Cache-Control: max-age=0');
                // If you're serving to IE 9, then the following may be needed
                header('Cache-Control: max-age=1');
                // If you're serving to IE over SSL, then the following may be needed
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
                header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header('Pragma: public');
                //
                include_once("excel/xlsxwriter.class.php");

                $header = array(
                    'First Name' => 'string',//text
                    'Last Name' => 'string',
                    'Email' => 'string',
                    'Profile URL' => 'string',
                    'Date of Birth' => 'string',
                    'Phone' => 'string',
                    'Sector' => 'string',
                    'Job Title' => 'string',
                    'Salary' => 'string',
                    'Description' => 'string',
                    'Facebook Link' => 'string',
                    'Twitter Link' => 'string',
                    'Linkedin Link' => 'string',
                    'Dribbble Link' => 'string',
                    'Country' => 'string',
                    'State' => 'string',
                    'City' => 'string',
                    'Postal Code' => 'string',
                    'Full Address' => 'string',
                    'Education' => 'string',
                    'Experience' => 'string',
                    'HONORS & AWARDS' => 'string',
                    'Custom Fields' => 'string',
                );

                $rows = array();
                $rand_num = rand(10000000, 99999999);
                $totl_ids = explode(',', $_POST['jobsearch_excel_admin_cand_id']);
                $custom_all_fields = get_option('jobsearch_custom_field_candidate');
                foreach ($totl_ids as $key => $candidate_id) {

                    $can_post_obj = get_post($candidate_id);
                    $candidate_content = isset($can_post_obj->post_content) ? $can_post_obj->post_content : '';
                    $candidate_content = apply_filters('the_content', $candidate_content);
                    $user_profile_url = isset($can_post_obj->post_name) ? $can_post_obj->post_name : '';

                    $user_id = jobsearch_get_candidate_user_id($candidate_id);
                    $user_obj = get_user_by('ID', $user_id);
                    $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
                    $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);

                    $user_bio = isset($user_obj->description) ? $user_obj->description : '';
                    $user_email = isset($user_obj->user_email) ? $user_obj->user_email : '';
                    $user_firstname = isset($user_obj->first_name) ? $user_obj->first_name : '';
                    $user_lastname = isset($user_obj->last_name) ? $user_obj->last_name : '';

                    $user_dob_whole = get_post_meta($candidate_id, 'jobsearch_field_user_dob_whole', true);
                    $user_phone = get_post_meta($candidate_id, 'jobsearch_field_user_phone', true);
                    //
                    $jobsearch_candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                    $candidate_salary = jobsearch_candidate_current_salary($candidate_id);
                    $sectors = wp_get_post_terms($candidate_id, 'sector');
                    $candidate_sector = isset($sectors[0]->term_id) ? $sectors[0]->term_id : '';
                    //
                    $sector_name = get_term($candidate_sector, 'sector');
                    $candidate_sector_name = isset($sector_name->name) ? $sector_name->name : '';

                    $user_facebook_url = get_post_meta($candidate_id, 'jobsearch_field_user_facebook_url', true);
                    $user_facebook_url = esc_url($user_facebook_url);
                    $user_twitter_url = get_post_meta($candidate_id, 'jobsearch_field_user_twitter_url', true);
                    $user_twitter_url = esc_url($user_twitter_url);
                    $user_google_plus_url = get_post_meta($candidate_id, 'jobsearch_field_user_google_plus_url', true);
                    $user_youtube_url = get_post_meta($candidate_id, 'jobsearch_field_user_youtube_url', true);
                    $user_youtube_url = esc_url($user_youtube_url);
                    $user_dribbble_url = get_post_meta($candidate_id, 'jobsearch_field_user_dribbble_url', true);
                    $user_dribbble_url = esc_url($user_dribbble_url);
                    $user_linkedin_url = get_post_meta($candidate_id, 'jobsearch_field_user_linkedin_url', true);
                    $user_linkedin_url = esc_url($user_linkedin_url);
                    //
                    $country = get_post_meta($candidate_id, 'jobsearch_field_location_location1', true);
                    $state = get_post_meta($candidate_id, 'jobsearch_field_location_location2', true);
                    $city = get_post_meta($candidate_id, 'jobsearch_field_location_location3', true);
                    $candidate_address = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);
                    $loc_postalcode = get_post_meta($candidate_id, 'jobsearch_field_location_postalcode', true);
                    //
                    $rows[] = array(
                        $user_firstname,
                        $user_lastname,
                        $user_email,
                        home_url('/' . $candidate_site_slug . '/') . $user_profile_url,
                        $user_dob_whole,
                        $user_phone,
                        $candidate_sector_name,
                        $jobsearch_candidate_jobtitle,
                        $candidate_salary,
                        $user_bio,
                        $user_facebook_url,
                        $user_twitter_url,
                        $user_linkedin_url,
                        $user_dribbble_url,
                        $country,
                        $state,
                        $city,
                        $loc_postalcode,
                        $candidate_address,
                    );
                    //
                    $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_title', true);
                    $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_education_description', true);
                    $education_academyfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_academy', true);
                    $education_yearfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_year', true);
                    //
                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                        $exfield_counter = 0;
                        $edu_detail = array();
                        foreach ($exfield_list as $edu_index => $exfield) {

                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                            $education_academyfield_val = isset($education_academyfield_list[$exfield_counter]) ? $education_academyfield_list[$exfield_counter] : '';
                            $education_yearfield_val = isset($education_yearfield_list[$exfield_counter]) ? $education_yearfield_list[$exfield_counter] : '';
                            //
                            $edu_detail[] = $education_yearfield_val . "\n" . $exfield . "\n" . $exfield_val . "\n";
                            $exfield_counter++;
                        }
                        $rows[$key][] = implode(" ", $edu_detail);
                    }

                    $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_title', true);
                    $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_experience_description', true);
                    $experience_start_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_start_date', true);
                    $experience_end_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_end_date', true);
                    $experience_prsnt_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_date_prsnt', true);
                    $experience_company_field_list = get_post_meta($candidate_id, 'jobsearch_field_experience_company', true);

                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                        $exfield_counter = 0;
                        $exp_detail = array();
                        foreach ($exfield_list as $exp_index => $exfield) {

                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                            $experience_start_datefield_val = isset($experience_start_datefield_list[$exfield_counter]) ? $experience_start_datefield_list[$exfield_counter] : '';
                            $experience_end_datefield_val = isset($experience_end_datefield_list[$exfield_counter]) ? $experience_end_datefield_list[$exfield_counter] : '';
                            $experience_prsnt_datefield_val = isset($experience_prsnt_datefield_list[$exfield_counter]) ? $experience_prsnt_datefield_list[$exfield_counter] : '';
                            $experience_end_companyfield_val = isset($experience_company_field_list[$exfield_counter]) ? $experience_company_field_list[$exfield_counter] : '';
                            $exp_detail[] = ($experience_start_datefield_val != '' ? date('Y', strtotime($experience_start_datefield_val)) : '') . (' - ') . esc_html__('Present', 'jobsearch-resume-export') . "\n" . $experience_end_companyfield_val . "\n" . jobsearch_esc_html($exfield) . "\n";
                            $exfield_counter++;
                        }
                        $rows[$key][] = implode(" ", $exp_detail);
                    }

                    $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_title', true);
                    $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_award_description', true);
                    $award_yearfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_year', true);
                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                        $rand_num = rand(1000000, 99999999);
                        $exfield_counter = 0;
                        $award_detail = [];
                        foreach ($exfield_list as $award_index => $exfield) {
                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                            $award_yearfield_val = isset($award_yearfield_list[$exfield_counter]) ? $award_yearfield_list[$exfield_counter] : '';
                            $award_detail[] = jobsearch_esc_html($award_yearfield_val) . "\n" . jobsearch_esc_html($exfield) . "\n" . jobsearch_esc_html($exfield_val) . "\n";
                            $exfield_counter++;
                        }
                        $rows[$key][] = implode(" ", $award_detail);
                    }
                    /*
                     * Custom Fields start
                     * */
                    if (!empty($custom_all_fields)) {
                        $fields_data = array();
                        $cust_field_data = array();
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
                            if ($type == 'checkbox') {
                                $drop_down_arr = array();
                                $cut_field_flag = 0;
                                foreach ($info['options']['value'] as  $cus_field_options_value) {
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

                                if (is_array($field_value) && count($field_value) > 0) {
                                    foreach ($field_value as $val) {
                                        $cust_field_data[] = jobsearch_esc_html($field_label) . "-" . jobsearch_esc_html($val);
                                    }
                                } else {
                                    $field_value = $type == 'date' ? date_i18n($info['date-format'], $field_value) : $field_value;
                                    $cust_field_data[] = jobsearch_esc_html($field_label) . "-" . jobsearch_esc_html($field_value);
                                }

                            }
                        }
                        foreach ($fields_data as $fields) {
                            $cust_field_data[] = jobsearch_esc_html($fields['label']) . "-" . jobsearch_esc_html($fields['value']);
                        }
                    }
                    $rows[$key][] = implode(" ", $cust_field_data);
                }

                $writer = new XLSXWriter();
                $writer->writeSheetHeader('Sheet1', $header);
                foreach ($rows as $row) {
                    $writer->writeSheetRow('Sheet1', $row);
                }
                echo $writer->writeToString();
                exit();
            }
        }

        /*
        * Export to PDF zip (admin side)
        * */

        public function jobsearch_all_admin_candidates_resume_export_submit()
        {
            global $rand_num,
                   $jobsearch_resume_pdf_default_template,
                   $jobsearch_resume_pdf_template_one,
                   $jobsearch_resume_pdf_template_two,
                   $jobsearch_resume_pdf_template_three,
                   $jobsearch_resume_pdf_template_four,
                   $jobsearch_resume_pdf_template_five,
                   $jobsearch_resume_pdf_template_six,
                   $jobsearch_resume_pdf_template_seven,
                   $jobsearch_resume_pdf_template_eight,
                   $jobsearch_resume_pdf_template_nine,
                   $jobsearch_resume_pdf_template_ten,
                   $jobsearch_resume_pdf_template_eleven,
                   $jobsearch_resume_pdf_template_twelve,
                   $jobsearch_resume_pdf_template_thirteen,
                   $jobsearch_resume_pdf_template_fourteen,
                   $jobsearch_resume_pdf_template_fifteen,
                   $jobsearch_resume_pdf_template_sixteen,
                   $jobsearch_resume_pdf_template_seventeen,
                   $jobsearch_resume_pdf_template_eighteen,
                   $jobsearch_resume_pdf_template_nineteen,
                   $jobsearch_resume_pdf_template_twenty;

            if (isset($_POST['admin_pdf_generate_form_submit'])) {
                $rand_num = rand(10000000, 99999999);
                $_job_id = isset($_POST['jobsearch_pdf_admin_job_id']) && !empty($_POST['jobsearch_pdf_admin_job_id']) ? $_POST['jobsearch_pdf_admin_job_id'] : '';
                $totl_ids = explode(',', $_POST['jobsearch_pdf_admin_cand_id']);
                foreach ($totl_ids as $candidate_id) {

                    $cand_user_id = jobsearch_get_candidate_user_id($candidate_id);
                    $saved_template = get_option('jobsearch_selected_pdf_template_' . $cand_user_id);
                    $saved_template = empty($saved_template) ? 'default' : $saved_template;

                    if ($saved_template == 'default') {
                        $jobsearch_resume_pdf_default_template->jobsearch_candidate_resume_bulk_export_template_default($candidate_id);
                    } else if ($saved_template == 'Template 1') {
                        $jobsearch_resume_pdf_template_one->jobsearch_candidate_resume_bulk_export_template_one($candidate_id);
                    } else if ($saved_template == 'Template 2') {
                        $jobsearch_resume_pdf_template_two->jobsearch_candidate_resume_bulk_export_template_two($candidate_id);
                    } else if ($saved_template == 'Template 3') {
                        $jobsearch_resume_pdf_template_three->jobsearch_candidate_resume_bulk_export_template_three($candidate_id);
                    } else if ($saved_template == 'Template 4') {
                        $jobsearch_resume_pdf_template_four->jobsearch_candidate_resume_bulk_export_template_four($candidate_id);
                    } else if ($saved_template == 'Template 5') {
                        $jobsearch_resume_pdf_template_five->jobsearch_candidate_resume_bulk_export_template_five($candidate_id);
                    } else if ($saved_template == 'Template 6') {
                        $jobsearch_resume_pdf_template_six->jobsearch_candidate_resume_bulk_export_template_six($candidate_id);
                    } else if ($saved_template == 'Template 7') {
                        $jobsearch_resume_pdf_template_seven->jobsearch_candidate_resume_bulk_export_template_seven($candidate_id);
                    } else if ($saved_template == 'Template 8') {
                        $jobsearch_resume_pdf_template_eight->jobsearch_candidate_resume_bulk_export_template_eight($candidate_id);
                    } else if ($saved_template == 'Template 9') {
                        $jobsearch_resume_pdf_template_nine->jobsearch_candidate_resume_bulk_export_template_nine($candidate_id);
                    } else if ($saved_template == 'Template 10') {
                        $jobsearch_resume_pdf_template_ten->jobsearch_candidate_resume_bulk_export_template_ten($candidate_id);
                    } else if ($saved_template == 'Template 11') {
                        $jobsearch_resume_pdf_template_eleven->jobsearch_candidate_resume_bulk_export_template_eleven($candidate_id);
                    } else if ($saved_template == 'Template 12') {
                        $jobsearch_resume_pdf_template_twelve->jobsearch_candidate_resume_bulk_export_template_twelve($candidate_id);
                    } else if ($saved_template == 'Template 13') {
                        $jobsearch_resume_pdf_template_thirteen->jobsearch_candidate_resume_bulk_export_template_thirteen($candidate_id);
                    } else if ($saved_template == 'Template 14') {
                        $jobsearch_resume_pdf_template_fourteen->jobsearch_candidate_resume_bulk_export_template_fourteen($candidate_id);
                    } else if ($saved_template == 'Template 15') {
                        $jobsearch_resume_pdf_template_fifteen->jobsearch_candidate_resume_bulk_export_template_fifteen($candidate_id);
                    } else if ($saved_template == 'Template 16') {
                        $jobsearch_resume_pdf_template_sixteen->jobsearch_candidate_resume_bulk_export_template_sixteen($candidate_id);
                    } else if ($saved_template == 'Template 17') {
                        $jobsearch_resume_pdf_template_seventeen->jobsearch_candidate_resume_bulk_export_template_seventeen($candidate_id);
                    } else if ($saved_template == 'Template 18') {
                        $jobsearch_resume_pdf_template_eighteen->jobsearch_candidate_resume_bulk_export_template_eighteen($candidate_id);
                    } else if ($saved_template == 'Template 19') {
                        $jobsearch_resume_pdf_template_nineteen->jobsearch_candidate_resume_bulk_export_template_nineteen($candidate_id);
                    } else if ($saved_template == 'Template 20') {
                        $jobsearch_resume_pdf_template_twenty->jobsearch_candidate_resume_bulk_export_template_twenty($candidate_id);
                    }
                }
                self::zipFolderResumeExport($totl_ids, $_job_id);
            }
        }

        public static function zipFolderResumeExport($totl_ids = array(), $_job_id)
        {
            global $jobsearch_resume_export, $jobsearch_pdf_temp_upload_file;

            ob_start();
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

            $job_det = get_post($_job_id);
            $file_name = !empty($job_det) ? $job_det->post_name . '.zip' : 'selected-candidates-cvs.zip';
            $zip_path = $jobsearch_resume_export->jobsearch_resume_export_get_path($file_name);

            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                $zip->open($jobsearch_resume_export->jobsearch_resume_export_get_path($file_name), ZipArchive::CREATE | ZipArchive::OVERWRITE);
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($location),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($location) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                $zip->close();
            }

            foreach ($totl_ids as $candidate_id) {
                $user_id = jobsearch_get_candidate_user_id($candidate_id);
                $user_obj = get_user_by('ID', $user_id);
                $user_firstname = isset($user_obj->first_name) ? $user_obj->first_name : '';
                $file = $location . $user_firstname . '-' . date('dmy') . "-" . $candidate_id . '.pdf';
                if (!class_exists('ZipArchive')) {
                    self::CompressFiles($location, $file, $zip_path, 'zip');
                }
            }

            if (file_exists($location)) {
                self::delete_directory($location);
            }

            header("Content-type: application/force-download");
            header("Content-Disposition: attachment; filename=" . $file_name);
            header('Content-Length: ' . filesize($zip_path));
            ob_end_clean();
            readfile($zip_path);
            unlink($zip_path);
            exit;
        }

        public static function CompressFiles($dir, $files, $zip_path, $archiver)
        {

            $path = scandir($dir);
            $files = array_values(array_diff($path, array('.', '..')));

            $list = array();
            foreach ($files as $file) {
                $list[] = $dir . "/" . $file;
            }

            switch ($archiver) {
                case 'zip':
                    require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
                    $archive = new PclZip($zip_path);
                    if (!$archive->Create($list, '', $dir)) {
                        // return $this->SetError('errArchive');
                    }
                    break;
            }
            return $path;
        }

        public static function delete_directory($dirname)
        {
            if (is_dir($dirname))
                $dir_handle = opendir($dirname);
            if (!$dir_handle)
                return false;
            while ($file = readdir($dir_handle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($dirname . "/" . $file))
                        unlink($dirname . "/" . $file);
                    else
                        self::delete_directory($dirname . '/' . $file);
                }
            }
            closedir($dir_handle);
            rmdir($dirname);
            return true;
        }

        public static function jobsearch_resume_candidate_custom_fields($candidate_id)
        {
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
                    foreach ($custom_all_fields as $info) {
                        if (!empty($info['name'])) {
                            $field_value = get_post_meta($candidate_id, $info['name'], true);
                            ?>
                            <div class="cndt-custom-field-inner">
                                <?php if (!empty($info['icon'])) { ?>
                                    <div class="cndt-custom-field-icon-wrap">
                                        <div class="cndt-custom-field-icon">
                                            <div><?php echo jobsearch_get_font_code($info['icon']) ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="cndt-custom-field-text">
                                    <div class="cndt-custom-field-title"><?php echo($info['label']) ?></div>
                                    <?php if (is_array($field_value) && count($field_value) > 0) {
                                        foreach ($field_value as $val) { ?>
                                            <div class="cndt-custom-field-sub"><?php echo($val) ?></div>
                                        <?php }
                                    } else { ?>
                                        <div class="cndt-custom-field-sub"><?php echo($field_value) ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php }
                    } ?>
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
            $skills_list = jobsearch_resume_export_job_get_all_skills($candidate_id, '', '', '', '', '<div>', '</div>', 'candidate');
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

        public function jobsearch_admin_resume_export_functions_js()
        {
            global $post;
            if (!empty($post) && $post->post_type == 'candidate' || (isset($_GET['page']) && $_GET['page'] == 'jobsearch-applicants-list')) { ?>
                <form id="pdf_generate_admin_form" method="post" enctype="multipart/form-data" style="display: none;">
                    <input type="text" name="jobsearch_pdf_admin_cand_id" class="jobsearch_pdf_admin_cand_id" value="">
                    <input type="text" name="jobsearch_pdf_admin_job_id" class="jobsearch_pdf_admin_job_id" value="">
                    <input type="submit" name="admin_pdf_generate_form_submit" value="Submit">
                </form>

                <form id="excel_generate_admin_form" method="post" enctype="multipart/form-data" style="display: none;">
                    <input type="text" name="jobsearch_excel_admin_cand_id" value="">
                    <input type="text" name="jobsearch_excel_admin_job_id" value="">
                    <input type="submit" name="excel_generate_admin_form_submit" value="Submit">
                </form>

                <script type="text/javascript">
                    /*
                     *  Append html next to the filters, when post type is candidate.
                     * */
                    var _html = '<div class="alignleft" style="display: none;" id="jobsearch-export-options"><select id="jobsearch-cand-export"><option selected="selected" value="0">' + jobsearch_export_vars.export_title + '</option><option value="jobsearch-cand-export-pdf">' + jobsearch_export_vars.export_to_pdf + '</option><option value="jobsearch-cand-export-excel">' + jobsearch_export_vars.export_to_excel + '</option><option value="jobsearch-cand-uploaded-resumes"><?php echo esc_js(esc_html__('User Uploaded Resumes', 'jobsearch-resume-export')) ?></option></select></div>';
                    jQuery(document).find(".tablenav.top").find(".alignleft").last().after(_html);

                    jQuery(document).on('click', '#cb-select-all-1', function () {
                        var _this = jQuery(this);
                        if (_this.is(':checked')) {
                            jQuery("#jobsearch-export-options").show();
                        } else {
                            jQuery("#jobsearch-export-options").hide();
                        }
                    });

                    /*
                    * Select All CVs against jobs
                    * */
                    jQuery(document).on('click', '.select-all-job-applicnts', function () {
                        var _this = jQuery(this), _job_id = jQuery(this).attr('data-job-id');
                        if (_this.is(':checked')) {
                            jQuery(".total-aplicnt-cta-" + _job_id + " .admin-applicants-export-options").show();
                            jQuery('.sjob-aplicants-' + _job_id + ' input[type="checkbox"][name^="jobsearch_applicant_id[]"]').prop('checked', true);
                            jQuery('.sjob-aplicants-' + _job_id + ' input[type="checkbox"][name^="jobsearch_applicant_id[]"]').trigger('change');
                        } else {
                            jQuery(".total-aplicnt-cta-" + _job_id + " .admin-applicants-export-options").hide();
                            jQuery('.sjob-aplicants-' + _job_id + ' input[type="checkbox"][name^="jobsearch_applicant_id[]"]').prop('checked', false);
                            jQuery('.sjob-aplicants-' + _job_id + ' input[type="checkbox"][name^="jobsearch_applicant_id[]"]').trigger('change');
                        }
                    });

                    /*
                  * Export To PDF in all applicants (admin side) Checkbox Event
                  * */

                    jQuery(document).on('click', '.jobsearch-applicant-id', function () {
                        var _this = jQuery(this), _job_id = _this.attr('data-job-id'),
                            _totl_aplicnts = jQuery('#job-apps-list' + _job_id).find('.jobsearch-column-12');
                        var checked_box_count = jQuery('#job-apps-list' + _job_id + ' input[type="checkbox"][name*="jobsearch_applicant_id[]"]:checked').length;

                        if (_totl_aplicnts.length == checked_box_count) {
                            jQuery(".sjob-aplicants-" + _job_id + " .select-all-job-applicnts").prop("checked", true)
                        } else {
                            jQuery(".sjob-aplicants-" + _job_id + " .select-all-job-applicnts").prop("checked", false)
                        }

                        if (checked_box_count > 0) {
                            jQuery(".total-aplicnt-cta-" + _job_id + " .admin-applicants-export-options").show();
                        } else {
                            jQuery(".total-aplicnt-cta-" + _job_id + " .admin-applicants-export-options").hide();
                        }
                    });

                    /*
                    * Export candidates PDF admin area in all applicants
                    * */

                    jQuery(document).on('click', '.jobsearch-cand-export-pdf', function () {
                        var _job_id = jQuery(this).attr("data-job-id");
                        var candidates_ids = [];
                        jQuery.each(jQuery("#job-apps-list" + _job_id + " input[name='jobsearch_applicant_id[]']:checked"), function () {
                            candidates_ids.push(jQuery(this).val());
                        });
                        jQuery("input[name='jobsearch_pdf_admin_cand_id']").val(candidates_ids.join(","));
                        jQuery("input[name='jobsearch_pdf_admin_job_id']").val(_job_id);
                        setTimeout(function () {
                            jQuery("input[name='admin_pdf_generate_form_submit']").trigger("click")
                        }, 500)
                    });

                    /*
                    * Export To Excel Event (admin area in all applicants)
                    * */

                    jQuery(document).on('click', '.jobsearch-cand-export-excel', function () {
                        var _job_id = jQuery(this).attr("data-job-id");
                        var candidates_ids = [];
                        jQuery.each(jQuery("#job-apps-list" + _job_id + " input[name='jobsearch_applicant_id[]']:checked"), function () {
                            candidates_ids.push(jQuery(this).val());
                        });
                        jQuery("input[name='jobsearch_excel_admin_cand_id']").val(candidates_ids.join(','));
                        jQuery("input[name='jobsearch_excel_admin_job_id']").val(_job_id);
                        setTimeout(function () {
                            jQuery("input[name='excel_generate_admin_form_submit']").trigger("click")
                        }, 500)
                    });

                    /*
                    * Export options on candidate post type admin side
                    * */

                    jQuery(document).on('change', '#jobsearch-cand-export', function () {
                        var _export_opts = jQuery(this).val();
                        if (_export_opts != 0) {
                            var candates_ids = [];
                            jQuery.each(jQuery("input[name='post[]']:checked"), function () {
                                candates_ids.push(jQuery(this).val());
                            });
                            if (_export_opts == 'jobsearch-cand-uploaded-resumes') {
                                var _this_form = jQuery('#posts-filter');
                                var _post_check_ids = _this_form.find('input[type=checkbox][name^="post"]');

                                var _candidtes_ids = [];
                                if (_post_check_ids.length > 0) {
                                    jQuery.each(_post_check_ids, function (_ind, _elm) {
                                        if (jQuery(this).is(':checked')) {
                                            var _cand_id = jQuery(this).attr('value');
                                            _candidtes_ids.push(_cand_id);
                                        }
                                    });
                                }
                                _candidtes_ids = _candidtes_ids.join();
                                if (_candidtes_ids != '') {
                                    jQuery('body').append('\<form id="user-resms-uplodedwn-actfrm" method="post">\
                                        <input type="hidden" name="candidate_ids" value="' + _candidtes_ids + '">\
                                        <input type="hidden" name="jobsearch_resumes_action" value="candbk_uploaded_resumes_downlodin">\
                                    </form>');
                                    document.getElementById("user-resms-uplodedwn-actfrm").submit();
                                }

                            } else if (_export_opts == 'jobsearch-cand-export-pdf') {
                                jQuery("input[name='jobsearch_pdf_admin_cand_id']").val(candates_ids.join(","));
                                setTimeout(function () {
                                    jQuery("input[name='admin_pdf_generate_form_submit']").trigger("click")
                                }, 500)
                            } else {
                                jQuery("input[name='jobsearch_excel_admin_cand_id']").val(candates_ids.join(","));
                                setTimeout(function () {
                                    jQuery("input[name='excel_generate_admin_form_submit']").trigger("click")
                                }, 500)
                            }
                        }
                    });

                    /*
                    * Export Options will be shown on candidate post type table (admin)
                    * */
                    jQuery(document).on('click', 'input[type="checkbox"][name*="post"]', function () {
                        var checked_box_count = jQuery('input[type="checkbox"][name*="post"]:checked').length;
                        if (checked_box_count > 0) {
                            jQuery("#jobsearch-export-options").show();
                        } else {
                            jQuery("#jobsearch-export-options").hide();
                        }
                    });
                </script>
                <?php
            }
        }

    }
}
new addon_jobsearch_export_resume_admin_hooks();