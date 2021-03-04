<?php
if (!class_exists('addon_jobsearch_pdf_resume_hooks')) {

    class addon_jobsearch_pdf_resume_hooks
    {
        public function __construct()
        {
            add_action('wp_footer', array($this, 'jobsearch_resume_export_functions_js'), 100);
            add_action('init', array($this, 'jobsearch_all_candidates_resume_export_submit'), 1);
            add_action('init', array($this, 'jobsearch_all_candidates_resume_export_excel_submit'), 1);
            add_action('jobsearch_empdash_aplics_btns_aftermore', array($this, 'jobsearch_export_options_callback'), 10, 1);
            add_action('jobsearch_emp_export_btns_list', array($this, 'jobsearch_emp_export_btns_list_callback'), 10, 1);
            add_action('jobsearch_export_selection_emp', array($this, 'jobsearch_export_selection_emp_callback'), 10, 2);
            add_action('jobsearch_export_select_all_emp', array($this, 'jobsearch_export_select_all_emp_callback'), 10, 1);
            add_filter('jobsearch_cand_generate_resume_btn', array($this, 'jobsearch_cand_generate_resume_aplicants_btn_callback'), 10, 1);
        }

        public function jobsearch_cand_generate_resume_aplicants_btn_callback($args = array())
        {
            global $jobsearch_resume_export, $jobsearch_plugin_options;
            $resume_intro_text = isset($jobsearch_plugin_options['resume-export-text']) && $jobsearch_plugin_options['candidate_rewrite_slug'] != '' ? $jobsearch_plugin_options['resume-export-text'] : '';
            $resume_export_box_switch = isset($jobsearch_plugin_options['my_resume_box_export_switch']) ? $jobsearch_plugin_options['my_resume_box_export_switch'] : '';
            $cand_default_resume = isset($jobsearch_plugin_options['cand_default_resume']) ? $jobsearch_plugin_options['cand_default_resume'] : '';

            $candidate_id = isset($args['candidate_id']) ? $args['candidate_id'] : '';
            $view = isset($args['view']) ? $args['view'] : '';
            $classs = isset($args['class']) ? $args['class'] : '';
            $icon = isset($args['icon']) ? '<i class="' . $args['icon'] . '"></i>' : '';
            $label = isset($args['label']) ? $args['label'] : esc_html__('Generate PDF', 'jobsearch-resume-export');
            $title = isset($args['title']) ? $args['title'] : '';

            $cand_pdf_package = get_post_meta($candidate_id, 'jobsearch_field_user_pdf_package', true);
            if (empty($cand_pdf_package)) {
                update_post_meta($candidate_id, 'jobsearch_field_user_pdf_package', 'pdf-package-default');
            }
            /*
             * Get all subscribed packages
             * */
            $all_cand_packages = jobsearch_pdf_pckges_list();
            /*
             * Get all PDFs resume packages list
             * */
            $args = array(
                'post_type' => 'package',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'order' => 'ASC',
                'orderby' => 'DATE',
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

            /*
             * The code is being used to get candidate saved pdf template
             * */
            $cand_user_id = jobsearch_get_candidate_user_id($candidate_id);
            $cand_saved_template = get_option('jobsearch_selected_pdf_template_' . $cand_user_id);
            if ($cand_saved_template == "") {
                update_option('jobsearch_selected_pdf_template_' . $cand_user_id, 'default');
            }
            //
            if ($view == 'package-view') {
                if ($resume_export_box_switch == 'off') {
                    return false;
                }

                if (!empty($resume_intro_text)) { ?>
                    <div class="jobsearch-export-candidate-pdf">
                        <?php echo($resume_intro_text) ?>
                    </div>
                <?php } ?>
                <div class="jobsearch-candidate-pdf-main-slider">
                    <!--Will fetch default template set from jobsearch option-->
                    <?php echo self::candidate_default_template($candidate_id) ?>
                    <?php
                    $candidate_pdfs_templates = [];
                    foreach ($all_cand_packages as $info) {
                        $jobsearc_pckg_id = get_post_meta($info->ID, 'jobsearch_order_package', true);
                        $candidate_pdfs_templates[] = get_post_meta($jobsearc_pckg_id, 'jobsearch_field_cand_pbase_pdfs', true);
                    }
                    ?>
                    <?php
                    if ($pkgs_query->have_posts()) {
                        foreach ($pdfs_posts as $key => $pdf_pckgs_info) {
                            $pdf_template = get_post_meta($pdf_pckgs_info->ID, 'jobsearch_field_cand_pbase_pdfs', true);
                            $jobsearch_field_charges_type = get_post_meta($pdf_pckgs_info->ID, 'jobsearch_field_charges_type', true);
                            $pkg_price = get_post_meta($pdf_pckgs_info->ID, 'jobsearch_field_package_price', true);
                            //
                            $temp_img_thumb = '';
                            $temp_img_large = '';

                            if ($pdf_template == 'default') {
                                $temp_img_thumb = 'cv-resume-thumb-default.jpg';
                                $temp_img_large = 'cv-resume-large-default.jpg';
                                $temp_download_txt = 'jobsearch-candidate-pdf-download jobsearch-get-cand-id';
                                $pdf_template_classs = '';
                            } else if ($pdf_template == 'Template 1') {
                                $temp_img_thumb = 'cv-resume-thumb-1.jpg';
                                $temp_img_large = 'cv-resume-large-1.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-one';
                                $pdf_template_classs = 'template-1';
                            } else if ($pdf_template == 'Template 2') {
                                $temp_img_thumb = 'cv-resume-thumb-2.jpg';
                                $temp_img_large = 'cv-resume-large-2.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-two';
                                $pdf_template_classs = 'template-2';
                            } else if ($pdf_template == 'Template 3') {
                                $temp_img_thumb = 'cv-resume-thumb-3.jpg';
                                $temp_img_large = 'cv-resume-large-3.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-three';
                                $pdf_template_classs = 'template-3';
                            } else if ($pdf_template == 'Template 4') {
                                $temp_img_thumb = 'cv-resume-thumb-4.jpg';
                                $temp_img_large = 'cv-resume-large-4.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-four';
                                $pdf_template_classs = 'template-4';
                            } else if ($pdf_template == 'Template 5') {
                                $temp_img_thumb = 'cv-resume-thumb-5.jpg';
                                $temp_img_large = 'cv-resume-large-5.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-five';
                                $pdf_template_classs = 'template-5';
                            } else if ($pdf_template == 'Template 6') {
                                $temp_img_thumb = 'cv-resume-thumb-6.jpg';
                                $temp_img_large = 'cv-resume-large-6.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-six';
                                $pdf_template_classs = 'template-6';
                            } else if ($pdf_template == 'Template 7') {
                                $temp_img_thumb = 'cv-resume-thumb-7.jpg';
                                $temp_img_large = 'cv-resume-large-7.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-seven';
                                $pdf_template_classs = 'template-7';
                            } else if ($pdf_template == 'Template 8') {
                                $temp_img_thumb = 'cv-resume-thumb-8.jpg';
                                $temp_img_large = 'cv-resume-large-8.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-eight';
                                $pdf_template_classs = 'template-8';
                            } else if ($pdf_template == 'Template 9') {
                                $temp_img_thumb = 'cv-resume-thumb-9.jpg';
                                $temp_img_large = 'cv-resume-large-9.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-nine';
                                $pdf_template_classs = 'template-9';
                            } else if ($pdf_template == 'Template 10') {
                                $temp_img_thumb = 'cv-resume-thumb-10.jpg';
                                $temp_img_large = 'cv-resume-large-10.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-ten';
                                $pdf_template_classs = 'template-10';
                            } else if ($pdf_template == 'Template 11') {
                                $temp_img_thumb = 'cv-resume-thumb-11.jpg';
                                $temp_img_large = 'cv-resume-large-11.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-eleven';
                                $pdf_template_classs = 'template-11';
                            } else if ($pdf_template == 'Template 12') {
                                $temp_img_thumb = 'cv-resume-thumb-12.jpg';
                                $temp_img_large = 'cv-resume-large-12.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-twelve';
                                $pdf_template_classs = 'template-12';
                            } else if ($pdf_template == 'Template 13') {
                                $temp_img_thumb = 'cv-resume-thumb-13.jpg';
                                $temp_img_large = 'cv-resume-large-13.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-thirteen';
                                $pdf_template_classs = 'template-13';
                            } else if ($pdf_template == 'Template 14') {
                                $temp_img_thumb = 'cv-resume-thumb-14.jpg';
                                $temp_img_large = 'cv-resume-large-14.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-fourteen';
                                $pdf_template_classs = 'template-14';
                            } else if ($pdf_template == 'Template 15') {
                                $temp_img_thumb = 'cv-resume-thumb-15.jpg';
                                $temp_img_large = 'cv-resume-large-15.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-fifteen';
                                $pdf_template_classs = 'template-15';
                            } else if ($pdf_template == 'Template 16') {
                                $temp_img_thumb = 'cv-resume-thumb-16.jpg';
                                $temp_img_large = 'cv-resume-large-16.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-sixteen';
                                $pdf_template_classs = 'template-16';
                            } else if ($pdf_template == 'Template 17') {
                                $temp_img_thumb = 'cv-resume-thumb-17.jpg';
                                $temp_img_large = 'cv-resume-large-17.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-seventeen';
                                $pdf_template_classs = 'template-17';
                            } else if ($pdf_template == 'Template 18') {
                                $temp_img_thumb = 'cv-resume-thumb-18.jpg';
                                $temp_img_large = 'cv-resume-large-18.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-eighteen';
                                $pdf_template_classs = 'template-18';
                            } else if ($pdf_template == 'Template 19') {
                                $temp_img_thumb = 'cv-resume-thumb-19.jpg';
                                $temp_img_large = 'cv-resume-large-19.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-nineteen';
                                $pdf_template_classs = 'template-19';
                            } else if ($pdf_template == 'Template 20') {
                                $temp_img_thumb = 'cv-resume-thumb-20.jpg';
                                $temp_img_large = 'cv-resume-large-20.jpg';
                                $temp_download_txt = 'jobsearch-get-cand-id-templt-twenty';
                                $pdf_template_classs = 'template-20';
                            }

                            if ($cand_default_resume != $pdf_template && $pdf_template != '') { ?>
                                <div class="jobsearch-candidate-pdf-slider-inner">
                                    <div class="jobsearch-candidate-pdf-list">
                                        <ul class="row">
                                            <li class="col-md-12">
                                                <figure>
                                                    <?php if (jobsearch_pdf_pckg_pdf_templates($candidate_pdfs_templates, $pdf_template) == true || $jobsearch_field_charges_type == 'free') { ?>
                                                        <a href="javascript:void(0)"
                                                           data-template="<?php echo($pdf_template) ?>"
                                                           class="jobsearch-activate-pdf-template <?php echo($pdf_template_classs) ?>">
                                                            <img src="<?php echo $jobsearch_resume_export->jobsearch_pdf_resume_get_url('/cv-resume-thumb/' . $temp_img_thumb) ?>">
                                                        </a>
                                                    <?php } else { ?>
                                                        <img src="<?php echo $jobsearch_resume_export->jobsearch_pdf_resume_get_url('/cv-resume-thumb/' . $temp_img_thumb) ?>">
                                                    <?php } ?>

                                                    <?php if ($cand_saved_template == $pdf_template) { ?>
                                                        <figcaption>
                                                            <?php echo esc_html__('Active', 'jobsearch-resume-export') ?>
                                                        </figcaption>
                                                    <?php } ?>
                                                    <?php if (jobsearch_pdf_pckg_pdf_templates($candidate_pdfs_templates, $pdf_template) != true && $jobsearch_field_charges_type != 'free') { ?>
                                                        <div class="jobsearch-candidate-pdf-locked">
                                                            <a href="javascript:void(0)"
                                                               class="fa fa-lock"></a>
                                                        </div>
                                                    <?php } ?>
                                                </figure>

                                                <div class="jobsearch-candidate-pdf-list-inner">
                                                    <a href="<?php echo $jobsearch_resume_export->jobsearch_pdf_resume_get_url('/cv-resume-large/' . $temp_img_large) ?>"
                                                       class="jobsearch-candidate-pdf-preview jobsearch-tooltipcon fancybox-galimg"
                                                       data-fancybox-group="group-<?php echo($key) ?>"
                                                       title="<?php echo($pdf_pckgs_info->post_title) ?>">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <?php if (jobsearch_pdf_pckg_pdf_templates($candidate_pdfs_templates, $pdf_template) == true || $jobsearch_field_charges_type == 'free') { ?>
                                                        <a href="javascript:void(0)"
                                                           data-cand-id="<?php echo($candidate_id) ?>"
                                                           data-template="<?php echo($pdf_template) ?>"
                                                           data-class="<?php echo($pdf_template_classs) ?>"
                                                           class="jobsearch-candidate-pdf-download <?php echo($temp_download_txt) ?>"><i
                                                                    class="fa fa-file-pdf-o"></i><?php echo esc_html__('Download PDF', 'jobsearch-resume-export') ?>
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="javascript:void(0)"
                                                           data-id="<?php echo($pdf_pckgs_info->ID) ?>"
                                                           class="jobsearch-subscribe-pdf-pkg jobsearch-candidate-pdf-buy"><?php echo esc_html__('Price: ', 'jobsearch-resume-export') ?><?php echo jobsearch_get_price_format($pkg_price) ?></a>
                                                        <span class="pkg-loding-msg" style="display:none;"></span>
                                                    <?php } ?>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php }
                    } ?>
                </div>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        if (jQuery('.jobsearch-candidate-pdf-main-slider').length > 0) {
                            jQuery('.jobsearch-candidate-pdf-main-slider').slick({
                                infinite: true,
                                slidesToShow: 4,
                                slidesToScroll: 2,
                                prevArrow: false,
                                nextArrow: false,
                                dots: false,
                                autoplay: true,
                                autoplaySpeed: 2000,
                                responsive: [
                                    {
                                        breakpoint: 1400,
                                        settings: {
                                            slidesToShow: 3,
                                            slidesToScroll: 1,
                                            infinite: true,
                                        }
                                    },
                                    {
                                        breakpoint: 1024,
                                        settings: {
                                            slidesToShow: 3,
                                            slidesToScroll: 1,
                                            infinite: true,
                                        }
                                    },
                                    {
                                        breakpoint: 800,
                                        settings: {
                                            slidesToShow: 1,
                                            slidesToScroll: 1
                                        }
                                    },
                                    {
                                        breakpoint: 400,
                                        settings: {
                                            slidesToShow: 1,
                                            slidesToScroll: 1
                                        }
                                    }
                                ]
                            });
                        }
                    });
                </script>
            <?php } else if ($view == 'list') {

                $flag = apply_filters('jobsearch_disable_resume_export', true);
                if ($flag == false) {
                    return false;
                }

                $cand_user_id = jobsearch_get_candidate_user_id($candidate_id);
                $all_cand_saved_template = get_option('jobsearch_selected_pdf_template_' . $cand_user_id);

                $download_template = '';
                if ($all_cand_saved_template == 'default') {
                    $download_template = 'jobsearch-get-cand-id';
                } else if ($all_cand_saved_template == 'Template 1') {
                    $download_template = 'jobsearch-get-cand-id-templt-one';
                } else if ($all_cand_saved_template == 'Template 2') {
                    $download_template = 'jobsearch-get-cand-id-templt-two';
                } else if ($all_cand_saved_template == 'Template 3') {
                    $download_template = 'jobsearch-get-cand-id-templt-three';
                } else if ($all_cand_saved_template == 'Template 4') {
                    $download_template = 'jobsearch-get-cand-id-templt-four';
                } else if ($all_cand_saved_template == 'Template 5') {
                    $download_template = 'jobsearch-get-cand-id-templt-five';
                } else if ($all_cand_saved_template == 'Template 6') {
                    $download_template = 'jobsearch-get-cand-id-templt-six';
                } else if ($all_cand_saved_template == 'Template 7') {
                    $download_template = 'jobsearch-get-cand-id-templt-seven';
                } else if ($all_cand_saved_template == 'Template 8') {
                    $download_template = 'jobsearch-get-cand-id-templt-eight';
                } else if ($all_cand_saved_template == 'Template 9') {
                    $download_template = 'jobsearch-get-cand-id-templt-nine';
                } else if ($all_cand_saved_template == 'Template 10') {
                    $download_template = 'jobsearch-get-cand-id-templt-ten';
                } else if ($all_cand_saved_template == 'Template 11') {
                    $download_template = 'jobsearch-get-cand-id-templt-eleven';
                } else if ($all_cand_saved_template == 'Template 12') {
                    $download_template = 'jobsearch-get-cand-id-templt-twelve';
                } else if ($all_cand_saved_template == 'Template 13') {
                    $download_template = 'jobsearch-get-cand-id-templt-thirteen';
                } else if ($all_cand_saved_template == 'Template 14') {
                    $download_template = 'jobsearch-get-cand-id-templt-fourteen';
                } else if ($all_cand_saved_template == 'Template 15') {
                    $download_template = 'jobsearch-get-cand-id-templt-fifteen';
                } else if ($all_cand_saved_template == 'Template 16') {
                    $download_template = 'jobsearch-get-cand-id-templt-sixteen';
                } else if ($all_cand_saved_template == 'Template 17') {
                    $download_template = 'jobsearch-get-cand-id-templt-seventeen';
                } else if ($all_cand_saved_template == 'Template 18') {
                    $download_template = 'jobsearch-get-cand-id-templt-eighteen';
                } else if ($all_cand_saved_template == 'Template 19') {
                    $download_template = 'jobsearch-get-cand-id-templt-nineteen';
                } else if ($all_cand_saved_template == 'Template 20') {
                    $download_template = 'jobsearch-get-cand-id-templt-twenty';
                } else {
                    $download_template = 'jobsearch-get-cand-id';
                }
                ?>
                <li>
                    <a href="javascript:void(0)" data-cand-id="<?php echo($candidate_id) ?>"
                       title="<?php echo($title) ?>"
                       class="<?php echo($classs) ?> <?php echo($download_template) ?>"><?php echo($icon) ?><?php echo($label) ?></a>
                </li>
            <?php } ?>
        <?php }

        public static function candidate_default_template($candidate_id)
        {
            global $jobsearch_plugin_options, $jobsearch_resume_export;
            $cand_user_id = jobsearch_get_candidate_user_id($candidate_id);
            $cand_default_resume = isset($jobsearch_plugin_options['cand_default_resume']) ? $jobsearch_plugin_options['cand_default_resume'] : '';
            $cand_saved_template = get_option('jobsearch_selected_pdf_template_' . $cand_user_id);
            if ($cand_saved_template == "") {
                update_option('jobsearch_selected_pdf_template_' . $cand_user_id, 'default');
            }
            $temp_img_thumb = '';
            $temp_img_large = '';
            $pdf_template_classs = '';
            $temp_download_txt = '';
            $pdf_template = $cand_default_resume;

            if ($pdf_template == 'default') {
                $temp_img_thumb = 'cv-resume-thumb-default.jpg';
                $temp_img_large = 'cv-resume-large-default.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id';
                $pdf_template_classs = 'default';

            } else if ($pdf_template == 'Template 1') {
                $temp_img_thumb = 'cv-resume-thumb-1.jpg';
                $temp_img_large = 'cv-resume-large-1.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-one';
                $pdf_template_classs = 'template-1';
            } else if ($pdf_template == 'Template 2') {
                $temp_img_thumb = 'cv-resume-thumb-2.jpg';
                $temp_img_large = 'cv-resume-large-2.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-two';
                $pdf_template_classs = 'template-2';
            } else if ($pdf_template == 'Template 3') {
                $temp_img_thumb = 'cv-resume-thumb-3.jpg';
                $temp_img_large = 'cv-resume-large-3.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-three';
                $pdf_template_classs = 'template-3';
            } else if ($pdf_template == 'Template 4') {
                $temp_img_thumb = 'cv-resume-thumb-4.jpg';
                $temp_img_large = 'cv-resume-large-4.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-four';
                $pdf_template_classs = 'template-4';
            } else if ($pdf_template == 'Template 5') {
                $temp_img_thumb = 'cv-resume-thumb-5.jpg';
                $temp_img_large = 'cv-resume-large-5.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-five';
                $pdf_template_classs = 'template-5';
            } else if ($pdf_template == 'Template 6') {
                $temp_img_thumb = 'cv-resume-thumb-6.jpg';
                $temp_img_large = 'cv-resume-large-6.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-six';
                $pdf_template_classs = 'template-6';
            } else if ($pdf_template == 'Template 7') {
                $temp_img_thumb = 'cv-resume-thumb-7.jpg';
                $temp_img_large = 'cv-resume-large-7.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-seven';
                $pdf_template_classs = 'template-7';
            } else if ($pdf_template == 'Template 8') {
                $temp_img_thumb = 'cv-resume-thumb-8.jpg';
                $temp_img_large = 'cv-resume-large-8.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-eight';
                $pdf_template_classs = 'template-8';
            } else if ($pdf_template == 'Template 9') {
                $temp_img_thumb = 'cv-resume-thumb-9.jpg';
                $temp_img_large = 'cv-resume-large-9.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-nine';
                $pdf_template_classs = 'template-9';
            } else if ($pdf_template == 'Template 10') {
                $temp_img_thumb = 'cv-resume-thumb-10.jpg';
                $temp_img_large = 'cv-resume-large-10.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-ten';
                $pdf_template_classs = 'template-10';
            } else if ($pdf_template == 'Template 11') {
                $temp_img_thumb = 'cv-resume-thumb-11.jpg';
                $temp_img_large = 'cv-resume-large-11.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-eleven';
                $pdf_template_classs = 'template-11';
            } else if ($pdf_template == 'Template 12') {
                $temp_img_thumb = 'cv-resume-thumb-12.jpg';
                $temp_img_large = 'cv-resume-large-12.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-twelve';
                $pdf_template_classs = 'template-12';
            } else if ($pdf_template == 'Template 13') {
                $temp_img_thumb = 'cv-resume-thumb-13.jpg';
                $temp_img_large = 'cv-resume-large-13.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-thirteen';
                $pdf_template_classs = 'template-13';
            } else if ($pdf_template == 'Template 14') {
                $temp_img_thumb = 'cv-resume-thumb-14.jpg';
                $temp_img_large = 'cv-resume-large-14.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-fourteen';
                $pdf_template_classs = 'template-14';
            } else if ($pdf_template == 'Template 15') {
                $temp_img_thumb = 'cv-resume-thumb-15.jpg';
                $temp_img_large = 'cv-resume-large-15.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-fifteen';
                $pdf_template_classs = 'template-15';
            } else if ($pdf_template == 'Template 16') {
                $temp_img_thumb = 'cv-resume-thumb-16.jpg';
                $temp_img_large = 'cv-resume-large-16.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-sixteen';
                $pdf_template_classs = 'template-16';
            } else if ($pdf_template == 'Template 17') {
                $temp_img_thumb = 'cv-resume-thumb-17.jpg';
                $temp_img_large = 'cv-resume-large-17.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-seventeen';
                $pdf_template_classs = 'template-17';
            } else if ($pdf_template == 'Template 18') {
                $temp_img_thumb = 'cv-resume-thumb-18.jpg';
                $temp_img_large = 'cv-resume-large-18.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-eighteen';
                $pdf_template_classs = 'template-18';
            } else if ($pdf_template == 'Template 19') {
                $temp_img_thumb = 'cv-resume-thumb-19.jpg';
                $temp_img_large = 'cv-resume-large-19.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-nineteen';
                $pdf_template_classs = 'template-19';
            } else if ($pdf_template == 'Template 20') {
                $temp_img_thumb = 'cv-resume-thumb-20.jpg';
                $temp_img_large = 'cv-resume-large-20.jpg';
                $temp_download_txt = 'jobsearch-get-cand-id-templt-twenty';
                $pdf_template_classs = 'template-20';
            }
            ?>
            <div class="jobsearch-candidate-pdf-slider-inner">
                <div class="jobsearch-candidate-pdf-list">
                    <ul class="row">
                        <li class="col-md-12">
                            <figure>
                                <a href="javascript:void(0)" data-template="<?php echo($pdf_template) ?>"
                                   class="jobsearch-activate-pdf-template <?php echo($pdf_template_classs) ?>">
                                    <img src="<?php echo $jobsearch_resume_export->jobsearch_pdf_resume_get_url('/cv-resume-thumb/' . $temp_img_thumb) ?>">
                                </a>
                                <?php if ($cand_saved_template == $pdf_template) { ?>
                                    <figcaption>
                                        <?php echo esc_html__('Active', 'jobsearch-resume-export') ?>
                                    </figcaption>
                                <?php } ?>
                            </figure>
                            <div class="jobsearch-candidate-pdf-list-inner">
                                <a href="<?php echo $jobsearch_resume_export->jobsearch_pdf_resume_get_url('/cv-resume-large/' . $temp_img_large) ?>"
                                   class="jobsearch-candidate-pdf-preview jobsearch-tooltipcon fancybox-galimg"
                                   data-fancybox-group="group"
                                   title="Default Template">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="javascript:void(0)" data-cand-id="<?php echo($candidate_id) ?>"
                                   data-template="<?php echo($pdf_template) ?>"
                                   data-class="<?php echo($pdf_template_classs) ?>"
                                   class="jobsearch-candidate-pdf-download <?php echo($temp_download_txt) ?>"><i
                                            class="fa fa-file-pdf-o"></i><?php echo esc_html__('Download PDF', 'jobsearch-resume-export') ?>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        <?php }

        public function jobsearch_export_select_all_emp_callback($_job_id)
        {
            ob_start(); ?>
            <div class="sort-select-all-aplicnt-opts">
                <input type="checkbox" data-job-id="<?php echo($_job_id) ?>"
                       class="select-all-job-applicnts" id="select-all-job-applicnts-<?php echo($_job_id) ?>">
                <label for="select-all-job-applicnts-<?php echo($_job_id) ?>"><?php echo esc_html__('Select All', 'jobsearch-resume-export') ?></label>
            </div>
            <?php
            $html = ob_get_clean();
            echo $html;
        }

        public function jobsearch_export_selection_emp_callback($_candidate_id, $_job_id)
        {
            ob_start(); ?>
            <input type="checkbox" class="jobsearch-applicant-id" name="jobsearch_applicant_id[]"
                   data-job-id="<?php echo($_job_id) ?>"
                   value="<?php echo($_candidate_id) ?>">
            <?php
            $html = ob_get_clean();
            echo $html;
        }

        public function jobsearch_emp_export_btns_list_callback($_job_id)
        { ?>
            <div class="emp-applicants-export-options" style="display:none;">
                <?php
                ob_start();
                ?>
                <a href="javascript:void(0)" data-job-id="<?php echo($_job_id) ?>"
                   class="jobsearch-cand-export-excel-applcnts applicnt-count-box excel-export"><?php esc_html_e('Export to Excel', 'jobsearch-resume-export') ?></a>
                <a href="javascript:void(0)" data-job-id="<?php echo($_job_id) ?>"
                   class="jobsearch-cand-export-pdf-applcnts applicnt-count-box pdf-export"><?php esc_html_e('Export to PDF', 'jobsearch-resume-export') ?></a>
                <?php
                $html = ob_get_clean();
                echo apply_filters('jobsearch_allaplics_front_export_cv_btns', $html, $_job_id);
                ?>
            </div>
            <?php
        }

        public function jobsearch_all_candidates_resume_export_excel_submit()
        {
            global $rand_num, $jobsearch_plugin_options, $sitepress;
            $candidate_site_slug = isset($jobsearch_plugin_options['candidate_rewrite_slug']) && $jobsearch_plugin_options['candidate_rewrite_slug'] != '' ? $jobsearch_plugin_options['candidate_rewrite_slug'] : 'candidate';

            if (isset($_POST['excel_generate_form_submit'])) {

                header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                header("Content-Disposition: attachment; filename=export-candidate.xlsx");
                header('Cache-Control: max-age=0');

                header('Cache-Control: max-age=1');

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
                $totl_ids = explode(',', $_POST['jobsearch_excel_cand_id']);
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
                    $cand_pdf_package = get_post_meta($candidate_id, 'jobsearch_field_user_pdf_package', true);
                    if (empty($cand_pdf_package)) {
                        update_post_meta($candidate_id, 'jobsearch_field_user_pdf_package', 'pdf-package-default');
                    }
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


        public function jobsearch_export_options_callback($job_id)
        {
            $flag = apply_filters('jobsearch_disable_resume_export', true);
            ob_start();
            ?>
            <div id="jobsearch-export-pdf" class="jobsearch-sort-cv-fields" style="display: none">
                <div class="more-fields-act-btn">
                    <a href="javascript:void(0);"
                       class="more-actions"><?php esc_html_e('Export', 'jobsearch-resume-export') ?> <span><i
                                    class="careerfy-icon careerfy-down-arrow"></i></span></a>
                    <ul>
                        <?php
                        ob_start();
                        ?>
                        <li>
                            <a href="javascript:void(0);" id="jobsearch-cand-export-pdf"
                               data-job-id="<?php echo($job_id) ?>"><?php esc_html_e('Export To PDF', 'jobsearch-resume-export') ?>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0);" id="jobsearch-cand-export-excel"
                               data-job-id="<?php echo($job_id) ?>"><?php esc_html_e('Export To Excel', 'jobsearch-resume-export') ?>
                            </a>
                        </li>
                        <?php
                        $export_itms_html = ob_get_clean();
                        echo apply_filters('jobsearch_inapplics_bulkexport_dropdwn_itms', $export_itms_html, $job_id);
                        ?>
                    </ul>
                </div>
            </div>

            <?php
            $html = ob_get_clean();
            echo $flag == true ? $html : '';
        }

        public
        function jobsearch_all_candidates_resume_export_submit()
        {
            global $rand_num, $jobsearch_resume_pdf_default_template,
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

            $flag = apply_filters('jobsearch_disable_resume_export', true);

            if ($flag == false) {
                return false;
            }

            if (isset($_POST['pdf_generate_form_submit'])) {

                $rand_num = rand(10000000, 99999999);
                $totl_ids = explode(',', $_POST['jobsearch_pdf_cand_id']);

                foreach ($totl_ids as $candidate_id) {

                    $cand_user_id = jobsearch_get_candidate_user_id($candidate_id);
                    $saved_template = get_option('jobsearch_selected_pdf_template_' . $cand_user_id);
                    $saved_template = empty($saved_template) ? 'default' : $saved_template;
                    /*
                     * All PDFs templates for bulk export. This option is for employer
                     * */
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
                self::zipFolderResumeExport($totl_ids, $_POST['jobsearch_pdf_job_id']);
            }
        }


        public static function zipFolderResumeExport($totl_ids = array(), $job_id)
        {
            $flag = apply_filters('jobsearch_disable_resume_export', true);
            if ($flag == false) {
                return false;
            }
            global $jobsearch_resume_export, $jobsearch_pdf_temp_upload_file;
            $job_det = get_post($job_id);
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

            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                $zip->open($jobsearch_resume_export->jobsearch_resume_export_get_path($job_det->post_name . '.zip'), ZipArchive::CREATE | ZipArchive::OVERWRITE);
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

            $zip_path = $jobsearch_resume_export->jobsearch_resume_export_get_path($job_det->post_name . '.zip');
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
            header("Content-Disposition: attachment; filename=" . $job_det->post_name . '.zip');
            header('Content-Length: ' . filesize($zip_path));
            ob_end_clean();
            readfile($zip_path);
            unlink($zip_path);
            exit;
        }

        public
        static function CompressFiles($dir, $files, $zip_path, $archiver)
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
                        //return $this->SetError('errArchive');
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

        public
        function jobsearch_resume_export_functions_js()
        {
            global $jobsearch_plugin_options, $sitepress;
            $flag = apply_filters('jobsearch_disable_resume_export', true);
            //
            $page_id = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $page_id = jobsearch__get_post_id($page_id, 'page');
            $lang_code = '';
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
            }
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $page_id = icl_object_id($page_id, 'page', false, $lang_code);
            }
            if (!is_page($page_id)) {
                return;
            }

            if ($flag == true) { ?>
                <form id="pdf_generate_form" method="post" enctype="multipart/form-data" style="display: none;">
                    <input type="text" name="jobsearch_pdf_cand_id" value="">
                    <input type="text" name="jobsearch_pdf_job_id" value="">
                    <input type="submit" name="pdf_generate_form_submit" value="Submit">
                </form>
            <?php } ?>

            <form id="excel_generate_form" method="post" enctype="multipart/form-data" style="display: none;">
                <input type="text" name="jobsearch_excel_cand_id" value="">
                <input type="text" name="jobsearch_excel_job_id" value="">
                <input type="submit" name="excel_generate_form_submit" value="Submit">
            </form>

            <script type="text/javascript">
                jQuery(document).on('click', '#select-all-job-app', function () {
                    //
                    var _this = jQuery(this);
                    if (_this.is(':checked')) {
                        jQuery("#jobsearch-export-pdf").show();
                    } else {
                        jQuery("#jobsearch-export-pdf").hide();
                    }
                });
                /*
                * Export To PDF Event
                * */
                jQuery(document).on('click', '#jobsearch-cand-export-pdf', function () {
                    var _job_id = jQuery(this).attr("data-job-id");
                    var candates_ids = [];
                    jQuery.each(jQuery("input[name='app_candidate_sel[]']:checked"), function () {
                        candates_ids.push(jQuery(this).val());
                    });
                    jQuery("input[name='jobsearch_pdf_cand_id']").val(candates_ids.join(","));
                    jQuery("input[name='jobsearch_pdf_job_id']").val(_job_id);
                    setTimeout(function () {
                        jQuery("input[name='pdf_generate_form_submit']").trigger("click")
                    }, 500)
                });

                /*
                * Export To Excel Event
                * */

                jQuery(document).on('click', '#jobsearch-cand-export-excel', function () {
                    var _job_id = jQuery(this).attr("data-job-id");
                    var candates_ids = [];
                    jQuery.each(jQuery("input[name='app_candidate_sel[]']:checked"), function () {
                        candates_ids.push(jQuery(this).val());
                    });
                    jQuery("input[name='jobsearch_excel_cand_id']").val(candates_ids.join(","));
                    jQuery("input[name='jobsearch_excel_job_id']").val(_job_id);
                    setTimeout(function () {
                        jQuery("input[name='excel_generate_form_submit']").trigger("click")
                    }, 500)
                });
                //
                jQuery(document).on('click', 'input[type="checkbox"][name*="app_candidate_sel"]', function () {
                    var checked_box_count = jQuery('input[type="checkbox"][name*="app_candidate_sel"]:checked').length;
                    if (checked_box_count > 0) {
                        jQuery("#jobsearch-export-pdf").show();
                    } else {
                        jQuery("#jobsearch-export-pdf").hide();
                    }
                });
                /*
                    * Select All CVs against job
                    * */
                var all_candates_ids = [];
                jQuery(document).on('click', '.select-all-job-applicnts', function () {
                    var _this = jQuery(this), _job_id = jQuery(this).attr('data-job-id');

                    if (_this.is(':checked')) {
                        jQuery(".sjob-aplicants-" + _job_id + " .emp-applicants-export-options").show();
                        jQuery('.sjob-aplicants-' + _job_id + ' input[type="checkbox"][name^="jobsearch_applicant_id[]"]').prop('checked', true);
                        jQuery('.sjob-aplicants-' + _job_id + ' input[type="checkbox"][name^="jobsearch_applicant_id[]"]').trigger('change');
                    } else {
                        jQuery('.sjob-aplicants-' + _job_id + ' input[type="checkbox"][name^="jobsearch_applicant_id[]"]').prop('checked', false);
                        jQuery('.sjob-aplicants-' + _job_id + ' input[type="checkbox"][name^="jobsearch_applicant_id[]"]').trigger('change');
                        jQuery(".sjob-aplicants-" + _job_id + " .emp-applicants-export-options").hide();
                    }
                });

                /*
                  * Export To PDF in all applicants Checkbox Event
                  * */
                jQuery(document).on('click', '.jobsearch-applicant-id', function () {
                    var _this = jQuery(this), _cand_id = _this.val(),
                        _job_id = _this.attr('data-job-id'),
                        _totl_aplicnts = jQuery('#job-apps-list' + _job_id).find('.jobsearch-column-12');
                    var checked_box_count = jQuery('#job-apps-list' + _job_id + ' input[type="checkbox"][name*="jobsearch_applicant_id"]:checked').length;

                    if (_totl_aplicnts.length == checked_box_count) {
                        jQuery(".sjob-aplicants-" + _job_id + " .select-all-job-applicnts").prop("checked", true)
                    } else {
                        jQuery(".sjob-aplicants-" + _job_id + " .select-all-job-applicnts").prop("checked", false)
                    }

                    if (checked_box_count > 0) {
                        jQuery(".sjob-aplicants-" + _job_id + " .emp-applicants-export-options").show();
                    } else {
                        jQuery(".sjob-aplicants-" + _job_id + " .emp-applicants-export-options").hide();
                    }
                });
                /*
               * Export To PDF all applicants against job Event
               * */
                jQuery(document).on('click', '.jobsearch-cand-export-pdf-applcnts', function () {
                    var _job_id = jQuery(this).attr("data-job-id"), candidates_ids = [];
                    jQuery.each(jQuery("#job-apps-list" + _job_id + " input[name='jobsearch_applicant_id[]']:checked"), function () {
                        candidates_ids.push(jQuery(this).val());
                    });

                    jQuery("input[name='jobsearch_pdf_cand_id']").val(candidates_ids.join(','));
                    jQuery("input[name='jobsearch_pdf_job_id']").val(_job_id);
                    setTimeout(function () {
                        jQuery("input[name='pdf_generate_form_submit']").trigger("click")
                    }, 500)
                });
                /*
               * Export To Excel all applicants against job Event
               * */
                jQuery(document).on('click', '.jobsearch-cand-export-excel-applcnts', function () {
                    var _job_id = jQuery(this).attr("data-job-id");
                    var candidates_ids = [];
                    jQuery.each(jQuery("#job-apps-list" + _job_id + " input[name='jobsearch_applicant_id[]']:checked"), function () {
                        candidates_ids.push(jQuery(this).val());
                    });
                    jQuery("input[name='jobsearch_excel_cand_id']").val(candidates_ids.join(','));
                    jQuery("input[name='jobsearch_excel_job_id']").val(_job_id);
                    setTimeout(function () {
                        jQuery("input[name='excel_generate_form_submit']").trigger("click")
                    }, 500)
                });
            </script>
        <?php }
    }
}
new addon_jobsearch_pdf_resume_hooks();