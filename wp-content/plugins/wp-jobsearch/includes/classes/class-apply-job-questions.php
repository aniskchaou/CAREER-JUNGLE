<?php
if (!defined('ABSPATH')) {
    die;
}

class JobSearch_Apply_Job_Questions {
    
    public $tocand_email_class;
    public $toemp_email_class;
    
    public $tocand_byemail_class;
    public $toemp_byemail_class;

    public function __construct() {
        //
        add_filter('jobsearch_plugin_opts_after_jobdetail_setts', array($this, 'apply_job_questions_settings'), 15, 1);

        add_filter('jobsearch_post_job_apply_job_questions', array($this, 'dash_job_apply_job_questions'), 10, 2);
        
        add_action('jobsearch_job_admin_meta_before_location', array($this, 'admin_job_apply_job_questions'), 15);
        
        //
        add_filter('jobsearch_job_apply_simple_btn_popopen', array($this, 'job_apply_simple_btn_popopen_flag'), 15, 2);
        
        add_action('jobsearch_apply_job_woutreg_in_formtag_html', array($this, 'in_form_apply_job_questions'), 15);
        add_filter('jobsearch_apply_job_woutreg_inform_tag_exattrs', array($this, 'in_form_apply_job_exattrs'), 15, 2);
        
        add_action('jobsearch_apply_job_withemail_in_formtag_html', array($this, 'in_form_apply_job_questions'), 15);
        add_filter('jobsearch_apply_job_withemail_inform_tag_exattrs', array($this, 'in_form_apply_job_exattrs'), 15, 2);
        
        add_action('jobsearch_apply_job_internal_bfr_main_html', array($this, 'in_form_apply_job_questions'), 15);
        add_filter('jobsearch_apply_job_internal_main_tag_exattrs', array($this, 'in_form_apply_job_exattrs'), 15, 2);
        
        add_action('jobsearch_job_applying_save_action', array($this, 'job_applying_save'), 15, 2);
        //
        add_action('jobsearch_job_applying_byemail_save_action', array($this, 'job_applying_save'), 11, 2);
        
        add_filter('employer_dash_apps_acts_listul_after', array($this, 'in_dash_simp_apply_job_quests_show'), 15, 3);
        add_filter('indash_email_apps_acts_list_after_download_link', array($this, 'in_dash_apply_job_quests_show'), 15, 3);
        
        add_action('wp_footer', array($this, 'show_questanswer_popup_common'), 20);
        add_action('admin_footer', array($this, 'show_questanswer_popup_common'), 20);
        
        add_filter('bckend_all_apps_acts_list_after_download_link', array($this, 'in_bkend_apply_job_quests_show'), 15, 3);
        add_filter('bckend_email_apps_acts_list_after_download_link', array($this, 'in_bkend_apply_job_quests_show'), 15, 3);
        
        add_filter('wp_jobsearch_applyjob_quset_file_downlod_url', array($this, 'upload_file_downlod_url'), 10, 4);
        
        add_action('wp_ajax_wp_jobsearch_get_aplyjob_quest_file', array($this, 'upload_file_downlod_action'));
        add_action('wp_ajax_nopriv_wp_jobsearch_get_aplyjob_quest_file', array($this, 'upload_file_downlod_action'));
        
        add_filter('jobsearch_jobaply_by_cand_tocand_codes', array($this, 'jobaply_by_cand_tocand_codes'), 35, 2);
        add_filter('jobsearch_jobaply_by_cand_toemp_codes', array($this, 'jobaply_by_cand_toemp_codes'), 35, 2);
        
        add_filter('jobsearch_jobaply_by_email_temp_codes', array($this, 'jobaply_by_email_temp_codes'), 35, 2);
        add_filter('jobsearch_jobaply_by_email_tocand_temp_codes', array($this, 'jobaply_by_email_tocand_temp_codes'), 35, 2);
        
        add_action('jobsearch_job_applying_before_action', array($this, 'job_applying_before_action'), 20, 2);
        add_action('jobsearch_applyin_job_wout_reg_bf4_usereg', array($this, 'applyin_job_wout_reg_before'), 20);
    }

    public function apply_job_questions_settings($section_settings) {
        
        $apply_settins = array();
        $apply_settins[] = array(
            'id' => 'apply_job_questions',
            'type' => 'button_set',
            'title' => __('Apply Job Questions', 'wp-jobsearch'),
            'subtitle' => __('Enable/Disable apply job questions.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $apply_settins[] = array(
            'id' => 'apply_job_quest_types',
            'type' => 'button_set',
            'multi' => true,
            'title' => __('Apply Job Question Types', 'wp-jobsearch'),
            'subtitle' => __('Select question types which will allow to add while job posting.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'dropdown' => __('Dropdown', 'wp-jobsearch'),
                'checkboxes' => __('Checkboxes', 'wp-jobsearch'),
                'number' => __('Number', 'wp-jobsearch'),
                'text' => __('Text', 'wp-jobsearch'),
                'textarea' => __('Textarea', 'wp-jobsearch'),
                'upload' => __('Upload', 'wp-jobsearch'),
            ),
            'default' => array('dropdown', 'checkboxes', 'number', 'text', 'textarea', 'upload'),
        );
        $apply_settins[] = array(
            'id' => 'apply_job_quest_filetypes',
            'type' => 'select',
            'multi' => true,
            'title' => __('Upload File Formats', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'text/plain' => __('text', 'wp-jobsearch'),
                'image/jpeg' => __('jpeg', 'wp-jobsearch'),
                'image/png' => __('png', 'wp-jobsearch'),
                'application/msword' => __('doc', 'wp-jobsearch'),
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __('docx', 'wp-jobsearch'),
                'application/vnd.ms-excel' => __('xls', 'wp-jobsearch'),
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => __('xlsx', 'wp-jobsearch'),
                'application/pdf' => __('pdf', 'wp-jobsearch'),
            ),
            'default' => array(
                'image/jpeg',
                'image/png',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/pdf'
            ),
            'subtitle' => __('Select which file formats are allowed to upload in upload field.', 'wp-jobsearch'),
        );
        $apply_settins[] = array(
            'id' => 'apply_job_quest_filesize',
            'type' => 'select',
            'title' => __('Max. Upload File Size', 'wp-jobsearch'),
            'subtitle' => __('Restrict the Upload file size to upload.', 'wp-jobsearch'),
            'options' => array(
                '300' => __('300KB', 'wp-jobsearch'),
                '500' => __('500KB', 'wp-jobsearch'),
                '750' => __('750KB', 'wp-jobsearch'),
                '1024' => __('1Mb', 'wp-jobsearch'),
                '2048' => __('2Mb', 'wp-jobsearch'),
                '3072' => __('3Mb', 'wp-jobsearch'),
                '4096' => __('4Mb', 'wp-jobsearch'),
                '5120' => __('5Mb', 'wp-jobsearch'),
                '10120' => __('10Mb', 'wp-jobsearch'),
                '50120' => __('50Mb', 'wp-jobsearch'),
                '100120' => __('100Mb', 'wp-jobsearch'),
                '200120' => __('200Mb', 'wp-jobsearch'),
                '300120' => __('300Mb', 'wp-jobsearch'),
                '500120' => __('500Mb', 'wp-jobsearch'),
                '1000120' => __('1Gb', 'wp-jobsearch'),
            ),
            'desc' => '',
            'default' => '5120',
        );
        
        $section_settings = array(
            'title' => __('Apply Job Questions', 'wp-jobsearch'),
            'id' => 'job-apply-quests-settins',
            'desc' => '',
            'subsection' => true,
            'fields' => $apply_settins
        );
        
        return $section_settings;
    }
    
    private function quest_field_type_title($field_type) {
        if ($field_type == 'text') {
            $title = '<i class="jobsearch-icon jobsearch-paper"></i><span>' . esc_html__('Text', 'wp-jobsearch') . '</span>';
        } else if ($field_type == 'textarea') {
            $title = '<i class="jobsearch-icon jobsearch-credit-card"></i><span>' . esc_html__('Textarea', 'wp-jobsearch') . '</span>';
        } else if ($field_type == 'checkboxes') {
            $title = '<i class="jobsearch-icon jobsearch-check-square"></i><span>' . esc_html__('Checkboxes', 'wp-jobsearch') . '</span>';
        } else if ($field_type == 'number') {
            $title = '<i class="jobsearch-icon jobsearch-newspaper"></i><span>' . esc_html__('Number', 'wp-jobsearch') . '</span>';
        } else if ($field_type == 'upload') {
            $title = '<i class="jobsearch-icon jobsearch-upload"></i><span>' . esc_html__('Upload', 'wp-jobsearch') . '</span>';
        } else {
            $title = '<i class="jobsearch-icon jobsearch-list"></i><span>' . esc_html__('Dropdown', 'wp-jobsearch') . '</span>';
        }
        
        return $title;
    }

    public function dash_job_apply_job_questions($job_id, $in_admin = false) {
        global $jobsearch_plugin_options;
        
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            
            $applyjob_quest_types = isset($jobsearch_plugin_options['apply_job_quest_types']) ? $jobsearch_plugin_options['apply_job_quest_types'] : '';
            $apply_job_quests = get_post_meta($job_id, 'apply_job_questions', true);
            //echo '<pre>';
            //var_dump($apply_job_quests);
            //echo '</pre>';
            ?>
            <div class="jobsearch-employer-box-section">
                <?php
                if ($in_admin) {
                    ?>
                    <div class="jobsearch-elem-heading"><h2><?php esc_html_e('Apply Job Questions', 'wp-jobsearch') ?></h2></div>
                    <?php
                } else {
                    ?>
                    <div class="jobsearch-profile-title"><h2><?php esc_html_e('Apply Job Questions', 'wp-jobsearch') ?></h2></div>
                    <?php
                }
                ?>
                <div class="jobsearch-applyquests-dash">
                    <div class="dash-applyquests-apendcon">
                        <?php
                        if (!empty($apply_job_quests)) {
                            foreach ($apply_job_quests as $quest_key => $job_quest) {
                                $question_type = isset($job_quest['type']) ? $job_quest['type'] : '';
                                $mandatory_field = isset($job_quest['mandatory']) ? $job_quest['mandatory'] : '';
                                $require_correct = isset($job_quest['require_correct']) ? $job_quest['require_correct'] : '';
                                $multi_option = isset($job_quest['multi_option']) ? $job_quest['multi_option'] : '';
                                $question_str = isset($job_quest['question']) ? $job_quest['question'] : '';
                                $question_options = isset($job_quest['options']) ? $job_quest['options'] : '';
                                
                                if (!in_array($question_type, $applyjob_quest_types)) {
                                    continue;
                                }
                                ?>
                                <div class="applyquest-item-box">
                                    <div class="applyquest-item-type">
                                        <div class="quest-typecon-leftsec">
                                            <?php echo $this->quest_field_type_title($question_type) ?>
                                        </div>
                                        <div class="quest-typecon-ritesec">
                                            <div class="chekunchk-opt-box"<?php echo ($question_type != 'dropdown' && $question_type != 'checkboxes' ? ' style="display: none;"' : '') ?>>
                                                <div class="chekunchk-opt-boxiner">
                                                    <input type="hidden" name="apply_job_questions[<?php echo ($quest_key) ?>][multi_option]" value="<?php echo ($multi_option) ?>">
                                                    <input id="multi-opt-<?php echo ($quest_key) ?>" type="checkbox" name="[<?php echo ($quest_key) ?>][multi_option]" class="corect-opt-chkunchk"<?php echo ($multi_option == 'on' ? ' checked' : '') ?>>
                                                    <label for="multi-opt-<?php echo ($quest_key) ?>">
                                                        <span class="chkunchk-onoffswitch-inner"></span>
                                                        <span class="chkunchk-onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                                <span class="chk-onoffswitch-title"><?php esc_html_e('Multi Select Answers', 'wp-jobsearch') ?></span>
                                            </div>
                                            <div class="chekunchk-opt-box">
                                                <div class="chekunchk-opt-boxiner">
                                                    <input type="hidden" name="apply_job_questions[<?php echo ($quest_key) ?>][mandatory]" value="<?php echo ($mandatory_field) ?>">
                                                    <input id="mandatory-opt-<?php echo ($quest_key) ?>" type="checkbox" name="[<?php echo ($quest_key) ?>][mandatory]" class="corect-opt-chkunchk"<?php echo ($mandatory_field == 'on' ? ' checked' : '') ?>>
                                                    <label for="mandatory-opt-<?php echo ($quest_key) ?>">
                                                        <span class="chkunchk-onoffswitch-inner"></span>
                                                        <span class="chkunchk-onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                                <span class="chk-onoffswitch-title"><?php esc_html_e('Mandatory', 'wp-jobsearch') ?></span>
                                            </div>
                                            <div class="quest-sortremve-sec">
                                                <a class="applyquest-item-drag"><i class="fa fa-arrows"></i></a>
                                                <a href="javascript:void(0);" class="applyquest-item-remove"><i class="fa fa-times"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="applyquest-answers-outer">
                                        <div class="applyquest-item-queststr">
                                            <div class="quest-hding-optcomb">
                                                <div class="quest-type-hding"><strong><?php esc_html_e('Question Title', 'wp-jobsearch') ?></strong></div>
                                                <div class="chekunchk-opt-box"<?php echo ($question_type != 'dropdown' && $question_type != 'checkboxes' ? ' style="display: none;"' : '') ?>>
                                                    <div class="chekunchk-opt-boxiner">
                                                        <input type="hidden" name="apply_job_questions[<?php echo ($quest_key) ?>][require_correct]" value="<?php echo ($require_correct) ?>">
                                                        <input id="require-corect-<?php echo ($quest_key) ?>" type="checkbox" name="[<?php echo ($quest_key) ?>][require_correct]" class="corect-opt-chkunchk"<?php echo ($require_correct == 'on' ? ' checked' : '') ?>>
                                                        <label for="require-corect-<?php echo ($quest_key) ?>">
                                                            <span class="chkunchk-onoffswitch-inner"></span>
                                                            <span class="chkunchk-onoffswitch-switch"></span>
                                                        </label>
                                                    </div>
                                                    <span class="chk-onoffswitch-title"><?php esc_html_e('Require correct answer on apply job', 'wp-jobsearch') ?></span>
                                                </div>
                                            </div>
                                            <input type="text" name="apply_job_questions[<?php echo ($quest_key) ?>][question]" placeholder="<?php esc_html_e('Type your question here...', 'wp-jobsearch') ?>" value="<?php echo jobsearch_esc_html($question_str) ?>">
                                            <input type="hidden" name="apply_job_questions[<?php echo ($quest_key) ?>][type]" value="<?php echo ($question_type) ?>">
                                        </div>
                                        <div class="applyquest-options-main<?php echo ($multi_option == 'on' ? ' multi-ansers' : '') ?>"<?php echo ($question_type != 'dropdown' && $question_type != 'checkboxes' ? ' style="display: none;"' : '') ?>>
                                            <div class="applyquest-options-apendcon">
                                                <?php
                                                if (!empty($question_options)) {
                                                    $option_counter = 0;
                                                    $correct_answers = isset($job_quest['correct_option']) ? $job_quest['correct_option'] : '';
                                                    foreach ($question_options as $question_opt) {
                                                        $correct_answer = isset($correct_answers[$option_counter]) ? $correct_answers[$option_counter] : '';
                                                        ?>
                                                        <div class="applyquest-option-itm">
                                                            <div class="applyquest-optionstr">
                                                                <input type="text" name="apply_job_questions[<?php echo ($quest_key) ?>][options][]" placeholder="<?php esc_html_e('Type option text here...', 'wp-jobsearch') ?>" value="<?php echo jobsearch_esc_html($question_opt) ?>">
                                                            </div>
                                                            <div class="applyquest-opts-btnsec">
                                                                <div class="chekunchk-opt-box">
                                                                    <div class="chekunchk-opt-boxiner">
                                                                        <input type="hidden" name="apply_job_questions[<?php echo ($quest_key) ?>][correct_option][]" value="<?php echo ($correct_answer) ?>">
                                                                        <input id="correct-<?php echo ($quest_key . '-' . $option_counter) ?>" type="checkbox" name="[<?php echo ($quest_key) ?>][correct_option]" class="corect-opt-chkunchk"<?php echo ($correct_answer == 'on' ? ' checked' : '') ?>>
                                                                        <label for="correct-<?php echo ($quest_key . '-' . $option_counter) ?>">
                                                                            <span class="chkunchk-onoffswitch-inner"></span>
                                                                            <span class="chkunchk-onoffswitch-switch"></span>
                                                                        </label>
                                                                    </div>
                                                                    <span class="chk-onoffswitch-title"><?php esc_html_e('Correct Answer', 'wp-jobsearch') ?></span>
                                                                </div>
                                                                <div class="opts-plusminus-sec">
                                                                    <?php
                                                                    if ($option_counter > 0) {
                                                                        ?>
                                                                        <a href="javascript:void(0);" class="questopt-item-remove"><i class="fa fa-minus"></i></a>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                    <a href="javascript:void(0);" class="questopt-item-add add-new-applyoptionbtn" data-id="<?php echo ($quest_key) ?>"><i class="fa fa-plus"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        $option_counter++;
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="addnew-questtypes-btnscon">
                        <div class="select-questype-hding"><strong><?php esc_html_e('Select Question Type', 'wp-jobsearch') ?></strong></div>
                        <div class="addnew-questtypes-btnsiner">
                            <?php
                            $is_active_ocur = false;
                            if (in_array('dropdown', $applyjob_quest_types)) {
                                ?>
                                <a href="javascript:void();" class="aplyquest-type-slectbtn active-type-itm" data-type="dropdown">
                                    <i class="jobsearch-icon jobsearch-list"></i><span><?php esc_html_e('Dropdown', 'wp-jobsearch') ?></span>
                                </a>
                                <?php
                                $is_active_ocur = true;
                            }
                            if (in_array('checkboxes', $applyjob_quest_types)) {
                                ?>
                                <a href="javascript:void();" class="aplyquest-type-slectbtn<?php echo ($is_active_ocur === false ? ' active-type-itm' : '') ?>" data-type="checkboxes">
                                    <i class="jobsearch-icon jobsearch-check-square"></i><span><?php esc_html_e('Checkboxes', 'wp-jobsearch') ?></span>
                                </a>
                                <?php
                                $is_active_ocur = true;
                            }
                            if (in_array('number', $applyjob_quest_types)) {
                                ?>
                                <a href="javascript:void();" class="aplyquest-type-slectbtn<?php echo ($is_active_ocur === false ? ' active-type-itm' : '') ?>" data-type="number">
                                    <i class="jobsearch-icon jobsearch-newspaper"></i><span><?php esc_html_e('Number', 'wp-jobsearch') ?></span>
                                </a>
                                <?php
                                $is_active_ocur = true;
                            }
                            if (in_array('text', $applyjob_quest_types)) {
                                ?>
                                <a href="javascript:void();" class="aplyquest-type-slectbtn<?php echo ($is_active_ocur === false ? ' active-type-itm' : '') ?>" data-type="text">
                                    <i class="jobsearch-icon jobsearch-paper"></i><span><?php esc_html_e('Text', 'wp-jobsearch') ?></span>
                                </a>
                                <?php
                                $is_active_ocur = true;
                            }
                            if (in_array('textarea', $applyjob_quest_types)) {
                                ?>
                                <a href="javascript:void();" class="aplyquest-type-slectbtn<?php echo ($is_active_ocur === false ? ' active-type-itm' : '') ?>" data-type="textarea">
                                    <i class="jobsearch-icon jobsearch-credit-card"></i><span><?php esc_html_e('Textarea', 'wp-jobsearch') ?></span>
                                </a>
                                <?php
                                $is_active_ocur = true;
                            }
                            if (in_array('upload', $applyjob_quest_types)) {
                                ?>
                                <a href="javascript:void();" class="aplyquest-type-slectbtn<?php echo ($is_active_ocur === false ? ' active-type-itm' : '') ?>" data-type="upload">
                                    <i class="jobsearch-icon jobsearch-upload"></i><span><?php esc_html_e('Upload Field', 'wp-jobsearch') ?></span>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="addnew-aplyquestbtn-con">
                            <button class="add-new-applyquestbtn"><?php esc_html_e('Add new Question', 'wp-jobsearch') ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $footr_script_hook = 'wp_footer';
            if ($in_admin) {
                $footr_script_hook = 'admin_footer';
            }
            add_action($footr_script_hook, function() {
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        jQuery('.apply-question-selectize').selectize({plugins: ['remove_button']});
                        jQuery(".dash-applyquests-apendcon").sortable({
                            handle: '.applyquest-item-drag',
                            cursor: 'move',
                            items: '.applyquest-item-box',
                        });
                    });
                    jQuery(document).on('click', '.add-new-applyquestbtn', function (ev) {
                        ev.preventDefault();
                        var this_rand_id = Math.floor(Math.random() * 99999999) + 1;
                        var field_type = jQuery('.addnew-questtypes-btnscon').find('a.aplyquest-type-slectbtn.active-type-itm').attr('data-type');
                        
                        var optns_field = false;
                        if (field_type == 'checkboxes' || field_type == 'dropdown') {
                            optns_field = true;
                        }
                        
                        var field_type_title = '<i class="jobsearch-icon jobsearch-list"></i><span><?php echo esc_js(esc_html__('Dropdown', 'wp-jobsearch')) ?></span>';
                        if (field_type == 'checkboxes') {
                            field_type_title = '<i class="jobsearch-icon jobsearch-check-square"></i><span><?php echo esc_js(esc_html__('Checkboxes', 'wp-jobsearch')) ?></span>';
                        } else if (field_type == 'number') {
                            field_type_title = '<i class="jobsearch-icon jobsearch-newspaper"></i><span><?php echo esc_js(esc_html__('Number', 'wp-jobsearch')) ?></span>';
                        } else if (field_type == 'text') {
                            field_type_title = '<i class="jobsearch-icon jobsearch-paper"></i><span><?php echo esc_js(esc_html__('Text', 'wp-jobsearch')) ?></span>';
                        } else if (field_type == 'textarea') {
                            field_type_title = '<i class="jobsearch-icon jobsearch-credit-card"></i><span><?php echo esc_js(esc_html__('Textarea', 'wp-jobsearch')) ?></span>';
                        } else if (field_type == 'upload') {
                            field_type_title = '<i class="jobsearch-icon jobsearch-upload"></i><span><?php echo esc_js(esc_html__('Upload', 'wp-jobsearch')) ?></span>';
                        }

                        jQuery('.dash-applyquests-apendcon').append('<div class="applyquest-item-box">\
                            <div class="applyquest-item-type">\
                                <div class="quest-typecon-leftsec">\
                                    ' + field_type_title + '\
                                </div>\
                                <div class="quest-typecon-ritesec">\
                                    <div class="chekunchk-opt-box"' + (optns_field === false ? ' style="display: none;"' : '') + '>\
                                        <div class="chekunchk-opt-boxiner">\
                                            <input type="hidden" name="apply_job_questions[' + this_rand_id + '][multi_option]">\
                                            <input id="multi-opt-' + this_rand_id + '" type="checkbox" name="[' + this_rand_id + '][multi_option]" class="corect-opt-chkunchk">\
                                            <label for="multi-opt-' + this_rand_id + '">\
                                                <span class="chkunchk-onoffswitch-inner"></span>\
                                                <span class="chkunchk-onoffswitch-switch"></span>\
                                            </label>\
                                        </div>\
                                        <span class="chk-onoffswitch-title"><?php echo esc_js(esc_html__('Multi Select Answers', 'wp-jobsearch')) ?></span>\
                                    </div>\
                                    <div class="chekunchk-opt-box">\
                                        <div class="chekunchk-opt-boxiner">\
                                            <input type="hidden" name="apply_job_questions[' + this_rand_id + '][mandatory]">\
                                            <input id="mandatory-opt-' + this_rand_id + '" type="checkbox" name="[' + this_rand_id + '][mandatory]" class="corect-opt-chkunchk">\
                                            <label for="mandatory-opt-' + this_rand_id + '">\
                                                <span class="chkunchk-onoffswitch-inner"></span>\
                                                <span class="chkunchk-onoffswitch-switch"></span>\
                                            </label>\
                                        </div>\
                                        <span class="chk-onoffswitch-title"><?php echo esc_js(esc_html__('Mandatory', 'wp-jobsearch')) ?></span>\
                                    </div>\
                                    <div class="quest-sortremve-sec">\
                                        <a class="applyquest-item-drag"><i class="fa fa-arrows"></i></a>\
                                        <a href="javascript:void(0);" class="applyquest-item-remove"><i class="fa fa-times"></i></a>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="applyquest-answers-outer">\
                                <div class="applyquest-item-queststr">\
                                    <div class="quest-hding-optcomb">\
                                        <div class="quest-type-hding"><strong><?php echo esc_js(esc_html__('Question Title', 'wp-jobsearch')) ?></strong></div>\
                                        <div class="chekunchk-opt-box"' + (optns_field === false ? ' style="display: none;"' : '') + '>\
                                            <div class="chekunchk-opt-boxiner">\
                                                <input type="hidden" name="apply_job_questions[' + this_rand_id + '][require_correct]">\
                                                <input id="require-corect-' + this_rand_id + '" type="checkbox" name="[' + this_rand_id + '][require_correct]" class="corect-opt-chkunchk">\
                                                <label for="require-corect-' + this_rand_id + '">\
                                                    <span class="chkunchk-onoffswitch-inner"></span>\
                                                    <span class="chkunchk-onoffswitch-switch"></span>\
                                                </label>\
                                            </div>\
                                            <span class="chk-onoffswitch-title"><?php echo esc_js(esc_html__('Require correct answer on apply job', 'wp-jobsearch')) ?></span>\
                                        </div>\
                                    </div>\
                                    <input type="text" name="apply_job_questions[' + this_rand_id + '][question]" placeholder="<?php echo esc_js(esc_html__('Type your question here...', 'wp-jobsearch')) ?>">\
                                    <input type="hidden" name="apply_job_questions[' + this_rand_id + '][type]" value="' + field_type + '">\
                                </div>\
                                <div class="applyquest-options-main"' + (optns_field === false ? ' style="display: none;"' : '') + '>\
                                    <div class="applyquest-options-apendcon">\
                                        <div class="applyquest-option-itm">\
                                            <div class="applyquest-optionstr">\
                                                <input type="text" name="apply_job_questions[' + this_rand_id + '][options][]" placeholder="<?php echo esc_js(esc_html__('Type option text here...', 'wp-jobsearch')) ?>">\
                                            </div>\
                                            <div class="applyquest-opts-btnsec">\
                                                <div class="chekunchk-opt-box">\
                                                    <div class="chekunchk-opt-boxiner">\
                                                        <input type="hidden" name="apply_job_questions[' + this_rand_id + '][correct_option][]" value="on">\
                                                        <input id="correct-' + this_rand_id + '" type="checkbox" name="[' + this_rand_id + '][correct_option]" class="corect-opt-chkunchk" checked>\
                                                        <label for="correct-' + this_rand_id + '">\
                                                            <span class="chkunchk-onoffswitch-inner"></span>\
                                                            <span class="chkunchk-onoffswitch-switch"></span>\
                                                        </label>\
                                                    </div>\
                                                    <span class="chk-onoffswitch-title"><?php echo esc_js(esc_html__('Correct Answer', 'wp-jobsearch')) ?></span>\
                                                </div>\
                                                <div class="opts-plusminus-sec">\
                                                    <a href="javascript:void(0);" class="questopt-item-add add-new-applyoptionbtn" data-id="' + this_rand_id + '"><i class="fa fa-plus"></i></a>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>');
                        jQuery('.apply-question-selectizejs').selectize({plugins: ['remove_button']});
                        jQuery('.apply-question-selectize').removeClass('apply-question-selectizejs');
                        return false;
                    });
                    
                    jQuery(document).on('click', '.add-new-applyoptionbtn', function (ev) {
                        ev.preventDefault();
                        var _this = jQuery(this);
                        var this_rand_id = Math.floor(Math.random() * 99999999) + 1;
                        var this_main_id = _this.attr('data-id');
                        
                        var _appendr_con = _this.parents('.applyquest-option-itm');
                        _appendr_con.after('<div class="applyquest-option-itm">\
                            <div class="applyquest-optionstr">\
                                <input type="text" name="apply_job_questions[' + this_main_id + '][options][]" placeholder="<?php echo esc_js(esc_html__('Type option text here...', 'wp-jobsearch')) ?>">\
                            </div>\
                            <div class="applyquest-opts-btnsec">\
                                <div class="chekunchk-opt-box">\
                                    <div class="chekunchk-opt-boxiner">\
                                        <input type="hidden" name="apply_job_questions[' + this_main_id + '][correct_option][]" value="">\
                                        <input id="correct-' + this_main_id + '-' + this_rand_id + '" type="checkbox" name="[' + this_main_id + '][correct_option]" class="corect-opt-chkunchk">\
                                        <label for="correct-' + this_main_id + '-' + this_rand_id + '">\
                                            <span class="chkunchk-onoffswitch-inner"></span>\
                                            <span class="chkunchk-onoffswitch-switch"></span>\
                                        </label>\
                                    </div>\
                                    <span class="chk-onoffswitch-title"><?php echo esc_js(esc_html__('Correct Answer', 'wp-jobsearch')) ?></span>\
                                </div>\
                                <div class="opts-plusminus-sec">\
                                    <a href="javascript:void(0);" class="questopt-item-remove"><i class="fa fa-minus"></i></a>\
                                    <a href="javascript:void(0);" class="questopt-item-add add-new-applyoptionbtn" data-id="' + this_main_id + '"><i class="fa fa-plus"></i></a>\
                                </div>\
                            </div>\
                        </div>');
                        
                        return false;
                    });
                    
                    jQuery(document).on('change', '.corect-opt-chkunchk', function () {
                        var _this = jQuery(this);
                        var main_question_parent = _this.parents('.applyquest-item-box');
                        var this_quest_optionscon = main_question_parent.find('.applyquest-options-main');
                        var in_multi_opt = false;
                        if (_this.parents('.applyquest-item-type').length > 0) {
                            in_multi_opt = true;
                        } else {
                            if (!this_quest_optionscon.hasClass('multi-ansers')) {
                                var opt_checkbox_btn = this_quest_optionscon.find('.chekunchk-opt-box').find('input[type=checkbox]').not(this);
                                opt_checkbox_btn.prop('checked', false);
                                opt_checkbox_btn.attr('checked', false);
                                this_quest_optionscon.find('.chekunchk-opt-box').find('input[type=hidden]').val('');
                            }
                        }
                        if (_this.is(":checked")) {
                            _this.parents('.chekunchk-opt-box').find('input[type=hidden]').val('on');
                            if (in_multi_opt === true) {
                                this_quest_optionscon.addClass('multi-ansers');
                            }
                        } else {
                            _this.parents('.chekunchk-opt-box').find('input[type=hidden]').val('');
                            if (in_multi_opt === true) {
                                this_quest_optionscon.removeClass('multi-ansers');
                            }
                        }
                    });
                    
                    jQuery(document).on('click', '.applyquest-item-remove', function () {
                        var _this = jQuery(this);
                        _this.parents('.applyquest-item-box').remove();
                    });
                    jQuery(document).on('click', '.questopt-item-remove', function () {
                        var _this = jQuery(this);
                        _this.parents('.applyquest-option-itm').remove();
                    });
                    
                    jQuery(document).on('click', '.aplyquest-type-slectbtn', function () {
                        var _this = jQuery(this);
                        var parent_con = _this.parents('.addnew-questtypes-btnscon');
                        
                        parent_con.find('.aplyquest-type-slectbtn').removeClass('active-type-itm');
                        _this.addClass('active-type-itm');
                    });
                </script>
                <?php
            });
        }
    }
    
    public function admin_job_apply_job_questions($job_id) {
        $this->dash_job_apply_job_questions($job_id, true);
    }
    
    public function job_apply_simple_btn_popopen_flag($flag, $job_id) {
        global $jobsearch_plugin_options;
        //$rand_num = isset($form_args['rand_num']) ? $form_args['rand_num'] : '';
        //$job_id = isset($form_args['job_id']) ? $form_args['job_id'] : '';
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';

        if ($apply_job_questions == 'on') {
            
            $applyjob_quest_types = isset($jobsearch_plugin_options['apply_job_quest_types']) ? $jobsearch_plugin_options['apply_job_quest_types'] : '';
            
            $apply_job_quests = get_post_meta($job_id, 'apply_job_questions', true);
            if (!empty($apply_job_quests) && !empty($applyjob_quest_types)) {
                $flag = true;
            }
        }
        return $flag;
    }
    
    public function in_form_apply_job_exattrs($atts, $form_args) {
        global $jobsearch_plugin_options;
        $rand_num = isset($form_args['rand_num']) ? $form_args['rand_num'] : '';
        $job_id = isset($form_args['job_id']) ? $form_args['job_id'] : '';
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            
            $applyjob_quest_types = isset($jobsearch_plugin_options['apply_job_quest_types']) ? $jobsearch_plugin_options['apply_job_quest_types'] : '';
            
            $apply_job_quests = get_post_meta($job_id, 'apply_job_questions', true);
            if (!empty($apply_job_quests) && !empty($applyjob_quest_types)) {
                $atts .= ' style="display: none;"';
            }
        }
        return $atts;
    }
    
    public function in_form_apply_job_questions($form_args) {
        global $jobsearch_plugin_options;
        $rand_num = isset($form_args['rand_num']) ? $form_args['rand_num'] : '';
        $job_id = isset($form_args['job_id']) ? $form_args['job_id'] : '';
        $apply_type = isset($form_args['apply_type']) ? $form_args['apply_type'] : '';
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            
            $applyjob_quest_types = isset($jobsearch_plugin_options['apply_job_quest_types']) ? $jobsearch_plugin_options['apply_job_quest_types'] : '';
            
            $apply_job_quests = get_post_meta($job_id, 'apply_job_questions', true);
            if (!empty($apply_job_quests) && !empty($applyjob_quest_types)) {

                $allow_file_types = isset($jobsearch_plugin_options['apply_job_quest_filetypes']) ? $jobsearch_plugin_options['apply_job_quest_filetypes'] : '';

                if (empty($allow_file_types)) {
                    $allow_file_types = array(
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/pdf',
                    );
                }
                $allow_file_types_json = json_encode($allow_file_types);
                $quest_field_name = 'apply_job_quests';
                ?>
                <<?php echo ($apply_type == 'internal' ? 'form method="post"' : 'div') ?> class="jobsearch-user-form jobsearch-user-form-coltwo apply-job-questsform">
                    <script type="text/javascript">
                        jQuery(document).on('click', '.jobsearch-apply-jobquests-btn', function (e) {
                            e.preventDefault();
                            <?php
                            if ($apply_type == 'internal') {
                            ?>
                            var main_parent_con = jQuery(this).parents('.modal-box-area');
                            <?php
                            } else {
                            ?>
                            var msg_form = jQuery(this).parents('form');
                            <?php
                            }
                            ?>
                            var quests_con = jQuery(this).parents('.apply-job-questsform');

                            var quest_form_file = quests_con.find('input[type=file]');

                            var error = 0;

                            var form_req_fields = quests_con.find('.required-cussel-field,input[required]');
                            if (form_req_fields.length > 0) {
                                jQuery.each(form_req_fields, function() {
                                    var _this_obj = jQuery(this);
                                    if (typeof _this_obj.attr('name') !== 'undefined' && _this_obj.attr('name') != '' && _this_obj.attr('name') != 'undefined') {
                                        var field_type = 'text';
                                        if (_this_obj.parent('.jobsearch-profile-select').length > 0) {
                                            field_type = 'select';
                                        }
                                        //alert(_this_obj.attr('name'));
                                        //alert(field_type);
                                        if (_this_obj.attr('type') == 'checkbox' || _this_obj.attr('type') == 'radio') {
                                            var chek_field_name = _this_obj.attr('name');
                                            if ((jQuery('input[name="' + chek_field_name + '"]:checked').length) <= 0) {
                                                error = 1;
                                                _this_obj.parents('.jobsearch-cusfield-checkbox').css({"border": "1px solid #ff0000"});
                                            } else {
                                                _this_obj.parents('.jobsearch-cusfield-checkbox').css({"border": "none"});
                                            }
                                        } else {
                                            if (_this_obj.val() == '' || _this_obj.val() === null) {
                                                error = 1;
                                                if (field_type == 'select') {
                                                    _this_obj.parent('.jobsearch-profile-select').css({"border": "1px solid #ff0000"});
                                                } else {
                                                    _this_obj.css({"border": "1px solid #ff0000"});
                                                }
                                            } else {
                                                if (field_type == 'select') {
                                                    _this_obj.parent('.jobsearch-profile-select').css({"border": "none"});
                                                } else {
                                                    _this_obj.css({"border": "1px solid #efefef"});
                                                }
                                            }
                                        }
                                    }
                                });
                            }

                            if (quest_form_file.length != 0) {
                                var quest_form_file_parent = quest_form_file.parents('.jobsearch-applyjob-upload-quest');
                                if (quest_form_file.val() == '' && quest_form_file.hasClass('questfile_is_req')) {
                                    error = 1;
                                    quest_form_file_parent.css({"border": "1px solid #ff0000"});
                                } else {
                                    quest_form_file_parent.css({"border": "none"});
                                }

                                if (quest_form_file.val() != '') {
                                    quest_form_file = quest_form_file.prop('files')[0];
                                    var file_size = quest_form_file.size;
                                    var file_type = quest_form_file.type;

                                    var allowed_types = '<?php echo esc_js($allow_file_types_json) ?>';
                                    var filesize_allow = quest_form_file_parent.attr('data-key');
                                    filesize_allow = parseInt(filesize_allow);
                                    file_size = parseFloat(file_size / 1024).toFixed(2);
                                    if (file_size > filesize_allow) {
                                        alert('File size is too large.');
                                        error = 1;
                                        quest_form_file_parent.css({"border": "1px solid #ff0000"});
                                    } else {
                                        quest_form_file_parent.css({"border": "none"});
                                    }
                                    if (allowed_types.indexOf(file_type) < 0) {
                                        alert('File type not allowed.');
                                        error = 1;
                                        quest_form_file_parent.css({"border": "1px solid #ff0000"});
                                    } else {
                                        quest_form_file_parent.css({"border": "none"});
                                    }
                                }
                            }

                            if (error == 0) {
                                <?php
                                if ($apply_type == 'internal') {
                                ?>
                                main_parent_con.find('.jobsearch-applyjob-internalmain').removeAttr('style');
                                main_parent_con.find('.apply-job-questsform').hide();
                                <?php
                                } else {
                                ?>
                                msg_form.find('.jobsearch-user-form').removeAttr('style');
                                msg_form.find('.apply-job-questsform').hide();
                                <?php
                                }
                                ?>
                            }
                            return false;
                        });
                    </script>
                    <ul>
                        <?php
                        foreach ($apply_job_quests as $quest_key => $job_quest) {
                            $question_type = isset($job_quest['type']) ? $job_quest['type'] : '';
                            $mandatory_field = isset($job_quest['mandatory']) ? $job_quest['mandatory'] : '';
                            $multi_option = isset($job_quest['multi_option']) ? $job_quest['multi_option'] : '';
                            $question_str = isset($job_quest['question']) ? $job_quest['question'] : '';
                            $question_options = isset($job_quest['options']) ? $job_quest['options'] : '';
                            
                            if ($question_type == 'checkboxes') {
                                $checkbox_field_required_str = '';
                                if ($mandatory_field == 'on') {
                                    $checkbox_field_required_str = 'class="required-cussel-field"';
                                }
                                ?>
                                <li class="jobsearch-user-form-coltwo-full">
                                    <label><?php echo ($question_str) ?><?php echo ($mandatory_field == 'on' ? ' *' : '') ?></label>
                                    <div class="jobsearch-cusfield-checkbox applyjob-quests-checkbox">
                                        <?php
                                        if (!empty($question_options)) {
                                            $opts_counter = 0;
                                            foreach ($question_options as $quest_opts) {
                                                if ($multi_option == 'on') {
                                                    ?>
                                                    <div class="cusfield-checkbox-radioitm jobsearch-checkbox">
                                                        <input id="opt-<?php echo ($quest_key . '-' . $opts_counter) ?>" <?php echo ($checkbox_field_required_str) ?>
                                                               type="checkbox" name="<?php echo ($quest_field_name) ?>[<?php echo ($quest_key) ?>][]"
                                                               value="<?php echo ($quest_opts) ?>">
                                                        <label for="opt-<?php echo ($quest_key . '-' . $opts_counter) ?>">
                                                            <span></span> <?php echo ($quest_opts) ?>
                                                        </label>
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="cusfield-checkbox-radioitm jobsearch-checkbox">
                                                        <input id="opt-<?php echo ($quest_key . '-' . $opts_counter) ?>" <?php echo ($checkbox_field_required_str) ?>
                                                               type="radio" name="<?php echo ($quest_field_name) ?>[<?php echo ($quest_key) ?>]"
                                                               value="<?php echo ($quest_opts) ?>">
                                                        <label for="opt-<?php echo ($quest_key . '-' . $opts_counter) ?>">
                                                            <span></span> <?php echo ($quest_opts) ?>
                                                        </label>
                                                    </div>
                                                    <?php
                                                }
                                                $opts_counter++;
                                            }
                                        }
                                        ?>
                                    </div>
                                </li>
                                <?php
                            } else if ($question_type == 'text') {
                                $text_field_required_str = '';
                                if ($mandatory_field == 'on') {
                                    $text_field_required_str = 'class="required-cussel-field"';
                                }
                                ?>
                                <li>
                                    <label><?php echo ($question_str) ?><?php echo ($mandatory_field == 'on' ? ' *' : '') ?></label>
                                    <input type="text" name="<?php echo ($quest_field_name) ?>[<?php echo ($quest_key) ?>]" <?php echo ($text_field_required_str) ?>>
                                </li>
                                <?php
                            } else if ($question_type == 'number') {
                                $number_field_required_str = '';
                                if ($mandatory_field == 'on') {
                                    $number_field_required_str = 'class="required-cussel-field"';
                                }
                                ?>
                                <li>
                                    <label><?php echo ($question_str) ?><?php echo ($mandatory_field == 'on' ? ' *' : '') ?></label>
                                    <input type="number" name="<?php echo ($quest_field_name) ?>[<?php echo ($quest_key) ?>]" <?php echo ($number_field_required_str) ?>>
                                </li>
                                <?php
                            } else if ($question_type == 'textarea') {
                                $textarea_field_required_str = '';
                                if ($mandatory_field == 'on') {
                                    $textarea_field_required_str = 'class="required-cussel-field"';
                                }
                                ?>
                                <li class="form-textarea jobsearch-user-form-coltwo-full">
                                    <label><?php echo ($question_str) ?><?php echo ($mandatory_field == 'on' ? ' *' : '') ?></label>
                                    <textarea name="<?php echo ($quest_field_name) ?>[<?php echo ($quest_key) ?>]" <?php echo ($textarea_field_required_str) ?>></textarea>
                                </li>
                                <?php
                            } else if ($question_type == 'upload') {
                                $file_sizes_arr = array(
                                    '300' => __('300KB', 'wp-jobsearch'),
                                    '500' => __('500KB', 'wp-jobsearch'),
                                    '750' => __('750KB', 'wp-jobsearch'),
                                    '1024' => __('1Mb', 'wp-jobsearch'),
                                    '2048' => __('2Mb', 'wp-jobsearch'),
                                    '3072' => __('3Mb', 'wp-jobsearch'),
                                    '4096' => __('4Mb', 'wp-jobsearch'),
                                    '5120' => __('5Mb', 'wp-jobsearch'),
                                    '10120' => __('10Mb', 'wp-jobsearch'),
                                    '50120' => __('50Mb', 'wp-jobsearch'),
                                    '100120' => __('100Mb', 'wp-jobsearch'),
                                    '200120' => __('200Mb', 'wp-jobsearch'),
                                    '300120' => __('300Mb', 'wp-jobsearch'),
                                    '500120' => __('500Mb', 'wp-jobsearch'),
                                    '1000120' => __('1Gb', 'wp-jobsearch'),
                                );
                                $questfile_size = '5120';
                                $questfile_size_str = __('5 Mb', 'wp-jobsearch');
                                $cand_quest_file_size = isset($jobsearch_plugin_options['apply_job_quest_filesize']) ? $jobsearch_plugin_options['apply_job_quest_filesize'] : '';
                                if (isset($file_sizes_arr[$cand_quest_file_size])) {
                                    $questfile_size = $cand_quest_file_size;
                                    $questfile_size_str = $file_sizes_arr[$cand_quest_file_size];
                                }

                                $sutable_files_arr = array();
                                $file_typs_comarr = array(
                                    'text/plain' => __('text', 'wp-jobsearch'),
                                    'image/jpeg' => __('jpeg', 'wp-jobsearch'),
                                    'image/png' => __('png', 'wp-jobsearch'),
                                    'application/msword' => __('doc', 'wp-jobsearch'),
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __('docx', 'wp-jobsearch'),
                                    'application/vnd.ms-excel' => __('xls', 'wp-jobsearch'),
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => __('xlsx', 'wp-jobsearch'),
                                    'application/pdf' => __('pdf', 'wp-jobsearch'),
                                );
                                foreach ($file_typs_comarr as $file_typ_key => $file_typ_comar) {
                                    if (in_array($file_typ_key, $allow_file_types)) {
                                        $sutable_files_arr[] = '.' . $file_typ_comar;
                                    }
                                }
                                $sutable_files_str = implode(', ', $sutable_files_arr);
                                ?>
                                <li class="jobsearch-user-form-coltwo-full">
                                    <label><?php echo ($question_str) ?><?php echo ($mandatory_field == 'on' ? ' *' : '') ?></label>
                                    <div id="jobsearch-upload-quest-main-<?php echo ($quest_key) ?>" data-key="<?php echo ($questfile_size) ?>" class="jobsearch-upload-cv jobsearch-applyjob-upload-quest">
                                        <div class="jobsearch-drpzon-con jobsearch-drag-dropcustom">
                                            <div id="questFilesDropzone-<?php echo ($quest_key) ?>" class="dropzone"
                                                 ondragover="jobsearch_dragover_evnt<?php echo ($quest_key) ?>(event)"
                                                 ondragleave="jobsearch_leavedrop_evnt<?php echo ($quest_key) ?>(event)"
                                                 ondrop="jobsearch_ondrop_evnt<?php echo ($quest_key) ?>(event)">
                                                <input type="file" id="cand_quest_filefield-<?php echo ($quest_key) ?>"
                                                       class="jobsearch-upload-btn <?php echo ($mandatory_field == 'on' ? 'questfile_is_req' : '') ?>"
                                                       name="<?php echo ($quest_field_name) ?>[<?php echo ($quest_key) ?>]"
                                                       onchange="jobsearchFileContainerChangeFile<?php echo ($quest_key) ?>(event)">
                                                <div class="fileContainerFileName" ondrop="jobsearch_ondrop_evnt<?php echo ($quest_key) ?>(event)" id="fileNameContainer-<?php echo ($quest_key) ?>">
                                                    <div class="dz-message jobsearch-dropzone-template">
                                                        <span class="upload-icon-con"><i class="jobsearch-icon jobsearch-upload"></i></span>
                                                        <strong><?php esc_html_e('Drop a file or click to upload.', 'wp-jobsearch') ?></strong>
                                                        <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $questfile_size_str, $sutable_files_str) ?></div>
                                                        <div class="upload-or-con">
                                                            <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a class="jobsearch-drpzon-btn"><i class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload File', 'wp-jobsearch') ?></a>
                                            </div>
                                            <script type="text/javascript">
                                                jQuery('#questFilesDropzone-<?php echo ($quest_key) ?>').find('input[type=file]').css({
                                                    position: 'absolute',
                                                    width: '100%',
                                                    height: '100%',
                                                    top: '0',
                                                    left: '0',
                                                    opacity: '0',
                                                    'z-index': '9',
                                                });

                                                function jobsearchFileContainerChangeFile<?php echo ($quest_key) ?>(e) {
                                                    var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                    if (e.target.files.length > 0) {
                                                        var slected_file_name = e.target.files[0].name;
                                                        the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                    }
                                                    document.getElementById('questFilesDropzone-<?php echo ($quest_key) ?>').classList.remove('fileContainerDragOver');
                                                    try {
                                                        droppedFiles = document.getElementById('cand_quest_filefield-<?php echo ($quest_key) ?>').files;
                                                        document.getElementById('fileNameContainer-<?php echo ($quest_key) ?>').textContent = the_show_msg;
                                                    } catch (error) {
                                                        ;
                                                    }
                                                    try {
                                                        aName = document.getElementById('cand_quest_filefield-<?php echo ($quest_key) ?>').value;
                                                        if (aName !== '') {
                                                            document.getElementById('fileNameContainer-<?php echo ($quest_key) ?>').textContent = the_show_msg;
                                                        }
                                                    } catch (error) {
                                                        ;
                                                    }
                                                }

                                                function jobsearch_ondrop_evnt<?php echo ($quest_key) ?>(e) {
                                                    var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                    if (e.target.files.length > 0) {
                                                        var slected_file_name = e.target.files[0].name;
                                                        the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                    }
                                                    document.getElementById('questFilesDropzone-<?php echo ($quest_key) ?>').classList.remove('fileContainerDragOver');
                                                    try {
                                                        droppedFiles = e.dataTransfer.files;
                                                        document.getElementById('fileNameContainer-<?php echo ($quest_key) ?>').textContent = the_show_msg;
                                                    } catch (error) {
                                                        ;
                                                    }
                                                }

                                                function jobsearch_dragover_evnt<?php echo ($quest_key) ?>(e) {
                                                    document.getElementById('questFilesDropzone-<?php echo ($quest_key) ?>').classList.add('fileContainerDragOver');
                                                    e.preventDefault();
                                                    e.stopPropagation();
                                                }

                                                function jobsearch_leavedrop_evnt<?php echo ($quest_key) ?>(e) {
                                                    document.getElementById('questFilesDropzone-<?php echo ($quest_key) ?>').classList.remove('fileContainerDragOver');
                                                }
                                            </script>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            } else {
                                $dropdown_field_required_str = '';
                                if ($mandatory_field == 'on') {
                                    $dropdown_field_required_str = 'required-cussel-field';
                                }
                                $dropdown_field_options_str = '<option value="">' . esc_html__('Select Option', 'wp-jobsearch') . '</option>';
                                if (!empty($question_options)) {
                                    $opts_counter = 0;
                                    foreach ($question_options as $quest_opts) {
                                        $dropdown_field_options_str .= '<option value="' . $quest_opts . '">' . $quest_opts . '</option>';
                                        $opts_counter++;
                                    }
                                }
                                ?>
                                <li>
                                    <label><?php echo ($question_str) ?><?php echo ($mandatory_field == 'on' ? ' *' : '') ?></label>
                                    <div class="jobsearch-profile-select">
                                        <select <?php echo ($multi_option == 'on' ? 'multiple="multiple" ' : '') ?>name="<?php echo ($quest_field_name) ?>[<?php echo ($quest_key) ?>]<?php echo ($multi_option == 'on' ? '[]' : '') ?>" placeholder="<?php esc_html_e('Select Option', 'wp-jobsearch') ?>"
                                            class="selectize-select <?php echo ($dropdown_field_required_str) ?>">
                                            <?php
                                            echo force_balance_tags($dropdown_field_options_str);
                                            ?>
                                        </select>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                        ?>
                        <li class="jobsearch-user-form-coltwo-full">
                            <input class="jobsearch-apply-jobquests-btn" data-id="<?php echo ($rand_num) ?>" type="submit" value="<?php esc_html_e('Next', 'wp-jobsearch') ?>">
                        </li>
                    </ul>
                </<?php echo ($apply_type == 'internal' ? 'form' : 'div') ?>>
                <?php
            }
        }
    }
    
    public function insert_applyjob_quest_file($upload_file) {
        
        global $jobsearch_uploding_candimg, $jobsearch_download_locations;
        $jobsearch_download_locations = false;
        $jobsearch_uploding_candimg = true;
        //
        if (isset($upload_file['tmp_name']) && $upload_file['tmp_name'] != '') {

            $jobsearch__options = get_option('jobsearch_plugin_options');
            
            add_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);

            // Get the path to the upload directory.
            $wp_upload_dir = wp_upload_dir();

            $test_uploaded_file = is_uploaded_file($upload_file['tmp_name']);

            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $allowed_file_types_list = isset($jobsearch__options['apply_job_quest_filetypes']) ? $jobsearch__options['apply_job_quest_filetypes'] : '';
            if (empty($allowed_file_types_list)) {
                $allowed_file_types = array(
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'pdf' => 'application/pdf',
                );
            } else {
                $allowed_file_types = array();
                if (in_array('image/jpeg', $allowed_file_types_list)) {
                    $allowed_file_types['jpg|jpeg|jpe'] = 'image/jpeg';
                    $allowed_file_types['png'] = 'image/png';
                }
                if (in_array('image/png', $allowed_file_types_list)) {
                    $allowed_file_types['jpg|jpeg|jpe'] = 'image/jpeg';
                    $allowed_file_types['png'] = 'image/png';
                }
                if (in_array('text/plain', $allowed_file_types_list)) {
                    $allowed_file_types['txt|asc|c|cc|h'] = 'text/plain';
                }
                if (in_array('application/msword', $allowed_file_types_list)) {
                    $allowed_file_types['doc'] = 'application/msword';
                }
                if (in_array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $allowed_file_types_list)) {
                    $allowed_file_types['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                }
                if (in_array('application/pdf', $allowed_file_types_list)) {
                    $allowed_file_types['pdf'] = 'application/pdf';
                }
                if (in_array('application/vnd.ms-excel', $allowed_file_types_list)) {
                    $allowed_file_types['xla|xls|xlt|xlw'] = 'application/vnd.ms-excel';
                }
                if (in_array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $allowed_file_types_list)) {
                    $allowed_file_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                }
            }

            $status_upload = wp_handle_upload($upload_file, array('test_form' => false, 'mimes' => $allowed_file_types));
            
            if (empty($status_upload['error'])) {

                $file_url = isset($status_upload['url']) ? $status_upload['url'] : '';
                $upload_file_path = $wp_upload_dir['path'] . '/' . basename($file_url);

                $folder_path = $wp_upload_dir['path'];

                $file_name = basename($file_url);

                $file_uniqid = jobsearch_get_unique_folder_byurl($file_url);

                $filetype = wp_check_filetype($file_name, null);
                $fileuplod_time = current_time('timestamp');

                $file_cred = array(
                    'name' => $file_name,
                    'folder_path' => $folder_path,
                    'file_path' => $upload_file_path,
                    'url' => $file_url,
                    'time' => $fileuplod_time,
                    'type' => $filetype,
                    'id' => $file_uniqid,
                );

                return $file_cred;
            }

            remove_filter('jobsearch_candimg_upload_dir', 'jobsearch_upload_candimg_path', 10, 1);
        }

        return false;
    }
    
    public function job_applying_save($candidate_id, $job_id) {
        global $jobsearch_plugin_options;
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            
            $applyjob_quest_types = isset($jobsearch_plugin_options['apply_job_quest_types']) ? $jobsearch_plugin_options['apply_job_quest_types'] : '';
            
            $apply_job_quests = get_post_meta($job_id, 'apply_job_questions', true);
            if (!empty($apply_job_quests)) {
                $apply_job_filled_quests = get_post_meta($job_id, 'apply_job_filled_questions', true);
                $apply_job_filled_quests = empty($apply_job_filled_quests) ? array() : $apply_job_filled_quests;
                $answers_arr = array();
                foreach ($apply_job_quests as $quest_key => $job_quest) {
                    $question_type = isset($job_quest['type']) ? $job_quest['type'] : '';
                    $mandatory_field = isset($job_quest['mandatory']) ? $job_quest['mandatory'] : '';
                    $multi_option = isset($job_quest['multi_option']) ? $job_quest['multi_option'] : '';
                    $question_str = isset($job_quest['question']) ? $job_quest['question'] : '';
                    $question_options = isset($job_quest['options']) ? $job_quest['options'] : '';
                    $correct_options = isset($job_quest['correct_option']) ? $job_quest['correct_option'] : '';
                    
                    if ($question_type != 'upload' && isset($_POST['apply_job_quests'][$quest_key])) {
                        $anser_val = $_POST['apply_job_quests'][$quest_key];
                        $answers_arr[$quest_key] = $job_quest;
                        $answers_arr[$quest_key]['answer'] = $anser_val;
                    } else if ($question_type == 'upload' && isset($_FILES['apply_job_quests'])) {
                        $anser_file = '';
                        if (isset($_FILES['apply_job_quests']['name'][$quest_key])) {
                            $anser_file = array();
                            $anser_file['name'] = $_FILES['apply_job_quests']['name'][$quest_key];
                            $anser_file['type'] = $_FILES['apply_job_quests']['type'][$quest_key];
                            $anser_file['tmp_name'] = $_FILES['apply_job_quests']['tmp_name'][$quest_key];
                            $anser_file['error'] = $_FILES['apply_job_quests']['error'][$quest_key];
                            $anser_file['size'] = $_FILES['apply_job_quests']['size'][$quest_key];
                        }
                        if (!empty($anser_file)) {
                            $anser_file = $this->insert_applyjob_quest_file($anser_file);
                        }
                        $answers_arr[$quest_key] = $job_quest;
                        $answers_arr[$quest_key]['answer'] = $anser_file;
                    }
                }
                
                $apply_job_filled_quests[$candidate_id] = $answers_arr;
                update_post_meta($job_id, 'apply_job_filled_questions', $apply_job_filled_quests);
            }
        }
    }
    
    public function answers_data_html_in_popup($apply_job_filled_quests, $job_id) {
        if (!empty($apply_job_filled_quests)) {
            ?>
            <div class="jobsearch-applyquesthtml-main">
                <div class="applyjob-questsall-items">
                    <?php
                    foreach ($apply_job_filled_quests as $quest_key => $quest_arr) {

                        $question_str = isset($quest_arr['question']) ? $quest_arr['question'] : '';
                        $question_type = isset($quest_arr['type']) ? $quest_arr['type'] : '';
                        $quest_answer = isset($quest_arr['answer']) ? $quest_arr['answer'] : '';

                        if ($question_type == 'upload') {
                            ?>
                            <div class="applyjob-quests-item">
                                <div class="quests-item-title">
                                    <strong><?php echo jobsearch_esc_html($question_str) ?></strong>
                                    <div class="title-icon-con"><i class="fa fa-bars"></i></div>
                                </div>
                                <div class="quests-item-answer">
                                    <?php
                                    if (isset($quest_answer['file_path']) && file_exists($quest_answer['file_path'])) {
                                        $file_name = $quest_answer['name'];
                                        $file_id = $quest_answer['id'];
                                        $file_url = apply_filters('wp_jobsearch_applyjob_quset_file_downlod_url', '', $job_id, $file_id, $file_name);
                                        ?>
                                        <p><a href="<?php echo ($file_url) ?>" oncontextmenu="javascript: return false;" onclick="javascript: if ((event.button == 0 &amp;&amp; event.ctrlKey)) {return false};" download="<?php echo ($file_name) ?>"><?php esc_html_e('Download File', 'wp-jobsearch') ?></a></p>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        } else {
                            if (is_array($quest_answer)) {
                                //
                            } else {
                                $quest_answer = jobsearch_esc_html($quest_answer);
                            }
                            $opts_quest = false;
                            if ($question_type == 'dropdown' || $question_type == 'checkboxes') {
                                $opts_quest = true;
                                $multi_option = isset($quest_arr['multi_option']) ? $quest_arr['multi_option'] : '';
                                $question_options = isset($quest_arr['options']) ? $quest_arr['options'] : '';
                                $correct_options = isset($quest_arr['correct_option']) ? $quest_arr['correct_option'] : '';
                            }
                            ?>
                            <div class="applyjob-quests-item">
                                <div class="quests-item-title">
                                    <strong><?php echo jobsearch_esc_html($question_str) ?></strong>
                                    <div class="title-icon-con"><i class="fa fa-bars"></i></div>
                                </div>
                                <div class="quests-item-answer">
                                    <p>
                                        <?php
                                        if ($opts_quest) {
                                            $opts_counter = 0;
                                            foreach ($question_options as $quest_opt_str) {
                                                $corect_opt = isset($correct_options[$opts_counter]) ? $correct_options[$opts_counter] : '';
                                                $opt_correct_clas = '';
                                                if ($multi_option == 'on') {
                                                    if (is_array($quest_answer) && in_array($quest_opt_str, $quest_answer)) {
                                                        if ($corect_opt == 'on') {
                                                            $opt_correct_clas = ' correct-opt-selected';
                                                        } else {
                                                            $opt_correct_clas = ' wrong-opt-selected';
                                                        }
                                                    }
                                                } else {
                                                    if ($quest_answer == $quest_opt_str) {
                                                        if ($corect_opt == 'on') {
                                                            $opt_correct_clas = ' correct-opt-selected';
                                                        } else {
                                                            $opt_correct_clas = ' wrong-opt-selected';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="answer-posible-option<?php echo ($opt_correct_clas) ?>"><?php echo ($quest_opt_str) ?></div>
                                                <?php
                                                $opts_counter++;
                                            }
                                        } else {
                                            echo ($quest_answer);
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <?php
        }
    }
    
    public function in_dash_simp_apply_job_quests_show($html, $candidate_id, $job_id) {
        global $jobsearch_plugin_options;
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            $applyjob_all_filled_quests = get_post_meta($job_id, 'apply_job_filled_questions', true);
            
            if (isset($applyjob_all_filled_quests[$candidate_id]) && !empty($applyjob_all_filled_quests[$candidate_id])) {
                $apply_job_filled_quests = $applyjob_all_filled_quests[$candidate_id];
                ob_start();
                ?>
                <ul class="quest-answers-acts">
                    <li>
                        <a href="javascript:void(0);" class="show-applyjob-questans-<?php echo ($candidate_id) ?>" data-id="<?php echo ($candidate_id) ?>"><?php esc_html_e('Questions/Answers', 'wp-jobsearch') ?></a>
                        <div id="cand-ansdata-<?php echo ($candidate_id) ?>" style="position: absolute; display: none;">
                            <?php $this->answers_data_html_in_popup($apply_job_filled_quests, $job_id); ?>
                        </div>
                    </li>
                </ul>
                <?php
                $html = ob_get_clean();
                $pop_args = array('job_id' => $job_id, 'candidate_id' => $candidate_id, 'apply_job_filled_quests' => $apply_job_filled_quests);
                add_action('wp_footer', function() use ($pop_args) {
                    extract(shortcode_atts(array(
                    'job_id' => '',
                    'candidate_id' => '',
                    'apply_job_filled_quests' => '',
                                ), $pop_args));
                    
                    ?>
                    <script type="text/javascript">
                        jQuery(document).on('click', '.show-applyjob-questans-<?php echo ($candidate_id) ?>', function () {
                            var _this_id = jQuery(this).attr('data-id');
                            var data_html = jQuery('#cand-ansdata-' + _this_id).html();
                            jQuery('#JobSearchModalApplyJobQuests').find('.jobsearch-applyjobans-con').html(data_html);
                            jobsearch_modal_popup_open('JobSearchModalApplyJobQuests');
                        });
                    </script>
                    <?php
                });
            }
        }
        
        return $html;
    }
    
    public function in_dash_apply_job_quests_show($html, $candidate_id, $job_id) {
        global $jobsearch_plugin_options;
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            $applyjob_all_filled_quests = get_post_meta($job_id, 'apply_job_filled_questions', true);
            
            if (isset($applyjob_all_filled_quests[$candidate_id]) && !empty($applyjob_all_filled_quests[$candidate_id])) {
                $apply_job_filled_quests = $applyjob_all_filled_quests[$candidate_id];
                ob_start();
                ?>
                <li>
                    <a href="javascript:void(0);" class="show-applyjob-questans-<?php echo ($candidate_id) ?>" data-id="<?php echo ($candidate_id) ?>"><?php esc_html_e('Questions/Answers', 'wp-jobsearch') ?></a>
                    <div id="cand-ansdata-<?php echo ($candidate_id) ?>" style="position: absolute; display: none;">
                        <?php $this->answers_data_html_in_popup($apply_job_filled_quests, $job_id); ?>
                    </div>
                </li>
                <?php
                $html = ob_get_clean();
                $pop_args = array('job_id' => $job_id, 'candidate_id' => $candidate_id, 'apply_job_filled_quests' => $apply_job_filled_quests);
                add_action('wp_footer', function() use ($pop_args) {
                    extract(shortcode_atts(array(
                    'job_id' => '',
                    'candidate_id' => '',
                    'apply_job_filled_quests' => '',
                                ), $pop_args));
                    
                    ?>
                    <script type="text/javascript">
                        jQuery(document).on('click', '.show-applyjob-questans-<?php echo ($candidate_id) ?>', function () {
                            var _this_id = jQuery(this).attr('data-id');
                            var data_html = jQuery('#cand-ansdata-' + _this_id).html();
                            jQuery('#JobSearchModalApplyJobQuests').find('.jobsearch-applyjobans-con').html(data_html);
                            jobsearch_modal_popup_open('JobSearchModalApplyJobQuests');
                        });
                    </script>
                    <?php
                });
            }
        }
        
        return $html;
    }
    
    public function in_bkend_apply_job_quests_show($html, $candidate_id, $job_id) {
        global $jobsearch_plugin_options;
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            $applyjob_all_filled_quests = get_post_meta($job_id, 'apply_job_filled_questions', true);
            
            if (isset($applyjob_all_filled_quests[$candidate_id]) && !empty($applyjob_all_filled_quests[$candidate_id])) {
                $apply_job_filled_quests = $applyjob_all_filled_quests[$candidate_id];
                ob_start();
                ?>
                <li>
                    <a href="javascript:void(0);" class="show-applyjob-questsbtn button button-primary show-applyjob-questans-<?php echo ($candidate_id) ?>" data-id="<?php echo ($candidate_id) ?>"><?php esc_html_e('View Answers', 'wp-jobsearch') ?></a>
                    <div id="cand-ansdata-<?php echo ($candidate_id) ?>" style="position: absolute; display: none;">
                        <?php $this->answers_data_html_in_popup($apply_job_filled_quests, $job_id); ?>
                    </div>
                </li>
                <?php
                $html = ob_get_clean();
                $pop_args = array('job_id' => $job_id, 'candidate_id' => $candidate_id, 'apply_job_filled_quests' => $apply_job_filled_quests);
                add_action('admin_footer', function() use ($pop_args) {
                    extract(shortcode_atts(array(
                    'job_id' => '',
                    'candidate_id' => '',
                    'apply_job_filled_quests' => '',
                                ), $pop_args));
                    
                    ?>
                    <script type="text/javascript">
                        jQuery(document).on('click', '.show-applyjob-questans-<?php echo ($candidate_id) ?>', function () {
                            var _this_id = jQuery(this).attr('data-id');
                            var data_html = jQuery('#cand-ansdata-' + _this_id).html();
                            jQuery('#JobSearchModalApplyJobQuests').find('.jobsearch-applyjobans-con').html(data_html);
                            jobsearch_modal_popup_open('JobSearchModalApplyJobQuests');
                        });
                    </script>
                    <?php
                });
            }
        }
        
        return $html;
    }
    
    public function show_questanswer_popup_common() {
        global $jobsearch_plugin_options, $pagenow;
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        $show_pophtml = true;

        if (is_admin()) {
            if (isset($_GET['page']) && ($_GET['page'] == 'jobsearch-applicants-list' || $_GET['page'] == 'jobsearch-emailapps-list')) {
                $show_pophtml = true;
            } else if ($pagenow == 'post.php') {
                $show_pophtml = true;
            } else {
                $show_pophtml = false;
            }
        }
        
        if ($apply_job_questions == 'on' && $show_pophtml) {
            ?>
            <div class="jobsearch-modal jobsearch-applyjobans-popup fade" id="JobSearchModalApplyJobQuests">
                <div class="modal-inner-area">&nbsp;</div>
                <div class="modal-content-area">
                    <div class="modal-box-area">
                        <div class="jobsearch-modal-title-box">
                            <h2><?php esc_html_e('Answers', 'wp-jobsearch') ?></h2>
                            <span class="modal-close"><i class="fa fa-times"></i></span>
                        </div>
                        <div class="jobsearch-applyjobans-con"></div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                if (jQuery('.quests-item-title').length > 0) {
                    jQuery(document).on('click', '.quests-item-title', function () {
                        var _this = jQuery(this);
                        var main_parent_con = _this.parents('.applyjob-questsall-items');
                        main_parent_con.find('.applyjob-quests-item').removeClass('quest-item-isopen');
                        main_parent_con.find('.applyjob-quests-item').find('.quests-item-answer').slideUp();
                        main_parent_con.find('.applyjob-quests-item').find('.title-icon-con i').attr('class', 'fa fa-bars');
                        
                        var this_parent_con = _this.parent('.applyjob-quests-item');
                        var answer_con = this_parent_con.find('.quests-item-answer');

                        if (this_parent_con.hasClass('quest-item-isopen')) {
                            this_parent_con.removeClass('quest-item-isopen');
                            answer_con.slideUp();
                            this_parent_con.find('.title-icon-con i').attr('class', 'fa fa-bars');
                        } else {
                            this_parent_con.addClass('quest-item-isopen');
                            answer_con.slideDown();
                            this_parent_con.find('.title-icon-con i').attr('class', 'jobsearch-icon jobsearch-down-arrow');
                        }
                    });
                }
            </script>
            <?php
        }
    }

    public function upload_file_downlod_url($url, $job_id = '', $file_id = '', $file_name = '') {

        $url = add_query_arg(array('action' => 'wp_jobsearch_get_aplyjob_quest_file', 'id' => $job_id, 'file_id' => $file_id, 'file_name' => $file_name), admin_url('admin-ajax.php'));

        return $url;
    }

    public function upload_file_downlod_action() {

        $job_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        $file_id = isset($_REQUEST['file_id']) ? $_REQUEST['file_id'] : '';
        $file_name = isset($_REQUEST['file_name']) ? $_REQUEST['file_name'] : '';

        $error_page_url = home_url('/404_error');

        if (is_numeric($job_id) && get_post_type($job_id) == 'job') {
            $file_creds = '';
            $applyjob_all_filled_quests = get_post_meta($job_id, 'apply_job_filled_questions', true);
            if (!empty($applyjob_all_filled_quests)) {
                foreach ($applyjob_all_filled_quests as $all_answers) {
                    if (!empty($all_answers)) {
                        foreach ($all_answers as $answers_arr) {
                            if (isset($answers_arr['type']) && $answers_arr['type'] == 'upload' && isset($answers_arr['answer']['id']) && $answers_arr['answer']['id'] == $file_id && $answers_arr['answer']['name'] == $file_name) {
                                $file_creds = $answers_arr['answer'];
                                break;
                            }
                        }
                    }
                }
            }

            if (!empty($file_creds) && isset($file_creds['file_path'])) {

                $file_path = $file_creds['file_path'];

                if (!$file_path || !file_exists($file_path)) {
                    wp_redirect($error_page_url);
                } else {

                    $file_mimetype = $file_creds['type']['type'];
                    //
                    header('Content-Description: File Transfer');
                    header('Content-Type: ' . $file_mimetype);
                    header('Content-Dispositon: attachment; filename="' . basename($file_path) . '"');
                    header('Content-Transfer-Encoding: Binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . @filesize($file_path));

                    ob_clean();
                    flush();
                    @readfile($file_path);
                    exit;
                }
            }
        } else {
            wp_redirect($error_page_url);
        }

        die;
    }

    public function jobaply_email_answers_html($apply_job_filled_quests, $job_id) {
        if (!empty($apply_job_filled_quests)) {
            ob_start();
            ?>
            <table class="blueTable">
                <thead>
                    <tr>
                        <th style="text-align: center;" colspan="2"><?php esc_html_e('Apply Job Answers', 'wp-jobsearch') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($apply_job_filled_quests as $quest_key => $quest_arr) {

                        $question_str = isset($quest_arr['question']) ? $quest_arr['question'] : '';
                        $question_type = isset($quest_arr['type']) ? $quest_arr['type'] : '';
                        $quest_answer = isset($quest_arr['answer']) ? $quest_arr['answer'] : '';

                        if ($question_type == 'upload') {
                            ?>
                            <tr>
                                <td><?php printf(esc_html__('Question: %s', 'wp-jobsearch'), jobsearch_esc_html($question_str)) ?></td>
                                <?php
                                if (isset($quest_answer['file_path']) && file_exists($quest_answer['file_path'])) {
                                    $file_name = $quest_answer['name'];
                                    $file_id = $quest_answer['id'];
                                    $file_url = apply_filters('wp_jobsearch_applyjob_quset_file_downlod_url', '', $job_id, $file_id, $file_name);
                                    ?>
                                    <td><a href="<?php echo ($file_url) ?>" download="<?php echo ($file_name) ?>"><?php esc_html_e('Download File', 'wp-jobsearch') ?></a></td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <?php
                        } else {
                            if (is_array($quest_answer)) {
                                $quest_answer = implode(', ', $quest_answer);
                            } else {
                                $quest_answer = jobsearch_esc_html($quest_answer);
                            }
                            ?>
                            <tr>
                                <td><?php printf(esc_html__('Question: %s', 'wp-jobsearch'), jobsearch_esc_html($question_str)) ?></td>
                                <td><?php printf(esc_html__('Answer: %s', 'wp-jobsearch'), $quest_answer) ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
            <?php
            $html = ob_get_clean();
            
            return $html;
        }
    }

    public function jobaply_by_cand_toemp_codes($codes, $email_class) {
        global $jobsearch_plugin_options;
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            $this->toemp_email_class = $email_class;

            $new_codes = array(
                array(
                    'var' => '{apply_answers}',
                    'display_text' => esc_html__('Apply Job Answers', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_apply_ans_toemp'),
                ),
            );
            array_splice($codes, 2, 0, $new_codes);
        }
        return $codes;
    }

    public function jobaply_by_cand_tocand_codes($codes, $email_class) {
        global $jobsearch_plugin_options;
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            $this->tocand_email_class = $email_class;

            $new_codes = array(
                array(
                    'var' => '{apply_answers}',
                    'display_text' => esc_html__('Apply Job Answers', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_apply_ans_tocand'),
                ),
            );
            array_splice($codes, 2, 0, $new_codes);
        }
        return $codes;
    }
    
    public function get_job_apply_ans_tocand() {
        
        $email_class = $this->tocand_email_class;
        $job_id = $email_class->job_id;
        
        $user_id = $email_class->user->ID;
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                
        $applyjob_all_filled_quests = get_post_meta($job_id, 'apply_job_filled_questions', true);
        $apply_job_filled_quests = isset($applyjob_all_filled_quests[$candidate_id]) ? $applyjob_all_filled_quests[$candidate_id] : '';
        
        $answers_html = $this->jobaply_email_answers_html($apply_job_filled_quests, $job_id);
        
        if ($answers_html != '') {
            return $answers_html;
        }
        
        return '-';
    }
    
    public function get_job_apply_ans_toemp() {
        
        $email_class = $this->toemp_email_class;
        $job_id = $email_class->job_id;
        
        $user_id = $email_class->user->ID;
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                
        $applyjob_all_filled_quests = get_post_meta($job_id, 'apply_job_filled_questions', true);
        $apply_job_filled_quests = isset($applyjob_all_filled_quests[$candidate_id]) ? $applyjob_all_filled_quests[$candidate_id] : '';
        
        $answers_html = $this->jobaply_email_answers_html($apply_job_filled_quests, $job_id);
        
        if ($answers_html != '') {
            return $answers_html;
        }
        
        return '-';
    }
    
    public function jobaply_by_email_temp_codes($codes, $email_class) {
        global $jobsearch_plugin_options;
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            $this->toemp_byemail_class = $email_class;
            $new_codes = array(
                array(
                    'var' => '{apply_answers}',
                    'display_text' => esc_html__('Apply Job Answers', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_apply_ans_by_email'),
                ),
            );
            array_splice($codes, 2, 0, $new_codes);
        }
        return $codes;
    }
    
    public function jobaply_by_email_tocand_temp_codes($codes, $email_class) {
        global $jobsearch_plugin_options;
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            $this->tocand_byemail_class = $email_class;
            $new_codes = array(
                array(
                    'var' => '{apply_answers}',
                    'display_text' => esc_html__('Apply Job Answers', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_apply_anscand_by_email'),
                ),
            );
            array_splice($codes, 2, 0, $new_codes);
        }
        return $codes;
    }
    
    public function get_job_apply_ans_by_email() {
        
        $email_class = $this->toemp_byemail_class;
        $job_id = isset($email_class->apply_detail['id']) ? $email_class->apply_detail['id'] : 0;
        $user_email = isset($email_class->apply_detail['user_email']) ? $email_class->apply_detail['user_email'] : '';
        
        $email_post_id = $this->aplyjob_by_email_app_id($job_id, $user_email);
        
        $applyjob_all_filled_quests = get_post_meta($job_id, 'apply_job_filled_questions', true);
        $apply_job_filled_quests = isset($applyjob_all_filled_quests[$email_post_id]) ? $applyjob_all_filled_quests[$email_post_id] : '';
        
        $answers_html = $this->jobaply_email_answers_html($apply_job_filled_quests, $job_id);
        
        if ($answers_html != '') {
            return $answers_html;
        }
        
        return '-';
    }
    
    public function get_job_apply_anscand_by_email() {
        
        $email_class = $this->tocand_byemail_class;
        $job_id = isset($email_class->apply_detail['id']) ? $email_class->apply_detail['id'] : 0;
        $user_email = isset($email_class->apply_detail['user_email']) ? $email_class->apply_detail['user_email'] : '';
        
        $email_post_id = $this->aplyjob_by_email_app_id($job_id, $user_email);
        
        $applyjob_all_filled_quests = get_post_meta($job_id, 'apply_job_filled_questions', true);
        $apply_job_filled_quests = isset($applyjob_all_filled_quests[$email_post_id]) ? $applyjob_all_filled_quests[$email_post_id] : '';
        
        $answers_html = $this->jobaply_email_answers_html($apply_job_filled_quests, $job_id);
        
        if ($answers_html != '') {
            return $answers_html;
        }
        
        return '-';
    }
    
    public function aplyjob_by_email_app_id($job_id, $user_email) {
        global $wpdb;
        $email_apps_id = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts AS posts"
            . " LEFT JOIN $wpdb->postmeta AS postmeta ON(posts.ID = postmeta.post_id) "
            . " LEFT JOIN $wpdb->postmeta AS mt1 ON(posts.ID = mt1.post_id) "
            . " WHERE post_type=%s AND (postmeta.meta_key = 'jobsearch_app_user_email' AND postmeta.meta_value = '{$user_email}')"
            . " AND (mt1.meta_key = 'jobsearch_app_job_id' AND mt1.meta_value = '{$job_id}')", 'email_apps'));
        if (isset($email_apps_id[0]) && $email_apps_id[0] > 0) {
            $app_id = $email_apps_id[0];
            
            return $app_id;
        }
    }

    private function correct_opts_val_arr($question_options, $correct_options) {
        $opts_countr = 0;
        $correct_options_arr = array();
        foreach ($question_options as $quest_opt) {
            $corct_opt = isset($correct_options[$opts_countr]) ? $correct_options[$opts_countr] : '';
            if ($corct_opt == 'on') {
                $correct_options_arr[] = $quest_opt;
            }
            $opts_countr++;
        }
        return $correct_options_arr;
    }

    public function applying_job_check_before($job_id) {
        global $jobsearch_plugin_options;
        $apply_job_questions = isset($jobsearch_plugin_options['apply_job_questions']) ? $jobsearch_plugin_options['apply_job_questions'] : '';
        
        if ($apply_job_questions == 'on') {
            
            $allow_apply = true;
            
            $apply_job_quests = get_post_meta($job_id, 'apply_job_questions', true);
            if (!empty($apply_job_quests)) {
                $answers_arr = array();
                foreach ($apply_job_quests as $quest_key => $job_quest) {
                    $question_type = isset($job_quest['type']) ? $job_quest['type'] : '';
                    $mandatory_field = isset($job_quest['mandatory']) ? $job_quest['mandatory'] : '';
                    $multi_option = isset($job_quest['multi_option']) ? $job_quest['multi_option'] : '';
                    $require_correct = isset($job_quest['require_correct']) ? $job_quest['require_correct'] : '';
                    $question_options = isset($job_quest['options']) ? $job_quest['options'] : '';
                    $correct_options = isset($job_quest['correct_option']) ? $job_quest['correct_option'] : '';
                    
                    if (($question_type == 'dropdown' || $question_type == 'checkboxes') && $require_correct == 'on' && isset($_POST['apply_job_quests'][$quest_key])) {
                        $anser_val = $_POST['apply_job_quests'][$quest_key];
                        //var_dump($anser_val);
                        if ($multi_option == 'on' && is_array($anser_val)) {
                            $correct_options_arr = $this->correct_opts_val_arr($question_options, $correct_options);
                            if (!empty($correct_options_arr) && !empty($anser_val)) {
                                $match_opts = array_intersect($correct_options_arr, $anser_val);
                                if (empty($match_opts)) {
                                    $allow_apply = false;
                                }
                            } else {
                                $allow_apply = false;
                            }
                            //var_dump($anser_val);
                            //var_dump($correct_options_arr);
                        } else {
                            $correct_options_arr = $this->correct_opts_val_arr($question_options, $correct_options);
                            if (!empty($correct_options_arr)) {
                                if (!in_array($anser_val, $correct_options_arr)) {
                                    $allow_apply = false;
                                }
                            } else {
                                $allow_apply = false;
                            }
                        }
                    }
                }
                if ($allow_apply === false) {
                    wp_send_json(array('error' => '1', 'status' => 0, 'msg' => __('You cannot apply job untill you choose the correct answer for all questions. <a href="javascript:void(0);" class="apply_wquest_bckbtk">Go Back</a>', 'wp-jobsearch')));
                }
            }
        }
    }

    public function job_applying_before_action($candidate_id, $job_id) {
        $this->applying_job_check_before($job_id);
    }

    public function applyin_job_wout_reg_before($job_id) {
        $this->applying_job_check_before($job_id);
    }

}

return new JobSearch_Apply_Job_Questions();
