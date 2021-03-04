<?php
/**
 * Directory Plus JobApplicationLoads Module
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Jobsearch_JobApplicationLoad')) {

    class Jobsearch_JobApplicationLoad
    {
        public function __construct()
        {
            add_filter('jobsearch_job_applications_btn', array($this, 'jobsearch_job_applications_btn_callback'), 11, 2);
            add_filter('jobsearch_job_applications_quick_detail_btn', array($this, 'jobsearch_job_applications_quick_detail_btn_callback'), 11, 2);
            add_action('wp_ajax_jobsearch_job_application_submit', array($this, 'jobsearch_job_application_submit_callback'));
            //
            add_action('wp_ajax_jobsearch_apply_job_with_cv_file', array($this, 'apply_job_with_cv_file'));
            //
            add_filter('jobsearch_job_detail_before_footer', array($this, 'job_application_popup_form'), 10, 1);
            add_filter('jobsearch_job_detail_quick_apply_before_footer', array($this, 'job_application_quick_apply_popup_form'), 10, 1);
            //add_action('wp_footer', array($this, 'job_application_quick_apply_popup_form'), 10, 1);
            //
            add_filter('wp_ajax_jobsearch_get_upcv_file_info', array($this, 'get_upcv_file_info'));
            add_filter('wp_ajax_nopriv_jobsearch_get_upcv_file_info', array($this, 'get_upcv_file_info'));
            //
            add_filter('wp_ajax_jobsearch_job_apply_without_login', array($this, 'job_apply_without_login'));
            add_filter('wp_ajax_nopriv_jobsearch_job_apply_without_login', array($this, 'job_apply_without_login'));
            //
            add_filter('wp_ajax_jobsearch_applying_job_with_email', array($this, 'job_apply_with_email'));
            add_filter('wp_ajax_nopriv_jobsearch_applying_job_with_email', array($this, 'job_apply_with_email'));
            //
            add_filter('wp', array($this, 'instamatch_apply_job_at_jobpage_load'));
        }

        public function instamatch_apply_job_at_jobpage_load()
        {
            if (isset($_GET['jobsearch_apply_instamatch']) && $_GET['jobsearch_apply_instamatch'] == '1') {
                $insta_id = isset($_GET['id']) ? $_GET['id'] : '';
                $insta_ids = explode('|', $insta_id);
                $insta_job_id = isset($insta_ids[0]) ? $insta_ids[0] : '';
                $insta_user_id = isset($insta_ids[1]) ? $insta_ids[1] : '';
                if ($insta_user_id > 0 && $insta_job_id > 0) {
                    $finded_instaresult_list = jobsearch_find_index_user_meta_list($insta_job_id, 'jobsearch_instamatch_job_ids', 'post_id', $insta_user_id);
                    if (!empty($finded_instaresult_list) && is_array($finded_instaresult_list)) {
                        $this->jobsearch_job_apply_by_job_id($insta_job_id, $insta_user_id);
                        $job_instamatch_list = get_post_meta($insta_job_id, 'jobsearch_instamatch_cands', true);
                        $candidate_id = jobsearch_get_user_candidate_id($insta_user_id);
                        //
                        if (!empty($job_instamatch_list)) {
                            $new_instamatch_list = array();
                            foreach ($job_instamatch_list as $instamatch_itm) {
                                if ($instamatch_itm != $candidate_id) {
                                    $new_instamatch_list[] = $instamatch_itm;
                                }
                            }
                            update_post_meta($insta_job_id, 'jobsearch_instamatch_cands', $new_instamatch_list);
                        }
                        //
                    }
                }
            }
        }

        public function apply_job_with_cv_file()
        {
            global $jobsearch_plugin_options;

            $user_id = get_current_user_id();

            $user_is_candidate = jobsearch_user_is_candidate($user_id);

            if ($user_is_candidate) {
                if (jobsearch_candidate_not_allow_to_mod()) {
                    $msg = esc_html__('You are not allowed to upload files.', 'wp-jobsearch');
                    echo json_encode(array('err_msg' => $msg));
                    die;
                }
                $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';

                $candidate_id = jobsearch_get_user_candidate_id($user_id);

                $atach_url = jobsearch_upload_candidate_cv('on_apply_cv_file', $candidate_id);

                if ($atach_url != '') {
                    $file_url = $atach_url;

                    $file_uniqid = uniqid();
                    $filename = basename($file_url);
                    $filetype = wp_check_filetype($filename, null);
                    $fileuplod_time = current_time('timestamp');

                    $arg_arr = array(
                        'file_name' => $filename,
                        'mime_type' => $filetype,
                        'time' => $fileuplod_time,
                        'file_url' => $file_url,
                        'file_id' => $file_uniqid,
                        'primary' => '',
                    );

                    $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
                    $ca_jat_cv_files = get_post_meta($candidate_id, 'jobsearch_field_user_cv_attachments', true);
                    $ca_at_cv_files = !empty($ca_at_cv_files) ? $ca_at_cv_files : array();
                    $ca_jat_cv_files = !empty($ca_jat_cv_files) ? $ca_jat_cv_files : array();

                    $ca_at_cv_files[] = $arg_arr;
                    $ca_jat_cv_files[] = $arg_arr;
                    update_post_meta($candidate_id, 'candidate_cv_files', $ca_at_cv_files);
                    update_post_meta($candidate_id, 'jobsearch_field_user_cv_attachments', $ca_jat_cv_files);

                    $cv_file_title = $filename;

                    $attach_date = $fileuplod_time;
                    $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';

                    if ($attach_mime == 'application/pdf') {
                        $attach_icon = 'fa fa-file-pdf-o';
                    } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                        $attach_icon = 'fa fa-file-word-o';
                    } else if ($attach_mime == 'text/plain') {
                        $attach_icon = 'fa fa-file-text-o';
                    } else if ($attach_mime == 'application/vnd.ms-excel' || $attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                        $attach_icon = 'fa fa-file-excel-o';
                    } else if ($attach_mime == 'image/jpeg' || $attach_mime == 'image/png') {
                        $attach_icon = 'fa fa-file-image-o';
                    } else {
                        $attach_icon = 'fa fa-file-word-o';
                    }

                    ob_start();
                    ?>
                    <li>
                        <i class="<?php echo($attach_icon) ?>"></i>
                        <label for="cv_file_<?php echo($file_uniqid) ?>">
                            <input id="cv_file_<?php echo($file_uniqid) ?>" type="radio" class="cv_file_item"
                                   name="cv_file_item" value="<?php echo($file_uniqid) ?>">
                            <?php echo(strlen($cv_file_title) > 40 ? substr($cv_file_title, 0, 40) . '...' : $cv_file_title) ?>
                            <span class="upload-datetime"><i
                                        class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), ($attach_date)) . ' ' . date_i18n(get_option('time_format'), ($attach_date)) ?></span>
                        </label>
                    </li>
                    <?php
                    $file_html = ob_get_clean();
                    echo json_encode(array('fileUrl' => $file_url, 'filehtml' => $file_html));
                }
            }
            wp_die();
        }

        public function job_application_quick_apply_popup_form($job_id)
        {
            global $jobsearch_plugin_options;
            $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';
            $rand_num = rand(100000, 9999999);
            $apply_without_login = isset($jobsearch_plugin_options['job-apply-without-login']) ? $jobsearch_plugin_options['job-apply-without-login'] : '';

            if ($apply_without_login == 'on' && !is_user_logged_in()) {

                if ($phone_validation_type == 'on') {
                    wp_enqueue_script('jobsearch-intlTelInput');
                }

                $wout_fields_sort = isset($jobsearch_plugin_options['aplywout_login_fields_sort']) ? $jobsearch_plugin_options['aplywout_login_fields_sort'] : '';
                $wout_fields_sort = isset($wout_fields_sort['fields']) ? $wout_fields_sort['fields'] : '';

                $popup_args = array(
                    'job_id' => $job_id,
                    'rand_num' => $rand_num,
                    'wout_fields_sort' => $wout_fields_sort,
                );

                extract(shortcode_atts(array(
                    'job_id' => '',
                    'rand_num' => '',
                    'wout_fields_sort' => '',
                ), $popup_args));

                $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';

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
                $cvfile_size = '5120';
                $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
                $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
                if (isset($file_sizes_arr[$cand_cv_file_size])) {
                    $cvfile_size = $cand_cv_file_size;
                    $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
                }
                $filesize_act = ceil($cvfile_size / 1024);

                $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

                if (empty($cand_files_types)) {
                    $cand_files_types = array(
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/pdf',
                    );
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
                    if (in_array($file_typ_key, $cand_files_types)) {
                        $sutable_files_arr[] = '.' . $file_typ_comar;
                    }
                }
                $sutable_files_str = implode(', ', $sutable_files_arr);
                ob_start();
                ?>
                <div class="jobsearch-modal jobsearch-typo-wrap fade" id="JobSearchNonuserApplyModal">
                    <div class="modal-inner-area">&nbsp;</div>
                    <div class="modal-content-area">
                        <div class="modal-box-area">
                            <div class="jobsearch-modal-title-box">
                                <h2><?php esc_html_e('Apply for this Job', 'wp-jobsearch') ?></h2>
                                <span class="modal-close"><i class="fa fa-times"></i></span>
                            </div>

                            <form id="apply-form-<?php echo absint($rand_num) ?>" method="post">
                                <div class="jobsearch-user-form jobsearch-user-form-coltwo">
                                    <ul class="apply-fields-list">
                                        <?php
                                        if (isset($wout_fields_sort['name'])) {
                                            foreach ($wout_fields_sort as $field_sort_key => $field_sort_val) {
                                                $field_name_swich_key = 'aplywout_log_f' . $field_sort_key . '_swch';
                                                $field_name_swich = isset($jobsearch_plugin_options[$field_name_swich_key]) ? $jobsearch_plugin_options[$field_name_swich_key] : '';
                                                if ($field_sort_key == 'name' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                    ?>
                                                    <li>
                                                        <label><?php esc_html_e('First Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                               name="pt_user_fname" type="text"
                                                               placeholder="<?php esc_html_e('First Name', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <li>
                                                        <label><?php esc_html_e('Last Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                               name="pt_user_lname" type="text"
                                                               placeholder="<?php esc_html_e('Last Name', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <?php
                                                } else if ($field_sort_key == 'email') {
                                                    ?>
                                                    <li>
                                                        <label><?php esc_html_e('Email: *', 'wp-jobsearch') ?></label>
                                                        <input class="required" name="user_email" type="text"
                                                               placeholder="<?php esc_html_e('Email Address', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <?php
                                                } else if ($field_sort_key == 'phone' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                    ?>
                                                    <li>
                                                        <label><?php esc_html_e('Phone:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                        <?php
                                                        if ($phone_validation_type == 'on') {
                                                            $rand_numb = rand(100000000, 999999999);
                                                            $itltell_input_ats = array(
                                                                'sepc_name' => 'user_phone',
                                                                'set_condial_intrvl' => 'yes',
                                                            );
                                                            jobsearch_phonenum_itltell_input('pt_user_phone', $rand_numb, '', $itltell_input_ats);
                                                        } else {
                                                            ?>
                                                            <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                   name="user_phone" type="tel"
                                                                   placeholder="<?php esc_html_e('Phone Number', 'wp-jobsearch') ?>">
                                                            <?php
                                                        }
                                                        ?>
                                                    </li>
                                                    <?php
                                                } else if ($field_sort_key == 'current_jobtitle' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                    ?>
                                                    <li>
                                                        <label><?php esc_html_e('Current Job Title:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                               name="user_job_title" type="text"
                                                               placeholder="<?php esc_html_e('Current Job Title', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <?php
                                                } else if ($field_sort_key == 'current_salary' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                    ?>
                                                    <li>
                                                        <label><?php esc_html_e('Current Salary:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                               name="user_salary" type="text"
                                                               placeholder="<?php esc_html_e('Current Salary', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <?php
                                                } else if ($field_sort_key == 'custom_fields' && $field_name_swich == 'on') {
                                                    do_action('jobsearch_form_custom_fields_load', 0, 'candidate');
                                                } else if ($field_sort_key == 'cv_attach' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) { ?>
                                                    <li class="jobsearch-user-form-coltwo-full">
                                                        <div id="jobsearch-upload-cv-main"
                                                             class="jobsearch-upload-cv jobsearch-applyjob-upload-cv">
                                                            <label><?php esc_html_e('Resume', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                            <div class="jobsearch-drpzon-con jobsearch-drag-dropcustom">
                                                                <div id="cvFilesDropzone" class="dropzone"
                                                                     ondragover="jobsearch_dragover_evnt(event)"
                                                                     ondragleave="jobsearch_leavedrop_evnt(event)"
                                                                     ondrop="jobsearch_ondrop_evnt(event)">
                                                                    <input type="file" id="cand_cv_filefield"
                                                                           class="jobsearch-upload-btn <?php echo($field_name_swich == 'on_req' ? 'cv_is_req' : '') ?>"
                                                                           name="candidate_cv_file"
                                                                           onchange="jobsearchFileContainerChangeFile(event)">
                                                                    <div class="fileContainerFileName"
                                                                         ondrop="jobsearch_ondrop_evnt(event)"
                                                                         id="fileNameContainer">
                                                                        <div class="dz-message jobsearch-dropzone-template">
                                                                                <span class="upload-icon-con"><i
                                                                                            class="jobsearch-icon jobsearch-upload"></i></span>
                                                                            <strong><?php esc_html_e('Drop a resume file or click to upload.', 'wp-jobsearch') ?></strong>
                                                                            <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                                                                            <div class="upload-or-con">
                                                                                <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <a class="jobsearch-drpzon-btn"><i
                                                                                class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload Resume', 'wp-jobsearch') ?>
                                                                    </a>
                                                                </div>
                                                                <script type="text/javascript">
                                                                    jQuery('#cvFilesDropzone').find('input[name=candidate_cv_file]').css({
                                                                        position: 'absolute',
                                                                        width: '100%',
                                                                        height: '100%',
                                                                        top: '0',
                                                                        left: '0',
                                                                        opacity: '0',
                                                                        'z-index': '9',
                                                                    });

                                                                    function jobsearchFileContainerChangeFile(e) {
                                                                        var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                        if (e.target.files.length > 0) {
                                                                            var slected_file_name = e.target.files[0].name;
                                                                            the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                        }
                                                                        document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                        try {
                                                                            droppedFiles = document.getElementById('cand_cv_filefield').files;
                                                                            document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                        } catch (error) {

                                                                        }
                                                                        try {
                                                                            aName = document.getElementById('cand_cv_filefield').value;
                                                                            if (aName !== '') {
                                                                                document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                            }
                                                                        } catch (error) {

                                                                        }
                                                                    }

                                                                    function jobsearch_ondrop_evnt(e) {
                                                                        var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                        if (e.target.files.length > 0) {
                                                                            var slected_file_name = e.target.files[0].name;
                                                                            the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                        }
                                                                        document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                        try {
                                                                            droppedFiles = e.dataTransfer.files;
                                                                            document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                        } catch (error) {

                                                                        }
                                                                    }

                                                                    function jobsearch_dragover_evnt(e) {
                                                                        document.getElementById('cvFilesDropzone').classList.add('fileContainerDragOver');
                                                                        e.preventDefault();
                                                                        e.stopPropagation();
                                                                    }

                                                                    function jobsearch_leavedrop_evnt(e) {
                                                                        document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                    }
                                                                </script>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                            $cand_resm_coverletr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
                                            if ($cand_resm_coverletr == 'on') {
                                                ?>
                                                <li class="form-textarea jobsearch-user-form-coltwo-full">
                                                    <label><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>
                                                        :</label>
                                                    <textarea name="cand_cover_letter"
                                                              placeholder="<?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>"></textarea>
                                                </li>
                                                <?php
                                            }
                                        } else {
                                            //
                                        }
                                        ?>
                                        <li class="jobsearch-user-form-coltwo-full">
                                            <input type="hidden" name="action"
                                                   value="<?php echo apply_filters('jobsearch_apply_btn_action_without_reg', 'jobsearch_job_apply_without_login') ?>">
                                            <input type="hidden" name="job_id"
                                                   value="<?php echo absint($job_id) ?>">
                                            <?php jobsearch_terms_and_con_link_txt() ?>
                                            <input class="<?php echo apply_filters('jobsearch_apply_btn_class_without_reg', 'jobsearch-apply-woutreg-btn') ?>"
                                                   data-id="<?php echo absint($rand_num) ?>" type="submit"
                                                   value="<?php esc_html_e('Apply Job', 'wp-jobsearch') ?>">
                                            <div class="form-loader"></div>
                                        </li>
                                    </ul>
                                    <div class="apply-job-form-msg"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                $html = ob_get_clean();
                return $html;
            }
        }

        public function jobsearch_job_applications_quick_detail_btn_callback($html, $arg = array())
        {
            global $jobsearch_plugin_options;
            $rand_id = rand(123400, 9999999);
            extract(shortcode_atts(array(
                'classes' => 'jobsearch-applyjob-btn',
                'btn_after_label' => '',
                'btn_before_label' => '',
                'btn_applied_label' => '',
                'before_icon' => '',
                'job_id' => ''
            ), $arg));

            $job_extrnal_apply_switch_arr = isset($jobsearch_plugin_options['apply-methods']) ? $jobsearch_plugin_options['apply-methods'] : '';
            $without_login_signin_restriction = isset($jobsearch_plugin_options['without-login-apply-restriction']) ? $jobsearch_plugin_options['without-login-apply-restriction'] : '';
            $job_apply_switch = isset($jobsearch_plugin_options['job-apply-switch']) ? $jobsearch_plugin_options['job-apply-switch'] : 'on';

            if (isset($job_apply_switch) && $job_apply_switch != 'on') {
                return $html;
            }

            $job_extrnal_apply_internal_switch = '';
            $job_extrnal_apply_external_switch = '';
            $job_extrnal_apply_email_switch = '';


            if (isset($job_extrnal_apply_switch_arr) && is_array($job_extrnal_apply_switch_arr) && sizeof($job_extrnal_apply_switch_arr) > 0) {
                foreach ($job_extrnal_apply_switch_arr as $apply_switch) {
                    if ($apply_switch == 'internal') {
                        $job_extrnal_apply_internal_switch = 'internal';
                    }
                    if ($apply_switch == 'external') {
                        $job_extrnal_apply_external_switch = 'external';
                    }
                    if ($apply_switch == 'email') {
                        $job_extrnal_apply_email_switch = 'email';
                    }
                }
            }

            $job_aply_type = get_post_meta($job_id, 'jobsearch_field_job_apply_type', true);
            if (empty($job_aply_type)) {
                $job_aply_type = 'internal';
            }

            $job_aply_extrnal_url = get_post_meta($job_id, 'jobsearch_field_job_apply_url', true);
            $apply_without_login = isset($jobsearch_plugin_options['job-apply-without-login']) ? $jobsearch_plugin_options['job-apply-without-login'] : '';
            $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';

            if ($job_id != '') {
                $classes_str = 'jobsearch-open-signin-tab jobsearch-wredirct-url';
                $multi_cvs = false;
                if (is_user_logged_in()) {
                    if (jobsearch_user_is_candidate()) {
                        if ($multiple_cv_files_allow == 'on') {
                            $multi_cvs = true;
                        }
                        $classes_str = 'jobsearch-apply-btn';
                    } else {
                        $classes_str = 'jobsearch-other-role-btn jobsearch-applyjob-msg-popup-btn';
                    }
                }
                ob_start();
                $jobsearch_applied_list = array();
                $btn_text = $btn_before_label;
                if (!is_user_logged_in() && $apply_without_login != 'on') {
                    $btn_text = apply_filters('jobsearch_loginto_apply_job_btn_text', esc_html__('Login to Apply Job', 'wp-jobsearch'));
                }
                $is_applied = false;
                if (is_user_logged_in()) {
                    $finded_result_list = jobsearch_find_index_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', 'post_id', jobsearch_get_user_id());
                    if (is_array($finded_result_list) && !empty($finded_result_list)) {
                        $classes_str = 'jobsearch-applied-btn';
                        $btn_text = $btn_applied_label;
                        $is_applied = true;
                    }
                }

                if ($apply_without_login == 'on' && !is_user_logged_in()) {
                    $classes_str = 'jobsearch-nonuser-apply-btn';
                }

                //
                $insta_applied = false;
                if (isset($_GET['jobsearch_apply_instamatch']) && $_GET['jobsearch_apply_instamatch'] == '1') {
                    $insta_id = isset($_GET['id']) ? $_GET['id'] : '';
                    $insta_ids = explode('|', $insta_id);
                    $insta_job_id = isset($insta_ids[0]) ? $insta_ids[0] : '';
                    $insta_user_id = isset($insta_ids[1]) ? $insta_ids[1] : '';
                    if ($insta_user_id > 0 && $insta_job_id > 0) {
                        $finded_instaresult_list = jobsearch_find_index_user_meta_list($job_id, 'jobsearch_instamatch_job_ids', 'post_id', $insta_user_id);
                        if (!empty($finded_instaresult_list) && is_array($finded_instaresult_list)) {
                            $insta_applied = true;
                        }
                    }
                }

                if ($insta_applied) {
                    $classes_str = 'jobsearch-applied-btn';
                    $btn_text = $btn_applied_label;
                    $is_applied = true;
                }

                // signin restriction on without login methods
                $internal_signin_switch = false;
                $external_signin_switch = false;
                $email_signin_switch = false;
                if (isset($without_login_signin_restriction) && is_array($without_login_signin_restriction) && sizeof($without_login_signin_restriction) > 0) {
                    foreach ($without_login_signin_restriction as $restrict_signin_switch) {
                        if ($restrict_signin_switch == 'internal') {
                            $internal_signin_switch = true;
                        }
                        if ($restrict_signin_switch == 'external') {
                            $external_signin_switch = true;
                        }
                        if ($restrict_signin_switch == 'email') {
                            $email_signin_switch = true;
                        }
                    }
                }

                if ($job_extrnal_apply_email_switch == 'email' && $job_aply_type == 'with_email') {

                    if ($apply_without_login == 'off' && !is_user_logged_in() && $email_signin_switch) {
                        $classes_str = 'jobsearch-open-signin-tab';
                        ?>
                        <a href="javascript:void(0);"
                           class="<?php echo esc_html($classes_str); ?> <?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?> <?php echo esc_html($classes); ?>"><?php echo ($btn_text) ?> </a>
                    <?php } else {
                        $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';
                        if ($phone_validation_type == 'on') {
                            wp_enqueue_script('jobsearch-intlTelInput');
                        }
                        ?>
                        <a href="javascript:void(0);"
                           class="<?php echo esc_html($classes); ?> <?php echo('aply-withemail-btn-' . $rand_id) ?>"><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?></a>
                        <script type="text/javascript">
                            jQuery(document).on('click', '.aply-withemail-btn-<?php echo($rand_id) ?>', function () {
                                jobsearch_modal_popup_open('JobSearchModalApplyWithEmail<?php echo($rand_id) ?>');
                            });
                        </script>
                        <?php
                        $popup_args = array(
                            'p_job_id' => $job_id,
                            'p_rand_id' => $rand_id,
                            'p_btn_text' => $btn_text,
                            'p_classes' => $classes,
                            'p_classes_str' => $classes_str,
                            'p_btn_after_label' => $btn_after_label,
                        );

                        $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';

                        $wout_fields_sort = isset($jobsearch_plugin_options['aplywout_login_fields_sort']) ? $jobsearch_plugin_options['aplywout_login_fields_sort'] : '';
                        $wout_fields_sort = isset($wout_fields_sort['fields']) ? $wout_fields_sort['fields'] : '';

                        extract(shortcode_atts(array(
                            'p_job_id' => '',
                            'p_rand_id' => '',
                            'p_btn_text' => '',
                            'p_classes' => '',
                            'p_classes_str' => '',
                            'p_btn_after_label' => '',
                        ), $popup_args));

                        $user_dname = '';
                        $user_demail = '';

                        if (is_user_logged_in()) {
                            $cuser_id = get_current_user_id();
                            $cuser_obj = get_user_by('ID', $cuser_id);
                            $user_dname = $cuser_obj->display_name;
                            $user_demail = $cuser_obj->user_email;
                        }

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
                        $cvfile_size = '5120';
                        $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
                        $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
                        if (isset($file_sizes_arr[$cand_cv_file_size])) {
                            $cvfile_size = $cand_cv_file_size;
                            $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
                        }
                        $filesize_act = ceil($cvfile_size / 1024);

                        $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

                        if (empty($cand_files_types)) {
                            $cand_files_types = array(
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/pdf',
                            );
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
                            if (in_array($file_typ_key, $cand_files_types)) {
                                $sutable_files_arr[] = '.' . $file_typ_comar;
                            }
                        }
                        $sutable_files_str = implode(', ', $sutable_files_arr);
                        if(is_admin()) return false;
                        ob_start();
                        ?>
                        <div class="jobsearch-modal fade"
                             id="JobSearchModalApplyWithEmail<?php echo($p_rand_id) ?>">
                            <div class="modal-inner-area">&nbsp;</div>
                            <div class="modal-content-area">
                                <div class="modal-box-area">
                                    <span class="modal-close"><i class="fa fa-times"></i></span>
                                    <?php
                                    if (isset($_COOKIE["jobsearch_email_apply_job_" . $p_job_id])) { ?>
                                        <p><?php esc_html_e('You have already applied for this job.', 'wp-jobsearch') ?></p>
                                    <?php } else { ?>
                                        <form id="apply-withemail-<?php echo($p_rand_id) ?>">
                                            <?php
                                            $from_args = array(
                                                'rand_num' => $p_rand_id,
                                                'job_id' => $p_job_id
                                            );
                                            do_action('jobsearch_apply_job_withemail_in_formtag_html', $from_args);
                                            ?>
                                            <div class="<?php echo apply_filters('jobsearch_apply_job_withemail_inform_class', 'jobsearch-apply-withemail-con jobsearch-user-form jobsearch-user-form-coltwo', $from_args) ?>"<?php echo apply_filters('jobsearch_apply_job_withemail_inform_tag_exattrs', '', $from_args) ?>>
                                                <ul class="apply-fields-list">
                                                    <?php
                                                    ob_start();
                                                    if (isset($wout_fields_sort['name'])) {
                                                        foreach ($wout_fields_sort as $field_sort_key => $field_sort_val) {
                                                            $field_name_swich_key = 'aplywout_log_f' . $field_sort_key . '_swch';
                                                            $field_name_swich = isset($jobsearch_plugin_options[$field_name_swich_key]) ? $jobsearch_plugin_options[$field_name_swich_key] : '';
                                                            if ($field_sort_key == 'name' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                                ?>
                                                                <li>
                                                                    <label><?php esc_html_e('First Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                    <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                           name="user_fullname" type="text"
                                                                           placeholder="<?php esc_html_e('First Name', 'wp-jobsearch') ?>">
                                                                </li>
                                                                <li>
                                                                    <label><?php esc_html_e('Last Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                    <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                           name="user_surname" type="text"
                                                                           placeholder="<?php esc_html_e('Last Name', 'wp-jobsearch') ?>">
                                                                </li>
                                                                <?php
                                                            } else if ($field_sort_key == 'email') {
                                                                $logedusr_email = '';
                                                                if (is_user_logged_in()) {
                                                                    $loged_user_obj = wp_get_current_user();
                                                                    $logedusr_email = isset($loged_user_obj->user_email) ? $loged_user_obj->user_email : '';
                                                                }
                                                                ?>
                                                                <li>
                                                                    <label><?php esc_html_e('Email: *', 'wp-jobsearch') ?></label>
                                                                    <input class="required" name="user_email"
                                                                           type="text" <?php if ($logedusr_email != '') { ?> value="<?php echo($logedusr_email) ?>" readonly<?php } ?>
                                                                           placeholder="<?php esc_html_e('Email Address', 'wp-jobsearch') ?>">
                                                                </li>
                                                            <?php } else if ($field_sort_key == 'phone' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) { ?>
                                                                <li>
                                                                    <label><?php esc_html_e('Phone:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                    <?php
                                                                    if ($phone_validation_type == 'on') {
                                                                        $rand_numb = rand(10000000, 99999999);
                                                                        $phone_field_req = false;
                                                                        if ($field_name_swich == 'on_req') {
                                                                            $phone_field_req = true;
                                                                        }
                                                                        jobsearch_phonenum_itltell_input('user_phone', $rand_numb, '', array('is_required' => $phone_field_req));
                                                                    } else {
                                                                        ?>
                                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                               name="user_phone" type="tel"
                                                                               placeholder="<?php esc_html_e('Phone Number', 'wp-jobsearch') ?>">
                                                                    <?php } ?>
                                                                </li>
                                                            <?php } else if ($field_sort_key == 'current_jobtitle' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                                ?>
                                                                <li>
                                                                    <label><?php esc_html_e('Current Job Title:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                    <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                           name="user_job_title" type="text"
                                                                           placeholder="<?php esc_html_e('Current Job Title', 'wp-jobsearch') ?>">
                                                                </li>
                                                                <?php
                                                            } else if ($field_sort_key == 'current_salary' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                                ?>
                                                                <li>
                                                                    <label><?php esc_html_e('Current Salary:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                    <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                           name="user_salary" type="text"
                                                                           placeholder="<?php esc_html_e('Current Salary', 'wp-jobsearch') ?>">
                                                                </li>
                                                                <?php
                                                            } else if ($field_sort_key == 'custom_fields' && $field_name_swich == 'on') {
                                                                do_action('jobsearch_form_custom_fields_load', 0, 'candidate');
                                                            } else if ($field_sort_key == 'cv_attach' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                                ?>
                                                                <li class="jobsearch-user-form-coltwo-full">
                                                                    <div id="jobsearch-upload-cv-main"
                                                                         class="jobsearch-upload-cv jobsearch-applyjob-upload-cv">
                                                                        <label><?php esc_html_e('Resume', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                        <div class="jobsearch-drpzon-con jobsearch-drag-dropcustom">
                                                                            <div id="cvFilesDropzone"
                                                                                 class="dropzone"
                                                                                 ondragover="jobsearch_dragover_evnt(event)"
                                                                                 ondragleave="jobsearch_leavedrop_evnt(event)"
                                                                                 ondrop="jobsearch_ondrop_evnt(event)">
                                                                                <input type="file"
                                                                                       id="cand_cv_filefield"
                                                                                       class="jobsearch-upload-btn <?php echo($field_name_swich == 'on_req' ? 'cv_is_req' : '') ?>"
                                                                                       name="cuser_cv_file"
                                                                                       onchange="jobsearchFileContainerChangeFile(event)">
                                                                                <div class="fileContainerFileName"
                                                                                     ondrop="jobsearch_ondrop_evnt(event)"
                                                                                     id="fileNameContainer">
                                                                                    <div class="dz-message jobsearch-dropzone-template">
                                                                                            <span class="upload-icon-con"><i
                                                                                                        class="jobsearch-icon jobsearch-upload"></i></span>
                                                                                        <strong><?php esc_html_e('Drop a resume file or click to upload.', 'wp-jobsearch') ?></strong>
                                                                                        <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                                                                                        <div class="upload-or-con">
                                                                                            <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <a class="jobsearch-drpzon-btn"><i
                                                                                            class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload Resume', 'wp-jobsearch') ?>
                                                                                </a>
                                                                            </div>
                                                                            <script type="text/javascript">
                                                                                jQuery('#cvFilesDropzone').find('input[name=cuser_cv_file]').css({
                                                                                    position: 'absolute',
                                                                                    width: '100%',
                                                                                    height: '100%',
                                                                                    top: '0',
                                                                                    left: '0',
                                                                                    opacity: '0',
                                                                                    'z-index': '9',
                                                                                });

                                                                                function jobsearchFileContainerChangeFile(e) {
                                                                                    var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                                    if (e.target.files.length > 0) {
                                                                                        var slected_file_name = e.target.files[0].name;
                                                                                        the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                                    }
                                                                                    document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                                    try {
                                                                                        droppedFiles = document.getElementById('cand_cv_filefield').files;
                                                                                        document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                                    } catch (error) {
                                                                                        ;
                                                                                    }
                                                                                    try {
                                                                                        aName = document.getElementById('cand_cv_filefield').value;
                                                                                        if (aName !== '') {
                                                                                            document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                                        }
                                                                                    } catch (error) {
                                                                                        ;
                                                                                    }
                                                                                }

                                                                                function jobsearch_ondrop_evnt(e) {
                                                                                    var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                                    if (e.target.files.length > 0) {
                                                                                        var slected_file_name = e.target.files[0].name;
                                                                                        the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                                    }
                                                                                    document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                                    try {
                                                                                        droppedFiles = e.dataTransfer.files;
                                                                                        document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                                    } catch (error) {
                                                                                        ;
                                                                                    }
                                                                                }

                                                                                function jobsearch_dragover_evnt(e) {
                                                                                    document.getElementById('cvFilesDropzone').classList.add('fileContainerDragOver');
                                                                                    e.preventDefault();
                                                                                    e.stopPropagation();
                                                                                }

                                                                                function jobsearch_leavedrop_evnt(e) {
                                                                                    document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                                }
                                                                            </script>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                        <li class="form-textarea jobsearch-user-form-coltwo-full">
                                                            <label><?php esc_html_e('Message', 'wp-jobsearch') ?>
                                                                :</label>
                                                            <textarea name="user_msg"
                                                                      placeholder="<?php esc_html_e('Type your Message', 'wp-jobsearch') ?>"></textarea>
                                                        </li>
                                                        <?php
                                                    }
                                                    $cv_html = ob_get_clean();
                                                    echo apply_filters('jobsearch_aply_with_cv_form_cv_field', $cv_html, $p_job_id, $p_rand_id);
                                                    ?>
                                                    <li class="jobsearch-user-form-coltwo-full">
                                                        <input type="hidden" name="job_id"
                                                               value="<?php echo($p_job_id) ?>">
                                                        <input type="hidden" name="action"
                                                               value="jobsearch_applying_job_with_email">
                                                        <?php
                                                        jobsearch_terms_and_con_link_txt();
                                                        //
                                                        ob_start();
                                                        ?>
                                                        <div class="terms-priv-chek-con">
                                                            <p><input type="checkbox"
                                                                      name="email_commun_check"> <?php esc_html_e('You accept email communication.', 'wp-jobsearch') ?>
                                                            </p>
                                                        </div>
                                                        <?php
                                                        $accpt_html = ob_get_clean();
                                                        echo apply_filters('jobsearch_jobaply_byemail_comuni_chkhtml', $accpt_html);
                                                        ?>
                                                        <a href="javascript:void(0);"
                                                           class="<?php echo esc_html($p_classes); ?> jobsearch-applyin-withemail"
                                                           data-randid="<?php echo absint($p_rand_id); ?>"
                                                           data-jobid="<?php echo absint($p_job_id); ?>"
                                                           data-btnafterlabel="<?php echo esc_html($p_btn_after_label) ?>"
                                                           data-btnbeforelabel="<?php echo wp_kses($p_btn_text,[]) ?>"><?php echo ($p_btn_text) ?></a>
                                                    </li>
                                                </ul>
                                                <div class="apply-job-form-msg"></div>
                                                <div class="apply-job-loader"></div>
                                            </div>
                                        </form>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        $popupp_hmtl = ob_get_clean();
                        echo apply_filters('jobsearch_applyjob_withemail_popup_html', $popupp_hmtl, $popup_args);

                    }
                } else if ($job_extrnal_apply_external_switch == 'external' && $job_aply_type == 'external' && $job_aply_extrnal_url != '') {

                    if ($apply_without_login == 'off' && !is_user_logged_in() && $external_signin_switch) {
                        $classes_str = 'jobsearch-open-signin-tab';
                        ?>
                        <a href="javascript:void(0);"
                           class="<?php echo esc_html($classes_str); ?> <?php echo esc_html($classes); ?>"><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?> </a>
                        <?php
                    } else {
                        ?>
                        <a href="<?php echo esc_url_raw($job_aply_extrnal_url) ?>" data-id="<?php echo ($job_id) ?>"
                           class="<?php echo esc_html($classes); ?> jobsearch-extrnal-applybtn-act"
                           target="_blank"><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?></a>
                        <?php
                    }
                } else if ($job_extrnal_apply_internal_switch == 'internal' && $job_aply_type == 'internal') {

                    $this_wredirct_url = jobsearch_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    if ($apply_without_login == 'off' && !is_user_logged_in() && $internal_signin_switch) {

                        $classes_str = 'jobsearch-open-signin-tab';
                        ?><a href="javascript:void(0);"
                             class="<?php echo esc_html($classes_str); ?> <?php echo esc_html($classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?> </a><?php
                    } else {
                        if ($multi_cvs === true) {
                            wp_enqueue_script('dropzone');
                            ?>
                            <script type="text/javascript">
                                jQuery(document).on('click', '.jobsearch-modelcvs-btn-<?php echo($rand_id) ?>', function () {

                                    jobsearch_modal_popup_open('JobSearchModalMultiCVs<?php echo($rand_id) ?>');
                                });
                            </script>
                            <a href="javascript:void(0);"
                               class="<?php echo esc_html($classes); ?> <?php echo($is_applied ? '' : 'jobsearch-modelcvs-btn-' . $rand_id) ?>"><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?></a>
                            <?php
                            $max_cvs_allow = isset($jobsearch_plugin_options['max_cvs_allow']) && absint($jobsearch_plugin_options['max_cvs_allow']) > 0 ? absint($jobsearch_plugin_options['max_cvs_allow']) : 5;
                            $popup_args = array(
                                'p_job_id' => $job_id,
                                'p_rand_id' => $rand_id,
                                'p_btn_text' => $btn_text,
                                'p_classes' => $classes,
                                'p_classes_str' => $classes_str,
                                'p_btn_after_label' => $btn_after_label,
                                'max_cvs_allow' => $max_cvs_allow,
                            );


                            $user_id = get_current_user_id();

                            $candidate_id = jobsearch_get_user_candidate_id($user_id);

                            extract(shortcode_atts(array(
                                'p_job_id' => '',
                                'p_rand_id' => '',
                                'p_btn_text' => '',
                                'p_classes' => '',
                                'p_classes_str' => '',
                                'p_btn_after_label' => '',
                                'max_cvs_allow' => '',
                            ), $popup_args));

                            //
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
                            $cvfile_size = '5120';
                            $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
                            $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
                            if (isset($file_sizes_arr[$cand_cv_file_size])) {
                                $cvfile_size = $cand_cv_file_size;
                                $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
                            }
                            $filesize_act = ceil($cvfile_size / 1024);

                            $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

                            if (empty($cand_files_types)) {
                                $cand_files_types = array(
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/pdf',
                                );
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
                                if (in_array($file_typ_key, $cand_files_types)) {
                                    $sutable_files_arr[] = '.' . $file_typ_comar;
                                }
                            }
                            $sutable_files_str = implode(', ', $sutable_files_arr);
                            $cand_cvr_leter = get_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', true);
                            $cand_resm_coverletr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
                            ?>
                            <div class="jobsearch-modal fade" id="JobSearchModalMultiCVs<?php echo($p_rand_id) ?>">
                                <div class="modal-inner-area">&nbsp;</div>
                                <div class="modal-content-area">
                                    <div class="modal-box-area">
                                        <div class="jobsearch-modal-title-box">
                                            <h2><?php esc_html_e('Select CV', 'wp-jobsearch') ?></h2>
                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                        </div>
                                        <?php
                                        $user_id = get_current_user_id();
                                        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                                        $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);

                                        //
                                        $from_args = array(
                                            'rand_num' => $p_rand_id,
                                            'job_id' => $p_job_id,
                                            'apply_type' => 'internal',
                                        );
                                        do_action('jobsearch_apply_job_internal_bfr_main_html', $from_args);
                                        ?>
                                        <div class="jobsearch-applyjob-internalmain jobsearch-apply-withcvs"<?php echo apply_filters('jobsearch_apply_job_internal_main_tag_exattrs', '', $from_args) ?>>
                                            <?php
                                            $cv_files_count = 0;
                                            if (!empty($ca_at_cv_files)) {
                                                $cv_files_count = count($ca_at_cv_files);

                                                $have_resumes = false;
                                                ob_start();
                                                $cvfile_count = 1;
                                                foreach ($ca_at_cv_files as $cv_file_key => $cv_file_val) {
                                                    $file_attach_id = isset($cv_file_val['file_id']) ? $cv_file_val['file_id'] : '';
                                                    $file_url = isset($cv_file_val['file_url']) ? $cv_file_val['file_url'] : '';
                                                    $filename = isset($cv_file_val['file_name']) ? $cv_file_val['file_name'] : '';
                                                    $filetype = isset($cv_file_val['mime_type']) ? $cv_file_val['mime_type'] : '';
                                                    $fileuplod_time = isset($cv_file_val['time']) ? $cv_file_val['time'] : '';
                                                    if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment') {
                                                        $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                                                        $filetype = array('type' => $attach_mime);
                                                    }

                                                    $cv_file_title = $filename;

                                                    $attach_date = $fileuplod_time;
                                                    $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';

                                                    if ($cvfile_count == 1) {
                                                        $cv_primary = 'yes';
                                                    } else {
                                                        $cv_primary = isset($cv_file_val['primary']) ? $cv_file_val['primary'] : '';
                                                    }

                                                    if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment') {
                                                        $cv_file_title = get_the_title($file_attach_id);
                                                        $attach_post = get_post($file_attach_id);
                                                        $file_path = get_attached_file($file_attach_id);
                                                        $filename = basename($file_path);

                                                        $attach_date = isset($attach_post->post_date) ? $attach_post->post_date : '';
                                                        $attach_date = strtotime($attach_date);
                                                        $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                                                    }

                                                    if ($attach_mime == 'application/pdf') {
                                                        $attach_icon = 'fa fa-file-pdf-o';
                                                    } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                                        $attach_icon = 'fa fa-file-word-o';
                                                    } else if ($attach_mime == 'text/plain') {
                                                        $attach_icon = 'fa fa-file-text-o';
                                                    } else if ($attach_mime == 'application/vnd.ms-excel' || $attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                                        $attach_icon = 'fa fa-file-excel-o';
                                                    } else if ($attach_mime == 'image/jpeg' || $attach_mime == 'image/png') {
                                                        $attach_icon = 'fa fa-file-image-o';
                                                    } else {
                                                        $attach_icon = 'fa fa-file-word-o';
                                                    }
                                                    if (!empty($filetype)) {
                                                        $have_resumes = true;
                                                        ?>
                                                        <li<?php echo($cv_primary == 'yes' ? ' class="active"' : '') ?>>
                                                            <i class="<?php echo($attach_icon) ?>"></i>
                                                            <label for="cv_file_<?php echo($file_attach_id) ?>">
                                                                <input id="cv_file_<?php echo($file_attach_id) ?>"
                                                                       type="radio" class="cv_file_item"
                                                                       name="cv_file_item" <?php echo($cv_primary == 'yes' ? 'checked="checked"' : '') ?>
                                                                       value="<?php echo($file_attach_id) ?>">
                                                                <?php echo(strlen($cv_file_title) > 40 ? substr($cv_file_title, 0, 40) . '...' : $cv_file_title) ?>
                                                                <?php
                                                                if ($attach_date != '') {
                                                                    ?>
                                                                    <span class="upload-datetime"><i
                                                                                class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), ($attach_date)) . ' ' . date_i18n(get_option('time_format'), ($attach_date)) ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </label>
                                                        </li>
                                                        <?php
                                                    }
                                                    $cvfile_count++;
                                                }
                                                $resumes_list_html = ob_get_clean();
                                                ?>
                                                <ul class="user-cvs-list"<?php echo($have_resumes ? '' : ' style="display: none;"') ?>>
                                                    <?php
                                                    echo($resumes_list_html);
                                                    ?>
                                                </ul>
                                                <?php
                                                if (!$have_resumes) {
                                                    ?>
                                                    <div class="user-nocvs-found">
                                                        <p><?php esc_html_e('No resume uploaded.', 'wp-jobsearch') ?></p>
                                                    </div>
                                                    <?php
                                                }
                                                if (isset($cv_files_count) && $cv_files_count < $max_cvs_allow) { ?>
                                                    <div class="upload-cvs-sep">
                                                        <div class="jobsearch-box-title">
                                                            <span><?php esc_html_e('OR', 'wp-jobsearch') ?></span>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <ul class="user-cvs-list" style="display: none;"></ul>
                                                <div class="user-nocvs-found">
                                                    <p><?php esc_html_e('No resume uploaded.', 'wp-jobsearch') ?></p>
                                                </div>
                                                <?php
                                            }
                                            if (isset($cv_files_count) && $cv_files_count < $max_cvs_allow) { ?>
                                                <div class="upload-new-cv-sec">
                                                    <div class="jobsearch-drpzon-con">
                                                        <script type="text/javascript">
                                                            jQuery(document).ready(function () {
                                                                jQuery('#cvFilesDropzone').dropzone({
                                                                    uploadMultiple: false,
                                                                    maxFiles: 1,
                                                                    <?php
                                                                    if (!empty($cand_files_types)) {
                                                                    ?>
                                                                    acceptedFiles: '<?php echo implode(',', $cand_files_types) ?>',
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    maxFilesize: <?php echo absint($filesize_act) ?>,
                                                                    paramName: 'on_apply_cv_file',
                                                                    init: function () {
                                                                        this.on("complete", function (file) {
                                                                            //console.log(file);
                                                                            if (file.status == 'success') {
                                                                                var ajresponse = file.xhr.response;
                                                                                ajresponse = jQuery.parseJSON(ajresponse);
                                                                                //console.log(ajresponse);
                                                                                jQuery('.jobsearch-apply-withcvs .user-cvs-list').append(ajresponse.filehtml);
                                                                                jQuery('.jobsearch-apply-withcvs .user-cvs-list').removeAttr('style');
                                                                                jQuery('.jobsearch-apply-withcvs .user-nocvs-found').hide();
                                                                                jQuery('.jobsearch-apply-withcvs .user-cvs-list li:last-child').find('input').trigger('click');
                                                                            }
                                                                            jQuery('.upload-new-cv-sec .fileUpLoader').html('');
                                                                        });
                                                                    },
                                                                    addedfile: function () {
                                                                        jQuery('.jobsearch-drpzon-con').css({
                                                                            'pointer-events': 'none',
                                                                            'opacity': '0.4'
                                                                        });
                                                                        jQuery('.upload-new-cv-sec .fileUpLoader').html('<i class="fa fa-refresh fa-spin"></i>');
                                                                    }
                                                                });
                                                            });
                                                        </script>
                                                        <form action="<?php echo admin_url('admin-ajax.php') ?>"
                                                              id="cvFilesDropzone" method="post" class="dropzone">
                                                            <div class="dz-message jobsearch-dropzone-template">
                                                                    <span class="upload-icon-con"><i
                                                                                class="jobsearch-icon jobsearch-upload"></i></span>
                                                                <strong><?php esc_html_e('Drop files here to upload.', 'wp-jobsearch') ?></strong>
                                                                <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                                                                <div class="upload-or-con">
                                                                    <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                                </div>
                                                                <a class="jobsearch-drpzon-btn"><i
                                                                            class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload Resume', 'wp-jobsearch') ?>
                                                                </a>
                                                            </div>
                                                            <input type="hidden" name="action"
                                                                   value="jobsearch_apply_job_with_cv_file">
                                                        </form>
                                                    </div>
                                                </div>
                                                <?php
                                            }

                                            //
                                            if ($cand_resm_coverletr == 'on') {
                                                ?>
                                                <div class="jobsearch-user-form jobsearch-user-form-coltwo jobsearch-frmfields-sec aply-cvr-letter">
                                                    <ul class="apply-fields-list">
                                                        <li class="form-textarea jobsearch-user-form-coltwo-full">
                                                            <label><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>
                                                                :</label>
                                                            <textarea name="cand_cover_letter"
                                                                      placeholder="<?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>"><?php echo($cand_cvr_leter) ?></textarea>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <?php
                                            }

                                            echo apply_filters('jobsearch_applying_job_after_cv_upload_file', '');
                                            echo apply_filters('jobsearch_applying_job_before_apply', '');
                                            ?>
                                            <a href="javascript:void(0);"
                                               class="<?php echo esc_html($p_classes_str); ?> jobsearch-applyjob-btn jobsearch-apply-btn-<?php echo absint($p_rand_id); ?> <?php echo esc_html($p_classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>
                                               data-randid="<?php echo absint($p_rand_id); ?>"
                                               data-jobid="<?php echo absint($p_job_id); ?>"
                                               data-btnafterlabel="<?php echo esc_html($p_btn_after_label) ?>"
                                               data-btnbeforelabel="<?php echo wp_kses($p_btn_text,[]) ?>"><?php echo ($p_btn_text) ?></a>
                                            <small class="apply-bmsg"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } else {

                            $cand_resm_coverletr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
                            //
                            $ferd_classes = '';
                            if ($apply_without_login == 'on' && !is_user_logged_in()) {
                                $ferd_classes = 'jobsearch-nonuser-apply-btn';
                            } else if (!is_user_logged_in()) {
                                $ferd_classes = 'jobsearch-open-signin-tab jobsearch-wredirct-url';
                            }

                            $cand_member_check = true;
                            if (is_user_logged_in()) {
                                if (!jobsearch_user_is_candidate()) {
                                    $cand_member_check = false;
                                }
                            }

                            ob_start();
                            $force_open_apply_popup = apply_filters('jobsearch_job_apply_simple_btn_popopen', false, $job_id);
                            if (($cand_resm_coverletr == 'on' || $force_open_apply_popup === true) && $cand_member_check) { ?>
                                <script type="text/javascript">
                                    jQuery(document).on('click', '.jobsearch-modelsimpapply-btn-<?php echo($rand_id) ?>', function () {
                                        jobsearch_modal_popup_open('JobSearchModalSimpleApply<?php echo($rand_id) ?>');
                                    });
                                </script>
                                <a href="javascript:void(0);"
                                   class="<?php echo esc_html($classes); ?> <?php echo($is_applied || (!is_user_logged_in()) ? '' : 'jobsearch-modelsimpapply-btn-' . $rand_id) ?> <?php echo($ferd_classes) ?>"
                                    <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?></a>

                                <?php
                                if (is_user_logged_in()) {
                                    $popup_args = array(
                                        'p_job_id' => $job_id,
                                        'p_rand_id' => $rand_id,
                                        'p_btn_text' => $btn_text,
                                        'p_classes' => $classes,
                                        'p_classes_str' => $classes_str,
                                        'p_btn_after_label' => $btn_after_label,
                                        'this_wredirct_url' => $this_wredirct_url,
                                        'cand_resm_coverletr' => $cand_resm_coverletr,
                                    );

                                    $user_id = get_current_user_id();
                                    $candidate_id = jobsearch_get_user_candidate_id($user_id);

                                    extract(shortcode_atts(array(
                                        'p_job_id' => '',
                                        'p_rand_id' => '',
                                        'p_btn_text' => '',
                                        'p_classes' => '',
                                        'p_classes_str' => '',
                                        'p_btn_after_label' => '',
                                        'this_wredirct_url' => '',
                                        'cand_resm_coverletr' => '',
                                    ), $popup_args));


                                    $cand_cvr_leter = get_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', true);
                                    ?>
                                    <div class="jobsearch-modal fade"
                                         id="JobSearchModalSimpleApply<?php echo($p_rand_id) ?>">
                                        <div class="modal-inner-area">&nbsp;</div>
                                        <div class="modal-content-area">
                                            <div class="modal-box-area">
                                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                                <?php
                                                $from_args = array(
                                                    'rand_num' => $p_rand_id,
                                                    'job_id' => $p_job_id,
                                                    'apply_type' => 'internal',
                                                );
                                                do_action('jobsearch_apply_job_internal_bfr_main_html', $from_args);
                                                ?>
                                                <div class="jobsearch-applyjob-internalmain"<?php echo apply_filters('jobsearch_apply_job_internal_main_tag_exattrs', '', $from_args) ?>>
                                                    <?php if ($cand_resm_coverletr == 'on') { ?>
                                                        <div class="jobsearch-user-form jobsearch-user-form-coltwo jobsearch-frmfields-sec aply-cvr-letter">
                                                            <ul class="apply-fields-list">
                                                                <li class="form-textarea jobsearch-user-form-coltwo-full">
                                                                    <label><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>
                                                                        :</label>
                                                                    <textarea name="cand_cover_letter"
                                                                              placeholder="<?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>"><?php echo($cand_cvr_leter) ?></textarea>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <?php
                                                    }
                                                    echo apply_filters('jobsearch_applying_job_before_apply_single', '');
                                                    ?>
                                                    <a href="javascript:void(0);"
                                                       class="<?php echo esc_html($p_classes_str); ?> jobsearch-apply-btn-<?php echo absint($p_rand_id); ?> <?php echo ($p_classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>
                                                       data-randid="<?php echo absint($p_rand_id); ?>"
                                                       data-jobid="<?php echo absint($p_job_id); ?>"
                                                       data-btnafterlabel="<?php echo esc_html($p_btn_after_label) ?>"
                                                       data-btnbeforelabel="<?php echo wp_kses($p_btn_text,[]) ?>"><?php echo ($p_btn_text) ?></a>
                                                    <small class="apply-bmsg"></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php

                                }
                                //

                            } else { ?>
                                <a href="javascript:void(0);"
                                   class="<?php echo esc_html($classes_str); ?> jobsearch-apply-btn-<?php echo absint($rand_id); ?> <?php echo esc_html($classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>
                                   data-randid="<?php echo absint($rand_id); ?>"
                                   data-jobid="<?php echo absint($job_id); ?>"
                                   data-btnafterlabel="<?php echo esc_html($btn_after_label) ?>"
                                   data-btnbeforelabel="<?php echo wp_kses($btn_text,array()) ?>"><?php echo(!empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''); ?><?php echo ($btn_text) ?></a>
                                <small class="apply-bmsg"></small>
                                <?php
                            }
                            $appbtn_html = ob_get_clean();
                            echo apply_filters('jobsearch_jobaplybtn_simple_default', $appbtn_html, $classes_str, $rand_id, $classes, $job_id, $btn_after_label, $btn_text);

                            //
                        }
                    }
                }
            }
            $html .= ob_get_clean();
            return $html;
        }

        public function jobsearch_job_applications_btn_callback($html, $arg = array())
        {
            global $jobsearch_plugin_options;
            $rand_id = rand(123400, 9999999);
            extract(shortcode_atts(array(
                'classes' => 'jobsearch-applyjob-btn',
                'btn_after_label' => '',
                'btn_before_label' => '',
                'btn_applied_label' => '',
                'before_icon' => '',
                'job_id' => ''
            ), $arg));

            $job_extrnal_apply_switch_arr = isset($jobsearch_plugin_options['apply-methods']) ? $jobsearch_plugin_options['apply-methods'] : '';
            $without_login_signin_restriction = isset($jobsearch_plugin_options['without-login-apply-restriction']) ? $jobsearch_plugin_options['without-login-apply-restriction'] : '';
            $job_apply_switch = isset($jobsearch_plugin_options['job-apply-switch']) ? $jobsearch_plugin_options['job-apply-switch'] : 'on';

            if (isset($job_apply_switch) && $job_apply_switch != 'on') {
                return $html;
            }

            $job_extrnal_apply_internal_switch = '';
            $job_extrnal_apply_external_switch = '';
            $job_extrnal_apply_email_switch = '';


            if (isset($job_extrnal_apply_switch_arr) && is_array($job_extrnal_apply_switch_arr) && sizeof($job_extrnal_apply_switch_arr) > 0) {
                foreach ($job_extrnal_apply_switch_arr as $apply_switch) {
                    if ($apply_switch == 'internal') {
                        $job_extrnal_apply_internal_switch = 'internal';
                    }
                    if ($apply_switch == 'external') {
                        $job_extrnal_apply_external_switch = 'external';
                    }
                    if ($apply_switch == 'email') {
                        $job_extrnal_apply_email_switch = 'email';
                    }
                }
            }

            $job_aply_type = get_post_meta($job_id, 'jobsearch_field_job_apply_type', true);
            if (empty($job_aply_type)) {
                $job_aply_type = 'internal';
            }

            $job_aply_extrnal_url = get_post_meta($job_id, 'jobsearch_field_job_apply_url', true);

            $apply_without_login = isset($jobsearch_plugin_options['job-apply-without-login']) ? $jobsearch_plugin_options['job-apply-without-login'] : '';

            $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';

            if ($job_id != '') {
                $classes_str = 'jobsearch-open-signin-tab jobsearch-wredirct-url';
                $multi_cvs = false;
                if (is_user_logged_in()) {
                    if (jobsearch_user_is_candidate()) {
                        if ($multiple_cv_files_allow == 'on') {
                            $multi_cvs = true;
                        }
                        $classes_str = 'jobsearch-apply-btn';
                    } else {
                        $classes_str = 'jobsearch-other-role-btn jobsearch-applyjob-msg-popup-btn';
                    }
                }
                ob_start();
                $jobsearch_applied_list = array();
                $btn_text = $btn_before_label;
                if (!is_user_logged_in() && $apply_without_login != 'on') {
                    $btn_text = apply_filters('jobsearch_loginto_apply_job_btn_text', esc_html__('Login to Apply Job', 'wp-jobsearch'));
                }
                $is_applied = false;
                if (is_user_logged_in()) {
                    $finded_result_list = jobsearch_find_index_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', 'post_id', jobsearch_get_user_id());
                    if (is_array($finded_result_list) && !empty($finded_result_list)) {
                        $classes_str = 'jobsearch-applied-btn';
                        $btn_text = $btn_applied_label;
                        $is_applied = true;
                    }
                }

                if ($apply_without_login == 'on' && !is_user_logged_in()) {
                    $classes_str = 'jobsearch-nonuser-apply-btn';
                }

                //
                $insta_applied = false;
                if (isset($_GET['jobsearch_apply_instamatch']) && $_GET['jobsearch_apply_instamatch'] == '1') {
                    $insta_id = isset($_GET['id']) ? $_GET['id'] : '';
                    $insta_ids = explode('|', $insta_id);
                    $insta_job_id = isset($insta_ids[0]) ? $insta_ids[0] : '';
                    $insta_user_id = isset($insta_ids[1]) ? $insta_ids[1] : '';
                    if ($insta_user_id > 0 && $insta_job_id > 0) {
                        $finded_instaresult_list = jobsearch_find_index_user_meta_list($job_id, 'jobsearch_instamatch_job_ids', 'post_id', $insta_user_id);
                        if (!empty($finded_instaresult_list) && is_array($finded_instaresult_list)) {
                            $insta_applied = true;
                        }
                    }
                }

                if ($insta_applied) {
                    $classes_str = 'jobsearch-applied-btn';
                    $btn_text = $btn_applied_label;
                    $is_applied = true;
                }

                // signin restriction on without login methods
                $internal_signin_switch = false;
                $external_signin_switch = false;
                $email_signin_switch = false;
                if (isset($without_login_signin_restriction) && is_array($without_login_signin_restriction) && sizeof($without_login_signin_restriction) > 0) {
                    foreach ($without_login_signin_restriction as $restrict_signin_switch) {
                        if ($restrict_signin_switch == 'internal') {
                            $internal_signin_switch = true;
                        }
                        if ($restrict_signin_switch == 'external') {
                            $external_signin_switch = true;
                        }
                        if ($restrict_signin_switch == 'email') {
                            $email_signin_switch = true;
                        }
                    }
                }

                if ($job_extrnal_apply_email_switch == 'email' && $job_aply_type == 'with_email') {
                    if ($apply_without_login == 'off' && !is_user_logged_in() && $email_signin_switch) {
                        $classes_str = 'jobsearch-open-signin-tab';
                        ?>
                        <a href="javascript:void(0);"
                           class="<?php echo esc_html($classes_str); ?> <?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?> <?php echo esc_html($classes); ?>"><?php echo ($btn_text) ?> </a>
                        <?php
                    } else {
                        $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';
                        if ($phone_validation_type == 'on') {
                            wp_enqueue_script('jobsearch-intlTelInput');
                        }
                        ?>
                        <a href="javascript:void(0);"
                           class="<?php echo esc_html($classes); ?> <?php echo('aply-withemail-btn-' . $rand_id) ?>"><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?></a>
                        <script type="text/javascript">
                            jQuery(document).on('click', '.aply-withemail-btn-<?php echo($rand_id) ?>', function () {
                                jobsearch_modal_popup_open('JobSearchModalApplyWithEmail<?php echo($rand_id) ?>');
                            });
                        </script>
                        <?php
                        $popup_args = array(
                            'p_job_id' => $job_id,
                            'p_rand_id' => $rand_id,
                            'p_btn_text' => $btn_text,
                            'p_classes' => $classes,
                            'p_classes_str' => $classes_str,
                            'p_btn_after_label' => $btn_after_label,
                        );
                        add_action('wp_footer', function () use ($popup_args) {
                            global $jobsearch_plugin_options;


                            $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';

                            $wout_fields_sort = isset($jobsearch_plugin_options['aplywout_login_fields_sort']) ? $jobsearch_plugin_options['aplywout_login_fields_sort'] : '';
                            $wout_fields_sort = isset($wout_fields_sort['fields']) ? $wout_fields_sort['fields'] : '';

                            extract(shortcode_atts(array(
                                'p_job_id' => '',
                                'p_rand_id' => '',
                                'p_btn_text' => '',
                                'p_classes' => '',
                                'p_classes_str' => '',
                                'p_btn_after_label' => '',
                            ), $popup_args));

                            $user_dname = '';
                            $user_demail = '';

                            if (is_user_logged_in()) {
                                $cuser_id = get_current_user_id();
                                $cuser_obj = get_user_by('ID', $cuser_id);
                                $user_dname = $cuser_obj->display_name;
                                $user_demail = $cuser_obj->user_email;
                            }

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
                            $cvfile_size = '5120';
                            $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
                            $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
                            if (isset($file_sizes_arr[$cand_cv_file_size])) {
                                $cvfile_size = $cand_cv_file_size;
                                $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
                            }
                            $filesize_act = ceil($cvfile_size / 1024);

                            $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

                            if (empty($cand_files_types)) {
                                $cand_files_types = array(
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/pdf',
                                );
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
                                if (in_array($file_typ_key, $cand_files_types)) {
                                    $sutable_files_arr[] = '.' . $file_typ_comar;
                                }
                            }
                            $sutable_files_str = implode(', ', $sutable_files_arr);

                            ob_start();
                            if(is_admin()) return false;
                            ?>
                            <div class="jobsearch-modal fade"
                                 id="JobSearchModalApplyWithEmail<?php echo($p_rand_id) ?>">
                                <div class="modal-inner-area">&nbsp;</div>
                                <div class="modal-content-area">
                                    <div class="modal-box-area">
                                        <span class="modal-close"><i class="fa fa-times"></i></span>
                                        <?php
                                        if (isset($_COOKIE["jobsearch_email_apply_job_" . $p_job_id])) { ?>
                                            <p><?php esc_html_e('You have already applied for this job.', 'wp-jobsearch') ?></p>
                                        <?php } else { ?>
                                            <form id="apply-withemail-<?php echo($p_rand_id) ?>">
                                                <?php
                                                $from_args = array(
                                                    'rand_num' => $p_rand_id,
                                                    'job_id' => $p_job_id
                                                );
                                                do_action('jobsearch_apply_job_withemail_in_formtag_html', $from_args);
                                                
                                                $candidate_id = 0;
                                                $user_fname = '';
                                                $user_lname = '';
                                                $user_phone = $user_justphone = $job_title = $_candidate_salary = '';
                                                if (is_user_logged_in()) {
                                                    $curr_user_obj = wp_get_current_user();
                                                    $curruser_id = get_current_user_id();
                                                    $user_fname = isset($curr_user_obj->first_name) ? $curr_user_obj->first_name : '';
                                                    $user_lname = isset($curr_user_obj->last_name) ? $curr_user_obj->last_name : '';
                                                    
                                                    $candidate_id = jobsearch_get_user_candidate_id($curruser_id);
                                                    
                                                    $user_phone = get_post_meta($candidate_id, 'jobsearch_field_user_phone', true);
                                                    $user_justphone = get_post_meta($candidate_id, 'jobsearch_field_user_justphone', true);
                                                    
                                                    $job_title = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                                                    $_candidate_salary = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary', true);
                                                }
                                                ?>
                                                <div class="<?php echo apply_filters('jobsearch_apply_job_withemail_inform_class', 'jobsearch-apply-withemail-con jobsearch-user-form jobsearch-user-form-coltwo', $from_args) ?>"<?php echo apply_filters('jobsearch_apply_job_withemail_inform_tag_exattrs', '', $from_args) ?>>
                                                    <ul class="apply-fields-list">
                                                        <?php
                                                        ob_start();
                                                        if (isset($wout_fields_sort['name'])) {
                                                            foreach ($wout_fields_sort as $field_sort_key => $field_sort_val) {
                                                                $field_name_swich_key = 'aplywout_log_f' . $field_sort_key . '_swch';
                                                                $field_name_swich = isset($jobsearch_plugin_options[$field_name_swich_key]) ? $jobsearch_plugin_options[$field_name_swich_key] : '';
                                                                if ($field_sort_key == 'name' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                                    ?>
                                                                    <li>
                                                                        <label><?php esc_html_e('First Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                               name="user_fullname" type="text"
                                                                               placeholder="<?php esc_html_e('First Name', 'wp-jobsearch') ?>" value="<?php echo ($user_fname) ?>">
                                                                    </li>
                                                                    <li>
                                                                        <label><?php esc_html_e('Last Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                               name="user_surname" type="text"
                                                                               placeholder="<?php esc_html_e('Last Name', 'wp-jobsearch') ?>" value="<?php echo ($user_lname) ?>">
                                                                    </li>
                                                                    <?php
                                                                } else if ($field_sort_key == 'email') {
                                                                    $logedusr_email = '';
                                                                    if (is_user_logged_in()) {
                                                                        $loged_user_obj = wp_get_current_user();
                                                                        $logedusr_email = isset($loged_user_obj->user_email) ? $loged_user_obj->user_email : '';
                                                                    }
                                                                    ?>
                                                                    <li>
                                                                        <label><?php esc_html_e('Email: *', 'wp-jobsearch') ?></label>
                                                                        <input class="required" name="user_email"
                                                                               type="text" <?php if ($logedusr_email != '') { ?> value="<?php echo($logedusr_email) ?>" readonly<?php } ?>
                                                                               placeholder="<?php esc_html_e('Email Address', 'wp-jobsearch') ?>">
                                                                    </li>
                                                                <?php } else if ($field_sort_key == 'phone' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                                    ?>
                                                                    <li>
                                                                        <label><?php esc_html_e('Phone:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                        <?php
                                                                        if ($phone_validation_type == 'on') {
                                                                            $rand_numb = rand(10000000, 99999999);
                                                                            $phone_field_req = false;
                                                                            if ($field_name_swich == 'on_req') {
                                                                                $phone_field_req = true;
                                                                            }
                                                                            jobsearch_phonenum_itltell_input('user_phone', $rand_numb, $user_justphone, array('is_required' => $phone_field_req));
                                                                        } else {
                                                                            ?>
                                                                            <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                                   name="user_phone" type="tel"
                                                                                   placeholder="<?php esc_html_e('Phone Number', 'wp-jobsearch') ?>" value="<?php echo ($user_phone) ?>">
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </li>
                                                                    <?php
                                                                } else if ($field_sort_key == 'current_jobtitle' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                                    ?>
                                                                    <li>
                                                                        <label><?php esc_html_e('Current Job Title:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                               name="user_job_title" type="text"
                                                                               placeholder="<?php esc_html_e('Current Job Title', 'wp-jobsearch') ?>" value="<?php echo ($job_title) ?>">
                                                                    </li>
                                                                    <?php
                                                                } else if ($field_sort_key == 'current_salary' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                                    ?>
                                                                    <li>
                                                                        <label><?php esc_html_e('Current Salary:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                               name="user_salary" type="text"
                                                                               placeholder="<?php esc_html_e('Current Salary', 'wp-jobsearch') ?>" value="<?php echo ($_candidate_salary) ?>">
                                                                    </li>
                                                                    <?php
                                                                } else if ($field_sort_key == 'custom_fields' && $field_name_swich == 'on') {
                                                                    do_action('jobsearch_form_custom_fields_load', $candidate_id, 'candidate');
                                                                } else if ($field_sort_key == 'cv_attach' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                                    $have_resumes = false;
                                                                    if (is_user_logged_in()) {
                                                                        $user_id = get_current_user_id();
                                                                        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                                                                        $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
                                                                        if ($multiple_cv_files_allow == 'on') {
                                                                            $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);

                                                                            if (!empty($ca_at_cv_files)) {
                                                                                $cv_files_count = count($ca_at_cv_files);

                                                                                ob_start();
                                                                                $cvfile_count = 1;
                                                                                foreach ($ca_at_cv_files as $cv_file_key => $cv_file_val) {
                                                                                    $file_attach_id = isset($cv_file_val['file_id']) ? $cv_file_val['file_id'] : '';
                                                                                    $file_url = isset($cv_file_val['file_url']) ? $cv_file_val['file_url'] : '';
                                                                                    $filename = isset($cv_file_val['file_name']) ? $cv_file_val['file_name'] : '';
                                                                                    $filetype = isset($cv_file_val['mime_type']) ? $cv_file_val['mime_type'] : '';
                                                                                    $fileuplod_time = isset($cv_file_val['time']) ? $cv_file_val['time'] : '';
                                                                                    if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment') {
                                                                                        $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                                                                                        $filetype = array('type' => $attach_mime);
                                                                                    }

                                                                                    $cv_file_title = $filename;

                                                                                    $attach_date = $fileuplod_time;
                                                                                    $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';

                                                                                    if ($cvfile_count == 1) {
                                                                                        $cv_primary = 'yes';
                                                                                    } else {
                                                                                        $cv_primary = isset($cv_file_val['primary']) ? $cv_file_val['primary'] : '';
                                                                                    }

                                                                                    if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment') {
                                                                                        $cv_file_title = get_the_title($file_attach_id);
                                                                                        $attach_post = get_post($file_attach_id);
                                                                                        $file_path = get_attached_file($file_attach_id);
                                                                                        $filename = basename($file_path);

                                                                                        $attach_date = isset($attach_post->post_date) ? $attach_post->post_date : '';
                                                                                        $attach_date = strtotime($attach_date);
                                                                                        $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                                                                                    }

                                                                                    if ($attach_mime == 'application/pdf') {
                                                                                        $attach_icon = 'fa fa-file-pdf-o';
                                                                                    } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                                                                        $attach_icon = 'fa fa-file-word-o';
                                                                                    } else if ($attach_mime == 'text/plain') {
                                                                                        $attach_icon = 'fa fa-file-text-o';
                                                                                    } else if ($attach_mime == 'application/vnd.ms-excel' || $attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                                                                        $attach_icon = 'fa fa-file-excel-o';
                                                                                    } else if ($attach_mime == 'image/jpeg' || $attach_mime == 'image/png') {
                                                                                        $attach_icon = 'fa fa-file-image-o';
                                                                                    } else {
                                                                                        $attach_icon = 'fa fa-file-word-o';
                                                                                    }
                                                                                    if (!empty($filetype)) {
                                                                                        $have_resumes = true;
                                                                                        ?>
                                                                                        <li<?php echo($cv_primary == 'yes' ? ' class="active"' : '') ?>>
                                                                                            <i class="<?php echo($attach_icon) ?>"></i>
                                                                                            <label for="cv_file_<?php echo($file_attach_id) ?>">
                                                                                                <input id="cv_file_<?php echo($file_attach_id) ?>"
                                                                                                       type="radio" class="cv_file_item"
                                                                                                       name="ucv_file_item" <?php echo($cv_primary == 'yes' ? 'checked="checked"' : '') ?>
                                                                                                       value="<?php echo($file_attach_id) ?>">
                                                                                                <?php echo(strlen($cv_file_title) > 40 ? substr($cv_file_title, 0, 40) . '...' : $cv_file_title) ?>
                                                                                                <?php
                                                                                                if ($attach_date != '') {
                                                                                                    ?>
                                                                                                    <span class="upload-datetime"><i
                                                                                                                class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), ($attach_date)) . ' ' . date_i18n(get_option('time_format'), ($attach_date)) ?></span>
                                                                                                    <?php
                                                                                                }
                                                                                                ?>
                                                                                            </label>
                                                                                        </li>
                                                                                        <?php
                                                                                    }
                                                                                    $cvfile_count++;
                                                                                }
                                                                                $resumes_list_html = ob_get_clean();
                                                                                ?>
                                                                                <li class="jobsearch-user-form-coltwo-full jobsearch-applyjob-cvslist jobsearch-apply-withcvs"<?php echo ($have_resumes ? '' : ' style="display: none;"') ?>>
                                                                                    <h2><?php esc_html_e('Select CV', 'wp-jobsearch') ?></h2>
                                                                                    <ul class="user-cvs-list">
                                                                                        <?php
                                                                                        echo ($resumes_list_html);
                                                                                        ?>
                                                                                    </ul>
                                                                                </li>
                                                                                <?php
                                                                            }
                                                                        } else {
                                                                            $candidate_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);
                                                                            if (!empty($candidate_cv_file)) {
                                                                                $filename = isset($candidate_cv_file['file_name']) ? $candidate_cv_file['file_name'] : '';
                                                                                $filetype = isset($candidate_cv_file['mime_type']) ? $candidate_cv_file['mime_type'] : '';
                                                                                $fileuplod_time = isset($candidate_cv_file['time']) ? $candidate_cv_file['time'] : '';
                                                                                $file_attach_id = $file_uniqid = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
                                                                                
                                                                                $cv_file_title = $filename;

                                                                                $attach_date = $fileuplod_time;
                                                                                $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';

                                                                                if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment') {
                                                                                    $cv_file_title = get_the_title($file_attach_id);
                                                                                    $attach_post = get_post($file_attach_id);
                                                                                    $file_path = get_attached_file($file_attach_id);
                                                                                    $filename = basename($file_path);

                                                                                    $attach_date = isset($attach_post->post_date) ? $attach_post->post_date : '';
                                                                                    $attach_date = strtotime($attach_date);
                                                                                    $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                                                                                }

                                                                                if ($attach_mime == 'application/pdf') {
                                                                                    $attach_icon = 'fa fa-file-pdf-o';
                                                                                } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                                                                    $attach_icon = 'fa fa-file-word-o';
                                                                                } else if ($attach_mime == 'text/plain') {
                                                                                    $attach_icon = 'fa fa-file-text-o';
                                                                                } else if ($attach_mime == 'application/vnd.ms-excel' || $attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                                                                    $attach_icon = 'fa fa-file-excel-o';
                                                                                } else if ($attach_mime == 'image/jpeg' || $attach_mime == 'image/png') {
                                                                                    $attach_icon = 'fa fa-file-image-o';
                                                                                } else {
                                                                                    $attach_icon = 'fa fa-file-word-o';
                                                                                }
                                                                                ob_start();
                                                                                if (!empty($filetype)) {
                                                                                    $have_resumes = true;
                                                                                    ?>
                                                                                    <li>
                                                                                        <i class="<?php echo($attach_icon) ?>"></i>
                                                                                        <label for="cv_file_<?php echo($file_attach_id) ?>">
                                                                                            <input id="cv_file_<?php echo($file_attach_id) ?>"
                                                                                                   type="radio" class="cv_file_item"
                                                                                                   name="ucv_file_item" checked="checked" 
                                                                                                   value="<?php echo($file_attach_id) ?>">
                                                                                            <?php echo(strlen($cv_file_title) > 40 ? substr($cv_file_title, 0, 40) . '...' : $cv_file_title) ?>
                                                                                            <?php
                                                                                            if ($attach_date != '') {
                                                                                                ?>
                                                                                                <span class="upload-datetime"><i
                                                                                                            class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), ($attach_date)) . ' ' . date_i18n(get_option('time_format'), ($attach_date)) ?></span>
                                                                                                <?php
                                                                                            }
                                                                                            ?>
                                                                                        </label>
                                                                                    </li>
                                                                                    <?php
                                                                                }
                                                                                $resumes_list_html = ob_get_clean();
                                                                                ?>
                                                                                <li class="jobsearch-user-form-coltwo-full jobsearch-applyjob-cvslist jobsearch-apply-withcvs"<?php echo ($have_resumes ? '' : ' style="display: none;"') ?>>
                                                                                    <ul class="user-cvs-list">
                                                                                        <?php
                                                                                        echo ($resumes_list_html);
                                                                                        ?>
                                                                                    </ul>
                                                                                </li>
                                                                                <?php
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <li class="jobsearch-user-form-coltwo-full">
                                                                        <div id="jobsearch-upload-cv-main" class="jobsearch-upload-cv jobsearch-applyjob-upload-cv">
                                                                            <label><?php esc_html_e('Resume', 'wp-jobsearch') ?><?php echo ($field_name_swich == 'on_req' && !$have_resumes ? ' *' : '') ?></label>
                                                                            <div class="jobsearch-drpzon-con jobsearch-drag-dropcustom">
                                                                                <div id="cvFilesDropzone"
                                                                                     class="dropzone"
                                                                                     ondragover="jobsearch_dragover_evnt(event)"
                                                                                     ondragleave="jobsearch_leavedrop_evnt(event)"
                                                                                     ondrop="jobsearch_ondrop_evnt(event)">
                                                                                    <input type="file"
                                                                                           id="cand_cv_filefield"
                                                                                           class="jobsearch-upload-btn <?php echo ($field_name_swich == 'on_req' && !$have_resumes ? 'cv_is_req' : '') ?>"
                                                                                           name="cuser_cv_file"
                                                                                           onchange="jobsearchFileContainerChangeFile(event)">
                                                                                    <div class="fileContainerFileName"
                                                                                         ondrop="jobsearch_ondrop_evnt(event)"
                                                                                         id="fileNameContainer">
                                                                                        <div class="dz-message jobsearch-dropzone-template">
                                                                                            <span class="upload-icon-con"><i
                                                                                                        class="jobsearch-icon jobsearch-upload"></i></span>
                                                                                            <strong><?php esc_html_e('Drop a resume file or click to upload.', 'wp-jobsearch') ?></strong>
                                                                                            <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                                                                                            <div class="upload-or-con">
                                                                                                <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <a class="jobsearch-drpzon-btn"><i
                                                                                                class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload Resume', 'wp-jobsearch') ?>
                                                                                    </a>
                                                                                </div>
                                                                                <script type="text/javascript">
                                                                                    jQuery('#cvFilesDropzone').find('input[name=cuser_cv_file]').css({
                                                                                        position: 'absolute',
                                                                                        width: '100%',
                                                                                        height: '100%',
                                                                                        top: '0',
                                                                                        left: '0',
                                                                                        opacity: '0',
                                                                                        'z-index': '9',
                                                                                    });

                                                                                    function jobsearchFileContainerChangeFile(e) {
                                                                                        var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                                        if (e.target.files.length > 0) {
                                                                                            var slected_file_name = e.target.files[0].name;
                                                                                            the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                                        }
                                                                                        document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                                        try {
                                                                                            droppedFiles = document.getElementById('cand_cv_filefield').files;
                                                                                            document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                                        } catch (error) {
                                                                                            ;
                                                                                        }
                                                                                        try {
                                                                                            aName = document.getElementById('cand_cv_filefield').value;
                                                                                            if (aName !== '') {
                                                                                                document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                                            }
                                                                                        } catch (error) {
                                                                                            ;
                                                                                        }
                                                                                    }

                                                                                    function jobsearch_ondrop_evnt(e) {
                                                                                        var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                                        if (e.target.files.length > 0) {
                                                                                            var slected_file_name = e.target.files[0].name;
                                                                                            the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                                        }
                                                                                        document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                                        try {
                                                                                            droppedFiles = e.dataTransfer.files;
                                                                                            document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                                        } catch (error) {
                                                                                            ;
                                                                                        }
                                                                                    }

                                                                                    function jobsearch_dragover_evnt(e) {
                                                                                        document.getElementById('cvFilesDropzone').classList.add('fileContainerDragOver');
                                                                                        e.preventDefault();
                                                                                        e.stopPropagation();
                                                                                    }

                                                                                    function jobsearch_leavedrop_evnt(e) {
                                                                                        document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                                    }
                                                                                </script>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                            <li class="form-textarea jobsearch-user-form-coltwo-full">
                                                                <label><?php esc_html_e('Message', 'wp-jobsearch') ?>
                                                                    :</label>
                                                                <textarea name="user_msg"
                                                                          placeholder="<?php esc_html_e('Type your Message', 'wp-jobsearch') ?>"></textarea>
                                                            </li>
                                                            <?php
                                                        }
                                                        $cv_html = ob_get_clean();
                                                        echo apply_filters('jobsearch_aply_with_cv_form_cv_field', $cv_html, $p_job_id, $p_rand_id);
                                                        ?>
                                                        <li class="jobsearch-user-form-coltwo-full">
                                                            <input type="hidden" name="job_id"
                                                                   value="<?php echo($p_job_id) ?>">
                                                            <input type="hidden" name="action"
                                                                   value="jobsearch_applying_job_with_email">
                                                            <?php
                                                            jobsearch_terms_and_con_link_txt();
                                                            //
                                                            ob_start();
                                                            ?>
                                                            <div class="terms-priv-chek-con">
                                                                <p><input type="checkbox"
                                                                          name="email_commun_check"> <?php esc_html_e('You accept email communication.', 'wp-jobsearch') ?>
                                                                </p>
                                                            </div>
                                                            <?php
                                                            $accpt_html = ob_get_clean();
                                                            echo apply_filters('jobsearch_jobaply_byemail_comuni_chkhtml', $accpt_html);
                                                            ?>
                                                            <a href="javascript:void(0);"
                                                               class="<?php echo esc_html($p_classes); ?> jobsearch-applyin-withemail"
                                                               data-randid="<?php echo absint($p_rand_id); ?>"
                                                               data-jobid="<?php echo absint($p_job_id); ?>"
                                                               data-btnafterlabel="<?php echo esc_html($p_btn_after_label) ?>"
                                                               data-btnbeforelabel="<?php echo wp_kses($p_btn_text,[]) ?>"><?php echo ($p_btn_text) ?></a>
                                                        </li>
                                                    </ul>
                                                    <div class="apply-job-form-msg"></div>
                                                    <div class="apply-job-loader"></div>
                                                </div>
                                            </form>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $popupp_hmtl = ob_get_clean();
                            echo apply_filters('jobsearch_applyjob_withemail_popup_html', $popupp_hmtl, $popup_args);
                        }, 11, 1);
                    }
                } else if ($job_extrnal_apply_external_switch == 'external' && $job_aply_type == 'external' && $job_aply_extrnal_url != '') {
                    
                    if ($apply_without_login == 'off' && !is_user_logged_in() && $external_signin_switch) {
                        $classes_str = 'jobsearch-open-signin-tab';
                        ?>
                        <a href="javascript:void(0);"
                           class="<?php echo esc_html($classes_str); ?> <?php echo esc_html($classes); ?>"><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?> </a>
                        <?php
                    } else {
                        ?>
                        <a href="<?php echo esc_url_raw($job_aply_extrnal_url) ?>" data-id="<?php echo ($job_id) ?>"
                           class="<?php echo esc_html($classes); ?> jobsearch-extrnal-applybtn-act"
                           target="_blank"><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?></a>
                        <?php
                    }
                } else if ($job_extrnal_apply_internal_switch == 'internal' && $job_aply_type == 'internal') {

                    $this_wredirct_url = jobsearch_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    if ($apply_without_login == 'off' && !is_user_logged_in() && $internal_signin_switch) {

                        $classes_str = 'jobsearch-open-signin-tab';
                        ?><a href="javascript:void(0);"
                             class="<?php echo esc_html($classes_str); ?> <?php echo esc_html($classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?> </a><?php
                    } else {
                        if ($multi_cvs === true) {
                            wp_enqueue_script('dropzone');
                            ?>
                            <script type="text/javascript">
                                jQuery(document).on('click', '.jobsearch-modelcvs-btn-<?php echo($rand_id) ?>', function () {

                                    jobsearch_modal_popup_open('JobSearchModalMultiCVs<?php echo($rand_id) ?>');
                                });
                            </script>
                            <a href="javascript:void(0);"
                               class="<?php echo esc_html($classes); ?> <?php echo($is_applied ? '' : 'jobsearch-modelcvs-btn-' . $rand_id) ?>"><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?></a>
                            <?php
                            $max_cvs_allow = isset($jobsearch_plugin_options['max_cvs_allow']) && absint($jobsearch_plugin_options['max_cvs_allow']) > 0 ? absint($jobsearch_plugin_options['max_cvs_allow']) : 5;
                            $popup_args = array(
                                'p_job_id' => $job_id,
                                'p_rand_id' => $rand_id,
                                'p_btn_text' => $btn_text,
                                'p_classes' => $classes,
                                'p_classes_str' => $classes_str,
                                'p_btn_after_label' => $btn_after_label,
                                'max_cvs_allow' => $max_cvs_allow,
                            );

                            add_action('wp_footer', function () use ($popup_args) {
                                global $jobsearch_plugin_options;
                                $user_id = get_current_user_id();

                                $candidate_id = jobsearch_get_user_candidate_id($user_id);

                                extract(shortcode_atts(array(
                                    'p_job_id' => '',
                                    'p_rand_id' => '',
                                    'p_btn_text' => '',
                                    'p_classes' => '',
                                    'p_classes_str' => '',
                                    'p_btn_after_label' => '',
                                    'max_cvs_allow' => '',
                                ), $popup_args));

                                //
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
                                $cvfile_size = '5120';
                                $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
                                $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
                                if (isset($file_sizes_arr[$cand_cv_file_size])) {
                                    $cvfile_size = $cand_cv_file_size;
                                    $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
                                }
                                $filesize_act = ceil($cvfile_size / 1024);

                                $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

                                if (empty($cand_files_types)) {
                                    $cand_files_types = array(
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/pdf',
                                    );
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
                                    if (in_array($file_typ_key, $cand_files_types)) {
                                        $sutable_files_arr[] = '.' . $file_typ_comar;
                                    }
                                }
                                $sutable_files_str = implode(', ', $sutable_files_arr);
                                $cand_cvr_leter = get_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', true);
                                $cand_resm_coverletr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
                                ?>
                                <div class="jobsearch-modal fade" id="JobSearchModalMultiCVs<?php echo($p_rand_id) ?>">
                                    <div class="modal-inner-area">&nbsp;</div>
                                    <div class="modal-content-area">
                                        <div class="modal-box-area">
                                            <div class="jobsearch-modal-title-box">
                                                <h2><?php esc_html_e('Select CV', 'wp-jobsearch') ?></h2>
                                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                            </div>
                                            <?php
                                            $user_id = get_current_user_id();
                                            $candidate_id = jobsearch_get_user_candidate_id($user_id);
                                            $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);

                                            //
                                            $from_args = array(
                                                'rand_num' => $p_rand_id,
                                                'job_id' => $p_job_id,
                                                'apply_type' => 'internal',
                                            );
                                            do_action('jobsearch_apply_job_internal_bfr_main_html', $from_args);
                                            ?>
                                            <div class="jobsearch-applyjob-internalmain jobsearch-apply-withcvs"<?php echo apply_filters('jobsearch_apply_job_internal_main_tag_exattrs', '', $from_args) ?>>
                                                <?php
                                                $cv_files_count = 0;
                                                if (!empty($ca_at_cv_files)) {
                                                    $cv_files_count = count($ca_at_cv_files);

                                                    $have_resumes = false;
                                                    ob_start();
                                                    $cvfile_count = 1;
                                                    foreach ($ca_at_cv_files as $cv_file_key => $cv_file_val) {
                                                        $file_attach_id = isset($cv_file_val['file_id']) ? $cv_file_val['file_id'] : '';
                                                        $file_url = isset($cv_file_val['file_url']) ? $cv_file_val['file_url'] : '';
                                                        $filename = isset($cv_file_val['file_name']) ? $cv_file_val['file_name'] : '';
                                                        $filetype = isset($cv_file_val['mime_type']) ? $cv_file_val['mime_type'] : '';
                                                        $fileuplod_time = isset($cv_file_val['time']) ? $cv_file_val['time'] : '';
                                                        if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment') {
                                                            $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                                                            $filetype = array('type' => $attach_mime);
                                                        }

                                                        $cv_file_title = $filename;

                                                        $attach_date = $fileuplod_time;
                                                        $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';

                                                        if ($cvfile_count == 1) {
                                                            $cv_primary = 'yes';
                                                        } else {
                                                            $cv_primary = isset($cv_file_val['primary']) ? $cv_file_val['primary'] : '';
                                                        }

                                                        if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment') {
                                                            $cv_file_title = get_the_title($file_attach_id);
                                                            $attach_post = get_post($file_attach_id);
                                                            $file_path = get_attached_file($file_attach_id);
                                                            $filename = basename($file_path);

                                                            $attach_date = isset($attach_post->post_date) ? $attach_post->post_date : '';
                                                            $attach_date = strtotime($attach_date);
                                                            $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                                                        }

                                                        if ($attach_mime == 'application/pdf') {
                                                            $attach_icon = 'fa fa-file-pdf-o';
                                                        } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                                            $attach_icon = 'fa fa-file-word-o';
                                                        } else if ($attach_mime == 'text/plain') {
                                                            $attach_icon = 'fa fa-file-text-o';
                                                        } else if ($attach_mime == 'application/vnd.ms-excel' || $attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                                            $attach_icon = 'fa fa-file-excel-o';
                                                        } else if ($attach_mime == 'image/jpeg' || $attach_mime == 'image/png') {
                                                            $attach_icon = 'fa fa-file-image-o';
                                                        } else {
                                                            $attach_icon = 'fa fa-file-word-o';
                                                        }
                                                        if (!empty($filetype)) {
                                                            $have_resumes = true;
                                                            ?>
                                                            <li<?php echo($cv_primary == 'yes' ? ' class="active"' : '') ?>>
                                                                <i class="<?php echo($attach_icon) ?>"></i>
                                                                <label for="cv_file_<?php echo($file_attach_id) ?>">
                                                                    <input id="cv_file_<?php echo($file_attach_id) ?>"
                                                                           type="radio" class="cv_file_item"
                                                                           name="cv_file_item" <?php echo($cv_primary == 'yes' ? 'checked="checked"' : '') ?>
                                                                           value="<?php echo($file_attach_id) ?>">
                                                                    <?php echo(strlen($cv_file_title) > 40 ? substr($cv_file_title, 0, 40) . '...' : $cv_file_title) ?>
                                                                    <?php
                                                                    if ($attach_date != '') {
                                                                        ?>
                                                                        <span class="upload-datetime"><i
                                                                                    class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), ($attach_date)) . ' ' . date_i18n(get_option('time_format'), ($attach_date)) ?></span>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </label>
                                                            </li>
                                                            <?php
                                                        }
                                                        $cvfile_count++;
                                                    }
                                                    $resumes_list_html = ob_get_clean();
                                                    ?>
                                                    <ul class="user-cvs-list"<?php echo($have_resumes ? '' : ' style="display: none;"') ?>>
                                                        <?php
                                                        echo($resumes_list_html);
                                                        ?>
                                                    </ul>
                                                    <?php
                                                    if (!$have_resumes) {
                                                        ?>
                                                        <div class="user-nocvs-found">
                                                            <p><?php esc_html_e('No resume uploaded.', 'wp-jobsearch') ?></p>
                                                        </div>
                                                        <?php
                                                    }
                                                    if (isset($cv_files_count) && $cv_files_count < $max_cvs_allow) { ?>
                                                        <div class="upload-cvs-sep">
                                                            <div class="jobsearch-box-title">
                                                                <span><?php esc_html_e('OR', 'wp-jobsearch') ?></span>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <ul class="user-cvs-list" style="display: none;"></ul>
                                                    <div class="user-nocvs-found">
                                                        <p><?php esc_html_e('No resume uploaded.', 'wp-jobsearch') ?></p>
                                                    </div>
                                                    <?php
                                                }
                                                if (isset($cv_files_count) && $cv_files_count < $max_cvs_allow) { ?>
                                                    <div class="upload-new-cv-sec">
                                                        <div class="jobsearch-drpzon-con">
                                                            <script type="text/javascript">
                                                                jQuery(document).ready(function () {
                                                                    jQuery('#cvFilesDropzone').dropzone({
                                                                        uploadMultiple: false,
                                                                        maxFiles: 1,
                                                                        <?php
                                                                        if (!empty($cand_files_types)) {
                                                                        ?>
                                                                        acceptedFiles: '<?php echo implode(',', $cand_files_types) ?>',
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                        maxFilesize: <?php echo absint($filesize_act) ?>,
                                                                        paramName: 'on_apply_cv_file',
                                                                        init: function () {
                                                                            this.on("complete", function (file) {
                                                                                //console.log(file);
                                                                                if (file.status == 'success') {
                                                                                    var ajresponse = file.xhr.response;
                                                                                    ajresponse = jQuery.parseJSON(ajresponse);
                                                                                    //console.log(ajresponse);
                                                                                    jQuery('.jobsearch-apply-withcvs .user-cvs-list').append(ajresponse.filehtml);
                                                                                    jQuery('.jobsearch-apply-withcvs .user-cvs-list').removeAttr('style');
                                                                                    jQuery('.jobsearch-apply-withcvs .user-nocvs-found').hide();
                                                                                    jQuery('.jobsearch-apply-withcvs .user-cvs-list li:last-child').find('input').trigger('click');
                                                                                }
                                                                                jQuery('.upload-new-cv-sec .fileUpLoader').html('');
                                                                            });
                                                                        },
                                                                        addedfile: function () {
                                                                            jQuery('.jobsearch-drpzon-con').css({
                                                                                'pointer-events': 'none',
                                                                                'opacity': '0.4'
                                                                            });
                                                                            jQuery('.upload-new-cv-sec .fileUpLoader').html('<i class="fa fa-refresh fa-spin"></i>');
                                                                        }
                                                                    });
                                                                });
                                                            </script>
                                                            <form action="<?php echo admin_url('admin-ajax.php') ?>"
                                                                  id="cvFilesDropzone" method="post" class="dropzone">
                                                                <div class="dz-message jobsearch-dropzone-template">
                                                                    <span class="upload-icon-con"><i
                                                                                class="jobsearch-icon jobsearch-upload"></i></span>
                                                                    <strong><?php esc_html_e('Drop files here to upload.', 'wp-jobsearch') ?></strong>
                                                                    <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                                                                    <div class="upload-or-con">
                                                                        <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                                    </div>
                                                                    <a class="jobsearch-drpzon-btn"><i
                                                                                class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload Resume', 'wp-jobsearch') ?>
                                                                    </a>
                                                                </div>
                                                                <input type="hidden" name="action"
                                                                       value="jobsearch_apply_job_with_cv_file">
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }

                                                //
                                                if ($cand_resm_coverletr == 'on') {
                                                    ?>
                                                    <div class="jobsearch-user-form jobsearch-user-form-coltwo jobsearch-frmfields-sec aply-cvr-letter">
                                                        <ul class="apply-fields-list">
                                                            <li class="form-textarea jobsearch-user-form-coltwo-full">
                                                                <label><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>
                                                                    :</label>
                                                                <textarea name="cand_cover_letter"
                                                                          placeholder="<?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>"><?php echo($cand_cvr_leter) ?></textarea>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <?php
                                                }

                                                echo apply_filters('jobsearch_applying_job_after_cv_upload_file', '');
                                                echo apply_filters('jobsearch_applying_job_before_apply', '');
                                                ?>
                                                <a href="javascript:void(0);"
                                                   class="<?php echo esc_html($p_classes_str); ?> jobsearch-applyjob-btn jobsearch-apply-btn-<?php echo absint($p_rand_id); ?> <?php echo esc_html($p_classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>
                                                   data-randid="<?php echo absint($p_rand_id); ?>"
                                                   data-jobid="<?php echo absint($p_job_id); ?>"
                                                   data-btnafterlabel="<?php echo esc_html($p_btn_after_label) ?>"
                                                   data-btnbeforelabel="<?php echo wp_kses($p_btn_text,[]) ?>"><?php echo ($p_btn_text) ?></a>
                                                <small class="apply-bmsg"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }, 11, 1);
                        } else {

                            $cand_resm_coverletr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
                            //
                            $ferd_classes = '';
                            if ($apply_without_login == 'on' && !is_user_logged_in()) {
                                $ferd_classes = 'jobsearch-nonuser-apply-btn';
                            } else if (!is_user_logged_in()) {
                                $ferd_classes = 'jobsearch-open-signin-tab jobsearch-wredirct-url';
                            }

                            $cand_member_check = true;
                            if (is_user_logged_in()) {
                                if (!jobsearch_user_is_candidate()) {
                                    $cand_member_check = false;
                                }
                            }

                            ob_start();
                            $force_open_apply_popup = apply_filters('jobsearch_job_apply_simple_btn_popopen', false, $job_id);
                            if (($cand_resm_coverletr == 'on' || $force_open_apply_popup === true) && $cand_member_check) { ?>
                                <script type="text/javascript">
                                    jQuery(document).on('click', '.jobsearch-modelsimpapply-btn-<?php echo($rand_id) ?>', function () {
                                        jobsearch_modal_popup_open('JobSearchModalSimpleApply<?php echo($rand_id) ?>');
                                    });
                                </script>
                                <a href="javascript:void(0);"
                                   class="<?php echo esc_html($classes); ?> <?php echo($is_applied || (!is_user_logged_in()) ? '' : 'jobsearch-modelsimpapply-btn-' . $rand_id) ?> <?php echo($ferd_classes) ?>"
                                    <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo ($btn_text) ?></a>

                                <?php
                                if (is_user_logged_in()) {
                                    $popup_args = array(
                                        'p_job_id' => $job_id,
                                        'p_rand_id' => $rand_id,
                                        'p_btn_text' => $btn_text,
                                        'p_classes' => $classes,
                                        'p_classes_str' => $classes_str,
                                        'p_btn_after_label' => $btn_after_label,
                                        'this_wredirct_url' => $this_wredirct_url,
                                        'cand_resm_coverletr' => $cand_resm_coverletr,
                                    );
                                    add_action('wp_footer', function () use ($popup_args) {
                                        global $jobsearch_plugin_options;

                                        $user_id = get_current_user_id();
                                        $candidate_id = jobsearch_get_user_candidate_id($user_id);

                                        extract(shortcode_atts(array(
                                            'p_job_id' => '',
                                            'p_rand_id' => '',
                                            'p_btn_text' => '',
                                            'p_classes' => '',
                                            'p_classes_str' => '',
                                            'p_btn_after_label' => '',
                                            'this_wredirct_url' => '',
                                            'cand_resm_coverletr' => '',
                                        ), $popup_args));


                                        $cand_cvr_leter = get_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', true);
                                        ?>
                                        <div class="jobsearch-modal fade"
                                             id="JobSearchModalSimpleApply<?php echo($p_rand_id) ?>">
                                            <div class="modal-inner-area">&nbsp;</div>
                                            <div class="modal-content-area">
                                                <div class="modal-box-area">
                                                    <span class="modal-close"><i class="fa fa-times"></i></span>
                                                    <?php
                                                    $from_args = array(
                                                        'rand_num' => $p_rand_id,
                                                        'job_id' => $p_job_id,
                                                        'apply_type' => 'internal',
                                                    );
                                                    do_action('jobsearch_apply_job_internal_bfr_main_html', $from_args);
                                                    ?>
                                                    <div class="jobsearch-applyjob-internalmain"<?php echo apply_filters('jobsearch_apply_job_internal_main_tag_exattrs', '', $from_args) ?>>
                                                        <?php
                                                        if ($cand_resm_coverletr == 'on') {
                                                            ?>
                                                            <div class="jobsearch-user-form jobsearch-user-form-coltwo jobsearch-frmfields-sec aply-cvr-letter">
                                                                <ul class="apply-fields-list">
                                                                    <li class="form-textarea jobsearch-user-form-coltwo-full">
                                                                        <label><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>
                                                                            :</label>
                                                                        <textarea name="cand_cover_letter"
                                                                                  placeholder="<?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>"><?php echo($cand_cvr_leter) ?></textarea>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <?php
                                                        }
                                                        echo apply_filters('jobsearch_applying_job_before_apply_single', '');
                                                        ?>
                                                        <a href="javascript:void(0);"
                                                           class="<?php echo esc_html($p_classes_str); ?> jobsearch-apply-btn-<?php echo absint($p_rand_id); ?> <?php echo esc_html($p_classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>
                                                           data-randid="<?php echo absint($p_rand_id); ?>"
                                                           data-jobid="<?php echo absint($p_job_id); ?>"
                                                           data-btnafterlabel="<?php echo esc_html($p_btn_after_label) ?>"
                                                           data-btnbeforelabel="<?php echo wp_kses($p_btn_text,[]) ?>"><?php echo ($p_btn_text) ?></a>
                                                        <small class="apply-bmsg"></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }, 11, 1);
                                }
                                //

                            } else { ?>
                                <a href="javascript:void(0);"
                                   class="<?php echo esc_html($classes_str); ?> jobsearch-apply-btn-<?php echo absint($rand_id); ?> <?php echo esc_html($classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>
                                   data-randid="<?php echo absint($rand_id); ?>"
                                   data-jobid="<?php echo absint($job_id); ?>"
                                   data-btnafterlabel="<?php echo esc_html($btn_after_label) ?>"
                                   data-btnbeforelabel="<?php echo wp_kses($btn_text,[]) ?>"><?php echo(!empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''); ?><?php echo ($btn_text) ?></a>
                                <small class="apply-bmsg"></small>
                                <?php
                            }
                            $appbtn_html = ob_get_clean();
                            echo apply_filters('jobsearch_jobaplybtn_simple_default', $appbtn_html, $classes_str, $rand_id, $classes, $job_id, $btn_after_label, $btn_text);

                            //
                        }
                    }
                }
            }
            $html .= ob_get_clean();
            return $html;
        }

        public function job_application_popup_form($job_id)
        {
            global $jobsearch_plugin_options;
            $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';
            $rand_num = rand(100000, 9999999);
            $apply_without_login = isset($jobsearch_plugin_options['job-apply-without-login']) ? $jobsearch_plugin_options['job-apply-without-login'] : '';
            if ($apply_without_login == 'on' && !is_user_logged_in()) {

                if ($phone_validation_type == 'on') {
                    wp_enqueue_script('jobsearch-intlTelInput');
                }

                $wout_fields_sort = isset($jobsearch_plugin_options['aplywout_login_fields_sort']) ? $jobsearch_plugin_options['aplywout_login_fields_sort'] : '';
                $wout_fields_sort = isset($wout_fields_sort['fields']) ? $wout_fields_sort['fields'] : '';

                $popup_args = array(
                    'job_id' => $job_id,
                    'rand_num' => $rand_num,
                    'wout_fields_sort' => $wout_fields_sort,
                );
                add_action('wp_footer', function () use ($popup_args) {

                    global $jobsearch_plugin_options;

                    extract(shortcode_atts(array(
                        'job_id' => '',
                        'rand_num' => '',
                        'wout_fields_sort' => '',
                    ), $popup_args));

                    $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';

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
                    $cvfile_size = '5120';
                    $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
                    $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
                    if (isset($file_sizes_arr[$cand_cv_file_size])) {
                        $cvfile_size = $cand_cv_file_size;
                        $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
                    }
                    $filesize_act = ceil($cvfile_size / 1024);

                    $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

                    if (empty($cand_files_types)) {
                        $cand_files_types = array(
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/pdf',
                        );
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
                        if (in_array($file_typ_key, $cand_files_types)) {
                            $sutable_files_arr[] = '.' . $file_typ_comar;
                        }
                    }
                    $sutable_files_str = implode(', ', $sutable_files_arr);
                    ?>
                    <div class="jobsearch-modal jobsearch-typo-wrap fade" id="JobSearchNonuserApplyModal">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <div class="jobsearch-modal-title-box">
                                    <h2><?php esc_html_e('Apply for this Job', 'wp-jobsearch') ?></h2>
                                    <span class="modal-close"><i class="fa fa-times"></i></span>
                                </div>

                                <form id="apply-form-<?php echo absint($rand_num) ?>" method="post">
                                    <?php
                                    $from_args = array(
                                        'rand_num' => $rand_num,
                                        'job_id' => $job_id
                                    );
                                    do_action('jobsearch_apply_job_woutreg_in_formtag_html', $from_args);
                                    ?>
                                    <div class="<?php echo apply_filters('jobsearch_apply_job_woutreg_inform_class', 'jobsearch-user-form jobsearch-user-form-coltwo', $from_args) ?>"<?php echo apply_filters('jobsearch_apply_job_woutreg_inform_tag_exattrs', '', $from_args) ?>>
                                        <ul class="apply-fields-list">
                                            <?php
                                            if (isset($wout_fields_sort['name'])) {
                                                foreach ($wout_fields_sort as $field_sort_key => $field_sort_val) {
                                                    $field_name_swich_key = 'aplywout_log_f' . $field_sort_key . '_swch';
                                                    $field_name_swich = isset($jobsearch_plugin_options[$field_name_swich_key]) ? $jobsearch_plugin_options[$field_name_swich_key] : '';
                                                    if ($field_sort_key == 'name' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                        ?>
                                                        <li>
                                                            <label><?php esc_html_e('First Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                            <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                   name="pt_user_fname" type="text"
                                                                   placeholder="<?php esc_html_e('First Name', 'wp-jobsearch') ?>">
                                                        </li>
                                                        <li>
                                                            <label><?php esc_html_e('Last Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                            <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                   name="pt_user_lname" type="text"
                                                                   placeholder="<?php esc_html_e('Last Name', 'wp-jobsearch') ?>">
                                                        </li>
                                                        <?php
                                                    } else if ($field_sort_key == 'email') {
                                                        ?>
                                                        <li>
                                                            <label><?php esc_html_e('Email: *', 'wp-jobsearch') ?></label>
                                                            <input class="required" name="user_email" type="text"
                                                                   placeholder="<?php esc_html_e('Email Address', 'wp-jobsearch') ?>">
                                                        </li>
                                                        <?php
                                                    } else if ($field_sort_key == 'phone' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                        ?>
                                                        <li>
                                                            <label><?php esc_html_e('Phone:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                            <?php
                                                            if ($phone_validation_type == 'on') {
                                                                $rand_numb = rand(100000000, 999999999);
                                                                $itltell_input_ats = array(
                                                                    'sepc_name' => 'user_phone',
                                                                    'set_condial_intrvl' => 'yes',
                                                                );
                                                                jobsearch_phonenum_itltell_input('pt_user_phone', $rand_numb, '', $itltell_input_ats);
                                                            } else {
                                                                ?>
                                                                <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                       name="user_phone" type="tel"
                                                                       placeholder="<?php esc_html_e('Phone Number', 'wp-jobsearch') ?>">
                                                                <?php
                                                            }
                                                            ?>
                                                        </li>
                                                        <?php
                                                    } else if ($field_sort_key == 'current_jobtitle' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                        ?>
                                                        <li>
                                                            <label><?php esc_html_e('Current Job Title:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                            <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                   name="user_job_title" type="text"
                                                                   placeholder="<?php esc_html_e('Current Job Title', 'wp-jobsearch') ?>">
                                                        </li>
                                                        <?php
                                                    } else if ($field_sort_key == 'current_salary' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                        ?>
                                                        <li>
                                                            <label><?php esc_html_e('Current Salary:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                            <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                   name="user_salary" type="text"
                                                                   placeholder="<?php esc_html_e('Current Salary', 'wp-jobsearch') ?>">
                                                        </li>
                                                        <?php
                                                    } else if ($field_sort_key == 'custom_fields' && $field_name_swich == 'on') {
                                                        do_action('jobsearch_form_custom_fields_load', 0, 'candidate');
                                                    } else if ($field_sort_key == 'cv_attach' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                        ?>
                                                        <li class="jobsearch-user-form-coltwo-full">
                                                            <div id="jobsearch-upload-cv-main"
                                                                 class="jobsearch-upload-cv jobsearch-applyjob-upload-cv">
                                                                <label><?php esc_html_e('Resume', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                                <div class="jobsearch-drpzon-con jobsearch-drag-dropcustom">
                                                                    <div id="cvFilesDropzone" class="dropzone"
                                                                         ondragover="jobsearch_dragover_evnt(event)"
                                                                         ondragleave="jobsearch_leavedrop_evnt(event)"
                                                                         ondrop="jobsearch_ondrop_evnt(event)">
                                                                        <input type="file" id="cand_cv_filefield"
                                                                               class="jobsearch-upload-btn <?php echo($field_name_swich == 'on_req' ? 'cv_is_req' : '') ?>"
                                                                               name="cand_woutreg_cv_file"
                                                                               onchange="jobsearchFileContainerChangeFile(event)">
                                                                        <div class="fileContainerFileName"
                                                                             ondrop="jobsearch_ondrop_evnt(event)"
                                                                             id="fileNameContainer">
                                                                            <div class="dz-message jobsearch-dropzone-template">
                                                                                <span class="upload-icon-con"><i
                                                                                            class="jobsearch-icon jobsearch-upload"></i></span>
                                                                                <strong><?php esc_html_e('Drop a resume file or click to upload.', 'wp-jobsearch') ?></strong>
                                                                                <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                                                                                <div class="upload-or-con">
                                                                                    <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <a class="jobsearch-drpzon-btn"><i
                                                                                    class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload Resume', 'wp-jobsearch') ?>
                                                                        </a>
                                                                    </div>
                                                                    <script type="text/javascript">
                                                                        jQuery('#cvFilesDropzone').find('input[name=cand_woutreg_cv_file]').css({
                                                                            position: 'absolute',
                                                                            width: '100%',
                                                                            height: '100%',
                                                                            top: '0',
                                                                            left: '0',
                                                                            opacity: '0',
                                                                            'z-index': '9',
                                                                        });

                                                                        function jobsearchFileContainerChangeFile(e) {
                                                                            var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                            if (e.target.files.length > 0) {
                                                                                var slected_file_name = e.target.files[0].name;
                                                                                the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                            }
                                                                            document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                            try {
                                                                                droppedFiles = document.getElementById('cand_cv_filefield').files;
                                                                                document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                            } catch (error) {
                                                                                ;
                                                                            }
                                                                            try {
                                                                                aName = document.getElementById('cand_cv_filefield').value;
                                                                                if (aName !== '') {
                                                                                    document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                                }
                                                                            } catch (error) {
                                                                                ;
                                                                            }
                                                                        }

                                                                        function jobsearch_ondrop_evnt(e) {
                                                                            var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                            if (e.target.files.length > 0) {
                                                                                var slected_file_name = e.target.files[0].name;
                                                                                the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                            }
                                                                            document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                            try {
                                                                                droppedFiles = e.dataTransfer.files;
                                                                                document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                            } catch (error) {
                                                                                ;
                                                                            }
                                                                        }

                                                                        function jobsearch_dragover_evnt(e) {
                                                                            document.getElementById('cvFilesDropzone').classList.add('fileContainerDragOver');
                                                                            e.preventDefault();
                                                                            e.stopPropagation();
                                                                        }

                                                                        function jobsearch_leavedrop_evnt(e) {
                                                                            document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                        }
                                                                    </script>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <?php
                                                    }
                                                }
                                                do_action('jobsearch_applypop_wout_reg_b4r_cover');
                                                $cand_resm_coverletr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
                                                if ($cand_resm_coverletr == 'on') {
                                                    ?>
                                                    <li class="form-textarea jobsearch-user-form-coltwo-full">
                                                        <label><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>
                                                            :</label>
                                                        <textarea name="cand_cover_letter"
                                                                  placeholder="<?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>"></textarea>
                                                    </li>
                                                    <?php
                                                }
                                            } else {
                                                //
                                            }
                                            ?>
                                            <li class="jobsearch-user-form-coltwo-full">
                                                <input type="hidden" name="action"
                                                       value="<?php echo apply_filters('jobsearch_apply_btn_action_without_reg', 'jobsearch_job_apply_without_login') ?>">
                                                <input type="hidden" name="job_id"
                                                       value="<?php echo absint($job_id) ?>">
                                                <?php jobsearch_terms_and_con_link_txt() ?>
                                                <input class="<?php echo apply_filters('jobsearch_apply_btn_class_without_reg', 'jobsearch-apply-woutreg-btn') ?>"
                                                       data-id="<?php echo absint($rand_num) ?>" type="submit"
                                                       value="<?php esc_html_e('Apply Job', 'wp-jobsearch') ?>">
                                                <div class="form-loader"></div>
                                            </li>
                                        </ul>
                                        <div class="apply-job-form-msg"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                }, 11, 1);
            }
        }

        public function get_upcv_file_info()
        {
            //$att_file_path = jobsearch_cv_attachment_upload_path('candidate_cv_file');
            //var_dump($_FILES['candidate_cv_file']);
            die;
        }

        public function job_apply_without_login()
        {

            global $jobsearch_plugin_options;

            $candidate_auto_approve = isset($jobsearch_plugin_options['candidate_auto_approve']) ? $jobsearch_plugin_options['candidate_auto_approve'] : '';

            $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';

            $job_filled = get_post_meta($job_id, 'jobsearch_field_job_filled', true);
            if ($job_filled == 'on') {
                $response = array();
                $response['error'] = '1';
                $response['msg'] = esc_html__('This job is filled and no longer accepting applications.', 'wp-jobsearch');
                echo json_encode($response);
                wp_die();
            }

            $user_fname = isset($_POST['pt_user_fname']) ? $_POST['pt_user_fname'] : '';
            $user_lname = isset($_POST['pt_user_lname']) ? $_POST['pt_user_lname'] : '';
            $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';

            //
            $field_name_swich = isset($jobsearch_plugin_options['aplywout_log_fname_swch']) ? $jobsearch_plugin_options['aplywout_log_fname_swch'] : '';

            $redirect_url = isset($jobsearch_plugin_options['job_apply_redirect_uri']) ? $jobsearch_plugin_options['job_apply_redirect_uri'] : '';

            $error = 0;

            if ($user_email != '' && $error == 0 && filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                $user_email = esc_html($user_email);
            } else {
                $error = 1;
                $msg = esc_html__('Please Enter a valid email.', 'wp-jobsearch');
            }

            if ($field_name_swich == 'on_req') {
                if ($user_fname != '' && $error == 0) {
                    $user_fname = esc_html($user_fname);
                } else {
                    $error = 1;
                    $msg = esc_html__('Please Enter First Name.', 'wp-jobsearch');
                }
            }

            if ($field_name_swich == 'on_req') {
                if ($user_lname != '' && $error == 0) {
                    $user_lname = esc_html($user_lname);
                } else {
                    $error = 1;
                    $msg = esc_html__('Please Enter your Last Name.', 'wp-jobsearch');
                }
            }

            if ($error == 1) {
                echo json_encode(array('error' => '1', 'msg' => $msg));
                die;
            }

            $email_parts = explode("@", $user_email);
            $user_login = isset($email_parts[0]) ? $email_parts[0] : '';
            if ($user_login != '' && username_exists($user_login)) {
                $user_login .= '_' . rand(10000, 99999);
            }
            if ($user_login == '') {
                $user_login = 'user_' . rand(10000, 99999);
                $user_email = 'user_' . rand(10000, 99999) . '@example.com';
            }

            do_action('jobsearch_applyin_job_wout_reg_bf4_usereg', $job_id);

            $send_reg_email = true;
            $new_user_is_reg = true;
            if (email_exists($user_email)) {
                $send_reg_email = false;
                $new_user_is_reg = false;
                $_user_obj = get_user_by('email', $user_email);
                $candidate_id = jobsearch_get_user_candidate_id($_user_obj->ID);
                if ($candidate_id > 0) {
                    $create_user = $_user_obj->ID;
                } else {
                    echo json_encode(array('error' => '1', 'msg' => __('You are not a candidate. Please try with another email address.', 'wp-jobsearch')));
                    die;
                }
            } else {
                $user_pass = wp_generate_password(12);
                $create_user = wp_create_user($user_login, $user_pass, $user_email);

                if (is_wp_error($create_user)) {

                    $registration_error_messages = $create_user->errors;

                    $display_errors = '';
                    foreach ($registration_error_messages as $error) {
                        $display_errors .= $error[0];
                    }

                    echo json_encode(array('error' => '1', 'msg' => $display_errors));
                    die;
                } else {
                    wp_update_user(array('ID' => $create_user, 'role' => 'jobsearch_candidate'));
                    if ($user_fname != '') {
                        $user_def_array = array(
                            'ID' => $create_user,
                            'first_name' => $user_fname,
                        );
                        if ($user_lname != '') {
                            $user_def_array['last_name'] = $user_lname;
                        }
                        wp_update_user($user_def_array);
                    }
                }
            }

            $_user_obj = get_user_by('ID', $create_user);

            if ($candidate_auto_approve == 'email' || $candidate_auto_approve == 'admin_email') {
                $send_reg_email = false;
                if ($new_user_is_reg) {
                    $verify_code = wp_generate_password(20);
                    $verify_code = str_replace(array('#', '&', '?'), array('-', '_', 'q'), $verify_code);
                    update_user_meta($create_user, 'jobsearch_accaprov_key', $verify_code);
                    update_user_meta($create_user, 'jobsearch_accaprov_allow', '0');
                    do_action('jobsearch_new_candidate_approval', $_user_obj, $user_pass);

                    $user_pass = base64_encode($user_pass);
                    update_user_meta($create_user, 'jobsearch_new_user_regtpass', $user_pass);
                }
            } else {
                wp_set_current_user($_user_obj->ID, $_user_obj->user_login);
                wp_set_auth_cookie($_user_obj->ID);
            }

            $candidate_id = jobsearch_get_user_candidate_id($create_user);

            if ($candidate_id > 0) {

                // For without reg case Remove by superior
                /*$candidate_status = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                if ($candidate_status != 'on') {
                    $response = array();
                    $response['error'] = '1';
                    $response['redrct_uri'] = '';
                    $response['msg'] = esc_html__('Your profile is not approved yet.', 'wp-jobsearch');
                    echo json_encode($response);
                    wp_die();
                }*/

                if ($user_fname != '') {

                    $cand_org_title = $user_fname;
                    if ($user_lname != '') {
                        $cand_org_title .= ' ' . $user_lname;
                    }

                    $cup_post = array(
                        'ID' => $candidate_id,
                        'post_title' => $cand_org_title,
                    );
                    wp_update_post($cup_post);
                }

                if (isset($_POST['user_phone'])) {
                    update_post_meta($candidate_id, 'jobsearch_field_user_phone', $_POST['user_phone']);
                }
                if (isset($_POST['user_job_title'])) {
                    update_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', $_POST['user_job_title']);
                }
                if (isset($_POST['user_salary'])) {
                    update_post_meta($candidate_id, 'jobsearch_field_candidate_salary', $_POST['user_salary']);
                }
                if (isset($_POST['cand_cover_letter']) && $_POST['cand_cover_letter'] != '') {
                    $cover_letter = $_POST['cand_cover_letter'];
                    update_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', $cover_letter);

                    //
                    $job_cver_ltrs = get_post_meta($job_id, 'jobsearch_job_apply_cvrs', true);
                    $job_cver_ltrs = !empty($job_cver_ltrs) ? $job_cver_ltrs : array();
                    $job_cver_ltrs[$candidate_id] = $cover_letter;
                    update_post_meta($job_id, 'jobsearch_job_apply_cvrs', $job_cver_ltrs);
                }

                $atach_url = jobsearch_upload_candidate_cv('cand_woutreg_cv_file', $candidate_id);

                $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';

                if ($atach_url != '') {
                    $file_url = $atach_url;
                    $file_uniqid = uniqid();

                    $filename = basename($file_url);
                    $filetype = wp_check_filetype($filename, null);
                    $fileuplod_time = current_time('timestamp');

                    if ($multiple_cv_files_allow == 'on') {
                        $arg_arr = array(
                            'file_name' => $filename,
                            'mime_type' => $filetype,
                            'time' => $fileuplod_time,
                            'file_url' => $file_url,
                            'file_id' => $file_uniqid,
                            'primary' => '',
                        );
                        $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
                        $ca_jat_cv_files = get_post_meta($candidate_id, 'jobsearch_field_user_cv_attachments', true);
                        $ca_at_cv_files = !empty($ca_at_cv_files) ? $ca_at_cv_files : array();
                        $ca_jat_cv_files = !empty($ca_jat_cv_files) ? $ca_jat_cv_files : array();

                        $ca_at_cv_files[] = $arg_arr;
                        $ca_jat_cv_files[] = $arg_arr;
                        update_post_meta($candidate_id, 'candidate_cv_files', $ca_at_cv_files);
                        update_post_meta($candidate_id, 'jobsearch_field_user_cv_attachments', $ca_jat_cv_files);
                    } else {
                        $arg_arr = array(
                            'file_name' => $filename,
                            'mime_type' => $filetype,
                            'time' => $fileuplod_time,
                            'file_url' => $file_url,
                            'file_id' => $file_uniqid,
                        );
                        update_post_meta($candidate_id, 'candidate_cv_file', $arg_arr);
                        update_post_meta($candidate_id, 'jobsearch_field_user_cv_attachment', $file_url);
                    }
                }

                $cv_field_swich = isset($jobsearch_plugin_options['aplywout_log_fcv_attach_swch']) ? $jobsearch_plugin_options['aplywout_log_fcv_attach_swch'] : '';
                if ($cv_field_swich == 'on_req' && $atach_url == '') {
                    $response = array();
                    $response['error'] = '1';
                    $response['redrct_uri'] = '';
                    $response['msg'] = esc_html__('Resume file is required. Please upload your resume first.', 'wp-jobsearch');
                    echo json_encode($response);
                    wp_die();
                }

                do_action('jobsearch_job_applying_before_action', $candidate_id, $job_id);

                // without login before apply hook
                $withour_args = array(
                    'user_obj' => $_user_obj,
                    'atach_url' => $atach_url,
                    'first_name' => $user_fname,
                    'last_name' => $user_lname,
                    'user_email' => $user_email,
                    'user_phone' => (isset($_POST['user_phone']) ? $_POST['user_phone'] : ''),
                    'job_title' => (isset($_POST['user_job_title']) ? $_POST['user_job_title'] : ''),
                    'current_salary' => (isset($_POST['user_salary']) ? $_POST['user_salary'] : ''),
                );
                do_action('jobsearch_user_wout_login_aply_before', $withour_args);

                do_action('jobsearch_user_wout_reg_aplyin_before', $candidate_id);

                $this->jobsearch_job_apply_by_job_id($job_id, $create_user);

                if ($candidate_auto_approve == 'email' || $candidate_auto_approve == 'admin_email') {
                    echo json_encode(array('error' => '0', 'msg' => __('Applied Successfully. You can view it after logged in your account. Please check your e-mail.', 'wp-jobsearch')));
                    die;
                }

                if (isset($user_pass) && $send_reg_email) {
                    $c_user = get_user_by('email', $user_email);
                    do_action('jobsearch_new_user_register', $c_user, $user_pass);
                }

                echo json_encode(array('error' => '0', 'redrct_uri' => $redirect_url, 'msg' => __('Applied Successfully. You can view it after logged in your account. Also please check your e-mail.', 'wp-jobsearch')));
            } else {
                echo json_encode(array('error' => '1', 'msg' => __('You cannot apply for this job.', 'wp-jobsearch')));
            }
            die;
        }

        public function jobsearch_job_apply_by_job_id($job_id, $user_id = '')
        {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            if ($job_id > 0 && $candidate_id > 0) {

                $default_args = array('status' => 1, 'msg' => '');
                $dealine_response = apply_filters('jobsearch_check_job_deadline_date', $default_args, $job_id);

                $job_filled = get_post_meta($job_id, 'jobsearch_field_job_filled', true);
                if ($job_filled == 'on') {
                    return false;
                }
                if ($dealine_response['status'] == 1) {

                    jobsearch_create_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $user_id);

                    //
                    $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
                    if ($job_applicants_list != '') {
                        $job_applicants_list = explode(',', $job_applicants_list);
                        if (!in_array($candidate_id, $job_applicants_list)) {
                            $job_applicants_list[] = $candidate_id;
                        }
                        $job_applicants_list = implode(',', $job_applicants_list);
                    } else {
                        $job_applicants_list = $candidate_id;
                    }
                    update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);

                    //
                    do_action('jobsearch_job_applying_save_action', $candidate_id, $job_id);

                    $user_obj = get_user_by('ID', $user_id);
                    do_action('jobsearch_job_applied_to_employer', $user_obj, $job_id);
                    do_action('jobsearch_job_applied_to_candidate', $user_obj, $job_id);

                    return $candidate_id;
                }
            }
        }

        public function jobsearch_job_application_submit_callback()
        {
            $job_id = $_REQUEST['job_id'];

            global $jobsearch_plugin_options;
            $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $dash_page_id = jobsearch__get_post_id($user_dashboard_page, 'page');
            $dash_page_url = jobsearch_wpml_lang_page_permalink($dash_page_id, 'page');
            $user = jobsearch_get_user_id();
            $response = array();
            if (isset($user) && $user <> '') {

                $free_job_apply = isset($jobsearch_plugin_options['free-job-apply-allow']) ? $jobsearch_plugin_options['free-job-apply-allow'] : '';
                $candidate_pkgs_page = isset($jobsearch_plugin_options['candidate_package_page']) ? $jobsearch_plugin_options['candidate_package_page'] : '';

                $redirect_url = isset($jobsearch_plugin_options['job_apply_redirect_uri']) ? $jobsearch_plugin_options['job_apply_redirect_uri'] : '';

                $candidate_pkgs_page_url = '';
                if ($candidate_pkgs_page != '') {
                    $candidate_pkgs_page_obj = get_page_by_path($candidate_pkgs_page);
                    if (is_object($candidate_pkgs_page_obj) && isset($candidate_pkgs_page_obj->ID)) {
                        $candidate_pkgs_page_url = get_permalink($candidate_pkgs_page_obj->ID);
                    }
                }

                $candidate_id = jobsearch_get_user_candidate_id($user);
                apply_filters('jobsearch_send_email_cand_working_state', $candidate_id, $job_id);

                if ((isset($job_id) && $job_id <> '') && $candidate_id > 0) {

                    $candidate_skills = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
                    if ($candidate_skills == 'on') {
                        $profile_link = add_query_arg(array('tab' => 'dashboard-settings'), $dash_page_url);
                        $candidate_approve_skill = isset($jobsearch_plugin_options['jobsearch-candidate-skills-percentage']) ? $jobsearch_plugin_options['jobsearch-candidate-skills-percentage'] : 0;
                        $candidate_skill_perc = get_post_meta($candidate_id, 'overall_skills_percentage', true);
                        if (($candidate_approve_skill > 0 && $candidate_skill_perc < $candidate_approve_skill)) {
                            $response['status'] = 0;
                            $response['msg'] = sprintf(__('You must have at least %s profile score to apply for this job. Click <a href="%s">here</a> to complete your profile score.', 'wp-jobsearch'), $candidate_approve_skill . '%', $profile_link);
                            echo json_encode($response);
                            wp_die();
                        }
                    }

                    $default_args = array('status' => 1, 'msg' => '');

                    $job_filled = get_post_meta($job_id, 'jobsearch_field_job_filled', true);
                    if ($job_filled == 'on') {
                        $response['status'] = 0;
                        $response['msg'] = esc_html__('This job is filled and no longer accepting applications.', 'wp-jobsearch');
                        echo json_encode($response);
                        wp_die();
                    }

                    $dealine_response = apply_filters('jobsearch_check_job_deadline_date', $default_args, $job_id);

                    if ($dealine_response['status'] == 1) {

                        $candidate_status = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                        if ($candidate_status != 'on') {
                            $response['status'] = 0;
                            $response['msg'] = esc_html__('Your profile is not approved yet.', 'wp-jobsearch');
                            echo json_encode($response);
                            wp_die();
                        }

                        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
                        $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
                        if ($free_job_apply != 'on' && !in_array($candidate_id, $job_applicants_list)) {
                            $user_app_pkg = jobsearch_candidate_first_subscribed_app_pkg();
                            if (!$user_app_pkg) {
                                $user_app_pkg = jobsearch_candprof_first_pkg_subscribed();
                            }
                            if ($user_app_pkg) {
                                do_action('jobsearch_add_candidate_apply_job_id_to_order', $candidate_id, $user_app_pkg);
                            } else {
                                $response['status'] = 0;
                                if ($candidate_pkgs_page_url != '') {
                                    $response['msg'] = wp_kses(sprintf(__('You have no package. <a href="%s">Click here</a> to subscribe a package.', 'wp-jobsearch'), $candidate_pkgs_page_url), array('a' => array('href' => array())));
                                } else {
                                    $response['msg'] = esc_html__('You have no package. Please subscribe to a package first.', 'wp-jobsearch');
                                }
                                echo json_encode($response);
                                wp_die();
                            }
                        }

                        //
                        do_action('jobsearch_job_applying_before_action', $candidate_id, $job_id);
                        //

                        $job_employer = get_post_meta($job_id, 'jobsearch_job_username', true);

                        jobsearch_create_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $user);

                        //
                        if (!in_array($candidate_id, $job_applicants_list)) {
                            $job_applicants_list[] = $candidate_id;
                        }
                        if (!empty($job_applicants_list)) {
                            $job_applicants_list = implode(',', $job_applicants_list);
                        } else {
                            $job_applicants_list = '';
                        }
                        update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
                        if (isset($_POST['attach_cv']) && $_POST['attach_cv'] > 0) {
                            $get_job_apps_cv_att = get_post_meta($job_id, 'job_apps_cv_att', true);
                            $get_job_apps_cv_att = !empty($get_job_apps_cv_att) ? $get_job_apps_cv_att : array();
                            $get_job_apps_cv_att[$candidate_id] = $_POST['attach_cv'];
                            update_post_meta($job_id, 'job_apps_cv_att', $get_job_apps_cv_att);
                        }

                        if (isset($_POST['cand_cover_letter']) && $_POST['cand_cover_letter'] != '') {
                            $cover_letter = $_POST['cand_cover_letter'];
                            //$cover_letter = sanitize_textarea_field($cover_letter);
                            update_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', $cover_letter);

                            //
                            $job_cver_ltrs = get_post_meta($job_id, 'jobsearch_job_apply_cvrs', true);
                            $job_cver_ltrs = !empty($job_cver_ltrs) ? $job_cver_ltrs : array();
                            $job_cver_ltrs[$candidate_id] = $cover_letter;
                            update_post_meta($job_id, 'jobsearch_job_apply_cvrs', $job_cver_ltrs);
                        }
                        //
                        do_action('jobsearch_job_applying_save_action', $candidate_id, $job_id);
                        //
                        //
                        $c_user = wp_get_current_user();
                        do_action('jobsearch_job_applied_to_employer', $c_user, $job_id);
                        do_action('jobsearch_job_applied_to_candidate', $c_user, $job_id);

                        //
                        do_action('jobsearch_job_applying_after_save_action', $candidate_id, $job_id, $response);

                        $response['status'] = 1;
                        $response['redrct_uri'] = $redirect_url;
                        $response['msg'] = '<i class="icon-thumbsup"></i><span>' . esc_html__('Applied', 'wp-jobsearch') . '</span>';
                        $response['succmsg'] = $redirect_url != '' ? esc_html__('redirecting...', 'wp-jobsearch') : '';
                        echo json_encode($response);
                        wp_die();
                    } else {
                        $response['status'] = 0;
                        $response['msg'] = esc_html__('The application deadline is closed.', 'wp-jobsearch');
                    }
                } else {
                    $response['status'] = 0;
                    $response['msg'] = esc_html__('You are not authorised', 'wp-jobsearch');
                }
            } else {
                $response['status'] = 0;
                $response['msg'] = esc_html__('You have to login first.', 'wp-jobsearch');
            }
            echo json_encode($response);

            wp_die();
        }

        public function job_apply_with_email()
        {
            global $wpdb, $jobsearch_plugin_options, $sitepress;

            $response = array();

            $redirect_url = isset($jobsearch_plugin_options['job_apply_redirect_uri']) ? $jobsearch_plugin_options['job_apply_redirect_uri'] : '';

            $user_name = isset($_POST['user_fullname']) ? $_POST['user_fullname'] : '';
            $user_surname = isset($_POST['user_surname']) ? $_POST['user_surname'] : '';
            $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
            $user_phone = isset($_POST['user_phone']) ? $_POST['user_phone'] : '';
            $user_msg = isset($_POST['user_msg']) ? $_POST['user_msg'] : '';
            $email_commun_check = isset($_POST['email_commun_check']) ? $_POST['email_commun_check'] : '';

            $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';

            if ($job_id > 0) {
                if (email_exists($user_email)) {
                    $_user_obj = get_user_by('email', $user_email);
                    $_user_id = isset($_user_obj->ID) ? $_user_obj->ID : '';
                    if (jobsearch_user_is_candidate($_user_id)) {
                        $_candidate_id = jobsearch_get_user_candidate_id($_user_id);
                    }
                }
                //
                $email_apps_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts AS posts"
                    . " LEFT JOIN $wpdb->postmeta AS postmeta ON(posts.ID = postmeta.post_id) "
                    . " LEFT JOIN $wpdb->postmeta AS mt1 ON(posts.ID = mt1.post_id) "
                    . " WHERE post_type=%s AND (postmeta.meta_key = 'jobsearch_app_user_email' AND postmeta.meta_value = '{$user_email}')"
                    . " AND (mt1.meta_key = 'jobsearch_app_job_id' AND mt1.meta_value = '{$job_id}')", 'email_apps'));
                if ($email_apps_count > 0) {
                    $response['error'] = '1';
                    $response['msg'] = esc_html__('Already applied for this job.', 'wp-jobsearch');
                    echo json_encode($response);
                    wp_die();
                }
                //

                $job_filled = get_post_meta($job_id, 'jobsearch_field_job_filled', true);
                if ($job_filled == 'on') {
                    $response['error'] = '1';
                    $response['msg'] = esc_html__('This job is filled and no longer accepting applications.', 'wp-jobsearch');
                    echo json_encode($response);
                    wp_die();
                }

                $employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                $job_apply_email = get_post_meta($job_id, 'jobsearch_field_job_apply_email', true);
                if ($job_apply_email == '') {
                    $emp_user_id = jobsearch_get_employer_user_id($employer_id);
                    $user_obj = get_user_by('ID', $emp_user_id);
                    $job_apply_email = $user_obj->user_email;
                }
                // cv file
                $att_file_path = '';
                if (is_user_logged_in() && isset($_POST['ucv_file_item']) && $_POST['ucv_file_item'] != '') {
                    $user_id = get_current_user_id();
                    $candidate_id = jobsearch_get_user_candidate_id($user_id);
                    $uplod_dir = wp_upload_dir();
                    $uplod_dirpath = $uplod_dir['basedir'];
                    $selcted_cv_id = $_POST['ucv_file_item'];
                    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
                    if ($multiple_cv_files_allow == 'on') {
                        $ca_at_cv_filess = get_post_meta($candidate_id, 'candidate_cv_files', true);
                        $ca_at_cv_files = array();
                        if (!empty($ca_at_cv_filess)) {
                            foreach ($ca_at_cv_filess as $caat_cv_file) {
                                $ca_at_cv_files[] = $caat_cv_file;
                            }
                        }
                        $attach_key = array_search($selcted_cv_id, array_column($ca_at_cv_files, 'file_id'));
                        $file_url = isset($ca_at_cv_files[$attach_key]['file_url']) ? $ca_at_cv_files[$attach_key]['file_url'] : '';
                        //
                        $sub_file_url = substr($file_url, strpos($file_url, 'jobsearch-resumes/'), strlen($file_url));
                        $att_file_path = $uplod_dirpath . '/' . $sub_file_url;
                    } else {
                        $ca_at_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);
                        $file_url = isset($ca_at_cv_file['file_url']) ? $ca_at_cv_file['file_url'] : '';
                        //
                        $sub_file_url = substr($file_url, strpos($file_url, 'jobsearch-resumes/'), strlen($file_url));
                        $att_file_path = $uplod_dirpath . '/' . $sub_file_url;
                    }
                }
                if (isset($_POST['cv_file_item']) && !empty($_POST['cv_file_item'])) {
                    $selcted_cv_att = $_POST['cv_file_item'];
                    if (is_array($selcted_cv_att)) {
                        add_filter('upload_dir', 'jobsearch_user_upload_files_path');
                        $wp_upload_dir = wp_upload_dir();
                        $upload_file_path = array();
                        foreach ($selcted_cv_att as $sel_cv_att) {
                            $att_file_url = wp_get_attachment_url($sel_cv_att);
                            $upload_file_path = $wp_upload_dir['path'] . '/' . basename($att_file_url);
                            $att_file_path[] = $upload_file_path;
                        }
                        remove_filter('upload_dir', 'jobsearch_user_upload_files_path');
                    } else {
                        $att_file_path = wp_get_attachment_url($selcted_cv_att);
                    }
                }
                if (isset($_FILES['cuser_cv_file']) && !empty($_FILES['cuser_cv_file'])) {
                    $uploded_file = $_FILES['cuser_cv_file'];
                    if (isset($uploded_file['name']) && $uploded_file['name'] != '') {
                        $att_file_path = jobsearch_cv_attachment_upload_path('cuser_cv_file');
                    }
                }
                $cv_field_swich = isset($jobsearch_plugin_options['aplywout_log_fcv_attach_swch']) ? $jobsearch_plugin_options['aplywout_log_fcv_attach_swch'] : '';
                if ($cv_field_swich == 'on_req' && $att_file_path == '') {
                    $response['error'] = '1';
                    $response['redrct_uri'] = '';
                    $response['msg'] = esc_html__('Resume file is required. Please upload your resume first.', 'wp-jobsearch');
                    echo json_encode($response);
                    wp_die();
                }

                do_action('jobsearch_job_applying_before_action', 0, $job_id);

                if (!isset($_COOKIE["jobsearch_email_apply_job_" . $job_id])) {
                    setcookie("jobsearch_email_apply_job_" . $job_id, '1', time() + (86400 * 365), "/");
                }
                
                if (isset($_candidate_id) && $_candidate_id > 0 && $user_msg != '') {
                    $cover_letter = $user_msg;

                    //
                    $job_cver_ltrs = get_post_meta($job_id, 'jobsearch_job_apply_cvrs', true);
                    $job_cver_ltrs = !empty($job_cver_ltrs) ? $job_cver_ltrs : array();
                    $job_cver_ltrs[$_candidate_id] = $cover_letter;
                    update_post_meta($job_id, 'jobsearch_job_apply_cvrs', $job_cver_ltrs);
                }

                $withour_args = array(
                    'user_obj' => false,
                    'atach_url' => $att_file_path,
                    'first_name' => $user_name,
                    'last_name' => $user_surname,
                    'user_email' => $user_email,
                    'user_phone' => (isset($_POST['user_phone']) ? $_POST['user_phone'] : ''),
                    'job_title' => (isset($_POST['user_job_title']) ? $_POST['user_job_title'] : ''),
                    'current_salary' => (isset($_POST['user_salary']) ? $_POST['user_salary'] : ''),
                );
                $att_file_path = apply_filters('jobsearch_with_email_aply_filepath', $att_file_path, $withour_args);
                //
                $apply_data = array(
                    'id' => $job_id,
                    'email' => $job_apply_email,
                    'username' => $user_name,
                    'user_surname' => $user_surname,
                    'user_email' => $user_email,
                    'user_phone' => $user_phone,
                    'user_msg' => $user_msg,
                    '_post_vals' => $_POST,
                    'job_title' => (isset($_POST['user_job_title']) ? $_POST['user_job_title'] : ''),
                    'current_salary' => (isset($_POST['user_salary']) ? $_POST['user_salary'] : ''),
                    'att_file_path' => $att_file_path,
                    'email_commun_check' => $email_commun_check,
                );
                do_action('jobsearch_new_apply_job_by_email', $apply_data);
                do_action('jobsearch_new_apply_by_email_tocand', $apply_data);

                $response['error'] = '0';
                $response['redrct_uri'] = $redirect_url;
                $response['msg'] = esc_html__('Job applied Successfully.', 'wp-jobsearch');

            } else {
                $response['error'] = '1';
                $response['redrct_uri'] = $redirect_url;
                $response['msg'] = esc_html__('No job found.', 'wp-jobsearch');
            }
            echo json_encode($response);
            wp_die();
        }

    }

    global $jobsearch_job_application_load;
    $jobsearch_job_application_load = new Jobsearch_JobApplicationLoad();
}