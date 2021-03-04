<?php
global $jobsearch_plugin_options, $diff_form_errs, $sitepress;

$lang_code = '';
if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
    $lang_code = $sitepress->get_current_language();
}

$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$user_displayname = $user_obj->display_name;
$user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
$user_email = $user_obj->user_email;

$user_is_candidate = jobsearch_user_is_candidate($user_id);
$user_is_employer = jobsearch_user_is_employer($user_id);

$security_switch = isset($jobsearch_plugin_options['security-questions-switch']) ? $jobsearch_plugin_options['security-questions-switch'] : '';

$security_questions = isset($jobsearch_plugin_options['jobsearch-security-questions']) ? $jobsearch_plugin_options['jobsearch-security-questions'] : '';

if (jobsearch_user_isemp_member($user_id)) {
    $sec_questions = get_user_meta($user_id, 'user_security_questions', true);
} else {
    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        $sec_questions = get_post_meta($employer_id, 'user_security_questions', true);
    } else {
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
        $sec_questions = get_post_meta($candidate_id, 'user_security_questions', true);
    }
}
?>
<div class="jobsearch-typo-wrap">
    <form class="jobsearch-employer-dasboard" method="post" action="<?php echo add_query_arg(array('tab' => 'change-password'), $page_url) ?>">
        <div class="jobsearch-employer-box-section">
            <div class="jobsearch-profile-title">
                <h2><?php esc_html_e('Change Password', 'wp-jobsearch') ?></h2>
            </div>
            <?php
            if (isset($_POST['user_password_change_form']) && $_POST['user_password_change_form'] == '1') {
                if (isset($diff_form_errs['user_not_allow_mod']) && $diff_form_errs['user_not_allow_mod'] == true) {
                    ?>
                    <div class="jobsearch-alert jobsearch-error-alert">
                        <p><?php echo wp_kses(__('<strong>Error!</strong> You are not allowed to modify settings.', 'wp-jobsearch'), array('strong' => array())) ?></p>
                    </div>
                    <?php
                } else if (isset($diff_form_errs['min_questions_err']) && $diff_form_errs['min_questions_err'] == true) {
                    ?>
                    <div class="jobsearch-alert jobsearch-error-alert">
                        <p><?php printf(wp_kses(__('<strong>Error!</strong> Please fill the answers of at least %s security questions.', 'wp-jobsearch'), array('strong' => array())), $diff_form_errs['min_questions_err']) ?></p>
                    </div>
                    <?php
                } else if (isset($diff_form_errs['old_pass_not_matched']) && $diff_form_errs['old_pass_not_matched'] == true) {
                    ?>
                    <div class="jobsearch-alert jobsearch-error-alert">
                        <p><?php echo wp_kses(__('<strong>Error!</strong> Password not changed. Your provided old password is mismatched.', 'wp-jobsearch'), array('strong' => array())) ?></p>
                    </div>
                    <?php
                } else if (isset($diff_form_errs['wrong_ans_err']) && $diff_form_errs['wrong_ans_err'] == true) {
                    ?>
                    <div class="jobsearch-alert jobsearch-error-alert">
                        <p><?php echo wp_kses(__('<strong>Error!</strong> Password not changed. Your provided answers for security questions are mismatched. Contact to Administrator for further help.', 'wp-jobsearch'), array('strong' => array())) ?></p>
                    </div>
                    <?php
                } else if (empty($diff_form_errs)) {
                    ?>
                    <div class="jobsearch-alert jobsearch-success-alert">
                        <p><?php esc_html_e('Password changed successfully.', 'wp-jobsearch') ?></p>
                    </div>
                    <?php
                }
            }
            ?>
            <ul class="jobsearch-row jobsearch-employer-profile-form">
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('Old Password *', 'wp-jobsearch') ?></label>
                    <input value="Password" type="password" name="old_pass">
                </li>
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('New Password *', 'wp-jobsearch') ?></label>
                    <input value="Password" class="jobsearch_chk_passfield" type="password" name="new_pass">
                    <span class="passlenth-chk-msg"></span>
                </li>
            </ul>
            <?php
            if ($security_switch == 'on' && !empty($security_questions) && sizeof($security_questions) >= 3) {
                ?>
                <div class="jobsearch-profile-title">
                    <h2><?php esc_html_e('Security Questions', 'wp-jobsearch') ?></h2>
                </div>
                <ul class="jobsearch-row jobsearch-employer-profile-form">
                    <?php
                    if (empty($sec_questions)) {

                        for ($qcount = 1; $qcount <= 3; $qcount++) {
                            $security_questions_opts = '';
                            $qopcount = 1;
                            foreach ($security_questions as $sec_ques_key => $security_question) {
                                $security_question = apply_filters('wpml_translate_single_string', $security_question, 'JobSearch Options', 'Security Question - ' . $security_question, $lang_code);
                                $security_questions_opts .= '<option ' . ($qopcount == $qcount ? 'selected="selected"' : '') . ' value="' . $security_question . '">' . $security_question . '</option>';
                                $qopcount++;
                            }
                            ?>
                            <li class="jobsearch-column-6">
                                <label><?php printf(esc_html__('Question No %s :', 'wp-jobsearch'), $qcount) ?></label>
                                <div class="jobsearch-profile-select">
                                    <?php
                                    if ($security_questions_opts != '') { ?>
                                        <select class="selectize-select" name="user_security_questions[questions][]">
                                            <?php echo ($security_questions_opts) ?>
                                        </select>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </li>
                            <li class="jobsearch-column-6">
                                <label><?php esc_html_e('Answer :', 'wp-jobsearch') ?></label>
                                <input type="text" name="user_security_questions[answers][]">
                            </li>
                            <?php
                        }
                    } else {

                        $answer_to_ques = isset($sec_questions['answers']) ? $sec_questions['answers'] : '';
                        $qcount = 0;
                        $qcount_num = 1;
                        if (!empty($answer_to_ques)) {
                            foreach ($answer_to_ques as $sec_ans) {
                                $_ques = isset($sec_questions['questions'][$qcount]) ? $sec_questions['questions'][$qcount] : '';
                                $_answer_to_ques = $sec_ans;

                                if ($_answer_to_ques != '') {
                                    ?>
                                    <li class="jobsearch-column-6">
                                        <label><?php printf(esc_html__('Question No %s :', 'wp-jobsearch'), $qcount_num) ?></label>
                                        <span><?php echo ($_ques) ?></span>
                                    </li>
                                    <li class="jobsearch-column-6">
                                        <label><?php esc_html_e('Answer :', 'wp-jobsearch') ?></label>
                                        <input type="text" name="user_security_quests[answers][]">
                                    </li>
                                    <?php
                                    $qcount_num++;
                                } else { ?>
                                    <li style="display:none;"><input type="hidden" name="user_security_quests[answers][]"></li>
                                    <?php
                                }
                                $qcount++;
                            }
                        }
                    }
                    ?>
                </ul>
                <?php
            }
            ?>

        </div>
        <input type="hidden" name="user_password_change_form" value="1">
        <?php
        ob_start();
        ?>
        <input type="submit" class="jobsearch-employer-profile-submit jobsearch-regpass-frmbtn jobsearch-disable-btn" disabled value="<?php echo esc_html__('Update Password', 'wp-jobsearch'); ?>">
        <?php
        $updtbtn_html = ob_get_clean();
        echo apply_filters('jobsearch_user_dash_chpass_updtpass_btn', $updtbtn_html);
        ?>
    </form>
</div>