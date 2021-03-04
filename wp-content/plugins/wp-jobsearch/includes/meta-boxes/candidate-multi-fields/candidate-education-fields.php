<?php
if (!function_exists('jobsearch_candidate_education_fields_save_callback')) {

    function jobsearch_candidate_education_fields_save_callback($post_id) {
        global $pagenow;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (isset($_POST)) {
            if (get_post_type($post_id) == 'candidate' && $pagenow == 'post.php') {
                // services save
                $jobsearch_field_education_title = 'jobsearch_field_education_title';
                $jobsearch_field_education_academy = 'jobsearch_field_education_academy';
                $jobsearch_field_education_start_date = 'jobsearch_field_education_start_date';
                $jobsearch_field_education_end_date = 'jobsearch_field_education_end_date';
                $jobsearch_field_education_date_prsnt = 'jobsearch_field_education_date_prsnt';
                $jobsearch_field_education_description = 'jobsearch_field_education_description';
                $education_title = isset($_POST[$jobsearch_field_education_title]) && !empty($_POST[$jobsearch_field_education_title]) ? $_POST[$jobsearch_field_education_title] : array();
                $education_academy = isset($_POST[$jobsearch_field_education_academy]) && !empty($_POST[$jobsearch_field_education_academy]) ? $_POST[$jobsearch_field_education_academy] : array();
                $education_start_date = isset($_POST[$jobsearch_field_education_start_date]) && !empty($_POST[$jobsearch_field_education_start_date]) ? $_POST[$jobsearch_field_education_start_date] : array();
                $education_end_date = isset($_POST[$jobsearch_field_education_end_date]) && !empty($_POST[$jobsearch_field_education_end_date]) ? $_POST[$jobsearch_field_education_end_date] : array();
                $education_prsnt_date = isset($_POST[$jobsearch_field_education_date_prsnt]) && !empty($_POST[$jobsearch_field_education_date_prsnt]) ? $_POST[$jobsearch_field_education_date_prsnt] : array();
                $education_description = isset($_POST[$jobsearch_field_education_description]) && !empty($_POST[$jobsearch_field_education_description]) ? $_POST[$jobsearch_field_education_description] : array();
                update_post_meta($post_id, $jobsearch_field_education_title, $education_title);
                update_post_meta($post_id, $jobsearch_field_education_academy, $education_academy);
                update_post_meta($post_id, $jobsearch_field_education_start_date, $education_start_date);
                update_post_meta($post_id, $jobsearch_field_education_end_date, $education_end_date);
                update_post_meta($post_id, $jobsearch_field_education_date_prsnt, $education_prsnt_date);
                update_post_meta($post_id, $jobsearch_field_education_description, $education_description);
            }
        }
    }

    add_action('save_post', 'jobsearch_candidate_education_fields_save_callback');
}
if (!function_exists('education_meta_fields_callback')) {

    function education_meta_fields_callback($post) {
        global $jobsearch_form_fields;
        $rand_num = rand(1000000, 99999999);
        wp_enqueue_script('jobsearch-plugin-custom-multi-meta-fields');
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('#education_start_date').datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d'
                });
                jQuery('#education_end_date').datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d'
                });
            });
        </script>
        <div class="jobsearch-educations">
            <div class="jobsearch-elem-heading">
                <h2><?php echo esc_html__('Education', 'wp-jobsearch') ?></h2>
            </div> 
            <div class="jobsearch-bk-multi-fields">
                <div class="multi-list-add">
                    <a class="jobsearch-bk-btn open-add-box" href="javascript:void(0)"><?php esc_html_e('Add Education', 'wp-jobsearch') ?></a>
                </div>
                <div class="multi-list-add-box" style="display:none;">
                    <div class="close-box"><a href="javascript:void(0)"><i class="dashicons dashicons-no-alt"></i></a></div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Title', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'education_title',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Academy', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'education_academy',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Start Date', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'education_start_date',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('End Date', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'education_end_date',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Present', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'education_prsnt_date',
                                'options' => array(
                                    'off' => esc_html__('No', 'wp-jobsearch'),
                                    'on' => esc_html__('Yes', 'wp-jobsearch'),
                                ),
                            );
                            $jobsearch_form_fields->select_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Description', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'education_description',
                            );
                            $jobsearch_form_fields->textarea_field($field_params);
                            ?>
                        </div>
                    </div> 
                    <div class="addto-list-btn"><a id="jobsearch-add-education-exfield" data-id="<?php echo absint($rand_num) ?>" class="jobsearch-bk-btn" href="javascript:void(0)"><?php esc_html_e('Add to List', 'wp-jobsearch') ?></a><span class="ajax-loader"></span></div>
                </div>
                <?php
                $exfield_list = get_post_meta($post->ID, 'jobsearch_field_education_title', true);
                $exfield_list_val = get_post_meta($post->ID, 'jobsearch_field_education_description', true);
                $education_academyfield_list = get_post_meta($post->ID, 'jobsearch_field_education_academy', true);
                $education_start_datefield_list = get_post_meta($post->ID, 'jobsearch_field_education_start_date', true);
                $education_end_datefield_list = get_post_meta($post->ID, 'jobsearch_field_education_end_date', true);
                $education_prsnt_datefield_list = get_post_meta($post->ID, 'jobsearch_field_education_date_prsnt', true);
                ?>
                <ul id="jobsearch-educationfields-con" class="jobsearch-bk-sortable">
                    <?php
                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {
                            $rand_num = rand(1000000, 99999999);

                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                            $education_academyfield_val = isset($education_academyfield_list[$exfield_counter]) ? $education_academyfield_list[$exfield_counter] : '';
                            $education_start_datefield_val = isset($education_start_datefield_list[$exfield_counter]) ? $education_start_datefield_list[$exfield_counter] : '';
                            $education_end_datefield_val = isset($education_end_datefield_list[$exfield_counter]) ? $education_end_datefield_list[$exfield_counter] : '';
                            $education_prsnt_datefield_val = isset($education_prsnt_datefield_list[$exfield_counter]) ? $education_prsnt_datefield_list[$exfield_counter] : '';
                            ?>
                            <li id="list-<?php echo absint($rand_num) ?>">
                                <script>
                                    jQuery(document).ready(function () {
                                        jQuery('#education_start_date_<?php echo absint($rand_num) ?>').datetimepicker({
                                            timepicker: false,
                                            format: 'Y-m-d'
                                        });
                                        jQuery('#education_end_date_<?php echo absint($rand_num) ?>').datetimepicker({
                                            timepicker: false,
                                            format: 'Y-m-d'
                                        });
                                    });
                                </script>
                                <div class="multi-list-header" id="list-head-<?php echo absint($rand_num) ?>">
                                    <ul>
                                        <li class="drag-point"><a><i class="dashicons dashicons-image-flip-vertical"></i></a></li>
                                        <li class="list-title"><?php echo wp_trim_words($exfield, 5, '...') ?></li>
                                        <li class="list-actions">
                                            <a class="list-open" data-visible="close" data-id="<?php echo absint($rand_num) ?>" href="javascript:void(0)"><i class="dashicons dashicons-arrow-down-alt2"></i></a>
                                            <a class="list-delete" data-id="<?php echo absint($rand_num) ?>" href="javascript:void(0)"><i class="dashicons dashicons-trash"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="list-content-<?php echo absint($rand_num) ?>" class="multi-list-content" style="display:none;">
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Title', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'education_title[]',
                                                'force_std' => $exfield,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Academy', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'education_academy[]',
                                                'force_std' => $education_academyfield_val,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Start Date', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'id' => 'education_start_date_' . absint($rand_num),
                                                'name' => 'education_start_date[]',
                                                'force_std' => $education_start_datefield_val,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('End Date', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'id' => 'education_end_date_' . absint($rand_num),
                                                'name' => 'education_end_date[]',
                                                'force_std' => $education_end_datefield_val,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Present', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'education_date_prsnt[]',
                                                'options' => array(
                                                    'off' => esc_html__('No', 'wp-jobsearch'),
                                                    'on' => esc_html__('Yes', 'wp-jobsearch'),
                                                ),
                                                'force_std' => $education_prsnt_datefield_val,
                                            );
                                            $jobsearch_form_fields->select_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Description', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'education_description[]',
                                                'force_std' => $exfield_val,
                                            );
                                            $jobsearch_form_fields->textarea_field($field_params);
                                            ?>
                                        </div>
                                    </div> 
                                    <div class="multi-list-update">
                                        <a class="jobsearch-bk-btn" href="javascript:void(0)"><?php esc_html_e('Update', 'wp-jobsearch') ?></a>
                                    </div>

                                </div>
                            </li>
                            <?php
                            $exfield_counter ++;
                        }
                    }
                    ?>
                </ul>
            </div>

        </div>
        <?php
    }

    add_action('candidate_multi_fields_meta', 'education_meta_fields_callback', 1, 10);
}
if (!function_exists('jobsearch_add_project_educationfield')) {

    /*
     * Doctor extra fields ajax
     * @return html
     */

    function jobsearch_add_project_educationfield($post_id = '', $excerpt_length = '') {
        global $jobsearch_form_fields;
        $title = isset($_POST['education_title']) ? $_POST['education_title'] : '';
        $academy = isset($_POST['education_academy']) ? $_POST['education_academy'] : '';
        $start_date = isset($_POST['education_start_date']) ? $_POST['education_start_date'] : '';
        $end_date = isset($_POST['education_end_date']) ? $_POST['education_end_date'] : '';
        $education_prsnt_datefield_val = isset($_POST['education_prsnt_date']) ? $_POST['education_prsnt_date'] : '';
        $education_description = isset($_POST['education_description']) ? $_POST['education_description'] : '';

        $rand_num = rand(1000000, 99999999);

        ob_start();
        ?>
        <li id="list-<?php echo absint($rand_num) ?>">
            <div class="multi-list-header" id="list-head-<?php echo absint($rand_num) ?>">
                <ul>
                    <li class="drag-point"><a><i class="dashicons dashicons-image-flip-vertical"></i></a></li>
                    <li class="list-title"><?php echo wp_trim_words($title, 5, '...') ?></li>
                    <li class="list-actions">
                        <a class="list-open" data-visible="close" data-id="<?php echo absint($rand_num) ?>" href="javascript:void(0)"><i class="dashicons dashicons-arrow-down-alt2"></i></a>
                        <a class="list-delete" data-id="<?php echo absint($rand_num) ?>" href="javascript:void(0)"><i class="dashicons dashicons-trash"></i></a>
                    </li>
                </ul>
            </div>
            <div id="list-content-<?php echo absint($rand_num) ?>" class="multi-list-content" style="display:none;">
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Title', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'education_title[]',
                            'force_std' => $title,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Academy', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'education_academy[]',
                            'force_std' => $academy,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Start Date', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'education_start_date[]',
                            'force_std' => $start_date,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('End Date', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'education_end_date[]',
                            'force_std' => $end_date,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Present', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'education_date_prsnt[]',
                            'options' => array(
                                'off' => esc_html__('No', 'wp-jobsearch'),
                                'on' => esc_html__('Yes', 'wp-jobsearch'),
                            ),
                            'force_std' => $education_prsnt_datefield_val,
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div>

                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Description', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'education_description[]',
                            'force_std' => $education_description,
                        );
                        $jobsearch_form_fields->textarea_field($field_params);
                        ?>
                    </div>
                </div> 

                <div class="multi-list-update">
                    <a class="jobsearch-bk-btn" href="javascript:void(0)"><?php esc_html_e('Update', 'wp-jobsearch') ?></a>
                </div>
            </div>
        </li>
        <?php
        $html = ob_get_clean();
        echo json_encode(array('html' => $html));
        die;
    }

    add_action('wp_ajax_jobsearch_add_project_educationfield', 'jobsearch_add_project_educationfield');
} 