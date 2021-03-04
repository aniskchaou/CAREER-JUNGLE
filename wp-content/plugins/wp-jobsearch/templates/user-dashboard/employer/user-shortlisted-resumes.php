<?php
use WP_Jobsearch\Candidate_Profile_Restriction;

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;

$cand_profile_restrict = new Candidate_Profile_Restriction;

$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

if (jobsearch_user_isemp_member($user_id)) {
    $employer_id = jobsearch_user_isemp_member($user_id);
} else {
    $employer_id = jobsearch_get_user_employer_id($user_id);
}

$reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

if ($employer_id > 0) {
    $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);
    
    $employer_resumes_list = apply_filters('jobsearch_emp_dash_savedcands_list_var', $employer_resumes_list);

    $cats_list = get_post_meta($employer_id, 'emp_resumesh_types', true);

    $sh_typedf = isset($_GET['sh_type']) ? $_GET['sh_type'] : '';
    ?>
    <div class="jobsearch-employer-dasboard">
        <div class="jobsearch-employer-box-section">
            <script>
                jQuery(document).on('click', '.addmor-shbtn-resumtyp', function () {
                    jQuery('#cand-avialdates-inplist').append('\
                    <li class="jobsearch-column-12">\
                        <label><?php esc_html_e('Title', 'wp-jobsearch') ?></label>\
                        <div class="avil-input-con">\
                            <input type="text" name="emp_ressh_types[]" value="">\
                            <a href="javascript:void(0);" class="trash-avaltime-btn"><i class="fa fa-times"></i></a>\
                        </div>\
                    </li>');
                });
                jQuery(document).on('click', '.trash-avaltime-btn', function () {
                    jQuery(this).parents('li').remove();
                });
                jQuery(document).on('click', '.savcands-setings-tobtn', function () {
                    jQuery('.jobsearch-profile-actreslis').hide();
                    jQuery('.jobsearch-profile-ressetin').show();
                    jQuery(this).hide();
                    jQuery('.savcands-list-tobtn').show();
                });
                jQuery(document).on('click', '.savcands-list-tobtn', function () {
                    jQuery('.jobsearch-profile-ressetin').hide();
                    jQuery('.jobsearch-profile-actreslis').show();
                    jQuery(this).hide();
                    jQuery('.savcands-setings-tobtn').show();
                });
                //
                jQuery(document).on('change', '#shrtlist_selctsvtyp_filtr', function () {
                    jQuery('#dashshrtlists_tab_form').find('input[name="shrtlist_type"]').val(jQuery(this).val());
                    jQuery('#dashshrtlists_tab_form').submit();
                });
            </script>
            <?php
            $frm_shrtlist_type = isset($_GET['shrtlist_type']) ? $_GET['shrtlist_type'] : '';
            ?>
            <form id="dashshrtlists_tab_form" style="display: none;" method="get">
                <input type="hidden" name="tab" value="user-resumes">
                <input type="hidden" name="shrtlist_type" value="<?php echo ($frm_shrtlist_type) ?>">
            </form>
            <div class="jobsearch-profile-title">
                <h2><?php echo apply_filters('jobsearch_empdash_savedcands_maintitle', esc_html__('Saved Candidates', 'wp-jobsearch')) ?></h2>
                <a href="javascript:void(0);" class="dash-hdtabchng-btn savcands-list-tobtn" style="display: <?php echo ($sh_typedf == 'settings' ? 'block' : 'none') ?>;"><?php esc_html_e('Candidates List', 'wp-jobsearch') ?></a>
                <a href="javascript:void(0);" class="dash-hdtabchng-btn savcands-setings-tobtn" style="display: <?php echo ($sh_typedf == 'settings' ? 'none' : 'block') ?>;"><?php esc_html_e('Settings', 'wp-jobsearch') ?></a>
                <?php
                echo apply_filters('jobsearch_empdash_resmsaved_aftrmacts_html', '');
                
                ob_start();
                if (!empty($cats_list)) {
                    ?>
                    <div class="cands-savetype-filter">
                        <span class="filtr-label"><?php esc_html_e('Sort by', 'wp-jobsearch') ?></span>
                        <div class="jobsearch-profile-select">
                            <select id="shrtlist_selctsvtyp_filtr" class="selectize-select" placeholder="<?php esc_html_e('Select Type', 'wp-jobsearch') ?>">
                                <option value=""><?php esc_html_e('Select Type', 'wp-jobsearch') ?></option>
                                <?php
                                $typsh_count = 1;
                                foreach ($cats_list as $sh_type_key => $sh_type_title) {
                                    ?>
                                    <option value="<?php echo ($sh_type_key) ?>" <?php echo ($frm_shrtlist_type == $sh_type_key ? 'selected="selected"' : '') ?>><?php echo ($sh_type_title) ?></option>
                                    <?php
                                    $typsh_count++;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php
                }
                $sortby_html = ob_get_clean();
                echo apply_filters('jobsearch_empdash_savdresms_filtr_html', $sortby_html, $cats_list);
                ?>
            </div>

            <div class="jobsearch-profile-ressetin" style="display: <?php echo ($sh_typedf == 'settings' ? 'block' : 'none') ?>;">
                <h2><?php esc_html_e('Resume Types', 'wp-jobsearch') ?></h2>

                <form id="emp-shresumetype-form" method="post" action="<?php echo add_query_arg(array('tab' => 'user-resumes', 'sh_type' => 'settings'), $page_url) ?>">
                    <div class="res-profile-reslist">
                        <ul id="cand-avialdates-inplist" class="jobsearch-row jobsearch-employer-profile-form cand-avialdates-listcon">

                            <?php
                            $cand_avalb_types = get_post_meta($employer_id, 'emp_resumesh_types', true);
                            if (!empty($cand_avalb_types)) {
                                $avalb_count = 1;
                                foreach ($cand_avalb_types as $cand_avtype_key => $cand_avtype) {
                                    ?>
                                    <li class="jobsearch-column-12">
                                        <label><?php esc_html_e('Title', 'wp-jobsearch') ?></label>
                                        <div class="avil-input-con">
                                            <input type="text" name="emp_ressh_types[]" value="<?php echo ($cand_avtype) ?>">
                                            <?php
                                            if ($avalb_count != 1) {
                                                ?>
                                                <a href="javascript:void(0);" class="trash-avaltime-btn"><i class="fa fa-times"></i></a>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </li>
                                    <?php
                                    $avalb_count++;
                                }
                            } else {
                                ?>
                                <li class="jobsearch-column-12">
                                    <label><?php esc_html_e('Title', 'wp-jobsearch') ?></label>
                                    <div class="avil-input-con">
                                        <input type="text" name="emp_ressh_types[]" value="">
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                            </li>
                        </ul>
                        <a href="javascript:void(0);" class="addmor-shbtn-resumtyp"><?php esc_html_e('Add More', 'wp-jobsearch') ?></a>
                    </div>
                    <input type="hidden" name="employer_shrestypes_form" value="1">
                    <input type="submit" class="jobsearch-employer-profile-submit" value="<?php esc_html_e('Save Settings', 'wp-jobsearch') ?>">
                </form>
            </div>

            <div class="jobsearch-profile-actreslis" style="display: <?php echo ($sh_typedf == 'settings' ? 'none' : 'block') ?>;">
                <?php
                echo apply_filters('jobsearch_empdash_resmsaved_list_bfrhtml', '');
                ob_start();
                if ($employer_resumes_list != '') {
                    $employer_resumes_list = explode(',', $employer_resumes_list);

                    //
                    $_resume_typsh_list = get_post_meta($employer_id, 'jobsearch_resumtypes_list', true);

                    if (isset($frm_shrtlist_type) && $frm_shrtlist_type != '') {
                        $filtr_resumes_list = array();
                        if (!empty($employer_resumes_list)) {
                            foreach ($employer_resumes_list as $candidate_id) {
                                $cand_savetypes_ffil = isset($_resume_typsh_list[$candidate_id]) ? $_resume_typsh_list[$candidate_id] : '';
                                //var_dump($cand_savetypes);
                                if (!empty($cand_savetypes_ffil) && in_array($frm_shrtlist_type, $cand_savetypes_ffil)) {
                                    $filtr_resumes_list[] = $candidate_id;
                                }
                            }
                        }

                        $employer_resumes_list = $filtr_resumes_list;
                    }

                    if (!empty($employer_resumes_list)) {
                        $emp_shcats_list = get_post_meta($employer_id, 'emp_resumesh_types', true);
                        $total_resumes = count($employer_resumes_list);
                        krsort($employer_resumes_list);

                        $start = ($page_num - 1) * ($reults_per_page);
                        $offset = $reults_per_page;

                        $employer_resumes_list = array_slice($employer_resumes_list, $start, $offset);
                        ?>
                        <div class="jobsearch-employer-resumes">
                            <ul class="jobsearch-row">
                                <?php
                                foreach ($employer_resumes_list as $candidate_id) {
                                    $send_message_form_rand = rand(1000000, 99999999);
                                    $cand_savetypes = isset($_resume_typsh_list[$candidate_id]) ? $_resume_typsh_list[$candidate_id] : '';
                                    $candidate_user_id = jobsearch_get_candidate_user_id($candidate_id);
                                    
                                    $user_def_avatar_url = jobsearch_candidate_img_url_comn($candidate_id);

                                    $candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                                    $get_candidate_location = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);

                                    $get_user_linkedin_url = get_post_meta($candidate_id, 'jobsearch_field_user_linkedin_url', true);

                                    $popup_args = array('employer_id' => $employer_id, 'candidate_id' => $candidate_id, 'masg_rand' => $send_message_form_rand);
                                    add_action('wp_footer', function () use ($popup_args) {

                                        extract(shortcode_atts(array(
                                            'employer_id' => '',
                                            'candidate_id' => '',
                                            'masg_rand' => ''
                                                        ), $popup_args));
                                        $cats_list = jobsearch_candsh_btn_catlist();
                                        ?>
                                        <div class="jobsearch-modal fade" id="JobSearchModalCandShPopup<?php echo ($candidate_id) ?>">
                                            <div class="modal-inner-area">&nbsp;</div>
                                            <div class="modal-content-area">
                                                <div class="modal-box-area">
                                                    <div class="jobsearch-modal-title-box">
                                                        <h2><?php esc_html_e('Choose Type', 'wp-jobsearch') ?></h2>
                                                        <span class="modal-close"><i class="fa fa-times"></i></span>
                                                    </div>
                                                    <?php
                                                    if (!empty($cats_list)) {
                                                        ?>
                                                        <div id="usercand-shrtlistsecs-<?php echo($candidate_id) ?>" class="jobsearch-usercand-shrtlistsec">
                                                            <div class="shcand-types-list">
                                                                <div class="jobsearch-profile-select">
                                                                    <select name="shrtlist_type[]" multiple="multiple" class="selectize-select" placeholder="<?php esc_html_e('Select Types', 'wp-jobsearch') ?>">
                                                                        <?php
                                                                        $typse_count = 1;
                                                                        foreach ($cats_list as $sh_type_key => $sh_type) {
                                                                            ?>
                                                                            <option value="<?php echo($sh_type_key) ?>"><?php echo($sh_type) ?></option>
                                                                            <?php
                                                                            $typse_count++;
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="usercand-shrtlist-btnsec">
                                                                <a href="javascript:void(0);"
                                                                   class="jobsearch-candidate-default-btn jobsearch-updcand-withtyp-tolist"
                                                                   data-id="<?php echo($candidate_id) ?>"><i class="jobsearch-icon jobsearch-add-list"></i> 
                                                                       <?php esc_html_e('Update Candidate', 'wp-jobsearch') ?>
                                                                </a>
                                                                <span class="resume-loding-msg"></span>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }, 11, 1);
                                    ob_start();
                                    ?>
                                    <li class="jobsearch-column-6">
                                        <script>
                                            jQuery(document).on('click', '.modeleupditm-btn-<?php echo ($candidate_id) ?>', function () {
                                                jobsearch_modal_popup_open('JobSearchModalCandShPopup<?php echo ($candidate_id) ?>');
                                            });
                                            jQuery(document).on('click', '.jobsearch-modelemail-btn-<?php echo ($send_message_form_rand) ?>', function () {
                                                jobsearch_modal_popup_open('JobSearchModalSendEmail<?php echo ($send_message_form_rand) ?>');
                                            });
                                        </script>
                                        <div class="jobsearch-employer-resumes-wrap">
                                            <a href="javascript:void(0);" class="jobsearch-updresmuesh-item-cc modeleupditm-btn-<?php echo ($candidate_id) ?>" data-id="<?php echo ($candidate_id) ?>"><i class="fa fa-pencil"></i></a>
                                            <a href="javascript:void(0);" class="jobsearch-rem-empresmue jobsearch-remresmuesh-item-cc modeledelitm-btn-<?php echo ($send_message_form_rand) ?>" data-id="<?php echo ($candidate_id) ?>"><i class="fa fa-times"></i></a>
                                            <figure>
                                                <a href="<?php echo get_permalink($candidate_id) ?>" class="jobsearch-resumes-thumb"><img src="<?php echo ($user_def_avatar_url) ?>" alt=""></a>
                                                <figcaption>
                                                    <h2 class="jobsearch-pst-title">
                                                        <?php
                                                        if ($cand_profile_restrict::cand_field_is_locked('profile_fields|display_name')) {
                                                            $user_displayname = $cand_profile_restrict::cand_restrict_display_name();
                                                            ?>
                                                            <a href="<?php echo get_permalink($candidate_id) ?>"><?php echo ($user_displayname) ?></a> 
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <a href="<?php echo get_permalink($candidate_id) ?>"><?php echo get_the_title($candidate_id) ?></a> 
                                                            <?php
                                                        }
                                                        echo apply_filters('jobsearch_dash_stats_apps_list_slist_btn', '', $candidate_id, 10);
                                                        $candidate_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);

                                                        $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
                                                        if ($multiple_cv_files_allow == 'on') {
                                                            $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
                                                            if (!empty($ca_at_cv_files)) {
                                                                ?>
                                                                <a href="<?php echo apply_filters('jobsearch_user_attach_cv_file_url', '', $candidate_id, 0) ?>" oncontextmenu="javascript: return false;" onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};" download="<?php echo apply_filters('jobsearch_user_attach_cv_file_title', '', $candidate_id, 0) ?>" class="jobsearch-resumes-download"><i class="jobsearch-icon jobsearch-download-arrow"></i> <?php esc_html_e('Download CV', 'wp-jobsearch') ?></a>
                                                                <?php
                                                            }
                                                        } else if (!empty($candidate_cv_file)) {
                                                            $file_attach_id = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
                                                            $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';

                                                            $filename = isset($candidate_cv_file['file_name']) ? $candidate_cv_file['file_name'] : '';

                                                            $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_attach_id, $candidate_id);
                                                            ?>
                                                            <a href="<?php echo ($file_url) ?>" oncontextmenu="javascript: return false;" onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};" download="<?php echo ($filename) ?>" class="jobsearch-resumes-download"><i class="jobsearch-icon jobsearch-download-arrow"></i> <?php esc_html_e('Download CV', 'wp-jobsearch') ?></a>
                                                            <?php
                                                        }
                                                        ?>
                                                    </h2>
                                                    <?php
                                                    if ($candidate_jobtitle != '') {
                                                        ?>
                                                        <span class="jobsearch-resumes-subtitle"><?php echo ($candidate_jobtitle) ?></span>
                                                        <?php
                                                    }
                                                    ?>
                                                    <ul>
                                                        <?php
                                                        if ($get_candidate_location != '') {
                                                            ?>
                                                            <li>
                                                                <span><?php esc_html_e('Location:', 'wp-jobsearch') ?></span>
                                                                <?php echo ($get_candidate_location) ?>
                                                            </li>
                                                            <?php
                                                        }

                                                        $candidate_salary_str = jobsearch_candidate_current_salary($candidate_id, '', '', 'small');
                                                        if ($candidate_salary_str != '') {
                                                            ?>
                                                            <li>
                                                                <span><?php esc_html_e('Current Salary:', 'wp-jobsearch') ?></span>
                                                                <?php echo ($candidate_salary_str) ?>
                                                            </li>
                                                            <?php
                                                        }
                                                        ?>
                                                    </ul>
                                                </figcaption>
                                            </figure>
                                            <?php
                                            $shty_catsli_arr = array();
                                            if (!empty($emp_shcats_list) && !empty($cand_savetypes)) {
                                                foreach ($cand_savetypes as $emp_cand_shtyp) {
                                                    if (isset($emp_shcats_list[$emp_cand_shtyp])) {
                                                        $shty_catsli_arr[] = $emp_shcats_list[$emp_cand_shtyp];
                                                    }
                                                }
                                            }
                                            if (!empty($shty_catsli_arr)) {
                                                ?>
                                                <div class="shsaved-groups">
                                                    <?php
                                                    echo implode(', ', $shty_catsli_arr);
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <ul class="jobsearch-resumes-options">
                                                <li><a href="javascript:void(0);" class="jobsearch-modelemail-btn-<?php echo ($send_message_form_rand) ?>"><i class="jobsearch-icon jobsearch-mail"></i> <?php esc_html_e('Message', 'wp-jobsearch') ?></a></li>
                                                <?php
                                                $popup_args = array('p_job_id' => '0', 'p_emp_id' => $employer_id, 'cand_id' => $candidate_id, 'p_masg_rand' => $send_message_form_rand);
                                                add_action('wp_footer', function () use ($popup_args) {

                                                    extract(shortcode_atts(array(
                                                        'p_job_id' => '',
                                                        'p_emp_id' => '',
                                                        'cand_id' => '',
                                                        'p_masg_rand' => ''
                                                                    ), $popup_args));
                                                    ?>
                                                    <div class="jobsearch-modal fade" id="JobSearchModalSendEmail<?php echo ($p_masg_rand) ?>">
                                                        <div class="modal-inner-area">&nbsp;</div>
                                                        <div class="modal-content-area">
                                                            <div class="modal-box-area">
                                                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                                                <div class="jobsearch-send-message-form">
                                                                    <form method="post" id="jobsearch_send_email_form<?php echo esc_html($p_masg_rand); ?>">
                                                                        <div class="jobsearch-user-form">
                                                                            <ul class="email-fields-list">
                                                                                <li>
                                                                                    <label>
                                                                                        <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>:
                                                                                    </label>
                                                                                    <div class="input-field">
                                                                                        <input type="text" name="send_message_subject" value="" />
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
                                                                                        <input type="submit" class="applicantto-email-submit-btn" data-jid="<?php echo absint($p_job_id); ?>" data-eid="<?php echo absint($p_emp_id); ?>" data-cid="<?php echo absint($cand_id); ?>" data-randid="<?php echo esc_html($p_masg_rand); ?>" name="send_message_content" value="<?php echo esc_html__('Send','wp-jobsearch') ?>"/>
                                                                                        <span class="loader-box loader-box-<?php echo esc_html($p_masg_rand); ?>"></span>
                                                                                    </div>
                                                                                </li>
                                                                            </ul> 
                                                                            <div class="message-box message-box-<?php echo esc_html($p_masg_rand); ?>" style="display:none;"></div>
                                                                        </div>
                                                                    </form>    
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }, 11, 1);
                                                ?>
                                                <li><a href="<?php echo get_permalink($candidate_id) ?>"><i class="jobsearch-icon jobsearch-user-1"></i> <?php esc_html_e('View Profile', 'wp-jobsearch') ?></a></li>
                                                <?php
                                                if ($get_user_linkedin_url != '') {
                                                    ?>
                                                    <li><a href="<?php echo ($get_user_linkedin_url) ?>"><i class="jobsearch-icon jobsearch-linkedin-1"></i> <?php esc_html_e('LinkedIn', 'wp-jobsearch') ?></a></li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </li>
                                    <?php
                                    $shl_html = ob_get_clean();
                                    echo apply_filters('jobsearch_empdash_shrtlists_item_html', $shl_html, $candidate_id, $employer_id, $send_message_form_rand);
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                        $total_pages = 1;
                        if ($total_resumes > 0 && $reults_per_page > 0 && $total_resumes > $reults_per_page) {
                            $total_pages = ceil($total_resumes / $reults_per_page);
                            ?>
                            <div class="jobsearch-pagination-blog">
                                <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                            </div>
                            <?php
                        }
                    }
                } else {
                    ?>
                    <p><?php esc_html_e('No record found.', 'wp-jobsearch') ?></p>
                    <?php
                }
                $shrtlist_html = ob_get_clean();
                echo apply_filters('jobsearch_empdash_shrtlists_whole_html', $shrtlist_html);
                ?>
            </div>
        </div>
    </div>
    <?php
}
