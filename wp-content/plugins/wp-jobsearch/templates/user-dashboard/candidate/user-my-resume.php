<?php

use WP_Jobsearch\Package_Limits;

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings, $diff_form_errs;

$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$user_pkg_limits = new Package_Limits;

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);
$candidate_id = jobsearch_get_user_candidate_id($user_id);

$reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;
$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

if ($candidate_id > 0) {

    $inopt_cover_letr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
    $inopt_resm_education = isset($jobsearch_plugin_options['cand_resm_education']) ? $jobsearch_plugin_options['cand_resm_education'] : '';
    $inopt_resm_experience = isset($jobsearch_plugin_options['cand_resm_experience']) ? $jobsearch_plugin_options['cand_resm_experience'] : '';
    $inopt_resm_portfolio = isset($jobsearch_plugin_options['cand_resm_portfolio']) ? $jobsearch_plugin_options['cand_resm_portfolio'] : '';
    $inopt_resm_skills = isset($jobsearch_plugin_options['cand_resm_skills']) ? $jobsearch_plugin_options['cand_resm_skills'] : '';
    $inopt_resm_langs = isset($jobsearch_plugin_options['cand_resm_langs']) ? $jobsearch_plugin_options['cand_resm_langs'] : '';
    $inopt_resm_honsawards = isset($jobsearch_plugin_options['cand_resm_honsawards']) ? $jobsearch_plugin_options['cand_resm_honsawards'] : '';
    $cover_letter = get_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', true);
    $termscon_chek = get_post_meta($candidate_id, 'terms_cond_check', true);

    do_action('jobsearch_before_cand_dash_resume_contnt', $candidate_id);
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery("#jobsearch-resume-edu-con ul").sortable({
                handle: '.el-drag-item',
                cursor: 'move',
            });
            jQuery("#jobsearch-resume-expr-con ul").sortable({
                handle: '.el-drag-item',
                cursor: 'move',
            });
            jQuery("#jobsearch-resume-portfolio-con ul").sortable({
                handle: '.el-drag-item',
                cursor: 'move',
            });
            jQuery("#jobsearch-resume-skills-con ul").sortable({
                handle: '.el-drag-item',
                cursor: 'move',
            });
            jQuery("#jobsearch-resume-awards-con ul").sortable({
                handle: '.el-drag-item',
                cursor: 'move',
            });
        });
    </script>
    <form method="post" id="jobsearch-candidate-resumesub" class="jobsearch-candidate-dasboard"
          action="<?php echo add_query_arg(array('tab' => 'my-resume'), $page_url) ?>">
        <div class="jobsearch-employer-box-section">
            <div class="jobsearch-profile-title">
                <h2><?php esc_html_e('My Resume', 'wp-jobsearch') ?></h2>
            </div>

            <?php
            $args = array(
                'candidate_id' => $candidate_id,
                'view' => 'package-view',
            );
            apply_filters('jobsearch_cand_generate_resume_btn', $args);
            ?>

            <div class="jobsearch-candidate-section">
                <?php
                if (isset($_POST['user_resume_form']) && $_POST['user_resume_form'] == '1') {
                    if (isset($diff_form_errs['user_not_allow_mod']) && $diff_form_errs['user_not_allow_mod'] == true) {
                        ?>
                        <div class="jobsearch-alert jobsearch-error-alert">
                            <p><?php echo wp_kses(__('<strong>Error!</strong> You are not allowed to modify settings.', 'wp-jobsearch'), array('strong' => array())) ?></p>
                        </div>
                        <?php
                    }
                }

                ob_start();
                ?>
                <div class="jobsearch-candidate-title"><h2><i
                                class="jobsearch-icon jobsearch-resume-1"></i> <?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>
                    </h2></div>
                <?php
                if ($user_pkg_limits::cand_field_is_locked('coverltr_defields')) {
                    echo($user_pkg_limits::cand_gen_locked_html());
                } else { ?>
                    <div class="jobsearch-candidate-dashboard-editor">
                        <textarea name="jobsearch_field_resume_cover_letter" rows="10"
                                  class="form-control"><?php echo($cover_letter) ?></textarea>
                    </div>
                <?php }

                $covrletr_html = ob_get_clean();
                if ($inopt_cover_letr != 'off') {
                    echo apply_filters('jobsearch_candidate_dash_resume_covrletr_html', $covrletr_html, $candidate_id);
                }

                //
                echo apply_filters('jobsearch_candidate_dash_resume_after_cover', '', $candidate_id);

                $cand_skills_switch = isset($jobsearch_plugin_options['cand_skills_switch']) ? $jobsearch_plugin_options['cand_skills_switch'] : '';
                $cand_max_skills_allow = isset($jobsearch_plugin_options['cand_max_skills']) && $jobsearch_plugin_options['cand_max_skills'] > 0 ? $jobsearch_plugin_options['cand_max_skills'] : 5;
                $cand_sugg_skills_allow = isset($jobsearch_plugin_options['cand_sugg_skills']) && $jobsearch_plugin_options['cand_sugg_skills'] > 0 ? $jobsearch_plugin_options['cand_sugg_skills'] : 0;

                if ($cand_skills_switch == 'on') {
                    ob_start();
                    ?>
                    <div class="jobsearch-candidate-resume-wrap jobsearch-employer-profile-form">
                        <div class="jobsearch-candidate-title">
                            <h2>
                                <i class="jobsearch-icon jobsearch-social-media"></i> <?php esc_html_e('Skills', 'wp-jobsearch') ?>
                            </h2>
                        </div>
                        <?php
                        wp_enqueue_script('jobsearch-tag-it');
                        $cand_saved_skills = wp_get_post_terms($candidate_id, 'skill');
                        ?>
                        <div class="jobseach-skills-con">
                            <script type="text/javascript">
                                jQuery(document).ready(function () {
                                    jQuery('#cand-skills').tagit({
                                        allowSpaces: true,
                                        tagLimit: '<?php echo($cand_max_skills_allow) ?>',
                                        placeholderText: '<?php esc_html_e('Add Skills', 'wp-jobsearch') ?>',
                                        fieldName: 'get_cand_skills[]',
                                        onTagLimitExceeded: function (event, ui) {
                                            jQuery(".tagit-new input").val("");
                                            alert('<?php printf(esc_html__('Only %s skills allowed.', 'wp-jobsearch'), $cand_max_skills_allow) ?>');
                                        }
                                    });
                                });
                                jQuery(document).on('focus', '.tagit-new input', function () {
                                    var _this = jQuery(this);
                                    _this.parents('.jobseach-skills-con').find('.suggested-skills-con').slideDown();
                                });
                                jQuery(document).on('click', 'body', function (evt) {
                                    var target = evt.target;
                                    var this_box = jQuery('.jobseach-skills-con');
                                    if (!this_box.is(evt.target) && this_box.has(evt.target).length === 0) {
                                        this_box.find('.suggested-skills-con').slideUp();
                                    }
                                });
                            </script>
                            <label><?php esc_html_e('Add Skills', 'wp-jobsearch') ?></label>
                            <ul id="cand-skills" class="jobseach-job-skills">
                                <?php
                                if (!empty($cand_saved_skills)) {
                                    foreach ($cand_saved_skills as $cand_saved_skill) {
                                        ?>
                                        <li><?php echo jobsearch_esc_html($cand_saved_skill->name) ?></li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                            <?php
                            if ($cand_sugg_skills_allow > 0) {
                                $cand_sectors = wp_get_post_terms($candidate_id, 'sector');
                                $candidate_sector = isset($cand_sectors[0]->term_id) ? $cand_sectors[0]->term_id : '';
                                $skills_terms = get_terms(array(
                                    'taxonomy' => 'skill',
                                    'orderby' => 'count',
                                    'number' => $cand_sugg_skills_allow,
                                    'hide_empty' => false,
                                ));
                                //
                                $sectr_terms = $wpdb->get_col($wpdb->prepare("SELECT terms.term_id FROM $wpdb->terms AS terms"
                                    . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                                    . " WHERE term_tax.taxonomy=%s"
                                    . " ORDER BY terms.term_id DESC", 'sector'));

                                if (!empty($sectr_terms) && !is_wp_error($sectr_terms)) {
                                    ob_start();
                                    ?>
                                    <div class="suggested-skills-con">
                                        <label><?php esc_html_e('Suggested Skills', 'wp-jobsearch') ?></label>
                                        <?php
                                        $sector_jmeta = get_term_meta($candidate_sector, 'careerfy_frame_cat_fields', true);
                                        $sector_skills = isset($sector_jmeta['skills']) ? $sector_jmeta['skills'] : '';
                                        if (!empty($sector_skills)) { ?>
                                            <ul class="suggested-skills suggested-skills-sector-<?php echo($candidate_sector) ?>">
                                                <?php
                                                $sector_skills_count = 1;
                                                foreach ($sector_skills as $sector_skill_sid) {
                                                    $skill_term_obj = get_term_by('id', $sector_skill_sid, 'skill');
                                                    ?>
                                                    <li class="skills-cloud"
                                                        onclick="jQuery('#cand-skills').tagit('createTag', '<?php echo(addslashes($skill_term_obj->name)) ?>'); return false;"><?php echo($skill_term_obj->name) ?></li>
                                                    <?php
                                                    if ($sector_skills_count >= $cand_sugg_skills_allow) {
                                                        break;
                                                    }
                                                    $sector_skills_count++;
                                                }
                                                ?>
                                            </ul>
                                            <?php
                                        } else {
                                            ?>
                                            <ul class="suggested-skills">
                                                <?php
                                                foreach ($skills_terms as $skill_term) {
                                                    ?>
                                                    <li class="skills-cloud"
                                                        onclick="jQuery('#cand-skills').tagit('createTag', '<?php echo(addslashes($skill_term->name)) ?>'); return false;"><?php echo($skill_term->name) ?></li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    $html = ob_get_clean();
                                    echo apply_filters('jobsearch_post_cand_sugg_skills_html', $html, $skills_terms);
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    $skills_html = ob_get_clean();
                    echo apply_filters('jobsearch_candash_resms_skills_html', $skills_html, $candidate_id);
                }

                echo apply_filters('jobsearch_candidate_dash_resume_after_skills', '', $candidate_id);

                $resm_edu_oall_html = $resm_exp_oall_html = $resm_port_oall_html = $resm_skill_oall_html = $resm_lang_oall_html = $resm_award_oall_html = '';
                //
                if ($inopt_resm_education != 'off') {
                    ob_start();
                    ?>
                    <div class="jobsearch-candidate-resume-wrap">
                        <?php
                        if ($user_pkg_limits::cand_field_is_locked('resmedu_defields')) {
                            ob_start();
                            ?>
                            <div class="jobsearch-candidate-title">
                                <h2>
                                    <i class="jobsearch-icon jobsearch-mortarboard"></i> <?php esc_html_e('Education', 'wp-jobsearch') ?>
                                </h2>
                            </div>
                            <?php echo($user_pkg_limits::cand_gen_locked_html()) ?>
                            <?php
                            $lock_field_cushtml = ob_get_clean();
                            $lock_field_html = $user_pkg_limits->cand_field_locked_html($lock_field_cushtml);
                            echo($lock_field_html);
                        } else {
                            ob_start();
                            ?>
                            <div class="jobsearch-candidate-title">
                                <h2>
                                    <i class="jobsearch-icon jobsearch-mortarboard"></i> <?php esc_html_e('Education', 'wp-jobsearch') ?>
                                    <a href="javascript:void(0)" class="jobsearch-resume-addbtn"><span
                                                class="fa fa-plus"></span> <?php esc_html_e('Add education', 'wp-jobsearch') ?>
                                    </a>
                                </h2>
                            </div>
                            <div class="jobsearch-add-popup jobsearch-add-resume-item-popup">
                                <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                <script>
                                    jQuery(document).ready(function () {
                                        jQuery('#add-edu-date-start').datetimepicker({
                                            timepicker: false,
                                            format: 'Y-m-d',
                                            onShow: function (ct) {
                                                this.setOptions({
                                                    maxDate: jQuery('#add-edu-date-end').val() ? jQuery('#add-edu-date-end').val() : false
                                                })
                                            },
                                        });
                                        jQuery('#add-edu-date-end').datetimepicker({
                                            timepicker: false,
                                            format: 'Y-m-d',
                                            onShow: function (ct) {
                                                this.setOptions({
                                                    minDate: jQuery('#add-edu-date-start').val() ? jQuery('#add-edu-date-start').val() : false
                                                })
                                            },
                                        });
                                        jQuery('input[name^="jobsearch_field_education_start_date"]').datetimepicker({
                                            timepicker: false,
                                            format: 'Y-m-d',
                                            onShow: function (ct) {
                                                this.setOptions({
                                                    maxDate: jQuery('input[name^="jobsearch_field_education_end_date"]').val() ? jQuery('input[name^="jobsearch_field_education_end_date"]').val() : false
                                                })
                                            },
                                        });
                                        jQuery('input[name^="jobsearch_field_education_end_date"]').datetimepicker({
                                            timepicker: false,
                                            format: 'Y-m-d',
                                            onShow: function (ct) {
                                                this.setOptions({
                                                    minDate: jQuery('input[name^="jobsearch_field_education_start_date"]').val() ? jQuery('input[name^="jobsearch_field_education_start_date"]').val() : false
                                                })
                                            },
                                        });
                                        //
                                        jQuery(document).on('click', '.cand-edu-prsntchkbtn', function () {
                                            var _this = jQuery(this);
                                            var thisu_id = _this.attr('data-id');
                                            if (_this.is(":checked")) {
                                                jQuery('.cand-edu-todatefield-' + thisu_id).hide();
                                                _this.parent('.cand-edu-prsntfield').find('input[type="hidden"]').val('on');
                                            } else {
                                                jQuery('.cand-edu-todatefield-' + thisu_id).show();
                                                _this.parent('.cand-edu-prsntfield').find('input[type="hidden"]').val('');
                                            }
                                        });
                                    });
                                </script>
                                <ul class="jobsearch-row jobsearch-employer-profile-form">
                                    <li class="jobsearch-column-12">
                                        <?php
                                        ob_start();
                                        ?>
                                        <label><?php esc_html_e('Title *', 'wp-jobsearch') ?></label>
                                        <?php
                                        $title_html = ob_get_clean();
                                        echo apply_filters('jobsearch_candash_resume_edutitle_label', $title_html);
                                        ?>
                                        <input id="add-edu-title" class="jobsearch-req-field" type="text">
                                    </li>

                                    <li class="jobsearch-column-4">
                                        <label><?php esc_html_e('From Date *', 'wp-jobsearch') ?></label>
                                        <input id="add-edu-date-start" class="jobsearch-req-field" type="text">
                                    </li>
                                    <li class="jobsearch-column-4 cand-edu-todatefield-0">
                                        <label><?php esc_html_e('To Date', 'wp-jobsearch') ?></label>
                                        <input id="add-edu-date-end" type="text">
                                    </li>
                                    <li class="jobsearch-column-4 cand-edu-prsntfield">
                                        <label><?php esc_html_e('Present', 'wp-jobsearch') ?></label>
                                        <input class="cand-edu-prsntchkbtn" data-id="0" type="checkbox">
                                        <input id="add-edu-date-prsent" type="hidden">
                                    </li>
                                    <li class="jobsearch-column-12">
                                        <label><?php esc_html_e('Institute *', 'wp-jobsearch') ?></label>
                                        <input id="add-edu-institute" class="jobsearch-req-field" type="text">
                                    </li>
                                    <?php
                                    echo apply_filters('jobsearch_cand_dash_resume_edu_add_bfor_desc', '');
                                    ?>
                                    <li class="jobsearch-column-12">
                                        <label><?php echo esc_html(_x('Description', 'Resume Education Description', 'wp-jobsearch')) ?></label>
                                        <textarea id="add-edu-desc" <?php echo apply_filters('jobsearch_candash_resume_edudesc_atts', ''); ?>></textarea>
                                    </li>
                                    <li class="jobsearch-column-12">
                                        <input id="<?php echo apply_filters('jobsearch_cand_dash_resume_edu_add_btnid', 'add-education-btn') ?>"
                                               type="submit"
                                               value="<?php esc_html_e('Add education', 'wp-jobsearch') ?>">
                                        <span class="edu-loding-msg"></span>
                                    </li>
                                </ul>
                            </div>
                            <?php
                            $edu_add_html = ob_get_clean();
                            echo apply_filters('jobsearch_cand_dash_resume_addedu_html', $edu_add_html, $candidate_id);
                            ob_start();
                            ?>
                            <div id="jobsearch-resume-edu-con" class="jobsearch-resume-education">
                                <ul class="jobsearch-row">
                                    <?php
                                    $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_title', true);
                                    $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_education_description', true);
                                    $education_academyfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_academy', true);
                                    $education_start_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_education_start_date', true);
                                    $education_end_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_education_end_date', true);
                                    $education_prsnt_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_education_date_prsnt', true);

                                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                                        $new_edu_filedsarr = array();
                                        $exfield_counter = 0;
                                        foreach ($exfield_list as $exfield) {

                                            $exfield_desc = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                            $exfield_start_date = isset($education_start_datefield_list[$exfield_counter]) ? $education_start_datefield_list[$exfield_counter] : '';
                                            $exfield_end_date = isset($education_end_datefield_list[$exfield_counter]) ? $education_end_datefield_list[$exfield_counter] : '';
                                            $exfield_prsnt_field = isset($education_prsnt_datefield_list[$exfield_counter]) ? $education_prsnt_datefield_list[$exfield_counter] : '';
                                            $exfield_institute = isset($education_academyfield_list[$exfield_counter]) ? $education_academyfield_list[$exfield_counter] : '';

                                            $edu_sort_date = 0;
                                            if ($exfield_start_date != '') {
                                                $edu_sort_date = strtotime($exfield_start_date);
                                            }

                                            $new_edu_filedsarr[] = array(
                                                'title' => $exfield,
                                                'desc' => $exfield_desc,
                                                'start_date' => $exfield_start_date,
                                                'end_date' => $exfield_end_date,
                                                'present' => $exfield_prsnt_field,
                                                'institute' => $exfield_institute,
                                                'sort_date' => $edu_sort_date,
                                            );

                                            $exfield_counter++;
                                        }

                                        //usort($new_edu_filedsarr, function($a, $b) {
                                        //return $b['sort_date'] <=> $a['sort_date'];
                                        //});
                                        usort($new_edu_filedsarr, function ($a, $b) {
                                            if ($a['sort_date'] == $b['sort_date']) {
                                                $ret_val = 0;
                                            }
                                            $ret_val = ($b['sort_date'] < $a['sort_date']) ? -1 : 1;
                                            return $ret_val;
                                        });

                                        $exfield_counter = 0;
                                        foreach ($new_edu_filedsarr as $new_exfield) {
                                            $rand_num = rand(1000000, 99999999);


                                            $exfield = isset($new_exfield['title']) ? $new_exfield['title'] : '';
                                            $exfield_val = isset($new_exfield['desc']) ? $new_exfield['desc'] : '';
                                            $education_start_datefield_val = isset($new_exfield['start_date']) ? $new_exfield['start_date'] : '';
                                            $education_end_datefield_val = isset($new_exfield['end_date']) ? $new_exfield['end_date'] : '';
                                            $education_prsnt_datefield_val = isset($new_exfield['present']) ? $new_exfield['present'] : '';
                                            $education_academyfield_val = isset($new_exfield['institute']) ? $new_exfield['institute'] : '';

                                            $exfield = jobsearch_esc_html($exfield);
                                            $exfield_val = jobsearch_esc_html($exfield_val);
                                            $education_start_datefield_val = jobsearch_esc_html($education_start_datefield_val);
                                            $education_end_datefield_val = jobsearch_esc_html($education_end_datefield_val);
                                            $education_prsnt_datefield_val = jobsearch_esc_html($education_prsnt_datefield_val);
                                            $education_academyfield_val = jobsearch_esc_html($education_academyfield_val);
                                            ?>
                                            <li class="jobsearch-column-12 resume-list-item resume-list-edu">
                                                <div class="jobsearch-resume-education-wrap">
                                                    <small><?php echo($education_start_datefield_val != '' ? date_i18n(get_option('date_format'), strtotime($education_start_datefield_val)) : '') ?>
                                                        - <?php echo($education_prsnt_datefield_val == 'on' ? 'Present' : '') ?><?php echo($education_end_datefield_val != '' && $education_prsnt_datefield_val != 'on' ? date_i18n(get_option('date_format'), strtotime($education_end_datefield_val)) : '') ?></small>
                                                    <h2 class="jobsearch-pst-title">
                                                        <a><?php echo jobsearch_esc_html($exfield) ?></a></h2>
                                                    <span><?php echo jobsearch_esc_html($education_academyfield_val) ?></span>
                                                </div>
                                                <div class="jobsearch-resume-education-btn">
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-icon jobsearch-edit jobsearch-tooltipcon update-resume-item"
                                                       title="<?php esc_html_e('Update', 'wp-jobsearch') ?>"></a>
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-icon jobsearch-rubbish jobsearch-tooltipcon <?php echo(apply_filters('jobsearch_candash_resume_edulist_itmdelclass', 'del-resume-item', $rand_num)) ?>"
                                                       data-id="<?php echo($rand_num) ?>"
                                                       title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"></a>
                                                </div>
                                                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                                                    <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                                                        <li class="jobsearch-column-12">
                                                            <?php
                                                            ob_start();
                                                            ?>
                                                            <label><?php esc_html_e('Title *', 'wp-jobsearch') ?></label>
                                                            <?php
                                                            $title_html = ob_get_clean();
                                                            echo apply_filters('jobsearch_candash_resume_edutitle_label', $title_html);
                                                            ?>
                                                            <input name="jobsearch_field_education_title[]" type="text"
                                                                   value="<?php echo jobsearch_esc_html($exfield) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-4">
                                                            <label><?php esc_html_e('Start Date *', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_education_start_date[]"
                                                                   type="text"
                                                                   value="<?php echo($education_start_datefield_val) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-4 cand-edu-todatefield-<?php echo($rand_num) ?>" <?php echo($education_prsnt_datefield_val == 'on' ? 'style="display: none;"' : '') ?>>
                                                            <label><?php esc_html_e('End Date', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_education_end_date[]"
                                                                   type="text"
                                                                   value="<?php echo($education_end_datefield_val) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-4 cand-edu-prsntfield">
                                                            <label><?php esc_html_e('Present', 'wp-jobsearch') ?></label>
                                                            <input class="cand-edu-prsntchkbtn"
                                                                   data-id="<?php echo($rand_num) ?>"
                                                                   type="checkbox" <?php echo($education_prsnt_datefield_val == 'on' ? 'checked' : '') ?>>
                                                            <input name="jobsearch_field_education_date_prsnt[]"
                                                                   type="hidden"
                                                                   value="<?php echo($education_prsnt_datefield_val) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-12">
                                                            <label><?php esc_html_e('Institute *', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_education_academy[]"
                                                                   type="text"
                                                                   value="<?php echo jobsearch_esc_html($education_academyfield_val) ?>">
                                                        </li>
                                                        <?php
                                                        echo apply_filters('jobsearch_cand_dash_resume_edu_updt_bfor_desc', '', $candidate_id, $exfield_counter);
                                                        ?>
                                                        <li class="jobsearch-column-12">
                                                            <label><?php echo esc_html(_x('Description', 'Resume Education Description', 'wp-jobsearch')) ?></label>
                                                            <textarea
                                                                    name="jobsearch_field_education_description[]" <?php echo apply_filters('jobsearch_candash_resume_edudesc_atts', ''); ?>><?php echo jobsearch_esc_html($exfield_val) ?></textarea>
                                                        </li>
                                                        <li class="jobsearch-column-12">
                                                            <input class="update-resume-list-btn" type="submit"
                                                                   value="<?php esc_html_e('Update', 'wp-jobsearch') ?>">
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
                            $edu_list_html = ob_get_clean();
                            echo apply_filters('jobsearch_cand_dash_resume_eduslist_html', $edu_list_html, $candidate_id);
                        }
                        ?>
                    </div>
                    <?php
                    $resm_edu_oall_html = ob_get_clean();
                    $resm_edu_oall_html = apply_filters('jobsearch_candidate_dash_resume_educ_html', $resm_edu_oall_html, $candidate_id);
                }

                if ($inopt_resm_experience != 'off') {
                    ob_start();
                    ?>
                    <div class="jobsearch-candidate-resume-wrap">
                        <?php
                        if ($user_pkg_limits::cand_field_is_locked('resmexp_defields')) {
                            ob_start();
                            ?>
                            <div class="jobsearch-candidate-title">
                                <h2>
                                    <?php
                                    ob_start();
                                    ?>
                                    <i class="jobsearch-icon jobsearch-social-media"></i> <?php esc_html_e('Experience', 'wp-jobsearch') ?>
                                    <?php
                                    $title_html = ob_get_clean();
                                    echo apply_filters('jobsearch_candash_resume_expmain_label', $title_html);
                                    ?>
                                </h2>
                            </div>
                            <?php echo($user_pkg_limits::cand_gen_locked_html()) ?>
                            <?php
                            $lock_field_cushtml = ob_get_clean();
                            $lock_field_html = $user_pkg_limits->cand_field_locked_html($lock_field_cushtml);
                            echo($lock_field_html);
                        } else {
                            ob_start();
                            ?>
                            <div class="jobsearch-candidate-title">
                                <h2>
                                    <?php
                                    ob_start();
                                    ?>
                                    <i class="jobsearch-icon jobsearch-social-media"></i> <?php esc_html_e('Experience', 'wp-jobsearch') ?>
                                    <?php
                                    $title_html = ob_get_clean();
                                    echo apply_filters('jobsearch_candash_resume_expmain_label', $title_html);
                                    ?>
                                    <a href="javascript:void(0)" class="jobsearch-resume-addbtn"><span
                                                class="fa fa-plus"></span> <?php esc_html_e('Add experience', 'wp-jobsearch') ?>
                                    </a>
                                </h2>
                            </div>

                            <div class="jobsearch-add-popup jobsearch-add-resume-item-popup">
                                <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                <script>
                                    jQuery(document).ready(function () {
                                        jQuery('#add-expr-date-start').datetimepicker({
                                            timepicker: false,
                                            format: 'Y-m-d',
                                            onShow: function (ct) {
                                                this.setOptions({
                                                    maxDate: jQuery('#add-expr-date-end').val() ? jQuery('#add-expr-date-end').val() : false
                                                })
                                            },
                                        });
                                        jQuery('#add-expr-date-end').datetimepicker({
                                            timepicker: false,
                                            format: 'Y-m-d',
                                            onShow: function (ct) {
                                                this.setOptions({
                                                    minDate: jQuery('#add-expr-date-start').val() ? jQuery('#add-expr-date-start').val() : false
                                                })
                                            },
                                        });
                                        jQuery('input[name^="jobsearch_field_experience_start_date"]').datetimepicker({
                                            timepicker: false,
                                            format: 'Y-m-d',
                                            onShow: function (ct) {
                                                this.setOptions({
                                                    maxDate: jQuery('input[name^="jobsearch_field_experience_end_date"]').val() ? jQuery('input[name^="jobsearch_field_experience_end_date"]').val() : false
                                                })
                                            },
                                        });
                                        jQuery('input[name^="jobsearch_field_experience_end_date"]').datetimepicker({
                                            timepicker: false,
                                            format: 'Y-m-d',
                                            onShow: function (ct) {
                                                this.setOptions({
                                                    minDate: jQuery('input[name^="jobsearch_field_experience_start_date"]').val() ? jQuery('input[name^="jobsearch_field_experience_start_date"]').val() : false
                                                })
                                            },
                                        });
                                        //
                                        jQuery(document).on('click', '.cand-expr-prsntchkbtn', function () {
                                            var _this = jQuery(this);
                                            var thisu_id = _this.attr('data-id');
                                            if (_this.is(":checked")) {
                                                jQuery('.cand-expr-todatefield-' + thisu_id).hide();
                                                _this.parent('.cand-expr-prsntfield').find('input[type="hidden"]').val('on');
                                            } else {
                                                jQuery('.cand-expr-todatefield-' + thisu_id).show();
                                                _this.parent('.cand-expr-prsntfield').find('input[type="hidden"]').val('');
                                            }
                                        });
                                    });
                                </script>
                                <ul class="jobsearch-row jobsearch-employer-profile-form">
                                    <li class="jobsearch-column-12">
                                        <label><?php esc_html_e('Title *', 'wp-jobsearch') ?></label>
                                        <input id="add-expr-title" class="jobsearch-req-field" type="text">
                                    </li>
                                    <li class="jobsearch-column-4">
                                        <label><?php esc_html_e('From Date *', 'wp-jobsearch') ?></label>
                                        <input id="add-expr-date-start" class="jobsearch-req-field" type="text">
                                    </li>
                                    <li class="jobsearch-column-4 cand-expr-todatefield-0">
                                        <label><?php esc_html_e('To Date', 'wp-jobsearch') ?></label>
                                        <input id="add-expr-date-end" type="text">
                                    </li>
                                    <li class="jobsearch-column-4 cand-expr-prsntfield">
                                        <label><?php esc_html_e('Present', 'wp-jobsearch') ?></label>
                                        <input class="cand-expr-prsntchkbtn" data-id="0" type="checkbox">
                                        <input id="add-expr-date-prsent" type="hidden">
                                    </li>
                                    <li class="jobsearch-column-12">
                                        <?php
                                        ob_start();
                                        ?>
                                        <label><?php esc_html_e('Company *', 'wp-jobsearch') ?></label>
                                        <?php
                                        $title_html = ob_get_clean();
                                        echo apply_filters('jobsearch_candash_resume_expcompny_label', $title_html);
                                        ?>
                                        <input id="add-expr-company" class="jobsearch-req-field" type="text">
                                    </li>
                                    <?php
                                    echo apply_filters('jobsearch_cand_dash_resume_expr_add_bfor_desc', '');
                                    ?>
                                    <li class="jobsearch-column-12">
                                        <?php
                                        ob_start();
                                        ?>
                                        <label><?php echo esc_html(_x('Description', 'Resume Experience Description', 'wp-jobsearch')) ?></label>
                                        <?php
                                        $title_html = ob_get_clean();
                                        echo apply_filters('jobsearch_candash_resume_expdesc_label', $title_html);
                                        ?>
                                        <textarea
                                                id="add-expr-desc" <?php echo apply_filters('jobsearch_candash_resume_expdesc_atts', '') ?>></textarea>
                                    </li>
                                    <li class="jobsearch-column-12">
                                        <input id="<?php echo apply_filters('jobsearch_cand_dash_resume_expr_add_btnid', 'add-experience-btn') ?>"
                                               type="submit"
                                               value="<?php esc_html_e('Add experience', 'wp-jobsearch') ?>">
                                        <span class="expr-loding-msg edu-loding-msg"></span>
                                    </li>
                                </ul>
                            </div>
                            <?php
                            $res_exp_html = ob_get_clean();
                            echo apply_filters('jobsearch_candidate_dash_resume_expadd_html', $res_exp_html, $candidate_id);

                            ob_start();
                            ?>
                            <div id="jobsearch-resume-expr-con" class="jobsearch-resume-education">
                                <ul class="jobsearch-row">
                                    <?php
                                    $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_title', true);
                                    $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_experience_description', true);
                                    $experience_start_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_start_date', true);
                                    $experience_end_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_end_date', true);
                                    $experience_prsnt_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_date_prsnt', true);
                                    $experience_company_field_list = get_post_meta($candidate_id, 'jobsearch_field_experience_company', true);
                                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                                        $new_exp_filedsarr = array();
                                        $exfield_counter = 0;
                                        foreach ($exfield_list as $exfield) {

                                            $exfield_desc = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                            $exfield_start_date = isset($experience_start_datefield_list[$exfield_counter]) ? $experience_start_datefield_list[$exfield_counter] : '';
                                            $exfield_end_date = isset($experience_end_datefield_list[$exfield_counter]) ? $experience_end_datefield_list[$exfield_counter] : '';
                                            $exfield_prsnt_field = isset($experience_prsnt_datefield_list[$exfield_counter]) ? $experience_prsnt_datefield_list[$exfield_counter] : '';
                                            $exfield_company = isset($experience_company_field_list[$exfield_counter]) ? $experience_company_field_list[$exfield_counter] : '';

                                            $exp_sort_date = 0;
                                            if ($exfield_start_date != '') {
                                                $exp_sort_date = strtotime($exfield_start_date);
                                            }

                                            $new_exp_filedsarr[] = array(
                                                'title' => $exfield,
                                                'desc' => $exfield_desc,
                                                'start_date' => $exfield_start_date,
                                                'end_date' => $exfield_end_date,
                                                'present' => $exfield_prsnt_field,
                                                'company' => $exfield_company,
                                                'sort_date' => $exp_sort_date,
                                            );

                                            $exfield_counter++;
                                        }

                                        //usort($new_exp_filedsarr, function($a, $b) {
                                        //return $b['sort_date'] <=> $a['sort_date'];
                                        //});
                                        usort($new_exp_filedsarr, function ($a, $b) {
                                            if ($a['sort_date'] == $b['sort_date']) {
                                                $ret_val = 0;
                                            }
                                            $ret_val = ($b['sort_date'] < $a['sort_date']) ? -1 : 1;
                                            return $ret_val;
                                        });

                                        $exfield_counter = 0;
                                        foreach ($new_exp_filedsarr as $new_exfield) {
                                            $rand_num = rand(1000000, 99999999);

                                            $exfield = isset($new_exfield['title']) ? $new_exfield['title'] : '';
                                            $exfield_val = isset($new_exfield['desc']) ? $new_exfield['desc'] : '';
                                            $experience_start_datefield_val = isset($new_exfield['start_date']) ? $new_exfield['start_date'] : '';
                                            $experience_end_datefield_val = isset($new_exfield['end_date']) ? $new_exfield['end_date'] : '';
                                            $experience_prsnt_datefield_val = isset($new_exfield['present']) ? $new_exfield['present'] : '';
                                            $experience_end_companyfield_val = isset($new_exfield['company']) ? $new_exfield['company'] : '';

                                            $exfield = jobsearch_esc_html($exfield);
                                            $exfield_val = jobsearch_esc_html($exfield_val);
                                            $experience_start_datefield_val = jobsearch_esc_html($experience_start_datefield_val);
                                            $experience_end_datefield_val = jobsearch_esc_html($experience_end_datefield_val);
                                            $experience_prsnt_datefield_val = jobsearch_esc_html($experience_prsnt_datefield_val);
                                            $experience_end_companyfield_val = jobsearch_esc_html($experience_end_companyfield_val);
                                            ?>
                                            <li class="jobsearch-column-12 resume-list-item resume-list-exp">
                                                <div class="jobsearch-resume-education-wrap">
                                                    <small><?php echo($experience_start_datefield_val != '' ? date_i18n(get_option('date_format'), strtotime($experience_start_datefield_val)) : '') ?>
                                                        - <?php echo($experience_prsnt_datefield_val == 'on' ? 'Present' : '') ?><?php echo($experience_end_datefield_val != '' && $experience_prsnt_datefield_val != 'on' ? date_i18n(get_option('date_format'), strtotime($experience_end_datefield_val)) : '') ?></small>
                                                    <h2 class="jobsearch-pst-title"><a><?php echo($exfield) ?></a></h2>
                                                    <span><?php echo($experience_end_companyfield_val) ?></span>
                                                </div>
                                                <div class="jobsearch-resume-education-btn">

                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-icon jobsearch-edit jobsearch-tooltipcon update-resume-item"
                                                       title="<?php esc_html_e('Update', 'wp-jobsearch') ?>"></a>
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-icon jobsearch-rubbish jobsearch-tooltipcon <?php echo(apply_filters('jobsearch_candash_resume_explist_itmdelclass', 'del-resume-item', $rand_num)) ?>"
                                                       data-id="<?php echo($rand_num) ?>"
                                                       title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"></a>
                                                </div>
                                                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                                                    <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                                                        <li class="jobsearch-column-12">
                                                            <label><?php esc_html_e('Title *', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_experience_title[]" type="text"
                                                                   value="<?php echo($exfield) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-4">
                                                            <label><?php esc_html_e('Start Date *', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_experience_start_date[]"
                                                                   type="text"
                                                                   value="<?php echo($experience_start_datefield_val) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-4 cand-expr-todatefield-<?php echo($rand_num) ?>" <?php echo($experience_prsnt_datefield_val == 'on' ? 'style="display: none;"' : '') ?>>
                                                            <label><?php esc_html_e('End Date', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_experience_end_date[]"
                                                                   type="text"
                                                                   value="<?php echo($experience_end_datefield_val) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-4 cand-expr-prsntfield">
                                                            <label><?php esc_html_e('Present', 'wp-jobsearch') ?></label>
                                                            <input class="cand-expr-prsntchkbtn"
                                                                   data-id="<?php echo($rand_num) ?>"
                                                                   type="checkbox" <?php echo($experience_prsnt_datefield_val == 'on' ? 'checked' : '') ?>>
                                                            <input name="jobsearch_field_experience_date_prsnt[]"
                                                                   type="hidden"
                                                                   value="<?php echo($experience_prsnt_datefield_val) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-12">
                                                            <?php
                                                            ob_start();
                                                            ?>
                                                            <label><?php esc_html_e('Company *', 'wp-jobsearch') ?></label>
                                                            <?php
                                                            $title_html = ob_get_clean();
                                                            echo apply_filters('jobsearch_candash_resume_expcompny_label', $title_html);
                                                            ?>
                                                            <input name="jobsearch_field_experience_company[]"
                                                                   type="text"
                                                                   value="<?php echo($experience_end_companyfield_val) ?>">
                                                        </li>
                                                        <?php
                                                        echo apply_filters('jobsearch_cand_dash_resume_expr_updt_bfor_desc', '', $candidate_id, $exfield_counter);
                                                        ?>
                                                        <li class="jobsearch-column-12">
                                                            <?php
                                                            ob_start();
                                                            ?>
                                                            <label><?php echo esc_html(_x('Description', 'Resume Experience Description', 'wp-jobsearch')) ?></label>
                                                            <?php
                                                            $title_html = ob_get_clean();
                                                            echo apply_filters('jobsearch_candash_resume_expdesc_label', $title_html);
                                                            ?>
                                                            <textarea
                                                                    name="jobsearch_field_experience_description[]" <?php echo apply_filters('jobsearch_candash_resume_expdesc_atts', '') ?>><?php echo($exfield_val) ?></textarea>
                                                        </li>
                                                        <li class="jobsearch-column-12">
                                                            <input class="update-resume-list-btn" type="submit"
                                                                   value="<?php esc_html_e('Update', 'wp-jobsearch') ?>">
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
                            $res_exp_html = ob_get_clean();
                            echo apply_filters('jobsearch_candidate_dash_resume_expslist_html', $res_exp_html, $candidate_id);
                        }
                        ?>
                    </div>
                    <?php
                    $resm_exp_oall_html = ob_get_clean();
                    $resm_exp_oall_html = apply_filters('jobsearch_cand_dash_resume_exp_oall', $resm_exp_oall_html, $candidate_id);
                }
                if ($inopt_resm_portfolio != 'off') {
                    if ($user_pkg_limits::cand_field_is_locked('resmport_defields')) {
                        ob_start();
                        ?>
                        <div class="jobsearch-candidate-title">
                            <h2>
                                <i class="jobsearch-icon jobsearch-social-media"></i> <?php esc_html_e('Portfolio', 'wp-jobsearch') ?>
                            </h2>
                        </div>
                        <?php echo($user_pkg_limits::cand_gen_locked_html()) ?>
                        <?php
                        $lock_field_cushtml = ob_get_clean();
                        $lock_field_html = $user_pkg_limits->cand_field_locked_html($lock_field_cushtml);
                        echo($lock_field_html);
                    } else {
                        ob_start();
                        ?>
                        <div class="jobsearch-candidate-resume-wrap">
                            <?php
                            ob_start();
                            ?>
                            <div class="jobsearch-candidate-title">
                                <h2>
                                    <i class="jobsearch-icon jobsearch-briefcase"></i> <?php esc_html_e('Portfolio', 'wp-jobsearch') ?>
                                    <a href="javascript:void(0)"
                                       class="jobsearch-resume-addbtn jobsearch-portfolio-add-btn"><span
                                                class="fa fa-plus"></span> <?php esc_html_e('Add Portfolio', 'wp-jobsearch') ?>
                                    </a>
                                </h2>
                            </div>
                            <div class="jobsearch-add-popup jobsearch-add-resume-item-popup">
                                <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                <ul class="jobsearch-row jobsearch-employer-profile-form">
                                    <li class="jobsearch-column-12">
                                        <label><?php esc_html_e('Title *', 'wp-jobsearch') ?></label>
                                        <input id="add-portfolio-title" class="jobsearch-req-field" type="text">
                                    </li>
                                    <li class="jobsearch-column-6">
                                        <label><?php esc_html_e('Image *', 'wp-jobsearch') ?></label>
                                        <div class="upload-img-holder-sec">
                                            <span class="file-loader"></span>
                                            <img src="" alt="">
                                            <br>
                                            <input name="add_portfolio_img" type="file" style="display: none;">
                                            <input type="hidden" id="add-portfolio-img-input"
                                                   class="jobsearch-req-field">
                                            <a href="javascript:void(0)" class="upload-port-img-btn"><i
                                                        class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Upload Photo', 'wp-jobsearch') ?>
                                            </a>
                                        </div>
                                    </li>
                                    <?php
                                    ob_start();
                                    ?>
                                    <li class="jobsearch-column-12">
                                        <label><?php esc_html_e('Video URL', 'wp-jobsearch') ?></label>
                                        <input id="add-portfolio-vurl" type="text">
                                        <em><?php esc_html_e('Add video URL of Youtube, Vimeo.', 'wp-jobsearch') ?></em>
                                    </li>
                                    <?php
                                    $vidurl_html = ob_get_clean();
                                    echo apply_filters('jobsearch_cand_dash_resume_port_add_vurl', $vidurl_html);
                                    ?>
                                    <li class="jobsearch-column-12">
                                        <label><?php esc_html_e('URL', 'wp-jobsearch') ?></label>
                                        <input id="add-portfolio-url" type="text">
                                    </li>
                                    <li class="jobsearch-column-12">
                                        <input type="submit" id="add-resume-portfolio-btn"
                                               value="<?php esc_html_e('Add Portfolio', 'wp-jobsearch') ?>">
                                        <span class="portfolio-loding-msg edu-loding-msg"></span>
                                    </li>
                                </ul>
                            </div>
                            <?php
                            $res_port_html = ob_get_clean();
                            echo apply_filters('jobsearch_candidate_dash_resume_portadd_html', $res_port_html, $candidate_id);

                            //
                            ob_start();
                            ?>
                            <div id="jobsearch-resume-portfolio-con" class="jobsearch-company-gallery">
                                <ul class="jobsearch-row jobsearch-portfolios-list-con">

                                    <?php
                                    $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_portfolio_title', true);
                                    $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_portfolio_image', true);
                                    $exfield_portfolio_url = get_post_meta($candidate_id, 'jobsearch_field_portfolio_url', true);
                                    $exfield_portfolio_vurl = get_post_meta($candidate_id, 'jobsearch_field_portfolio_vurl', true);
                                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                                        $exfield_counter = 0;
                                        foreach ($exfield_list as $exfield) {
                                            $rand_num = rand(1000000, 99999999);

                                            $portfolio_img = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                            $portfolio_url = isset($exfield_portfolio_url[$exfield_counter]) ? $exfield_portfolio_url[$exfield_counter] : '';
                                            $portfolio_vurl = isset($exfield_portfolio_vurl[$exfield_counter]) ? $exfield_portfolio_vurl[$exfield_counter] : '';

                                            $exfield = jobsearch_esc_html($exfield);
                                            $portfolio_img = jobsearch_esc_html($portfolio_img);
                                            $portfolio_url = jobsearch_esc_html($portfolio_url);
                                            $portfolio_vurl = jobsearch_esc_html($portfolio_vurl);

                                            $portfolio_img_src = jobsearch_get_cand_portimg_url($candidate_id, $portfolio_img);
                                            ?>
                                            <li class="jobsearch-column-3 resume-list-item resume-list-port">
                                                <figure>
                                                    <a class="portfolio-img-holder"><span
                                                                style="background-image: url('<?php echo($portfolio_img_src) ?>');"></span></a>
                                                    <figcaption>
                                                        <span><?php echo($exfield) ?></span>
                                                        <div class="jobsearch-company-links">
                                                            <a href="javascript:void(0);"
                                                               class="jobsearch-icon jobsearch-sort jobsearch-tooltipcon el-drag-item"
                                                               title="<?php esc_html_e('Drag', 'wp-jobsearch') ?>"></a>
                                                            <a href="javascript:void(0);"
                                                               class="jobsearch-icon jobsearch-edit jobsearch-tooltipcon update-resume-item"
                                                               title="<?php esc_html_e('Update', 'wp-jobsearch') ?>"></a>
                                                            <a href="javascript:void(0);"
                                                               class="jobsearch-icon jobsearch-rubbish jobsearch-tooltipcon <?php echo(apply_filters('jobsearch_candash_resume_portlist_itmdelclass', 'del-resume-item', $rand_num)) ?>"
                                                               data-id="<?php echo($rand_num) ?>"
                                                               title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"></a>
                                                        </div>
                                                    </figcaption>
                                                </figure>
                                                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                                                    <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                                                        <li class="jobsearch-column-12">
                                                            <label><?php esc_html_e('Title *', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_portfolio_title[]" type="text"
                                                                   value="<?php echo($exfield) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-6">
                                                            <label><?php esc_html_e('Image *', 'wp-jobsearch') ?></label>
                                                            <div class="upload-img-holder-sec">
                                                                <span class="file-loader"></span>
                                                                <img src="<?php echo($portfolio_img_src) ?>" alt="">
                                                                <br>
                                                                <input name="add_portfolio_img" type="file"
                                                                       style="display: none;">
                                                                <input type="hidden" class="img-upload-save-field"
                                                                       name="jobsearch_field_portfolio_image[]"
                                                                       value="<?php echo($portfolio_img) ?>">
                                                                <a href="javascript:void(0)"
                                                                   class="upload-port-img-btn"><i
                                                                            class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Upload Photo', 'wp-jobsearch') ?>
                                                                </a>
                                                            </div>
                                                        </li>
                                                        <?php
                                                        ob_start();
                                                        ?>
                                                        <li class="jobsearch-column-12">
                                                            <label><?php esc_html_e('Video URL', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_portfolio_vurl[]" type="text"
                                                                   value="<?php echo($portfolio_vurl) ?>">
                                                            <em><?php esc_html_e('Add video URL of Youtube, Vimeo.', 'wp-jobsearch') ?></em>
                                                        </li>
                                                        <?php
                                                        $vidurl_html = ob_get_clean();
                                                        echo apply_filters('jobsearch_cand_dash_resume_port_updte_vurl', $vidurl_html, $portfolio_vurl, $candidate_id);
                                                        ?>
                                                        <li class="jobsearch-column-12">
                                                            <label><?php esc_html_e('URL', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_portfolio_url[]" type="text"
                                                                   value="<?php echo($portfolio_url) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-12">
                                                            <input class="update-resume-list-btn" type="submit"
                                                                   value="<?php esc_html_e('Update', 'wp-jobsearch') ?>">
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
                            $res_port_html = ob_get_clean();
                            echo apply_filters('jobsearch_candidate_dash_resume_portslist_html', $res_port_html, $candidate_id);
                            ?>
                        </div>
                        <?php
                        $resm_port_oall_html = ob_get_clean();
                        $resm_port_oall_html = apply_filters('jobsearch_cand_dash_resume_port_oall', $resm_port_oall_html, $candidate_id);
                    }
                }
                if ($inopt_resm_skills != 'off') {
                    if ($user_pkg_limits::cand_field_is_locked('resmskills_defields')) {
                        ob_start();
                        ?>
                        <div class="jobsearch-candidate-title">
                            <h2>
                                <?php
                                ob_start();
                                ?>
                                <i class="jobsearch-icon jobsearch-social-media"></i> <?php esc_html_e('Expertise', 'wp-jobsearch') ?>
                                <?php
                                $title_html = ob_get_clean();
                                echo apply_filters('jobsearch_candash_resume_exprtizemain_label', $title_html);
                                ?>
                            </h2>
                        </div>
                        <?php echo($user_pkg_limits::cand_gen_locked_html()) ?>
                        <?php
                        $lock_field_cushtml = ob_get_clean();
                        $lock_field_html = $user_pkg_limits->cand_field_locked_html($lock_field_cushtml);
                        echo($lock_field_html);
                    } else {
                        ob_start();
                        ?>
                        <div class="jobsearch-candidate-resume-wrap">
                            <?php
                            ob_start();
                            ?>
                            <div class="jobsearch-candidate-title">
                                <h2>
                                    <?php
                                    ob_start();
                                    ?>
                                    <i class="jobsearch-icon jobsearch-social-media"></i> <?php esc_html_e('Expertise', 'wp-jobsearch') ?>
                                    <?php
                                    $title_html = ob_get_clean();
                                    echo apply_filters('jobsearch_candash_resume_exprtizemain_label', $title_html);
                                    ?>
                                    <a href="javascript:void(0)" class="jobsearch-resume-addbtn"><span
                                                class="fa fa-plus"></span> <?php esc_html_e('Add Expertise', 'wp-jobsearch') ?>
                                    </a>
                                </h2>
                            </div>

                            <div class="jobsearch-add-popup jobsearch-add-resume-item-popup">
                                <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                <ul class="jobsearch-row jobsearch-employer-profile-form">
                                    <li class="jobsearch-column-12">
                                        <label><?php esc_html_e('Label *', 'wp-jobsearch') ?></label>
                                        <input id="add-skill-title" class="jobsearch-req-field" type="text">
                                    </li>
                                    <li class="jobsearch-column-12">
                                        <label><?php esc_html_e('Percentage *', 'wp-jobsearch') ?></label>
                                        <input id="add-skill-percentage" class="jobsearch-req-field" type="number"
                                               placeholder="<?php esc_html_e('Enter a number between 1 to 100', 'wp-jobsearch') ?>"
                                               min="1" max="100">
                                    </li>
                                    <li class="jobsearch-column-12">
                                        <input type="submit" id="add-resume-skills-btn"
                                               value="<?php esc_html_e('Add Expertise', 'wp-jobsearch') ?>">
                                        <span class="skills-loding-msg edu-loding-msg"></span>
                                    </li>
                                </ul>
                            </div>
                            <?php
                            $res_skill_html = ob_get_clean();
                            echo apply_filters('jobsearch_candidate_dash_resume_skilladd_html', $res_skill_html, $candidate_id);
                            ?>
                            <div id="jobsearch-resume-skills-con"
                                 class="<?php echo apply_filters('jobsearch_canddash_resume_skillist_mainclass', 'jobsearch-add-skills') ?>">
                                <ul class="jobsearch-row">
                                    <?php
                                    $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_skill_title', true);
                                    $skill_percentagefield_list = get_post_meta($candidate_id, 'jobsearch_field_skill_percentage', true);
                                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                                        $exfield_counter = 0;
                                        foreach ($exfield_list as $exfield) {
                                            $rand_num = rand(1000000, 99999999);

                                            $skill_percentagefield_val = isset($skill_percentagefield_list[$exfield_counter]) ? $skill_percentagefield_list[$exfield_counter] : '';

                                            $exfield = jobsearch_esc_html($exfield);
                                            $skill_percentagefield_val = jobsearch_esc_html($skill_percentagefield_val);
                                            ?>
                                            <li class="jobsearch-column-12 resume-list-item resume-list-skill">
                                                <?php
                                                ob_start();
                                                ?>
                                                <div class="jobsearch-add-skills-wrap">
                                                    <span><?php echo($skill_percentagefield_val) ?></span>
                                                    <h2 class="jobsearch-pst-title"><a><?php echo($exfield) ?></a></h2>
                                                </div>
                                                <?php
                                                $skillist_html = ob_get_clean();
                                                echo apply_filters('jobsearch_canddash_resume_skillist_itmhtml', $skillist_html, $candidate_id, $exfield_counter);
                                                ?>
                                                <div class="jobsearch-resume-education-btn">
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-icon jobsearch-sort jobsearch-tooltipcon el-drag-item"
                                                       title="<?php esc_html_e('Drag', 'wp-jobsearch') ?>"></a>
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-icon jobsearch-edit jobsearch-tooltipcon update-resume-item"
                                                       title="<?php esc_html_e('Update', 'wp-jobsearch') ?>"></a>
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-icon jobsearch-rubbish jobsearch-tooltipcon <?php echo(apply_filters('jobsearch_candash_resume_skilllist_itmdelclass', 'del-resume-item', $rand_num)) ?>"
                                                       data-id="<?php echo($rand_num) ?>"
                                                       title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"></a>
                                                </div>
                                                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                                                    <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                                    <?php
                                                    ob_start();
                                                    ?>
                                                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                                                        <li class="jobsearch-column-12">
                                                            <label><?php esc_html_e('Label *', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_skill_title[]" type="text"
                                                                   value="<?php echo($exfield) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-6">
                                                            <label><?php esc_html_e('Percentage *', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_skill_percentage[]"
                                                                   type="number"
                                                                   placeholder="<?php esc_html_e('Enter a number between 1 to 100', 'wp-jobsearch') ?>"
                                                                   min="1" max="100"
                                                                   value="<?php echo($skill_percentagefield_val) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-12">
                                                            <input class="update-resume-list-btn" type="submit"
                                                                   value="<?php esc_html_e('Update', 'wp-jobsearch') ?>">
                                                        </li>
                                                    </ul>
                                                    <?php
                                                    $res_skill_html = ob_get_clean();
                                                    echo apply_filters('jobsearch_canddash_resm_skillupd_fields_html', $res_skill_html, $candidate_id, $exfield_counter);
                                                    ?>
                                                </div>
                                            </li>
                                            <?php
                                            $exfield_counter++;
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <?php
                        $resm_skill_oall_html = ob_get_clean();
                        $resm_skill_oall_html = apply_filters('jobsearch_cand_dash_resume_skill_oall', $resm_skill_oall_html, $candidate_id);
                    }
                }
                if ($inopt_resm_langs != 'off') {
                    ob_start();
                    ?>
                    <div class="jobsearch-candidate-resume-wrap">
                        <?php
                        ob_start();
                        ?>
                        <div class="jobsearch-candidate-title">
                            <h2>
                                <i class="jobsearch-icon jobsearch-social-media"></i> <?php esc_html_e('Languages', 'wp-jobsearch') ?>
                                <a href="javascript:void(0)" class="jobsearch-resume-addbtn"><span
                                            class="fa fa-plus"></span> <?php esc_html_e('Add Language', 'wp-jobsearch') ?>
                                </a>
                            </h2>
                        </div>

                        <div class="jobsearch-add-popup jobsearch-add-resume-item-popup">
                            <span class="close-popup-item"><i class="fa fa-times"></i></span>
                            <ul class="jobsearch-row jobsearch-employer-profile-form">
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Label *', 'wp-jobsearch') ?></label>
                                    <input id="add-lang-title" class="jobsearch-req-field" type="text">
                                </li>
                                <li class="jobsearch-column-6">
                                    <label><?php esc_html_e('Level', 'wp-jobsearch') ?></label>
                                    <div class="jobsearch-profile-select">
                                        <select id="add-lang-level" class="selectize-select"
                                                placeholder="<?php _e('Speaking Level', 'wp-jobsearch') ?>">
                                            <option value="beginner"><?php esc_html_e('Beginner', 'wp-jobsearch') ?></option>
                                            <option value="intermediate"><?php esc_html_e('Intermediate', 'wp-jobsearch') ?></option>
                                            <option value="proficient"><?php esc_html_e('Proficient', 'wp-jobsearch') ?></option>
                                        </select>
                                    </div>
                                </li>
                                <li class="jobsearch-column-12">
                                    <label><?php esc_html_e('Percentage', 'wp-jobsearch') ?></label>
                                    <input id="add-lang-percentage" class="jobsearch-req-field" type="number"
                                           placeholder="<?php esc_html_e('Enter a number between 1 to 100', 'wp-jobsearch') ?>"
                                           min="1" max="100">
                                </li>
                                <li class="jobsearch-column-12">
                                    <input type="submit" id="add-resume-langs-btn"
                                           value="<?php esc_html_e('Add Language', 'wp-jobsearch') ?>">
                                    <span class="langs-loding-msg edu-loding-msg"></span>
                                </li>
                            </ul>
                        </div>
                        <?php
                        $res_lang_html = ob_get_clean();
                        echo apply_filters('jobsearch_candidate_dash_resume_langadd_html', $res_lang_html, $candidate_id);
                        ?>
                        <div id="jobsearch-resume-langs-con"
                             class="<?php echo apply_filters('jobsearch_canddash_resume_langist_mainclass', 'jobsearch-add-skills') ?>">
                            <ul class="jobsearch-row">
                                <?php
                                $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_lang_title', true);
                                $lang_percentagefield_list = get_post_meta($candidate_id, 'jobsearch_field_lang_percentage', true);
                                $lang_level_list = get_post_meta($candidate_id, 'jobsearch_field_lang_level', true);
                                if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                                    $exfield_counter = 0;
                                    foreach ($exfield_list as $exfield) {
                                        $rand_num = rand(1000000, 99999999);

                                        $lang_percentagefield_val = isset($lang_percentagefield_list[$exfield_counter]) ? $lang_percentagefield_list[$exfield_counter] : '';
                                        $lang_level = isset($lang_level_list[$exfield_counter]) ? $lang_level_list[$exfield_counter] : '';

                                        $exfield = jobsearch_esc_html($exfield);
                                        $lang_percentagefield_val = jobsearch_esc_html($lang_percentagefield_val);
                                        ?>
                                        <li class="jobsearch-column-12 resume-list-item resume-list-lang">
                                            <?php
                                            ob_start();
                                            ?>
                                            <div class="jobsearch-add-skills-wrap">
                                                <span><?php echo($lang_percentagefield_val) ?></span>
                                                <h2 class="jobsearch-pst-title"><a><?php echo($exfield) ?></a></h2>
                                            </div>
                                            <?php
                                            $langist_html = ob_get_clean();
                                            echo apply_filters('jobsearch_canddash_resume_langist_itmhtml', $langist_html, $candidate_id, $exfield_counter);
                                            ?>
                                            <div class="jobsearch-resume-education-btn">
                                                <a href="javascript:void(0);"
                                                   class="jobsearch-icon jobsearch-sort jobsearch-tooltipcon el-drag-item"
                                                   title="<?php esc_html_e('Drag', 'wp-jobsearch') ?>"></a>
                                                <a href="javascript:void(0);"
                                                   class="jobsearch-icon jobsearch-edit jobsearch-tooltipcon update-resume-item"
                                                   title="<?php esc_html_e('Update', 'wp-jobsearch') ?>"></a>
                                                <a href="javascript:void(0);"
                                                   class="jobsearch-icon jobsearch-rubbish jobsearch-tooltipcon <?php echo(apply_filters('jobsearch_candash_resume_langlist_itmdelclass', 'del-resume-item', $rand_num)) ?>"
                                                   data-id="<?php echo($rand_num) ?>"
                                                   title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"></a>
                                            </div>
                                            <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                                                <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                                <?php
                                                ob_start();
                                                ?>
                                                <ul class="jobsearch-row jobsearch-employer-profile-form">
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Label *', 'wp-jobsearch') ?></label>
                                                        <input name="jobsearch_field_lang_title[]" type="text"
                                                               value="<?php echo($exfield) ?>">
                                                    </li>
                                                    <li class="jobsearch-column-6">
                                                        <label><?php esc_html_e('Level', 'wp-jobsearch') ?></label>
                                                        <div class="jobsearch-profile-select">
                                                            <select name="jobsearch_field_lang_level[]"
                                                                    class="selectize-select"
                                                                    placeholder="<?php _e('Speaking Level', 'wp-jobsearch') ?>">
                                                                <option value="beginner" <?php echo($lang_level == 'beginner' ? 'selected="selected"' : '') ?>><?php esc_html_e('Beginner', 'wp-jobsearch') ?></option>
                                                                <option value="intermediate" <?php echo($lang_level == 'intermediate' ? 'selected="selected"' : '') ?>><?php esc_html_e('Intermediate', 'wp-jobsearch') ?></option>
                                                                <option value="proficient" <?php echo($lang_level == 'proficient' ? 'selected="selected"' : '') ?>><?php esc_html_e('Proficient', 'wp-jobsearch') ?></option>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <li class="jobsearch-column-12">
                                                        <label><?php esc_html_e('Percentage', 'wp-jobsearch') ?></label>
                                                        <input name="jobsearch_field_lang_percentage[]"
                                                               type="number"
                                                               placeholder="<?php esc_html_e('Enter a number between 1 to 100', 'wp-jobsearch') ?>"
                                                               min="1" max="100"
                                                               value="<?php echo($lang_percentagefield_val) ?>">
                                                    </li>
                                                    <li class="jobsearch-column-12">
                                                        <input class="update-resume-list-btn" type="submit"
                                                               value="<?php esc_html_e('Update', 'wp-jobsearch') ?>">
                                                    </li>
                                                </ul>
                                                <?php
                                                $res_lang_html = ob_get_clean();
                                                echo apply_filters('jobsearch_canddash_resm_langupd_fields_html', $res_lang_html, $candidate_id, $exfield_counter);
                                                ?>
                                            </div>
                                        </li>
                                        <?php
                                        $exfield_counter++;
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <?php
                    $resm_lang_oall_html = ob_get_clean();
                    $resm_lang_oall_html = apply_filters('jobsearch_cand_dash_resume_lang_oall', $resm_lang_oall_html, $candidate_id);
                }
                if ($inopt_resm_honsawards != 'off') {
                    if ($user_pkg_limits::cand_field_is_locked('resmawards_defields')) {
                        ob_start();
                        ?>
                        <div class="jobsearch-candidate-title">
                            <h2>
                                <i class="jobsearch-icon jobsearch-social-media"></i> <?php esc_html_e('Honors & Awards', 'wp-jobsearch') ?>
                            </h2>
                        </div>
                        <?php echo($user_pkg_limits::cand_gen_locked_html()) ?>
                        <?php
                        $lock_field_cushtml = ob_get_clean();
                        $lock_field_html = $user_pkg_limits->cand_field_locked_html($lock_field_cushtml);
                        echo($lock_field_html);
                    } else {
                        ob_start();
                        ?>
                        <div class="jobsearch-candidate-resume-wrap">
                            <div class="jobsearch-candidate-title">
                                <h2>
                                    <i class="jobsearch-icon jobsearch-trophy"></i> <?php esc_html_e('Honors & Awards', 'wp-jobsearch') ?>
                                    <a href="javascript:void(0)" class="jobsearch-resume-addbtn"><span
                                                class="fa fa-plus"></span> <?php esc_html_e('Add Award', 'wp-jobsearch') ?>
                                    </a>
                                </h2>
                            </div>

                            <div class="jobsearch-add-popup jobsearch-add-resume-item-popup">
                                <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                <ul class="jobsearch-row jobsearch-employer-profile-form">
                                    <li class="jobsearch-column-6">
                                        <label><?php esc_html_e('Award Title *', 'wp-jobsearch') ?></label>
                                        <input id="add-award-title" class="jobsearch-req-field" type="text">
                                    </li>
                                    <li class="jobsearch-column-6">
                                        <label><?php esc_html_e('Year *', 'wp-jobsearch') ?></label>
                                        <input id="add-award-year" class="jobsearch-req-field" type="text">
                                    </li>
                                    <li class="jobsearch-column-12">
                                        <label><?php echo esc_html(_x('Description', 'Resume Awards Description', 'wp-jobsearch')) ?></label>
                                        <textarea id="add-award-desc"></textarea>
                                    </li>
                                    <li class="jobsearch-column-12">
                                        <input id="add-resume-awards-btn" type="submit"
                                               value="<?php esc_html_e('Add Award', 'wp-jobsearch') ?>">
                                        <span class="awards-loding-msg edu-loding-msg"></span>
                                    </li>
                                </ul>
                            </div>
                            <div id="jobsearch-resume-awards-con"
                                 class="jobsearch-resume-education jobsearch-resume-awards">
                                <ul class="jobsearch-row">
                                    <?php
                                    $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_title', true);
                                    $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_award_description', true);
                                    $award_yearfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_year', true);
                                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                                        $exfield_counter = 0;
                                        foreach ($exfield_list as $exfield) {
                                            $rand_num = rand(1000000, 99999999);

                                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                            $award_yearfield_val = isset($award_yearfield_list[$exfield_counter]) ? $award_yearfield_list[$exfield_counter] : '';

                                            $exfield = jobsearch_esc_html($exfield);
                                            $exfield_val = jobsearch_esc_html($exfield_val);
                                            $award_yearfield_val = jobsearch_esc_html($award_yearfield_val);
                                            ?>
                                            <li class="jobsearch-column-12 resume-list-item resume-list-award">
                                                <div class="jobsearch-resume-education-wrap">
                                                    <small><?php echo($award_yearfield_val) ?></small>
                                                    <h2 class="jobsearch-pst-title"><a><?php echo($exfield) ?></a></h2>
                                                </div>
                                                <div class="jobsearch-resume-education-btn">
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-icon jobsearch-sort jobsearch-tooltipcon el-drag-item"
                                                       title="<?php esc_html_e('Drag', 'wp-jobsearch') ?>"></a>
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-icon jobsearch-edit jobsearch-tooltipcon update-resume-item"
                                                       title="<?php esc_html_e('Update', 'wp-jobsearch') ?>"></a>
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-icon jobsearch-rubbish jobsearch-tooltipcon <?php echo(apply_filters('jobsearch_candash_resume_awardlist_itmdelclass', 'del-resume-item', $rand_num)) ?>"
                                                       data-id="<?php echo($rand_num) ?>"
                                                       title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"></a>
                                                </div>
                                                <div class="jobsearch-add-popup jobsearch-update-resume-items-sec">
                                                    <span class="close-popup-item"><i class="fa fa-times"></i></span>
                                                    <ul class="jobsearch-row jobsearch-employer-profile-form">
                                                        <li class="jobsearch-column-6">
                                                            <label><?php esc_html_e('Title *', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_award_title[]" type="text"
                                                                   value="<?php echo($exfield) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-6">
                                                            <label><?php esc_html_e('Year *', 'wp-jobsearch') ?></label>
                                                            <input name="jobsearch_field_award_year[]" type="text"
                                                                   value="<?php echo($award_yearfield_val) ?>">
                                                        </li>
                                                        <li class="jobsearch-column-12">
                                                            <label><?php echo esc_html(_x('Description', 'Resume Awards Description', 'wp-jobsearch')) ?></label>
                                                            <textarea
                                                                    name="jobsearch_field_award_description[]"><?php echo($exfield_val) ?></textarea>
                                                        </li>
                                                        <li class="jobsearch-column-12">
                                                            <input class="update-resume-list-btn" type="submit"
                                                                   value="<?php esc_html_e('Update', 'wp-jobsearch') ?>">
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
                        </div>
                        <?php
                        $resm_award_oall_html = ob_get_clean();
                        $resm_award_oall_html = apply_filters('jobsearch_cand_dash_resume_award_oall', $resm_award_oall_html, $candidate_id);
                    }
                }
                $oall_fields_arr = array(
                    'resm_edu_item' => $resm_edu_oall_html,
                    'resm_exp_item' => $resm_exp_oall_html,
                    'resm_port_item' => $resm_port_oall_html,
                    'resm_skill_item' => $resm_skill_oall_html,
                    'resm_lang_item' => $resm_lang_oall_html,
                    'resm_award_item' => $resm_award_oall_html,
                );
                $oall_fields_arr = apply_filters('jobsearch_cand_dash_resm_oall_farray', $oall_fields_arr);
                foreach ($oall_fields_arr as $oall_field_item) {
                    echo $oall_field_item;
                }
                ?>
            </div>
        </div>
        <input type="hidden" name="user_resume_form" value="1">
        <?php
        ob_start();
        jobsearch_terms_and_con_link_txt($termscon_chek);
        $upres_btn = ob_get_clean();
        echo apply_filters('jobsearch_canddash_resumesett_update_termscon', $upres_btn);

        ob_start();
        ?>
        <input type="submit" class="jobsearch-employer-profile-submit"
               value="<?php esc_html_e('Update Resume', 'wp-jobsearch') ?>">
        <?php
        $upres_btn = ob_get_clean();
        echo apply_filters('jobsearch_canddash_resume_update_mainbtn', $upres_btn);
        ?>
    </form>
    <?php
}