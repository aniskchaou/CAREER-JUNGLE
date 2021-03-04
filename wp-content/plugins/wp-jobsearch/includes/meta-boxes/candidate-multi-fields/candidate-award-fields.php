<?php
if (!function_exists('jobsearch_candidate_award_fields_save_callback')) {

    function jobsearch_candidate_award_fields_save_callback($post_id) {
        global $pagenow;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (isset($_POST)) {
            if (get_post_type($post_id) == 'candidate' && $pagenow == 'post.php') {
                // services save
                $jobsearch_field_award_title = 'jobsearch_field_award_title'; 
                $jobsearch_field_award_year = 'jobsearch_field_award_year';
                $jobsearch_field_award_description = 'jobsearch_field_award_description';
                $award_title = isset($_POST[$jobsearch_field_award_title]) && !empty($_POST[$jobsearch_field_award_title]) ? $_POST[$jobsearch_field_award_title] : array();
                $award_year = isset($_POST[$jobsearch_field_award_year]) && !empty($_POST[$jobsearch_field_award_year]) ? $_POST[$jobsearch_field_award_year] : array();
                $award_description = isset($_POST[$jobsearch_field_award_description]) && !empty($_POST[$jobsearch_field_award_description]) ? $_POST[$jobsearch_field_award_description] : array();
                update_post_meta($post_id, $jobsearch_field_award_title, $award_title);
                update_post_meta($post_id, $jobsearch_field_award_year, $award_year);
                update_post_meta($post_id, $jobsearch_field_award_description, $award_description);
            }
        }
    }

    add_action('save_post', 'jobsearch_candidate_award_fields_save_callback');
}
if (!function_exists('award_meta_fields_callback')) {

    function award_meta_fields_callback($post) {
        global $jobsearch_form_fields;
        wp_enqueue_script('jobsearch-plugin-custom-multi-meta-fields');
        ?>
        <div class="jobsearch-awards">
            <div class="jobsearch-elem-heading">
                <h2><?php echo esc_html__('Award', 'wp-jobsearch') ?></h2>
            </div> 
            <div class="jobsearch-bk-multi-fields">
                <div class="multi-list-add">
                    <a class="jobsearch-bk-btn open-add-box" href="javascript:void(0)"><?php esc_html_e('Add Award', 'wp-jobsearch') ?></a>
                </div>
                <div class="multi-list-add-box" style="display:none;">
                    <div class="close-box"><a href="javascript:void(0)"><i class="dashicons dashicons-no-alt"></i></a></div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Title *', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'award_title',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div> 
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Year', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'award_year',
                            );
                            $jobsearch_form_fields->input_field($field_params);
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
                                'id' => 'award_description',
                            );
                            $jobsearch_form_fields->textarea_field($field_params);
                            ?>
                        </div>
                    </div> 
                    <div class="addto-list-btn"><a id="jobsearch-add-award-exfield" class="jobsearch-bk-btn" href="javascript:void(0)"><?php esc_html_e('Add to List', 'wp-jobsearch') ?></a><span class="ajax-loader"></span></div>
                </div>
                <?php
                $exfield_list = get_post_meta($post->ID, 'jobsearch_field_award_title', true);
                $exfield_list_val = get_post_meta($post->ID, 'jobsearch_field_award_description', true); 
                $award_yearfield_list = get_post_meta($post->ID, 'jobsearch_field_award_year', true);
                ?>
                <ul id="jobsearch-awardfields-con" class="jobsearch-bk-sortable">
                    <?php
                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {
                            $rand_num = rand(1000000, 99999999);

                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : ''; 
                            $award_yearfield_val = isset($award_yearfield_list[$exfield_counter]) ? $award_yearfield_list[$exfield_counter] : '';
                            ?>
                            <li id="list-<?php echo absint($rand_num) ?>">
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
                                                'name' => 'award_title[]',
                                                'force_std' => $exfield,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div> 
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Year', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'award_year[]',
                                                'force_std' => $award_yearfield_val,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
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
                                                'name' => 'award_description[]',
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

    add_action('candidate_multi_fields_meta', 'award_meta_fields_callback', 1, 10);
}
if (!function_exists('jobsearch_add_project_awardfield')) {

    /*
     * Doctor extra fields ajax
     * @return html
     */

    function jobsearch_add_project_awardfield($post_id = '', $excerpt_length = '') {
        global $jobsearch_form_fields;
        $title = isset($_POST['award_title']) ? $_POST['award_title'] : ''; 
        $year = isset($_POST['exaward_year']) ? $_POST['exaward_year'] : '';
        $award_description = isset($_POST['award_description']) ? $_POST['award_description'] : '';

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
                            'name' => 'award_title[]',
                            'force_std' => $title,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div> 
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Year', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'award_year[]',
                            'force_std' => $year,
                        );
                        $jobsearch_form_fields->input_field($field_params);
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
                            'name' => 'award_description[]',
                            'force_std' => $award_description,
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

    add_action('wp_ajax_jobsearch_add_project_awardfield', 'jobsearch_add_project_awardfield');
} 