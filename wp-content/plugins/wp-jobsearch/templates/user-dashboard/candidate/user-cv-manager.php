<?php
global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$candidate_id = jobsearch_get_user_candidate_id($user_id);

$reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

if ($candidate_id > 0) {
    wp_enqueue_script('dropzone');
    $candidate_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);
    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
    $max_cvs_allow = isset($jobsearch_plugin_options['max_cvs_allow']) && absint($jobsearch_plugin_options['max_cvs_allow']) > 0 ? absint($jobsearch_plugin_options['max_cvs_allow']) : 5;
    
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
    
    $filesize_act = ceil($cvfile_size/1024);
    $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
    
    $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';
    if (empty($cand_files_types)) {
        $cand_files_types = array(
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf',
        );
    }
    $cand_files_types_json = json_encode($cand_files_types);
    $sutable_files_arr = array();
    $sutable_files_mimes = array();
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
            $sutable_files_mimes[] = $file_typ_key;
        }
    }
    $sutable_files_str = implode(', ', $sutable_files_arr);
    ?>
    <div class="jobsearch-employer-box-section">
        <div class="jobsearch-profile-title">
            <h2><?php esc_html_e('CV Manager', 'wp-jobsearch') ?></h2>
        </div>
        <?php
        if ($multiple_cv_files_allow == 'on') {
            $cv_files_count = 0;
            ?>
            <div id="com-file-holder">
                <?php
                if (!empty($ca_at_cv_files)) {
                    $att_filecount = 0;
                    $cv_files_count = count($ca_at_cv_files);
                    foreach ($ca_at_cv_files as $cv_file_key => $cv_file_val) {
                        $file_uniqid = isset($cv_file_val['file_id']) ? $cv_file_val['file_id'] : '';
                        $file_url = isset($cv_file_val['file_url']) ? $cv_file_val['file_url'] : '';
                        $filename = isset($cv_file_val['file_name']) ? $cv_file_val['file_name'] : '';
                        $filetype = isset($cv_file_val['mime_type']) ? $cv_file_val['mime_type'] : '';
                        if (is_numeric($file_uniqid) && get_post_type($file_uniqid) == 'attachment') {
                            $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                            $filetype = array('type' => $attach_mime);
                        }
                        $fileuplod_time = isset($cv_file_val['time']) ? $cv_file_val['time'] : '';
                        $cv_primary = isset($cv_file_val['primary']) ? $cv_file_val['primary'] : '';
                        
                        $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_uniqid, $candidate_id);

                        if (!empty($filetype)) {
                            
                            $cv_file_title = $filename;

                            $attach_date = $fileuplod_time;
                            $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';
                            
                            if (is_numeric($file_uniqid) && get_post_type($file_uniqid) == 'attachment') {
                                $cv_file_title = get_the_title($file_uniqid);
                                $attach_post = get_post($file_uniqid);
                                $file_path = get_attached_file($file_uniqid);
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
                            ?>
                            <div class="jobsearch-cv-manager-list">
                                <ul class="jobsearch-row">
                                    <li class="jobsearch-column-12">
                                        <div class="jobsearch-cv-manager-wrap">
                                            <a class="jobsearch-cv-manager-thumb"><i class="<?php echo ($attach_icon) ?>"></i></a>
                                            <div class="jobsearch-cv-manager-text">
                                                <div class="jobsearch-cv-manager-left">
                                                    <h2 class="jobsearch-pst-title"><a href="<?php echo ($file_url) ?>" oncontextmenu="javascript: return false;" onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};" download="<?php echo ($filename) ?>"><?php echo ($filename) ?></a></h2>
                                                    <?php
                                                    if ($attach_date != '') {
                                                        ?>
                                                        <ul>
                                                            <li><i class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), ($attach_date)) . ' ' . date_i18n(get_option('time_format'), ($attach_date)) ?></li>
                                                        </ul>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <a href="javascript:void(0);" class="jobsearch-cv-manager-link jobsearch-del-user-cv" data-id="<?php echo ($file_uniqid) ?>"><i class="jobsearch-icon jobsearch-rubbish"></i></a>
                                                <a href="<?php echo ($file_url) ?>" class="jobsearch-cv-manager-link jobsearch-cv-manager-download" oncontextmenu="javascript: return false;" onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};" download="<?php echo ($filename) ?>"><i class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <?php
                        }
                        $att_filecount++;
                    }
                }
                ?>
            </div>
            <?php
            if (isset($cv_files_count) && $cv_files_count >= $max_cvs_allow) {
                ?>
                <div id="jobsearch-upload-cv-reached" class="jobsearch-upload-cv">
                    <p><?php esc_html_e('You have uploaded maximum CV files. Remove one of your CV files to upload new file.', 'wp-jobsearch') ?></p>
                </div>
                <?php
            } else {
                $max_cvfiles_injs = $max_cvs_allow;
                if ($cv_files_count > 0 && $max_cvs_allow > $cv_files_count) {
                    $max_cvfiles_injs = $max_cvs_allow - $cv_files_count;
                }
                
                //
                echo apply_filters('jobsearch_candash_cvmanage_resmupld_html', '');
                ?>
                <div class="jobsearch-drpzon-con">
                    <script>
                        jQuery(document).ready(function() {
                            jQuery('#cvFilesDropzone').dropzone({
                                url: '<?php echo admin_url('admin-ajax.php') ?>',
                                paramName: 'candidate_cv_file',
                                uploadMultiple: false,
                                maxFiles: <?php echo ($max_cvfiles_injs) ?>,
                                <?php
                                if (!empty($sutable_files_mimes)) {
                                ?>
                                acceptedFiles: '<?php echo implode(',', $sutable_files_mimes) ?>',
                                <?php
                                }
                                ?>
                                maxFilesize: <?php echo absint($filesize_act) ?>,
                                init: function() {
                                    this.on("sending", function(file, xhr, formData) {
                                        formData.append("action", 'jobsearch_dashboard_updating_candidate_cv_file');
                                    });
                                    this.on("complete", function(file) {
                                        //console.log(file);
                                        if (file.status == 'success') {
                                            var ajresponse = file.xhr.response;
                                            ajresponse = jQuery.parseJSON(ajresponse);
                                            //console.log(ajresponse);
                                            jQuery('#com-file-holder').append(ajresponse.filehtml);
                                        }
                                    });
                                },
                                queuecomplete: function() {
                                    window.location.reload(true);
                                }
                            });
                        });
                    </script>
                    <div id="cvFilesDropzone" class="dropzone">
                        <div class="dz-message jobsearch-dropzone-template">
                            <span class="upload-icon-con"><i class="jobsearch-icon jobsearch-upload"></i></span>
                            <strong><?php esc_html_e('Drop files here to upload.', 'wp-jobsearch') ?></strong>
                            <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                            <div class="upload-or-con">
                                <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                            </div>
                            <a class="jobsearch-drpzon-btn"><i class="jobsearch-icon jobsearch-arrows-2"></i> <?php echo apply_filters('jobsearch_cvmanager_upload_resume_txt', esc_html__('Upload Resume', 'wp-jobsearch')) ?></a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div id="jobsearch-upload-cv-main" class="jobsearch-upload-cv" style="display: <?php echo (empty($candidate_cv_file) ? 'block' : 'none') ?>;">
                <?php
                echo apply_filters('jobsearch_candash_cvmanage_resmupld_html', '');
                ?>
                <div class="jobsearch-drpzon-con">
                    <script>
                        jQuery(document).ready(function() {
                            jQuery('#cvFilesDropzone').dropzone({
                                url: '<?php echo admin_url('admin-ajax.php') ?>',
                                paramName: 'candidate_cv_file',
                                uploadMultiple: false,
                                maxFiles: 1,
                                <?php
                                if (!empty($sutable_files_mimes)) {
                                ?>
                                acceptedFiles: '<?php echo implode(',', $sutable_files_mimes) ?>',
                                <?php
                                }
                                ?>
                                maxFilesize: <?php echo absint($filesize_act) ?>,
                                init: function() {
                                    this.on("sending", function(file, xhr, formData) {
                                        formData.append("action", 'jobsearch_dashboard_updating_candidate_cv_file');
                                    });
                                    this.on("complete", function(file) {
                                        //console.log(file);
                                        if (file.status == 'success') {
                                            var ajresponse = file.xhr.response;
                                            ajresponse = jQuery.parseJSON(ajresponse);
                                            //console.log(ajresponse);
                                            jQuery('#com-file-holder').append(ajresponse.filehtml);
                                            //window.location.reload(true);
                                        }
                                    });
                                },
                                queuecomplete: function() {
                                    window.location.reload(true);
                                }
                            });
                        });
                    </script>
                    <div id="cvFilesDropzone" class="dropzone">
                        <div class="dz-message jobsearch-dropzone-template">
                            <span class="upload-icon-con"><i class="jobsearch-icon jobsearch-upload"></i></span>
                            <strong><?php esc_html_e('Drop files here to upload.', 'wp-jobsearch') ?></strong>
                            <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                            <div class="upload-or-con">
                                <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                            </div>
                            <a class="jobsearch-drpzon-btn"><i class="jobsearch-icon jobsearch-arrows-2"></i> <?php echo apply_filters('jobsearch_cvmanager_upload_resume_txt', esc_html__('Upload Resume', 'wp-jobsearch')) ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="com-file-holder">
                <?php
                if (!empty($candidate_cv_file)) {
                    $filename = isset($candidate_cv_file['file_name']) ? $candidate_cv_file['file_name'] : '';
                    $filetype = isset($candidate_cv_file['mime_type']) ? $candidate_cv_file['mime_type'] : '';
                    $fileuplod_time = isset($candidate_cv_file['time']) ? $candidate_cv_file['time'] : '';
                    $file_attach_id = $file_uniqid = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
                    $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';
                    
                    $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_uniqid, $candidate_id);

                    $cv_file_title = $filename;

                    $attach_date = $fileuplod_time;
                    $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';
                    
                    if (is_numeric($file_uniqid) && get_post_type($file_uniqid) == 'attachment') {
                        $filename = basename($file_path);
                        $cv_file_title = get_the_title($file_attach_id);
                        $attach_post = get_post($file_attach_id);
                        $file_path = get_attached_file($file_attach_id);

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

                    if (!empty($candidate_cv_file)) {
                        ?>
                        <div class="jobsearch-cv-manager-list">
                            <ul class="jobsearch-row">
                                <li class="jobsearch-column-12">
                                    <div class="jobsearch-cv-manager-wrap">
                                        <a class="jobsearch-cv-manager-thumb"><i class="<?php echo ($attach_icon) ?>"></i></a>
                                        <div class="jobsearch-cv-manager-text">
                                            <div class="jobsearch-cv-manager-left">
                                                <h2 class="jobsearch-pst-title"><a href="<?php echo ($file_url) ?>" oncontextmenu="javascript: return false;" onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};" download="<?php echo ($filename) ?>"><?php echo ($filename) ?></a></h2>
                                                <?php
                                                if ($attach_date != '') {
                                                    ?>
                                                    <ul>
                                                        <li><i class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), ($attach_date)) . ' ' . date_i18n(get_option('time_format'), ($attach_date)) ?></li>
                                                    </ul>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <a href="javascript:void(0);" class="jobsearch-cv-manager-link jobsearch-del-user-cv" data-id="<?php echo ($file_uniqid) ?>"><i class="jobsearch-icon jobsearch-rubbish"></i></a>
                                            <a href="<?php echo ($file_url) ?>" class="jobsearch-cv-manager-link jobsearch-cv-manager-download" oncontextmenu="javascript: return false;" onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};" download="<?php echo ($filename) ?>"><i class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }
        echo apply_filters('jobsearch_dashboard_after_cv_upload_files', '');
        ?>
    </div>
    <?php
}
