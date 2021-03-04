<?php
global $jobsearch_form_fields;
$jobsearch_chat_settings = get_option('jobsearch_chat_settings');


?>
<br><br>
<form id="jobsearch-chat-settings-form" method="post">
    <div class="jobsearch-post-settings">
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Disable for Employer', 'jobsearch-ajchat') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'std' => 'off',
                    'cus_name' => 'cusjob_chat_disable_for_emp',
                    'force_std' => $jobsearch_chat_settings['cusjob_chat_disable_for_emp']
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>
    </div>
    <div class="jobsearch-post-settings">
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Disable for Candidate', 'jobsearch-ajchat') ?></label>
            </div>
            <div class="elem-field">
                <?php

                $field_params = array(
                    'std' => 'off',
                    'cus_name' => 'cusjob_chat_disable_for_cand',
                    'force_std' => $jobsearch_chat_settings['cusjob_chat_disable_for_cand']
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>
    </div>

    <div class="jobsearch-post-settings">
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Chat Package Type', 'jobsearch-ajchat') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $chat_pkg_option = array(
                    'free' => esc_html__('Free', 'jobsearch-ajchat'),
                    'paid' => esc_html__('With Package', 'jobsearch-ajchat'),
                );

                $cs_opt_array = array(
                    'cus_id' => '',
                    'cus_name' => 'chat_pkg',
                    'force_std' => $jobsearch_chat_settings['chat_pkg'],
                    'desc' => '',
                    'classes' => 'selectize-select',
                    'options' => $chat_pkg_option,
                );
                $jobsearch_form_fields->select_field($cs_opt_array);

                ?>
            </div>
        </div>
    </div>

    <div class="jobsearch-post-settings">
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Save Settings', 'jobsearch-ajchat') ?></label>
            </div>
            <div class="elem-field">
                <input type="submit" class="button button-primary" name="jobsearch_chat_setingsubmit" value="<?php esc_html_e('Save Settings', 'jobsearch-ajchat') ?>">
            </div>
        </div>
    </div>
</form>