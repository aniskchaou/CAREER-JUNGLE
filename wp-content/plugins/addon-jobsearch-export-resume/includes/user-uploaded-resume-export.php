<?php
if (!class_exists('Jobsearch_User_Uploaded_CVs_Bulk_Export')) {

    class Jobsearch_User_Uploaded_CVs_Bulk_Export
    {

        public function __construct()
        {
            add_filter('jobsearch_inapplics_bulkexport_dropdwn_itms', array($this, 'inapplics_bulkexport_btn'), 10, 2);
            add_filter('jobsearch_allaplics_front_export_cv_btns', array($this, 'inallapplics_bulkexport_btn'), 10, 2);
            add_filter('jobsearch_allaplics_admin_export_cv_btns', array($this, 'inallapplics_bk_bulkexport_btn'), 10, 2);
            add_action('wp_ajax_jobsearch_downloadin_user_uploded_cvs_bulk', array($this, 'download_user_uploded_cvs_bulk'));
            add_action('init', array($this, 'download_cvs_bulk_zip'));
            add_action('admin_init', array($this, 'admin_download_cvs_bulk_zip'));
        }

        public function inapplics_bulkexport_btn($html, $job_id)
        {
            ob_start();
            ?>
            <li>
                <a href="javascript:void(0);" id="jobsearch-uploaded-cvs-export"
                   data-jid="<?php echo($job_id) ?>"><?php esc_html_e('User Uploaded Resumes', 'jobsearch-resume-export') ?>
                    <span class="cvdownld-loder"></span></a>
            </li>
            <?php
            $html .= ob_get_clean();
            add_action('wp_footer', function () use ($job_id) { ?>
                <script type="text/javascript">
                    jQuery(document).on('click', '#jobsearch-uploaded-cvs-export', function () {
                        var _this = jQuery(this);
                        var loader_con = _this.find('.cvdownld-loder');
                        var job_id = _this.attr('data-jid');

                        var _this_form = jQuery('.jobsearch-applied-jobs');
                        var _post_check_ids = _this_form.find('input[type=checkbox][name^="app_candidate_sel"]');

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

                        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                        var request = jQuery.ajax({
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            method: "POST",
                            data: {
                                job_id: job_id,
                                candidtes_ids: _candidtes_ids,
                                resume_type: 'user_uploaded',
                                action: 'jobsearch_downloadin_user_uploded_cvs_bulk',
                            },
                            dataType: "json"
                        });
                        request.done(function (response) {
                            if (response.html !== undefined) {
                                jQuery('body').append(response.html);
                            }
                        });
                        request.complete(function () {
                            loader_con.html('');
                        });
                        return false;
                    });
                </script>
                <?php
            }, 35);
            return $html;
        }

        public function inallapplics_bulkexport_btn($html, $job_id)
        {
            ob_start();
            ?>
            <a href="javascript:void(0);" id="jobsearch-uploaded-cvs-export-<?php echo($job_id) ?>"
               class="jobsearch-cand-export-uploaded-cvs applicnt-count-box cand-expt-files-btn"
               data-jid="<?php echo($job_id) ?>"><?php esc_html_e('Export Uploaded Resumes', 'jobsearch-resume-export') ?>
                <span class="cvdownld-loder"></span></a>
            <?php
            $html .= ob_get_clean();
            add_action('wp_footer', function () use ($job_id) { ?>
                <script type="text/javascript">
                    jQuery(document).on('click', '#jobsearch-uploaded-cvs-export-<?php echo($job_id) ?>', function () {
                        var _this = jQuery(this);
                        var loader_con = _this.find('.cvdownld-loder');
                        var job_id = _this.attr('data-jid');

                        var _this_form = jQuery('.sjob-aplicants-<?php echo($job_id) ?>');
                        var _post_check_ids = _this_form.find('input[type=checkbox][name^="jobsearch_applicant_id"]');

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

                        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                        var request = jQuery.ajax({
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            method: "POST",
                            data: {
                                job_id: job_id,
                                candidtes_ids: _candidtes_ids,
                                resume_type: 'user_uploaded',
                                action: 'jobsearch_downloadin_user_uploded_cvs_bulk',
                            },
                            dataType: "json"
                        });
                        request.done(function (response) {
                            if (response.html !== undefined) {
                                jQuery('body').append(response.html);
                            }
                        });
                        request.complete(function () {
                            loader_con.html('');
                        });
                        return false;
                    });
                </script>
                <?php
            }, 35);
            return $html;
        }

        public function inallapplics_bk_bulkexport_btn($html, $job_id)
        {
            ob_start();
            ?>
            <li class="admin-applicants-export-options" style="display: none">
                <div class="applicnt-count-box cand-expt-files-btn">
                    <a href="javascript:void(0);" id="jobsearch-uploaded-cvs-export-<?php echo($job_id) ?>"
                       class="jobsearch-cand-export-uploaded-cvs"
                       data-jid="<?php echo($job_id) ?>"><?php esc_html_e('Export Uploaded Resumes', 'jobsearch-resume-export') ?>
                        <span class="cvdownld-loder"></span></a>
                </div>
            </li>
            <?php
            $html .= ob_get_clean();
            add_action('admin_footer', function () use ($job_id) {
                ?>
                <script type="text/javascript">
                    jQuery(document).on('click', '#jobsearch-uploaded-cvs-export-<?php echo($job_id) ?>', function () {
                        var _this = jQuery(this);
                        var loader_con = _this.find('.cvdownld-loder');
                        var job_id = _this.attr('data-jid');

                        var _this_form = jQuery('.sjob-aplicants-<?php echo($job_id) ?>');
                        var _post_check_ids = _this_form.find('input[type=checkbox][name^="jobsearch_applicant_id"]');

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

                        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                        var request = jQuery.ajax({
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            method: "POST",
                            data: {
                                job_id: job_id,
                                candidtes_ids: _candidtes_ids,
                                resume_type: 'user_uploaded',
                                action: 'jobsearch_downloadin_user_uploded_cvs_bulk',
                            },
                            dataType: "json"
                        });
                        request.done(function (response) {
                            if (response.html !== undefined) {
                                jQuery('body').append(response.html);
                            }
                        });
                        request.complete(function () {
                            loader_con.html('');
                        });
                        return false;
                    });
                </script>
                <?php
            }, 35);
            return $html;
        }

        public function candidates_file_paths_arr($candidate_ids)
        {
            $file_paths_arr = array();
            $jobsearch__options = get_option('jobsearch_plugin_options');
            $multiple_cv_files_allow = isset($jobsearch__options['multiple_cv_uploads']) ? $jobsearch__options['multiple_cv_uploads'] : '';
            $uplod_dir = wp_upload_dir();
            $uplod_dirpath = $uplod_dir['basedir'];
            foreach ($candidate_ids as $candidate_id) {
                if ($multiple_cv_files_allow == 'on') {
                    $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);

                    $attach_key = 0;
                    if (!empty($ca_at_cv_files)) {
                        $file_url = '';
                        foreach ($ca_at_cv_files as $ca_at_cv_file) {
                            if (isset($ca_at_cv_file['file_id']) && isset($ca_at_cv_file['file_url']) && $ca_at_cv_file['file_url'] != '') {
                                $file_url = $ca_at_cv_file['file_url'];
                            }
                        }

                        if ($file_url != '') {

                            $in_foldr_file = false;
                            if (strpos($file_url, 'jobsearch-user-files/')) {
                                $in_foldr_file = true;
                                $sub_file_url = substr($file_url, strpos($file_url, 'jobsearch-user-files/'), strlen($file_url));
                            } else if (strpos($file_url, 'jobsearch-resumes/')) {
                                $in_foldr_file = true;
                                $sub_file_url = substr($file_url, strpos($file_url, 'jobsearch-resumes/'), strlen($file_url));
                            }

                            if ($in_foldr_file) {
                                $file_path = $uplod_dirpath . '/' . $sub_file_url;
                            } else {
                                $file_path = str_replace(get_site_url() . '/', ABSPATH, $file_url);
                            }

                            $file_paths_arr[] = $file_path;
                        }
                    }
                } else {

                    $candidate_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);
                    $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';
                    $in_foldr_file = false;

                    if (strpos($file_url, 'jobsearch-user-files/')) {
                        $in_foldr_file = true;
                        $sub_file_url = substr($file_url, strpos($file_url, 'jobsearch-user-files/'), strlen($file_url));
                    } else if (strpos($file_url, 'jobsearch-resumes/')) {
                        $in_foldr_file = true;
                        $sub_file_url = substr($file_url, strpos($file_url, 'jobsearch-resumes/'), strlen($file_url));
                    }

                    if ($in_foldr_file) {
                        $file_path = $uplod_dirpath . '/' . $sub_file_url;
                    } else {
                        $file_path = str_replace(get_site_url() . '/', ABSPATH, $file_url);
                    }
                    $file_paths_arr[] = $file_path;

                }
            }

            return $file_paths_arr;
        }

        public function download_user_uploded_cvs_bulk()
        {
            $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';
            $candidate_ids = isset($_POST['candidtes_ids']) ? $_POST['candidtes_ids'] : '';
            $candidate_ids = $candidate_ids != '' ? explode(',', $candidate_ids) : '';

            $downlod_err = true;
            $user_id = get_current_user_id();

            $cur_user_obj = wp_get_current_user();
            if (jobsearch_user_isemp_member($user_id)) {
                $downlod_err = false;
            }
            $user_is_employer = jobsearch_user_is_employer($user_id);
            if ($user_is_employer) {
                $downlod_err = false;
            }

            if (in_array('administrator', (array)$cur_user_obj->roles)) {
                $downlod_err = false;
            }

            if ($downlod_err) {
                $send_data = array('error' => '1', 'msg' => esc_html__('You are not allowed.', 'wp-jobsearch'));
                wp_send_json($send_data);
            }

            if (!empty($candidate_ids)) {

                $file_paths_arr = $this->candidates_file_paths_arr($candidate_ids);

                global $jobsearch_pdf_temp_upload_file;
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
                $temp_folder = 'user-uploded-cvs-' . (current_time('timestamp') + rand(10000, 99999));

                $make_zip_file = false;
                $file_move_paths = array();
                if (!empty($file_paths_arr)) {
                    foreach ($file_paths_arr as $cvfile_path) {
                        if (file_exists($cvfile_path)) {
                            $make_zip_file = true;
                            $file_move_paths[] = $cvfile_path;
                        }
                    }
                }
                if (!$make_zip_file) {
                    $send_data = array('error' => '1', 'msg' => esc_html__('No attached resume file found for download.', 'wp-jobsearch'));
                    wp_send_json($send_data);
                } else {
                    $temp_folder_dir = $location . $temp_folder;
                    wp_mkdir_p($temp_folder_dir);
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    WP_Filesystem();
                    global $wp_filesystem;

                    foreach ($file_move_paths as $file_move_path) {
                        $move_file_name = basename($file_move_path);
                        $wp_filesystem->copy($file_move_path, $temp_folder_dir . '/' . $move_file_name);
                    }

                    ob_start();
                    ?>
                    <form id="user-resms-uplodedwn-actfrm" method="post">
                        <input type="hidden" name="temp_folder" value="<?php echo($temp_folder_dir) ?>">
                        <input type="hidden" name="job_id" value="<?php echo($job_id) ?>">
                        <input type="hidden" name="candidate_ids"
                               value="<?php echo isset($_POST['candidtes_ids']) ? $_POST['candidtes_ids'] : '' ?>">
                        <input type="hidden" name="jobsearch_resumes_action" value="user_uploaded_resumes_downlodin">
                    </form>
                    <script>document.getElementById("user-resms-uplodedwn-actfrm").submit();</script>
                    <?php
                    $html = ob_get_clean();
                    $send_data = array('error' => '0', 'msg' => esc_html__('Zip file is downloading.', 'wp-jobsearch'), 'html' => $html);
                    wp_send_json($send_data);
                }
            }
            $send_data = array('error' => '1', 'msg' => esc_html__('No record found.', 'wp-jobsearch'));
            wp_send_json($send_data);
        }

        public function download_cvs_bulk_zip()
        {
            if (isset($_POST['jobsearch_resumes_action']) && $_POST['jobsearch_resumes_action'] == 'user_uploaded_resumes_downlodin') {
                $temp_dir = isset($_POST['temp_folder']) ? $_POST['temp_folder'] : '';
                $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';
                $candidate_ids = isset($_POST['candidate_ids']) ? $_POST['candidate_ids'] : '';
                //
                $this->zipFolderResumeExport($temp_dir, $job_id, $candidate_ids);
            }
        }

        public function zipFolderResumeExport($temp_dir, $job_id, $candidate_ids)
        {

            global $jobsearch_resume_export;
            $flag = apply_filters('jobsearch_disable_resume_export', true);

            if ($flag == false) {
                return false;
            }
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            WP_Filesystem();
            global $wp_filesystem;

            if(is_array($candidate_ids)){
                $candidate_ids = $candidate_ids;
            } else {
                $candidate_ids = $candidate_ids != '' ? explode(',', $candidate_ids) : '';
            }
            $file_paths_arr = $this->candidates_file_paths_arr($candidate_ids);
            $file_move_paths = array();
            if (!empty($file_paths_arr)) {
                foreach ($file_paths_arr as $cvfile_path) {
                    if (file_exists($cvfile_path)) {
                        $make_zip_file = true;
                        $file_move_paths[] = $cvfile_path;
                    }
                }
            }


            ob_start();
            if ($job_id > 0 && get_post_type($job_id) == 'job') {
                $job_det = get_post($job_id);

                $zip_file_name = $job_det->post_name . '-uploaded-resumes.zip';
            } else {
                $zip_file_name = 'uploaded-resumes-' . current_time('d-m-Y') . '.zip';
            }
            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                $zip->open($jobsearch_resume_export->jobsearch_resume_export_get_path($zip_file_name), ZipArchive::CREATE | ZipArchive::OVERWRITE);
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($temp_dir), RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($temp_dir) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                $zip->close();
            }

            $zip_path = $jobsearch_resume_export->jobsearch_resume_export_get_path($zip_file_name);

            if (!class_exists('ZipArchive')) {
                foreach ($file_move_paths as $file_move_path) {
                    $move_file_name = basename($file_move_path);
                    self::CompressFiles($temp_dir, $move_file_name, $zip_path, 'zip');
                }
            }

            header("Content-type: application/force-download");
            header("Content-Disposition: attachment; filename=" . $zip_file_name);
            header('Content-Length: ' . filesize($zip_path));
            ob_end_clean();
            readfile($zip_path);
            unlink($zip_path);
            $wp_filesystem->rmdir($temp_dir, true);
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
                        //return $this->SetError('errArchive');
                    }
                    break;
            }
            return $path;
        }

        public function admin_download_cvs_bulk_zip()
        {
            if (isset($_POST['jobsearch_resumes_action']) && $_POST['jobsearch_resumes_action'] == 'candbk_uploaded_resumes_downlodin') {
                $candidate_ids = isset($_POST['candidate_ids']) ? $_POST['candidate_ids'] : '';
                $candidate_ids = $candidate_ids != '' ? explode(',', $candidate_ids) : '';
                if (!empty($candidate_ids)) {
                    $file_paths_arr = $this->candidates_file_paths_arr($candidate_ids);
                    global $jobsearch_pdf_temp_upload_file;
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
                    $temp_folder = 'user-uploded-cvs-' . (current_time('timestamp') + rand(10000, 99999));

                    $make_zip_file = false;
                    $file_move_paths = array();
                    if (!empty($file_paths_arr)) {
                        foreach ($file_paths_arr as $cvfile_path) {
                            if (file_exists($cvfile_path)) {
                                $make_zip_file = true;
                                $file_move_paths[] = $cvfile_path;
                            }
                        }
                    }

                    if ($make_zip_file) {
                        $temp_folder_dir = $location . $temp_folder;
                        wp_mkdir_p($temp_folder_dir);
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                        WP_Filesystem();
                        global $wp_filesystem;
                        foreach ($file_move_paths as $file_move_path) {
                            $move_file_name = basename($file_move_path);
                            $wp_filesystem->copy($file_move_path, $temp_folder_dir . '/' . $move_file_name);
                        }
                        $this->zipFolderResumeExport($temp_folder_dir, 0, $candidate_ids);
                    }
                }
            }
        }
    }
}

new Jobsearch_User_Uploaded_CVs_Bulk_Export();
