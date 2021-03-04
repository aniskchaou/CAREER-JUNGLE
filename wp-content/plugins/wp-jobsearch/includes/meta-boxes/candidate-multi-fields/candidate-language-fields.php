<?php
if (!function_exists('jobsearch_candidate_lang_fields_save_callback')) {

    function jobsearch_candidate_lang_fields_save_callback($post_id) {
        global $pagenow;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (isset($_POST)) {
            if (get_post_type($post_id) == 'candidate' && $pagenow == 'post.php') {
                // services save
                $jobsearch_field_lang_title = 'jobsearch_field_lang_title'; 
                $jobsearch_field_lang_percentage = 'jobsearch_field_lang_percentage'; 
                $jobsearch_field_lang_level = 'jobsearch_field_lang_level'; 
                $lang_title = isset($_POST[$jobsearch_field_lang_title]) && !empty($_POST[$jobsearch_field_lang_title]) ? $_POST[$jobsearch_field_lang_title] : array();
                $lang_percentage = isset($_POST[$jobsearch_field_lang_percentage]) && !empty($_POST[$jobsearch_field_lang_percentage]) ? $_POST[$jobsearch_field_lang_percentage] : array(); 
                $lang_level = isset($_POST[$jobsearch_field_lang_level]) && !empty($_POST[$jobsearch_field_lang_level]) ? $_POST[$jobsearch_field_lang_level] : array(); 
                update_post_meta($post_id, $jobsearch_field_lang_title, $lang_title);
                update_post_meta($post_id, $jobsearch_field_lang_percentage, $lang_percentage); 
                update_post_meta($post_id, $jobsearch_field_lang_level, $lang_level); 
            }
        }
    }

    add_action('save_post', 'jobsearch_candidate_lang_fields_save_callback');
}
if (!function_exists('lang_meta_fields_callback')) {

    function lang_meta_fields_callback($post) {
        global $jobsearch_form_fields;
        $rand_num = rand(10000000, 99999999); 
        $_post_id = $post->ID;
        wp_enqueue_script('jobsearch-plugin-custom-multi-meta-fields');
        ?>
        <div class="jobsearch-langs">
            <div class="jobsearch-elem-heading">
                <h2><?php echo apply_filters('jobsearch_candadmin_meta_exprtise_title', esc_html__('Languages', 'wp-jobsearch')) ?></h2>
            </div> 
            <div class="jobsearch-bk-multi-fields">
                <?php
                ob_start();
                ?>
                <div class="multi-list-add">
                    <a class="jobsearch-bk-btn open-add-box" href="javascript:void(0)"><?php esc_html_e('Add Language', 'wp-jobsearch') ?></a>
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
                                'id' => 'lang_title',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div> 
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Level', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'lang_level',
                                'options' => array(
                                    'beginner' => esc_html__('Beginner', 'wp-jobsearch'),
                                    'intermediate' => esc_html__('Intermediate', 'wp-jobsearch'),
                                    'proficient' => esc_html__('Proficient', 'wp-jobsearch'),
                                )
                            );
                            $jobsearch_form_fields->select_field($field_params);
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
                                'id' => 'lang_percentage',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>%
                        </div>
                    </div> 
                    <div class="addto-list-btn"><a id="jobsearch-add-lang-exfield" data-id="<?php echo absint($rand_num) ?>" class="jobsearch-bk-btn" href="javascript:void(0)"><?php esc_html_e('Add to List', 'wp-jobsearch') ?></a><span class="ajax-loader"></span></div>
                </div>
                <?php
                $expadd_html = ob_get_clean();
                echo apply_filters('jobsearch_candadmin_meta_exprtise_addform', $expadd_html, $_post_id, $rand_num);
                
                //
                $exfield_list = get_post_meta($post->ID, 'jobsearch_field_lang_title', true); 
                $lang_percentagefield_list = get_post_meta($post->ID, 'jobsearch_field_lang_percentage', true);
                $lang_level_list = get_post_meta($post->ID, 'jobsearch_field_lang_level', true);
                ?>
                <ul id="jobsearch-langfields-con" class="jobsearch-bk-sortable">
                    <?php
                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {
                            $rand_num = rand(1000000, 99999999); 
                            $lang_percentagefield_val = isset($lang_percentagefield_list[$exfield_counter]) ? $lang_percentagefield_list[$exfield_counter] : '';
                            $lang_level = isset($lang_level_list[$exfield_counter]) ? $lang_level_list[$exfield_counter] : '';
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
                                                'name' => 'lang_title[]',
                                                'force_std' => $exfield,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div> 
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Level', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'lang_level[]',
                                                'force_std' => $lang_level,
                                                'options' => array(
                                                    'beginner' => esc_html__('Beginner', 'wp-jobsearch'),
                                                    'intermediate' => esc_html__('Intermediate', 'wp-jobsearch'),
                                                    'proficient' => esc_html__('Proficient', 'wp-jobsearch'),
                                                )
                                            );
                                            $jobsearch_form_fields->select_field($field_params);
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
                                                'name' => 'lang_percentage[]',
                                                'force_std' => $lang_percentagefield_val,
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

    add_action('candidate_multi_fields_meta', 'lang_meta_fields_callback', 1, 10);
}
if (!function_exists('jobsearch_add_project_langfield')) {

    /*
     * Doctor extra fields ajax
     * @return html
     */

    function jobsearch_add_project_langfield($post_id = '', $excerpt_length = '') {
        global $jobsearch_form_fields;
        $title = isset($_POST['lang_title']) ? $_POST['lang_title'] : ''; 
        $percentage = isset($_POST['exlang_percentage']) ? $_POST['exlang_percentage'] : ''; 
        $lang_level = isset($_POST['exlang_level']) ? $_POST['exlang_level'] : '';

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
                            'name' => 'lang_title[]',
                            'force_std' => $title,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div> 
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Level', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'lang_level[]',
                            'force_std' => $lang_level,
                            'options' => array(
                                'beginner' => esc_html__('Beginner', 'wp-jobsearch'),
                                'intermediate' => esc_html__('Intermediate', 'wp-jobsearch'),
                                'proficient' => esc_html__('Proficient', 'wp-jobsearch'),
                            )
                        );
                        $jobsearch_form_fields->select_field($field_params);
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
                            'name' => 'lang_percentage[]',
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

    add_action('wp_ajax_jobsearch_add_project_langfield', 'jobsearch_add_project_langfield');
} 