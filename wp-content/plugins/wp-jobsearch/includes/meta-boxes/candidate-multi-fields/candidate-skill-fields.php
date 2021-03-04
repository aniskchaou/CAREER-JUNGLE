<?php
if (!function_exists('jobsearch_candidate_skill_fields_save_callback')) {

    function jobsearch_candidate_skill_fields_save_callback($post_id) {
        global $pagenow;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (isset($_POST)) {
            if (get_post_type($post_id) == 'candidate' && $pagenow == 'post.php') {
                // services save
                $jobsearch_field_skill_title = 'jobsearch_field_skill_title'; 
                $jobsearch_field_skill_percentage = 'jobsearch_field_skill_percentage'; 
                $jobsearch_field_skill_desc = 'jobsearch_field_skill_desc'; 
                $skill_title = isset($_POST[$jobsearch_field_skill_title]) && !empty($_POST[$jobsearch_field_skill_title]) ? $_POST[$jobsearch_field_skill_title] : array();
                $skill_percentage = isset($_POST[$jobsearch_field_skill_percentage]) && !empty($_POST[$jobsearch_field_skill_percentage]) ? $_POST[$jobsearch_field_skill_percentage] : array(); 
                $skill_desc = isset($_POST[$jobsearch_field_skill_desc]) && !empty($_POST[$jobsearch_field_skill_desc]) ? $_POST[$jobsearch_field_skill_desc] : array(); 
                update_post_meta($post_id, $jobsearch_field_skill_title, $skill_title);
                update_post_meta($post_id, $jobsearch_field_skill_percentage, $skill_percentage); 
                update_post_meta($post_id, $jobsearch_field_skill_desc, $skill_desc); 
            }
        }
    }

    add_action('save_post', 'jobsearch_candidate_skill_fields_save_callback');
}
if (!function_exists('skill_meta_fields_callback')) {

    function skill_meta_fields_callback($post) {
        global $jobsearch_form_fields;
        $rand_num = rand(10000000, 99999999); 
        $_post_id = $post->ID;
        wp_enqueue_script('jobsearch-plugin-custom-multi-meta-fields');
        ?>
        <div class="jobsearch-skills">
            <div class="jobsearch-elem-heading">
                <h2><?php echo apply_filters('jobsearch_candadmin_meta_exprtise_title', esc_html__('Expertise', 'wp-jobsearch')) ?></h2>
            </div> 
            <div class="jobsearch-bk-multi-fields">
                <?php
                ob_start();
                ?>
                <div class="multi-list-add">
                    <a class="jobsearch-bk-btn open-add-box" href="javascript:void(0)"><?php esc_html_e('Add Expertise', 'wp-jobsearch') ?></a>
                </div>
                <div class="multi-list-add-box" style="display:none;">
                    <div class="close-box"><a href="javascript:void(0)"><i class="dashicons dashicons-no-alt"></i></a></div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Label', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'skill_title',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div> 
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Percentage', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'skill_percentage',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>%
                        </div>
                    </div> 
                    <div class="addto-list-btn"><a id="jobsearch-add-skill-exfield" data-id="<?php echo absint($rand_num) ?>" class="jobsearch-bk-btn" href="javascript:void(0)"><?php esc_html_e('Add to List', 'wp-jobsearch') ?></a><span class="ajax-loader"></span></div>
                </div>
                <?php
                $expadd_html = ob_get_clean();
                echo apply_filters('jobsearch_candadmin_meta_exprtise_addform', $expadd_html, $_post_id, $rand_num);
                
                //
                $exfield_list = get_post_meta($post->ID, 'jobsearch_field_skill_title', true); 
                $skill_percentagefield_list = get_post_meta($post->ID, 'jobsearch_field_skill_percentage', true);
                ?>
                <ul id="jobsearch-skillfields-con" class="jobsearch-bk-sortable">
                    <?php
                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {
                            $rand_num = rand(1000000, 99999999); 
                            $skill_percentagefield_val = isset($skill_percentagefield_list[$exfield_counter]) ? $skill_percentagefield_list[$exfield_counter] : '';
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
                                    <?php
                                    ob_start();
                                    ?>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Label', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'skill_title[]',
                                                'force_std' => $exfield,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div> 
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Percentage', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'skill_percentage[]',
                                                'force_std' => $skill_percentagefield_val,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>%
                                        </div>
                                    </div>
                                    <?php
                                    $expadd_html = ob_get_clean();
                                    echo apply_filters('jobsearch_candadmin_meta_exprtise_updform_fields', $expadd_html, $_post_id, $rand_num, $exfield_counter);
                                    ?>
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

    add_action('candidate_multi_fields_meta', 'skill_meta_fields_callback', 1, 10);
}
if (!function_exists('jobsearch_add_project_skillfield')) {

    /*
     * Doctor extra fields ajax
     * @return html
     */

    function jobsearch_add_project_skillfield($post_id = '', $excerpt_length = '') {
        global $jobsearch_form_fields;
        $title = isset($_POST['skill_title']) ? $_POST['skill_title'] : ''; 
        $percentage = isset($_POST['exskill_percentage']) ? $_POST['exskill_percentage'] : ''; 

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
                            'name' => 'skill_title[]',
                            'force_std' => $title,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div> 
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Percentage', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'skill_percentage[]',
                            'force_std' => $percentage,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>%
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

    add_action('wp_ajax_jobsearch_add_project_skillfield', 'jobsearch_add_project_skillfield');
} 