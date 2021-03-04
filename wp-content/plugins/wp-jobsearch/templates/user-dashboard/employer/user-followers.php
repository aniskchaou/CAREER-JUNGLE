<?php
global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
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
    $employer_resumes_list = get_user_meta($user_id, 'jobsearch-user-followins-list', true);
    ?>
    <div class="jobsearch-employer-dasboard">
        <div class="jobsearch-employer-box-section">
            <div class="jobsearch-profile-title">
                <h2><?php esc_html_e('Followers', 'wp-jobsearch') ?></h2>
            </div>
            <div class="jobsearch-profile-actreslis">
                <?php
                echo apply_filters('jobsearch_empdash_resmsaved_list_bfrhtml', '');
                
                ob_start();
                if (!empty($employer_resumes_list)) {
                    $total_resumes = count($employer_resumes_list);
                    krsort($employer_resumes_list);

                    $start = ($page_num - 1) * ($reults_per_page);
                    $offset = $reults_per_page;

                    $employer_resumes_list = array_slice($employer_resumes_list, $start, $offset);
                    ?>
                    <div class="jobsearch-employer-resumes">
                        <ul class="jobsearch-row">
                            <?php
                            foreach ($employer_resumes_list as $resume_item) {
                                $candidate_id = isset($resume_item['post_id']) ? $resume_item['post_id'] : '';
                                $send_message_form_rand = rand(1000000, 99999999);
                                $candidate_user_id = jobsearch_get_candidate_user_id($candidate_id);
                                
                                $user_def_avatar_url = jobsearch_candidate_img_url_comn($candidate_id);

                                $candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                                $get_candidate_location = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);

                                $get_user_linkedin_url = get_post_meta($candidate_id, 'jobsearch_field_user_linkedin_url', true);

                                ob_start();
                                ?>
                                <li class="jobsearch-column-6">
                                    <script>
                                        jQuery(document).on('click', '.jobsearch-modelemail-btn-<?php echo ($send_message_form_rand) ?>', function () {
                                            jobsearch_modal_popup_open('JobSearchModalSendEmail<?php echo ($send_message_form_rand) ?>');
                                        });
                                    </script>
                                    <div class="jobsearch-employer-resumes-wrap">
                                        <figure>
                                            <a href="<?php echo get_permalink($candidate_id) ?>" class="jobsearch-resumes-thumb"><img src="<?php echo ($user_def_avatar_url) ?>" alt=""></a>
                                            <figcaption>
                                                <h2 class="jobsearch-pst-title">
                                                    <a href="<?php echo get_permalink($candidate_id) ?>"><?php echo get_the_title($candidate_id) ?></a> 
                                                    <?php
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
