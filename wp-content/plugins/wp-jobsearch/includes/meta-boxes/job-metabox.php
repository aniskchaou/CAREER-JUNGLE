<?php
/**
 * Define Meta boxes for plugin
 * and theme.
 *
 */
if (!function_exists('jobsearch_delete_job_callback')) {

    function jobsearch_delete_job_callback($post_id)
    {
        if (get_post_type($post_id) == 'job') {
            $job_employer_id = get_post_meta($post_id, 'jobsearch_field_job_posted_by', true); // get job employer
            $employer_job_count = get_post_meta($job_employer_id, 'jobsearch_field_employer_job_count', true); // get jobs count in employer profile
            if ($employer_job_count != '' && $employer_job_count > 0) {
                $employer_job_count--;
            }
            if ($employer_job_count < 0 || $employer_job_count == '') {
                $employer_job_count = 0;
            }
            update_post_meta($job_employer_id, 'jobsearch_field_employer_job_count', $employer_job_count); // update jobs count in employer
            update_post_meta($post_id, 'jobsearch_field_job_employer_count_updated', 'no'); // update count status in job
        }
    }

    add_action('wp_trash_post', 'jobsearch_delete_job_callback');
    add_action('delete_post', 'jobsearch_delete_job_callback');
}
if (!function_exists('jobsearch_jobs_save')) {

    function jobsearch_jobs_save($post_id, $post, $update)
    {
        global $pagenow;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        $post_type = '';
        if ($pagenow == 'post.php') {
            $post_type = get_post_type();
        }
        if (isset($_REQUEST)) {
            if ($post_type == 'job') {

                $current_time = current_time('timestamp');

                // set job cron time
                if (isset($_POST['jobsearch_field_job_expiry_date']) && isset($_POST['job_actexpiry_date'])) {
                    $job_actexpiry = absint($_POST['job_actexpiry_date']);
                    $postin_expiry_date = $_POST['jobsearch_field_job_expiry_date'] != '' ? absint(strtotime($_POST['jobsearch_field_job_expiry_date'])) : 0;

                    if ($postin_expiry_date > 0 && $postin_expiry_date > $current_time && $postin_expiry_date != $job_actexpiry) {

                        $job_employer_id = get_post_meta($post_id, 'jobsearch_field_job_posted_by', true);
                        $user_id = jobsearch_get_employer_user_id($job_employer_id);
                        $cronevnt_timestamp = wp_next_scheduled('jobsearch_job_expiry_cron_event_' . $post_id);

                        wp_clear_scheduled_hook('jobsearch_job_expiry_cron_event_' . $post_id, array($post_id, $user_id));

                        if (!$cronevnt_timestamp) {
                            wp_schedule_single_event($postin_expiry_date, 'jobsearch_job_expiry_cron_event_' . $post_id, array($post_id, $user_id));
                            update_post_meta($post_id, 'jobsearch_job_single_exp_cron', 'yes');
                        }
                    }
                }

                // extra save 
                if (isset($_POST['jobsearch_field_job_publish_date'])) {
                    if ($_POST['jobsearch_field_job_publish_date'] != '') {
                        $_posted_time = strtotime($_POST['jobsearch_field_job_publish_date']);

                        update_post_meta($post_id, 'jobsearch_field_job_publish_date', $_posted_time);
                    }
                }

                if (isset($_POST['jobsearch_field_job_expiry_date'])) {
                    if ($_POST['jobsearch_field_job_expiry_date'] != '') {
                        $_POST['jobsearch_field_job_expiry_date'] = strtotime($_POST['jobsearch_field_job_expiry_date']);
                        //var_dump($_POST['jobsearch_field_job_expiry_date']);
                        //die;
                        update_post_meta($post_id, 'jobsearch_field_job_expiry_date', $_POST['jobsearch_field_job_expiry_date']);
                    }
                }
                if (isset($_POST['jobsearch_field_job_application_deadline_date'])) {
                    if ($_POST['jobsearch_field_job_application_deadline_date'] != '') {
                        $_POST['jobsearch_field_job_application_deadline_date'] = strtotime($_POST['jobsearch_field_job_application_deadline_date']);
                        update_post_meta($post_id, 'jobsearch_field_job_application_deadline_date', $_POST['jobsearch_field_job_application_deadline_date']);
                    }
                }
                $the_post_obj = get_post($post_id);
                $post_status = isset($the_post_obj->post_status) ? $the_post_obj->post_status : '';

                $user_data = wp_get_current_user();
                // update employer job count
                $job_employer_count_updated = get_post_meta($post_id, 'jobsearch_field_job_employer_count_updated', true);
                $job_employer_id = get_post_meta($post_id, 'jobsearch_field_job_posted_by', true); // get job employer 
                if ((!isset($job_employer_count_updated) || $job_employer_count_updated != 'yes' || empty($job_employer_count_updated)) && $job_employer_id != '') {

                    $employer_job_count = get_post_meta($job_employer_id, 'jobsearch_field_employer_job_count', true); // get jobs count in employer profile
                    if ($employer_job_count != '' && $employer_job_count > 0) {
                        $employer_job_count++;
                    } else {
                        $employer_job_count = 1;
                    }
                    update_post_meta($job_employer_id, 'jobsearch_field_employer_job_count', $employer_job_count); // update jobs count in employer
                    update_post_meta($post_id, 'jobsearch_field_job_employer_count_updated', 'yes'); // update count status in job
                }

                // Email Employer at Job approved by admin
                $prev_job_status = isset($_POST['jobsearch_job_presnt_status']) ? $_POST['jobsearch_job_presnt_status'] : '';
                if ($prev_job_status != '') {
                    update_post_meta($post_id, 'jobsearch_job_presnt_status', $prev_job_status);
                }

                // Employer jobs status change according his/her status
                do_action('jobsearch_employer_update_jobs_status', $job_employer_id);


                // Attachments
                $gal_ids_arr = array();
                if (isset($_POST['jobsearch_field_job_attachment_files']) && !empty($_POST['jobsearch_field_job_attachment_files'])) {
                    $gal_ids_arr = array_merge($gal_ids_arr, $_POST['jobsearch_field_job_attachment_files']);
                }
                update_post_meta($post_id, 'jobsearch_field_job_attachment_files', $gal_ids_arr);

                // Cus Fields Upload Files /////
                do_action('jobsearch_custom_field_upload_files_save', $post_id, 'job');
                //

                // urgent job from bckend
                if (isset($_POST['cusjob_urgent_fbckend'])) {
                    $cusjob_urgent_fbckend = $_POST['cusjob_urgent_fbckend'];
                    if ($cusjob_urgent_fbckend == 'on') {
                        update_post_meta($post_id, '_urgent_job_frmadmin', 'yes');
                        update_post_meta($post_id, 'jobsearch_field_urgent_job', 'on');
                    } else if ($cusjob_urgent_fbckend == 'off') {
                        update_post_meta($post_id, '_urgent_job_frmadmin', 'no');
                        update_post_meta($post_id, 'jobsearch_field_urgent_job', 'off');
                    }
                }
            }
        }
    }

    add_action('save_post', 'jobsearch_jobs_save', 999, 3);
}

/**
 * Job settings meta box.
 */
function jobsearch_jobs_settings_meta_boxes()
{
    add_meta_box('jobsearch-jobs-settings', esc_html__('Job Settings', 'wp-jobsearch'), 'jobsearch_jobs_meta_settings', 'job', 'normal');
}

/**
 * Job settings meta box callback.
 */
function jobsearch_jobs_meta_settings()
{
    global $post, $jobsearch_form_fields, $jobsearch_plugin_options, $jobsearch_currencies_list, $in_jobpost_form_sh;

    $in_jobpost_form_sh = true;

    $rand_num = rand(1000000, 99999999);

    $job_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';

    $job_apply_deadline_sw = isset($jobsearch_plugin_options['job_appliction_deadline']) ? $jobsearch_plugin_options['job_appliction_deadline'] : '';

    $_post_id = $post->ID;
    $job_custom_currency_switch = isset($jobsearch_plugin_options['job_custom_currency']) ? $jobsearch_plugin_options['job_custom_currency'] : '';

    $job_posted_by = get_post_meta($post->ID, 'jobsearch_field_job_posted_by', true);
    $job_publish_date = get_post_meta($post->ID, 'jobsearch_field_job_publish_date', true);
    $job_publish_date = isset($job_publish_date) && $job_publish_date != '' ? date('d-m-Y H:i:s', $job_publish_date) : '';
    $job_expiry_date = get_post_meta($post->ID, 'jobsearch_field_job_expiry_date', true);
    $job_expiry_date = isset($job_expiry_date) && $job_expiry_date != '' ? date('d-m-Y H:i:s', $job_expiry_date) : '';
    $job_app_deadline_date = get_post_meta($post->ID, 'jobsearch_field_job_application_deadline_date', true);
    $job_app_deadline_date = isset($job_app_deadline_date) && $job_app_deadline_date != '' ? date('d-m-Y H:i:s', $job_app_deadline_date) : '';

    $salar_cur_list = array('default' => esc_html__('Default', 'wp-jobsearch'));
    if (!empty($jobsearch_currencies_list)) {
        foreach ($jobsearch_currencies_list as $jobsearch_curr_key => $jobsearch_curr_item) {
            $cus_cur_name = isset($jobsearch_curr_item['name']) ? $jobsearch_curr_item['name'] : '';
            $cus_cur_symbol = isset($jobsearch_curr_item['symbol']) ? $jobsearch_curr_item['symbol'] : '';
            $salar_cur_list[$jobsearch_curr_key] = $cus_cur_name . ' - ' . $cus_cur_symbol;
        }
    }

    $job_employer_id = get_post_meta($post->ID, 'jobsearch_field_job_posted_by', true);
    $job_status = get_post_meta($post->ID, 'jobsearch_field_job_status', true);
    $prev_job_status = get_post_meta($post->ID, 'jobsearch_job_presnt_status', true);

    $job_singlsxp_cron = get_post_meta($post->ID, 'jobsearch_job_single_exp_cron', true);

    wp_enqueue_script('jobsearch-selectize');
    ?>
    <script>
        jQuery(document).ready(function () {
            var todayDate = new Date().getDate();
            jQuery('#jobsearch_job_publish_date').datetimepicker({
                minDate: new Date(new Date().setDate(todayDate)),
                timepicker: true,
                format: 'd-m-Y H:i:s'
            });
            jQuery('#jobsearch_job_expiry_date').datetimepicker({
                minDate: new Date(new Date().setDate(todayDate)),
                timepicker: true,
                format: 'd-m-Y H:i:s'
            });
            jQuery('#job_application_deadline_date').datetimepicker({
                minDate: new Date(new Date().setDate(todayDate)),
                timepicker: true,
                format: 'd-m-Y H:i:s'
            });
            jQuery('#jobsearch_job_feature_till').datetimepicker({
                minDate: new Date(new Date().setDate(todayDate)),
                timepicker: true,
                format: 'd-m-Y H:i:s'
            });
            // Selectize script
            jQuery('.applicants-selectize').selectize({
                plugins: ['remove_button'],
            });
        });
    </script>
    <div class="jobsearch-post-settings">
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Publish Date', 'wp-jobsearch'); ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'force_std' => $job_publish_date,
                    'id' => 'jobsearch_job_publish_date',
                    'name' => 'job_publish_date',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Expiry Date', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $job_actexpiry = get_post_meta($_post_id, 'jobsearch_field_job_expiry_date', true);
                $field_params = array(
                    'force_std' => $job_expiry_date,
                    'id' => 'jobsearch_job_expiry_date',
                    'name' => 'job_expiry_date',
                    'ext_attr' => 'required="required"',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
                <input type="hidden" name="job_actexpiry_date" value="<?php echo($job_actexpiry) ?>">
            </div>
        </div>

        <?php
        if ($job_apply_deadline_sw != 'off') {
            ?>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Application Deadline Date', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'force_std' => $job_app_deadline_date,
                        'id' => 'job_application_deadline_date',
                        'name' => 'job_application_deadline_date',
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <?php
        }


        $job_apply_switch = isset($jobsearch_plugin_options['job-apply-switch']) ? $jobsearch_plugin_options['job-apply-switch'] : 'on';
        if (isset($job_apply_switch) && $job_apply_switch == 'on') {
            $job_extrnal_apply_switch = isset($jobsearch_plugin_options['apply-methods']) ? $jobsearch_plugin_options['apply-methods'] : '';
            $internal_flag = false;
            $external_flag = false;
            $email_flag = false;
            if (isset($job_extrnal_apply_switch) && is_array($job_extrnal_apply_switch) && sizeof($job_extrnal_apply_switch) > 0) {
                foreach ($job_extrnal_apply_switch as $apply_switch) {
                    if ($apply_switch == 'internal') {
                        $internal_flag = true;
                        $type_hidden_value = 'internal';
                    }
                    if ($apply_switch == 'external') {
                        $external_flag = true;
                        $type_hidden_value = 'external';
                    }
                    if ($apply_switch == 'email') {
                        $email_flag = true;
                        $type_hidden_value = 'with_email';
                    }
                }
            }
            $dropdown_flag = false;
            if ($internal_flag && $external_flag && $email_flag) { // in case of all selected
                $dropdown_flag = true;
            }
            if ($internal_flag && $external_flag) { // in case of internal and external
                $dropdown_flag = true;
            }
            if ($internal_flag && $email_flag) {
                $dropdown_flag = true;
            }
            if ($external_flag && $email_flag) {
                $dropdown_flag = true;
            }
            $apply_type_options = array();
            if ($internal_flag) {
                $apply_type_options['internal'] = esc_html__('Internal', 'wp-jobsearch');
            }
            if ($external_flag) {
                $apply_type_options['external'] = esc_html__('External URL', 'wp-jobsearch');
            }
            if ($email_flag) {
                $apply_type_options['with_email'] = esc_html__('By Email', 'wp-jobsearch');
            }
            $apply_type_options['none'] = esc_html__('None', 'wp-jobsearch');

            ?>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Job Apply Type', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'job_apply_type',
                        'options' => $apply_type_options,
                    );
                    $jobsearch_form_fields->select_field($field_params);
                    ?>
                </div>
            </div>
            <?php if ($external_flag) { ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('External URL for Apply Job', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'job_apply_url',
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
            <?php } ?>

            <?php if ($email_flag) { ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Job Apply Email', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'job_apply_email',
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
            }
            echo apply_filters('jobsearch_jobadmin_meta_after_applyopts_html', '', $_post_id);
        }

        $_job_salary_type = get_post_meta($_post_id, 'jobsearch_field_job_salary_type', true);

        ob_start();
        $salary_onoff_switch = isset($jobsearch_plugin_options['salary_onoff_switch']) ? $jobsearch_plugin_options['salary_onoff_switch'] : '';
        if ($salary_onoff_switch != 'off') {
            ?>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Min. Salary', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'job_salary',
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Max. Salary', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'job_max_salary',
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
                $salar_types['negotiable'] = esc_html__('Negotiable', 'wp-jobsearch');
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Salary Type', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'job_salary_type',
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
                            'name' => 'job_salary_currency',
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
                            'name' => 'job_salary_pos',
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
                            'name' => 'job_salary_sep',
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
                            'name' => 'job_salary_deci',
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
            }
        }

        $salry_fieldshtml = ob_get_clean();
        echo apply_filters('jobsearch_bckend_addup_job_salary_fields_html', $salry_fieldshtml, $_post_id);
        ?>
        <div class="jobsearch-element-field combine-onoff-container">
            <div class="elem-label">
                <label><?php esc_html_e('Featured expiry date', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'std' => 'on',
                    'name' => 'job_featured',
                );
                $jobsearch_form_fields->checkbox_field($field_params);

                //
                $field_params = array(
                    'id' => 'jobsearch_job_feature_till',
                    'name' => 'job_feature_till',
                    'ext_attr' => 'placeholder="' . esc_html__('Featured expiry date', 'wp-jobsearch') . '"',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Urgent Job', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $job_urgnt_val = get_post_meta($_post_id, 'cusjob_urgent_fbckend', true);

                if ($job_urgnt_val != 'on' && $job_urgnt_val != 'off') {
                    $_urgntjob_val = get_post_meta($_post_id, 'jobsearch_field_urgent_job', true);
                    $urgnt_att_pckg = get_post_meta($job_employer_id, 'att_urgent_pkg_orderid', true);
                    if (!jobsearch_promote_profile_pkg_is_expired($urgnt_att_pckg) && $_urgntjob_val == 'on') {
                        $job_urgnt_val = 'on';
                    }
                }

                $field_params = array(
                    'force_std' => $job_urgnt_val,
                    'name' => 'job_urgent',
                    'cus_name' => 'cusjob_urgent_fbckend',
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Filled', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'std' => 'on',
                    'name' => 'job_filled',
                    'field_desc' => esc_html__('Filled listings will no longer accept applications.', 'wp-jobsearch'),
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>
        <?php do_action('jobsearch_job_detail_bk_after_filljob_field') ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Posted By', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                //                jobsearch_get_custom_post_field($job_posted_by, 'employer', esc_html__('employer', 'wp-jobsearch'), 'job_posted_by');
                //                //
                //                $job_employer_id = get_post_meta($_post_id, 'jobsearch_field_job_posted_by', true);
                //                if ($job_employer_id != '') {
                //                    $user_phone = get_post_meta($job_employer_id, 'jobsearch_field_user_phone', true);
                //                    $employer_user_id = jobsearch_get_employer_user_id($job_employer_id);
                //                    $emp_user_obj = get_user_by('ID', $employer_user_id);
                //                    if (isset($emp_user_obj->user_email)) {
                //                        echo '<p>' . sprintf(esc_html__('User email : %s', 'wp-jobsearch'), $emp_user_obj->user_email) . '</p>';
                //                    }
                //                    if ($user_phone != '') {
                //                        echo '<p>' . sprintf(esc_html__('User Phone : %s', 'wp-jobsearch'), $user_phone) . '</p>';
                //                    }
                //                }
                ?>
                <div class="attachd-user-mcon" style="position: relative; display: inline-block;">
                    <?php
                    $job_employer_id = get_post_meta($_post_id, 'jobsearch_field_job_posted_by', true);

                    if ($job_employer_id != '' && get_post_type($job_employer_id) == 'employer') {

                        $employer_user_id = jobsearch_get_employer_user_id($job_employer_id);
                        $user_obj = get_user_by('ID', $employer_user_id);

                        $atch_user_logname = isset($user_obj->display_name) ? $user_obj->display_name : '';
                        $atch_user_logname = apply_filters('jobsearch_user_display_name', $atch_user_logname, $user_obj);

                        if ($atch_user_logname == '') {
                            $atch_user_logname = get_the_title($job_employer_id);
                        }
                        echo '<strong class="atch-userlogin">' . jobsearch_esc_html($atch_user_logname) . '</strong>';
                        //
                        $user_phone = get_post_meta($job_employer_id, 'jobsearch_field_user_phone', true);
                        $user_phone = $user_phone != '' ? $user_phone : esc_html__('N/L', 'wp-jobsearch');
                        echo '<p class="atch-useremail">' . sprintf(__('User email : <span>%s</span>', 'wp-jobsearch'), isset($user_obj->user_email) ? $user_obj->user_email : '') . '</p>';
                        echo '<p class="atch-userphone">' . sprintf(__('User Phone : <span>%s</span>', 'wp-jobsearch'), jobsearch_esc_html($user_phone)) . '</p>';
                    } else {
                        $elsemp_title = esc_html__('N/L', 'wp-jobsearch');
                        ?>
                        <strong class="atch-userlogin"><?php echo jobsearch_esc_html($elsemp_title) ?></strong>
                        <p class="atch-useremail"><?php _e('User email : <span>N/L</span>', 'wp-jobsearch') ?></p>
                        <p class="atch-userphone"><?php _e('User Phone : <span>N/L</span>', 'wp-jobsearch') ?></p>
                        <?php
                    }
                    ?>
                    <input type="hidden" name="jobsearch_field_job_posted_by" value="<?php echo($job_employer_id) ?>">
                </div>
                <div class="change-userbtn-con">
                    <a href="javascript:void(0);"
                       id="chnge-attachuser-toemp"><?php esc_html_e('Change Employer', 'wp-jobsearch') ?></a>
                </div>
                <?php
                $popup_args = array('p_id' => $_post_id, 'p_rand' => $rand_num);
                add_action('admin_footer', function () use ($popup_args) {

                    global $wpdb;
                    extract(shortcode_atts(array(
                        'p_id' => '',
                        'p_rand' => ''
                    ), $popup_args));

                    $totl_users = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='employer' AND post_status='publish'");
                    ?>
                    <div class="jobsearch-modal empmeta-atchuser-modal fade"
                         id="JobSearchModalAttchUser<?php echo($p_rand) ?>">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <div class="jobsearch-useratach-popup">
                                    <span class="modal-close"><i class="fa fa-times"></i></span>
                                    <?php
                                    $attusers_query = "SELECT posts.ID,posts.post_title FROM $wpdb->posts AS posts WHERE post_type='employer' AND post_status='publish' ORDER BY ID DESC LIMIT %d";
                                    $attall_users = $wpdb->get_results($wpdb->prepare($attusers_query, 10), 'ARRAY_A');

                                    if (!empty($attall_users)) {
                                        ?>
                                        <div class="users-list-con">
                                            <strong class="users-list-hdng"><?php esc_html_e('Employers List', 'wp-jobsearch') ?></strong>

                                            <div class="user-atchp-srch">
                                                <label><?php esc_html_e('Search', 'wp-jobsearch') ?></label>
                                                <input type="text" id="user_srchinput_<?php echo($p_rand) ?>">
                                                <span></span>
                                            </div>

                                            <div id="inerlist-users-<?php echo($p_rand) ?>" class="inerlist-users-sec">
                                                <ul class="jobsearch-users-list">
                                                    <?php
                                                    foreach ($attall_users as $attch_usritm) {
                                                        ?>
                                                        <li><a href="javascript:void(0);" class="atchuser-itm-btn"
                                                               data-id="<?php echo($attch_usritm['ID']) ?>"><?php echo($attch_usritm['post_title']) ?></a>
                                                            <span></span></li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                                <?php
                                                if ($totl_users > 10) {
                                                    $total_pages = ceil($totl_users / 10);
                                                    ?>
                                                    <div class="lodmore-users-btnsec">
                                                        <a href="javascript:void(0);" class="lodmore-users-btn"
                                                           data-tpages="<?php echo($total_pages) ?>" data-keyword=""
                                                           data-gtopage="2"><?php esc_html_e('Load More', 'wp-jobsearch') ?></a>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    } else {
                                        echo '<p>' . esc_html__('No User Found.', 'wp-jobsearch') . '</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        jQuery(document).on('click', '#chnge-attachuser-toemp', function () {
                            jobsearch_modal_popup_open('JobSearchModalAttchUser<?php echo($p_rand) ?>');
                        });
                        jQuery(document).on('click', '.atchuser-itm-btn', function () {
                            var _this = jQuery(this);
                            var loader_con = _this.parent('li').find('span');
                            var parentl_con = jQuery('.attachd-user-mcon');
                            var atch_usernme_con = parentl_con.find('.atch-userlogin');
                            var atch_useremail_con = parentl_con.find('.atch-useremail span');
                            var atch_userphone_con = parentl_con.find('.atch-userphone span');
                            loader_con.html('<i class="fa fa-refresh fa-spin"></i>');

                            var request = jQuery.ajax({
                                url: ajaxurl,
                                method: "POST",
                                data: {
                                    id: _this.attr('data-id'),
                                    p_id: '<?php echo($p_id) ?>',
                                    action: 'jobsearch_jobmeta_atchemp_throgh_popup'
                                },
                                dataType: "json"
                            });
                            request.done(function (response) {
                                if (typeof response.username !== 'undefined') {
                                    atch_usernme_con.html(response.username);
                                    atch_useremail_con.html(response.email);
                                    atch_userphone_con.html(response.phone);
                                    jQuery('input[name=jobsearch_field_job_posted_by]').val(response.id);
                                    jQuery('.jobsearch-modal').removeClass('fade-in').addClass('fade');
                                    jQuery('body').removeClass('jobsearch-modal-active');
                                }
                                loader_con.html('');
                            });
                            request.fail(function (jqXHR, textStatus) {
                                loader_con.html('');
                            });
                        });
                        jQuery(document).on('click', '.lodmore-users-btn', function (e) {
                            e.preventDefault();
                            var _this = jQuery(this),
                                total_pages = _this.attr('data-tpages'),
                                page_num = _this.attr('data-gtopage'),
                                keyword = _this.attr('data-keyword'),
                                this_html = _this.html(),
                                appender_con = jQuery('#inerlist-users-<?php echo($p_rand) ?> .jobsearch-users-list');
                            if (!_this.hasClass('ajax-loadin')) {
                                _this.addClass('ajax-loadin');
                                _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

                                total_pages = parseInt(total_pages);
                                page_num = parseInt(page_num);
                                var request = jQuery.ajax({
                                    url: ajaxurl,
                                    method: "POST",
                                    data: {
                                        page_num: page_num,
                                        keyword: keyword,
                                        action: 'jobsearch_load_memps_jobmeta_popupinlist',
                                    },
                                    dataType: "json"
                                });

                                request.done(function (response) {
                                    if ('undefined' !== typeof response.html) {
                                        page_num += 1;
                                        _this.attr('data-gtopage', page_num)
                                        if (page_num > total_pages) {
                                            _this.parent('div').hide();
                                        }
                                        appender_con.append(response.html);
                                    }
                                    _this.html(this_html);
                                    _this.removeClass('ajax-loadin');
                                });

                                request.fail(function (jqXHR, textStatus) {
                                    _this.html(this_html);
                                    _this.removeClass('ajax-loadin');
                                });
                            }
                            return false;

                        });
                        jQuery(document).on('keyup', '#user_srchinput_<?php echo($p_rand) ?>', function () {
                            var _this = jQuery(this);
                            var loader_con = _this.parent('.user-atchp-srch').find('span');
                            var appender_con = jQuery('#inerlist-users-<?php echo($p_rand) ?> .jobsearch-users-list');

                            loader_con.html('<i class="fa fa-refresh fa-spin"></i>');

                            var request = jQuery.ajax({
                                url: ajaxurl,
                                method: "POST",
                                data: {
                                    keyword: _this.val(),
                                    action: 'jobsearch_jobmeta_serchemps_throgh_popup'
                                },
                                dataType: "json"
                            });
                            request.done(function (response) {
                                if (typeof response.html !== 'undefined') {
                                    appender_con.html(response.html);
                                    jQuery('#inerlist-users-<?php echo($p_rand) ?>').find('.lodmore-users-btnsec').html(response.lodrhtml);
                                    if (response.count > 10) {
                                        jQuery('#inerlist-users-<?php echo($p_rand) ?>').find('.lodmore-users-btnsec').show();
                                    } else {
                                        jQuery('#inerlist-users-<?php echo($p_rand) ?>').find('.lodmore-users-btnsec').hide();
                                    }
                                }
                                loader_con.html('');
                            });
                            request.fail(function (jqXHR, textStatus) {
                                loader_con.html('');
                            });
                        });
                    </script>
                    <?php
                }, 11, 1);
                ?>

            </div>
        </div>

        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Status', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $status_options = array(
                    'admin-review' => esc_html__('Admin Review', 'wp-jobsearch'),
                    'pending' => esc_html__('Pending', 'wp-jobsearch'),
                    'approved' => esc_html__('Approved', 'wp-jobsearch'),
                    'canceled' => esc_html__('Canceled', 'wp-jobsearch'),
                );
                $field_params = array(
                    'name' => 'job_status',
                    'options' => $status_options,
                );
                $jobsearch_form_fields->select_field($field_params);
                ?>
                <input type="hidden" name="jobsearch_job_presnt_status" value="<?php echo($job_status) ?>">
            </div>
        </div>
        <?php
        // load custom fields which is configured in job custom fields
        do_action('jobsearch_custom_fields_load', $post->ID, 'job');
        //
        do_action('jobsearch_job_meta_ext_fields', $post->ID);
        // before location
        do_action('jobsearch_job_admin_meta_before_location', $post->ID);

        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

        if ($all_location_allow == 'on') {
            ob_start();
            ?>
            <div class="jobsearch-elem-heading">
                <h2><?php esc_html_e('Location', 'wp-jobsearch') ?></h2>
            </div>
            <?php
            $lochding_html = ob_get_clean();
            echo apply_filters('jobsearch_bkend_locfields_title_html', $lochding_html);
        }
        do_action('jobsearch_admin_location_map', $post->ID);

        echo apply_filters('jobsearch_job_backend_meta_after_locs_field', '', $_post_id);

        /*
        * Job Attachment
        */
        ?>
        <div class="jobsearch-elem-heading">
            <h2><?php esc_html_e('Add Attachment', 'wp-jobsearch') ?></h2>
        </div>
        <div class="jobsearch-element-field">
            <?php
            $job_id = $post->ID;
            $job_attachments_switch = isset($jobsearch_plugin_options['job_attachments']) ? $jobsearch_plugin_options['job_attachments'] : '';
            if ($job_attachments_switch == 'on') { ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('File Attachments', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <div class="jobsearch-fileUpload">
                            <input id="job_attach_files" name="job_attach_files[]" type="button"
                                   class="upload jobsearch-upload" value="Attach Files"/>
                        </div>
                        <div id="attach-files-holder" class="gallery-imgs-holder jobsearch-company-gallery">
                            <?php
                            $all_attach_files = get_post_meta($job_id, 'jobsearch_field_job_attachment_files', true);
                            if (!empty($all_attach_files)) { ?>
                                <ul>
                                    <?php
                                    foreach ($all_attach_files as $_attach_file) {
                                        $_attach_id = jobsearch_get_attachment_id_from_url($_attach_file);
                                        $_attach_post = get_post($_attach_id);
                                        $_attach_mime = isset($_attach_post->post_mime_type) ? $_attach_post->post_mime_type : '';
                                        $_attach_guide = isset($_attach_post->guid) ? $_attach_post->guid : '';
                                        $attach_name = basename($_attach_guide);
                                        $file_icon = 'fa fa-file-text-o';
                                        if ($_attach_mime == 'image/png' || $_attach_mime == 'image/jpeg') {
                                            $file_icon = 'fa fa-file-image-o';
                                        } else if ($_attach_mime == 'application/msword' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                            $file_icon = 'fa fa-file-word-o';
                                        } else if ($_attach_mime == 'application/vnd.ms-excel' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                            $file_icon = 'fa fa-file-excel-o';
                                        } else if ($_attach_mime == 'application/pdf') {
                                            $file_icon = 'fa fa-file-pdf-o';
                                        }
                                        ?>
                                        <li class="jobsearch-column-3">
                                            <a href="javascript:void(0);" class="fa fa-remove el-remove"></a>
                                            <div class="file-container">
                                                <a href="<?php echo($_attach_file) ?>"
                                                   oncontextmenu="javascript: return false;"
                                                   onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                   download="<?php echo($attach_name) ?>"><i
                                                            class="<?php echo($file_icon) ?>"></i> <?php echo($attach_name) ?>
                                                </a>
                                            </div>
                                            <input type="hidden" name="jobsearch_field_job_attachment_files[]"
                                                   value="<?php echo($_attach_file) ?>">
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
        /*
         * Job Attachment End
         */
        $job_packages_arr = get_post_meta($_post_id, 'attach_packages_array', true);
        if (!empty($job_packages_arr)) {
            $attached_pkg = end($job_packages_arr);
            ?>
            <div class="jobsearch-elem-heading">
                <h2><?php esc_html_e('Attached Package Info', 'wp-jobsearch') ?></h2>
            </div>
            <ul class="job-attached-pinfo">
                <?php
                if (isset($attached_pkg['package_name'])) {
                    $pkge_name = $attached_pkg['package_name'];
                    echo '<li><span class="pinfo-title">' . esc_html__('Package Name:', 'wp-jobsearch') . '</span> <span class="pinfo-value">' . $pkge_name . '</span></li>';
                }
                if (isset($attached_pkg['package_price'])) {
                    $pkge_price = $attached_pkg['package_price'];
                    echo '<li><span class="pinfo-title">' . esc_html__('Package Price:', 'wp-jobsearch') . '</span> <span class="pinfo-value">' . jobsearch_get_price_format($pkge_price) . '</span></li>';
                }
                if (isset($attached_pkg['num_of_jobs'])) {
                    $pkge_num_jobs = $attached_pkg['num_of_jobs'];
                    echo '<li><span class="pinfo-title">' . esc_html__('Number of Jobs:', 'wp-jobsearch') . '</span> <span class="pinfo-value">' . $pkge_num_jobs . '</span></li>';
                } else if (isset($attached_pkg['num_of_fjobs'])) {
                    $pkge_num_jobs = $attached_pkg['num_of_fjobs'];
                    echo '<li><span class="pinfo-title">' . esc_html__('Number of Jobs:', 'wp-jobsearch') . '</span> <span class="pinfo-value">' . $pkge_num_jobs . '</span></li>';
                }
                if (isset($attached_pkg['job_expiry_time']) && isset($attached_pkg['job_expiry_time_unit'])) {
                    $pkg_exp_dur = $attached_pkg['job_expiry_time'];
                    $pkg_exp_dur_unit = $attached_pkg['job_expiry_time_unit'];
                    echo '<li><span class="pinfo-title">' . esc_html__('Job expires in:', 'wp-jobsearch') . '</span> <span class="pinfo-value">' . (absint($pkg_exp_dur) . ' ' . jobsearch_get_duration_unit_str($pkg_exp_dur_unit)) . '</span></li>';
                } else if (isset($attached_pkg['fjob_expiry_time']) && isset($attached_pkg['fjob_expiry_time_unit'])) {
                    $pkg_exp_dur = $attached_pkg['fjob_expiry_time'];
                    $pkg_exp_dur_unit = $attached_pkg['fjob_expiry_time_unit'];
                    echo '<li><span class="pinfo-title">' . esc_html__('Job expires in:', 'wp-jobsearch') . '</span> <span class="pinfo-value">' . (absint($pkg_exp_dur) . ' ' . jobsearch_get_duration_unit_str($pkg_exp_dur_unit)) . '</span></li>';
                }
                ?>
            </ul>
            <?php
        }

        ob_start();
        // Add Applicants
        $_job_applicants_list = get_post_meta($_post_id, 'jobsearch_job_applicants_list', true);
        $_job_applicants_list = jobsearch_is_post_ids_array($_job_applicants_list, 'candidate');


        if (empty($_job_applicants_list)) {
            $_job_applicants_list = array();
        }
        ?>

        <div class="jobsearch-elem-heading">
            <h2><?php esc_html_e('Add Applicants', 'wp-jobsearch') ?></h2>
        </div>
        <div class="jobsearch-element-field">
            <script>
                jQuery(document).on('click', '#job-appslist-<?php echo($rand_num); ?>', function () {
                    var _this = jQuery(this);
                    if (!_this.hasClass('ajax-loaded')) {
                        var loader_con = _this.find('.apps-loader');
                        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                        var request = jQuery.ajax({
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            method: "POST",
                            data: {
                                'job_id': '<?php echo($_post_id) ?>',
                                'action': 'jobsearch_admin_meta_job_apps_list'
                            },
                            dataType: "json"
                        });
                        request.done(function (response) {
                            if (typeof response.html !== 'undefined') {
                                _this.html(response.html);
                                _this.attr('class', 'jobsearch-meta-multiselect ajax-loaded');
                            }
                            loader_con.html('');
                        });

                        request.fail(function (jqXHR, textStatus) {
                            loader_con.html('');
                        });
                    }
                });
                jQuery(document).on('click', '#job-appssav-btn-<?php echo($rand_num); ?>', function () {
                    var _this = jQuery(this);
                    var loader_con = _this.parent('.jobsearch-apps-svbtn').find('.apps-loader');
                    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                    var all_apps = jQuery('select[name^="job_all_apps_list"]').val();
                    var request = jQuery.ajax({
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        method: "POST",
                        data: {
                            'all_apps': all_apps,
                            'job_id': '<?php echo($_post_id) ?>',
                            'action': 'jobsearch_admin_meta_job_apps_list_save'
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        loader_con.html('');
                        if (typeof response.msg !== 'undefined') {
                            loader_con.html(response.msg);
                        }
                    });

                    request.fail(function (jqXHR, textStatus) {
                        loader_con.html('');
                    });
                });
            </script>
            <div class="elem-label">
                <label><?php esc_html_e('Applicants', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <div id="job-appslist-<?php echo($rand_num); ?>" class="jobsearch-meta-multiselect">
                    <span class="apps-loader"></span>
                    <select name="job_all_apps_list[]" multiple="multiple" class="applicants-selectize"
                            placeholder="<?php esc_html_e('Select Candidates', 'wp-jobsearch') ?>">
                        <?php
                        if (!empty($_job_applicants_list)) {
                            foreach ($_job_applicants_list as $job_app_id) {
                                ?>
                                <option value="<?php echo($job_app_id) ?>"
                                        selected="selected"><?php echo get_the_title($job_app_id) ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <br/>
                <div class="jobsearch-apps-svbtn">
                    <a id="job-appssav-btn-<?php echo($rand_num); ?>" href="javascript:void(0);"
                       class="button button-primary button-large"><?php esc_html_e('Save Job Applicants', 'wp-jobsearch') ?></a>
                    &nbsp;
                    <span class="apps-loader"></span>
                </div>
            </div>
        </div>
        <?php
        //
        $job_applicants_list = jobsearch_job_applicants_sort_list($_post_id);
        if (!empty($job_applicants_list)) {
            wp_enqueue_script('jobsearch-user-dashboard');
            ?>
            <div class="jobsearch-elem-heading">
                <h2><?php esc_html_e('Applicants', 'wp-jobsearch') ?></h2>
            </div>
            <?php
            global $Jobsearch_User_Dashboard_Settings;

            $employer_user_id = jobsearch_get_employer_user_id($job_employer_id);

            $user_obj = get_user_by('ID', $employer_user_id);

            $employer_id = $job_employer_id;

            $page_url = admin_url('post.php');
            $page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
            $reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

            $_job_id = $_post_id;

            $job_applicants_list = get_post_meta($_job_id, 'jobsearch_job_applicants_list', true);
            $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
            if (empty($job_applicants_list)) {
                $job_applicants_list = array();
            }

            $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;

            $viewed_candidates = get_post_meta($_job_id, 'jobsearch_viewed_candidates', true);
            if (empty($viewed_candidates)) {
                $viewed_candidates = array();
            }
            $viewed_candidates = jobsearch_is_post_ids_array($viewed_candidates, 'candidate');

            $job_short_int_list = get_post_meta($_job_id, '_job_short_interview_list', true);
            $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
            if (empty($job_short_int_list)) {
                $job_short_int_list = array();
            }
            $job_short_int_list = jobsearch_is_post_ids_array($job_short_int_list, 'candidate');
            $job_short_int_list_c = !empty($job_short_int_list) ? count($job_short_int_list) : 0;

            $job_reject_int_list = get_post_meta($_job_id, '_job_reject_interview_list', true);
            $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : '';
            if (empty($job_reject_int_list)) {
                $job_reject_int_list = array();
            }
            $job_reject_int_list = jobsearch_is_post_ids_array($job_reject_int_list, 'candidate');
            $job_reject_int_list_c = !empty($job_reject_int_list) ? count($job_reject_int_list) : 0;

            $_selected_view = isset($_GET['ap_view']) && $_GET['ap_view'] != '' ? $_GET['ap_view'] : 'less';

            $_mod_tab = isset($_GET['mod']) && $_GET['mod'] != '' ? $_GET['mod'] : 'applicants';
            $_sort_selected = isset($_GET['sort_by']) && $_GET['sort_by'] != '' ? $_GET['sort_by'] : '';
            ?>
            <div class="jobsearch-applicants-tabs">
                <script>
                    jQuery(document).on('click', '.jobsearch-modelemail-btn-<?php echo($_job_id) ?>', function () {
                        jobsearch_modal_popup_open('JobSearchModalSendEmail<?php echo($_job_id) ?>');
                    });
                </script>
                <ul class="tabs-list">
                    <li <?php echo($_mod_tab == '' || $_mod_tab == 'applicants' ? 'class="active"' : '') ?>><a
                                href="<?php echo add_query_arg(array('post' => $_job_id, 'action' => 'edit', 'view' => 'applicants'), $page_url) ?>"><?php printf(esc_html__('Applicants (%s)', 'wp-jobsearch'), $job_applicants_count) ?></a>
                    </li>
                    <li <?php echo($_mod_tab == 'shortlisted' ? 'class="active"' : '') ?>><a
                                href="<?php echo add_query_arg(array('post' => $_job_id, 'action' => 'edit', 'view' => 'applicants', 'mod' => 'shortlisted'), $page_url) ?>"><?php printf(esc_html__('Shortlisted for Interview (%s)', 'wp-jobsearch'), $job_short_int_list_c) ?></a>
                    </li>
                    <li <?php echo($_mod_tab == 'rejected' ? 'class="active"' : '') ?>><a
                                href="<?php echo add_query_arg(array('post' => $_job_id, 'action' => 'edit', 'view' => 'applicants', 'mod' => 'rejected'), $page_url) ?>"><?php printf(esc_html__('Rejected (%s)', 'wp-jobsearch'), $job_reject_int_list_c) ?></a>
                    </li>
                </ul>
                <?php
                if ($_mod_tab == 'shortlisted') {
                    $job_applicants_list = jobsearch_job_applicants_sort_list($_job_id, $_sort_selected, '_job_short_interview_list');
                } else if ($_mod_tab == 'rejected') {
                    $job_applicants_list = jobsearch_job_applicants_sort_list($_job_id, $_sort_selected, '_job_reject_interview_list');
                } else {
                    $job_applicants_list = jobsearch_job_applicants_sort_list($_job_id, $_sort_selected);
                }

                $total_records = !empty($job_applicants_list) ? count($job_applicants_list) : 0;

                $start = ($page_num - 1) * ($reults_per_page);
                $offset = $reults_per_page;
                $job_applicants_list = array_slice($job_applicants_list, $start, $offset);
                ?>
                <div class="jobsearch-applied-jobs">
                    <?php
                    if (!empty($job_applicants_list)) {
                        ?>
                        <ul class="jobsearch-row">
                            <?php
                            foreach ($job_applicants_list as $_candidate_id) {
                                $candidate_user_id = jobsearch_get_candidate_user_id($_candidate_id);
                                if (absint($candidate_user_id) <= 0) {
                                    continue;
                                }
                                $user_def_avatar_url = jobsearch_candidate_img_url_comn($_candidate_id);

                                $candidate_jobtitle = get_post_meta($_candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                                $get_candidate_location = get_post_meta($_candidate_id, 'jobsearch_field_location_address', true);

                                $candidate_city_title = '';
                                $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location3', true);
                                if ($get_candidate_city == '') {
                                    $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location2', true);
                                }
                                if ($get_candidate_city == '') {
                                    $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location1', true);
                                }

                                $candidate_city_tax = $get_candidate_city != '' ? get_term_by('slug', $get_candidate_city, 'job-location') : '';
                                if (is_object($candidate_city_tax)) {
                                    $candidate_city_title = $candidate_city_tax->name;
                                }

                                $sectors = wp_get_post_terms($_candidate_id, 'sector');
                                $candidate_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';

                                $candidate_salary = jobsearch_candidate_current_salary($_candidate_id);
                                $candidate_age = jobsearch_candidate_age($_candidate_id);

                                $candidate_phone = get_post_meta($_candidate_id, 'jobsearch_field_user_phone', true);

                                $send_message_form_rand = rand(100000, 999999);
                                ?>
                                <li class="jobsearch-column-12">
                                    <script>
                                        jQuery(document).on('click', '.jobsearch-modelemail-btn-<?php echo($send_message_form_rand) ?>', function () {
                                            jobsearch_modal_popup_open('JobSearchModalSendEmail<?php echo($send_message_form_rand) ?>');
                                        });
                                    </script>
                                    <div class="jobsearch-applied-jobs-wrap">
                                        <a class="jobsearch-applied-jobs-thumb">
                                            <img src="<?php echo($user_def_avatar_url) ?>" alt="">
                                        </a>
                                        <div class="jobsearch-applied-jobs-text">
                                            <div class="jobsearch-applied-jobs-left">
                                                <?php
                                                if ($candidate_jobtitle != '') {
                                                    ?>
                                                    <span> <?php echo($candidate_jobtitle) ?></span>
                                                    <?php
                                                }

                                                if (in_array($_candidate_id, $viewed_candidates)) {
                                                    ?>
                                                    <small class="profile-view viewed"><?php esc_html_e('(Viewed)', 'wp-jobsearch') ?></small>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <small class="profile-view unviewed"><?php esc_html_e('(Unviewed)', 'wp-jobsearch') ?></small>
                                                    <?php
                                                }
                                                echo apply_filters('jobsearch_applicants_list_before_title', '', $_candidate_id, $_job_id);
                                                ?>
                                                <h2>
                                                    <a href="<?php echo get_permalink($_candidate_id) ?>"><?php echo get_the_title($_candidate_id) ?></a>
                                                    <?php
                                                    if ($candidate_age != '') {
                                                        ?>
                                                        <small><?php echo apply_filters('jobsearch_dash_applicants_age_html', sprintf(esc_html__('(Age: %s years)', 'wp-jobsearch'), $candidate_age)) ?></small>
                                                        <?php
                                                    }
                                                    if ($candidate_phone != '') {
                                                        ?>
                                                        <small><?php printf(esc_html__('Phone: %s', 'wp-jobsearch'), $candidate_phone) ?></small>
                                                        <?php
                                                    }
                                                    ?>
                                                </h2>
                                                <ul>
                                                    <?php
                                                    if ($candidate_salary != '') {
                                                        ?>
                                                        <li>
                                                            <i class="fa fa-money"></i> <?php printf(esc_html__('Salary: %s', 'wp-jobsearch'), $candidate_salary) ?>
                                                        </li>
                                                        <?php
                                                    }
                                                    if ($candidate_city_title != '') {
                                                        ?>
                                                        <li>
                                                            <i class="fa fa-map-marker"></i> <?php echo($candidate_city_title) ?>
                                                        </li>
                                                        <?php
                                                    }
                                                    if ($candidate_sector != '') {
                                                        ?>
                                                        <li>
                                                            <i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>
                                                            <a><?php echo($candidate_sector) ?></a></li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <div class="jobsearch-applied-job-btns">
                                                <ul>
                                                    <li>
                                                        <a href="<?php echo add_query_arg(array('job_id' => $_job_id, 'employer_id' => $employer_id, 'action' => 'preview_profile'), get_permalink($_candidate_id)) ?>"
                                                           class="preview-candidate-profile"><i
                                                                    class="fa fa-eye"></i> <?php esc_html_e('Preview', 'wp-jobsearch') ?>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <div class="candidate-more-acts-con">
                                                            <a href="javascript:void(0);"
                                                               class="more-actions"><?php esc_html_e('Actions', 'wp-jobsearch') ?>
                                                                <i class="fa fa-angle-down"></i></a>
                                                            <ul>
                                                                <?php
                                                                $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
                                                                $candidate_cv_file = get_post_meta($_candidate_id, 'candidate_cv_file', true);

                                                                if ($multiple_cv_files_allow == 'on') {
                                                                    $ca_at_cv_files = get_post_meta($_candidate_id, 'candidate_cv_files', true);
                                                                    if (!empty($ca_at_cv_files)) {
                                                                        ?>
                                                                        <li>
                                                                            <a href="<?php echo apply_filters('jobsearch_user_attach_cv_file_url', '', $_candidate_id, $_job_id) ?>"
                                                                               oncontextmenu="javascript: return false;"
                                                                               onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                                               download="<?php echo apply_filters('jobsearch_user_attach_cv_file_title', '', $_candidate_id, $_job_id) ?>"><?php esc_html_e('Download CV', 'wp-jobsearch') ?></a>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                } else if (!empty($candidate_cv_file)) {
                                                                    $file_attach_id = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
                                                                    $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';

                                                                    $filename = isset($candidate_cv_file['file_name']) ? $candidate_cv_file['file_name'] : '';

                                                                    $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_attach_id, $_candidate_id);
                                                                    ?>
                                                                    <li><a href="<?php echo($file_url) ?>"
                                                                           oncontextmenu="javascript: return false;"
                                                                           onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                                           download="<?php echo($filename) ?>"><?php esc_html_e('Download CV', 'wp-jobsearch') ?></a>
                                                                    </li>
                                                                    <?php
                                                                }
                                                                echo apply_filters('employer_dash_apps_acts_list_after_download_link', '', $_candidate_id, $_job_id);
                                                                ?>
                                                                <li>
                                                                    <a href="javascript:void(0);"
                                                                       class="jobsearch-modelemail-btn-<?php echo($send_message_form_rand) ?>"><?php esc_html_e('Email to Candidate', 'wp-jobsearch') ?></a>
                                                                    <?php
                                                                    $popup_args = array('p_job_id' => $_job_id, 'cand_id' => $_candidate_id, 'p_emp_id' => $employer_id, 'p_masg_rand' => $send_message_form_rand);
                                                                    add_action('admin_footer', function () use ($popup_args) {

                                                                        extract(shortcode_atts(array(
                                                                            'p_job_id' => '',
                                                                            'p_emp_id' => '',
                                                                            'cand_id' => '',
                                                                            'p_masg_rand' => ''
                                                                        ), $popup_args));
                                                                        ?>
                                                                        <div class="jobsearch-modal fade"
                                                                             id="JobSearchModalSendEmail<?php echo($p_masg_rand) ?>">
                                                                            <div class="modal-inner-area">&nbsp;</div>
                                                                            <div class="modal-content-area">
                                                                                <div class="modal-box-area">
                                                                                    <span class="modal-close"><i
                                                                                                class="fa fa-times"></i></span>
                                                                                    <div class="jobsearch-send-message-form">
                                                                                        <form method="post"
                                                                                              id="jobsearch_send_email_form<?php echo esc_html($p_masg_rand); ?>">
                                                                                            <div class="jobsearch-user-form">
                                                                                                <ul class="email-fields-list">
                                                                                                    <li>
                                                                                                        <label>
                                                                                                            <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>
                                                                                                            :
                                                                                                        </label>
                                                                                                        <div class="input-field">
                                                                                                            <input type="text"
                                                                                                                   name="send_message_subject"
                                                                                                                   value=""/>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                    <li>
                                                                                                        <label>
                                                                                                            <?php echo esc_html__('Message', 'wp-jobsearch'); ?>
                                                                                                            :
                                                                                                        </label>
                                                                                                        <div class="input-field">
                                                                                                            <textarea
                                                                                                                    name="send_message_content"></textarea>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                    <li>
                                                                                                        <div class="input-field-submit">
                                                                                                            <input type="submit"
                                                                                                                   class="applicantto-email-submit-btn"
                                                                                                                   data-jid="<?php echo absint($p_job_id); ?>"
                                                                                                                   data-eid="<?php echo absint($p_emp_id); ?>"
                                                                                                                   data-cid="<?php echo absint($cand_id); ?>"
                                                                                                                   data-randid="<?php echo esc_html($p_masg_rand); ?>"
                                                                                                                   name="send_message_content"
                                                                                                                   value="Send"/>
                                                                                                            <span class="loader-box loader-box-<?php echo esc_html($p_masg_rand); ?>"></span>
                                                                                                        </div>
                                                                                                        <?php jobsearch_terms_and_con_link_txt(); ?>
                                                                                                    </li>
                                                                                                </ul>
                                                                                                <div class="message-box message-box-<?php echo esc_html($p_masg_rand); ?>"
                                                                                                     style="display:none;"></div>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <?php
                                                                    }, 11, 1);
                                                                    ?>
                                                                </li>
                                                                <li>
                                                                    <?php
                                                                    if (in_array($_candidate_id, $job_short_int_list)) {
                                                                        ?>
                                                                        <a href="javascript:void(0);"
                                                                           class="shortlist-cand-to-intrview"><?php esc_html_e('Shortlisted', 'wp-jobsearch') ?></a>
                                                                        <?php
                                                                    } else {
                                                                        ?>
                                                                        <a href="javascript:void(0);"
                                                                           class="shortlist-cand-to-intrview ajax-enable"
                                                                           data-jid="<?php echo absint($_job_id); ?>"
                                                                           data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Shortlist for Interview', 'wp-jobsearch') ?>
                                                                            <span class="app-loader"></span></a>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </li>
                                                                <li>
                                                                    <?php
                                                                    if (in_array($_candidate_id, $job_reject_int_list)) {
                                                                        ?>
                                                                        <a href="javascript:void(0);"
                                                                           class="reject-cand-to-intrview"><?php esc_html_e('Rejected', 'wp-jobsearch') ?></a>
                                                                        <?php
                                                                    } else {
                                                                        ?>
                                                                        <a href="javascript:void(0);"
                                                                           class="reject-cand-to-intrview ajax-enable"
                                                                           data-jid="<?php echo absint($_job_id); ?>"
                                                                           data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Reject', 'wp-jobsearch') ?>
                                                                            <span class="app-loader"></span></a>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:void(0);"
                                                                       class="delete-cand-from-job ajax-enable"
                                                                       data-jid="<?php echo absint($_job_id); ?>"
                                                                       data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Delete', 'wp-jobsearch') ?>
                                                                        <span class="app-loader"></span></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                        <?php
                    }
                    ?>
                </div>
                <?php
                if (!empty($job_applicants_list)) {
                    $total_pages = 1;
                    if ($total_records > 0 && $reults_per_page > 0 && $total_records > $reults_per_page) {
                        $total_pages = ceil($total_records / $reults_per_page);
                        ?>
                        <div class="jobsearch-pagination-blog">
                            <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }

        $apps_html = ob_get_clean();
        echo apply_filters('jobsearch_job_meta_job_applicants_html', $apps_html, $_post_id);

        echo apply_filters('jobsearch_job_meta_after_job_applicants', '', $_post_id);
        ?>
    </div>
    <?php
    //
    do_action('jobsearch_job_update_bkend_all_fileds', $_post_id);
    if ($job_employer_id > 0 && $prev_job_status != 'approved' && $job_status == 'approved') {
        $employer_user_id = jobsearch_get_employer_user_id($job_employer_id);
        $user_obj = get_user_by('ID', $employer_user_id);
        if (isset($user_obj->ID)) {
            update_post_meta($_post_id, 'jobsearch_job_presnt_status', 'approved');
            do_action('jobsearch_job_approved_to_employer', $user_obj, $_post_id);
        }
    }
}
