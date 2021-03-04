<?php
if (!function_exists('jobsearch_employer_team_fields_save_callback')) {

    function jobsearch_employer_team_fields_save_callback($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (isset($_POST)) {
            if (get_post_type($post_id) == 'employer') {
                // services save
                $jobsearch_field_team_title = 'jobsearch_field_team_title';
                $jobsearch_field_team_designation = 'jobsearch_field_team_designation';
                $jobsearch_field_team_experience = 'jobsearch_field_team_experience';
                $jobsearch_field_team_facebook = 'jobsearch_field_team_facebook';
                $jobsearch_field_team_google = 'jobsearch_field_team_google';
                $jobsearch_field_team_twitter = 'jobsearch_field_team_twitter';
                $jobsearch_field_team_linkedin = 'jobsearch_field_team_linkedin';
                $jobsearch_field_team_description = 'jobsearch_field_team_description';
                $team_title = isset($_POST[$jobsearch_field_team_title]) && !empty($_POST[$jobsearch_field_team_title]) ? $_POST[$jobsearch_field_team_title] : array();
                $team_designation = isset($_POST[$jobsearch_field_team_designation]) && !empty($_POST[$jobsearch_field_team_designation]) ? $_POST[$jobsearch_field_team_designation] : array();
                $team_experience = isset($_POST[$jobsearch_field_team_experience]) && !empty($_POST[$jobsearch_field_team_experience]) ? $_POST[$jobsearch_field_team_experience] : array();
                $team_facebook = isset($_POST[$jobsearch_field_team_facebook]) && !empty($_POST[$jobsearch_field_team_facebook]) ? $_POST[$jobsearch_field_team_facebook] : array();
                $team_google = isset($_POST[$jobsearch_field_team_google]) && !empty($_POST[$jobsearch_field_team_google]) ? $_POST[$jobsearch_field_team_google] : array();
                $team_twitter = isset($_POST[$jobsearch_field_team_twitter]) && !empty($_POST[$jobsearch_field_team_twitter]) ? $_POST[$jobsearch_field_team_twitter] : array();
                $team_linkedin = isset($_POST[$jobsearch_field_team_linkedin]) && !empty($_POST[$jobsearch_field_team_linkedin]) ? $_POST[$jobsearch_field_team_linkedin] : array();
                $team_description = isset($_POST[$jobsearch_field_team_description]) && !empty($_POST[$jobsearch_field_team_description]) ? $_POST[$jobsearch_field_team_description] : array();
                update_post_meta($post_id, $jobsearch_field_team_title, $team_title);
                update_post_meta($post_id, $jobsearch_field_team_designation, $team_designation);
                update_post_meta($post_id, $jobsearch_field_team_experience, $team_experience);
                update_post_meta($post_id, $jobsearch_field_team_facebook, $team_facebook);
                update_post_meta($post_id, $jobsearch_field_team_google, $team_google);
                update_post_meta($post_id, $jobsearch_field_team_twitter, $team_twitter);
                update_post_meta($post_id, $jobsearch_field_team_linkedin, $team_linkedin);
                update_post_meta($post_id, $jobsearch_field_team_description, $team_description);
                
                if (isset($_POST['jobsearch_field_affiliation_title'])) {
                    update_post_meta($post_id, 'jobsearch_field_affiliation_title', $_POST['jobsearch_field_affiliation_title']);
                } else {
                    update_post_meta($post_id, 'jobsearch_field_affiliation_title', '');
                }
                if (isset($_POST['jobsearch_field_affiliation_image'])) {
                    update_post_meta($post_id, 'jobsearch_field_affiliation_image', $_POST['jobsearch_field_affiliation_image']);
                } else {
                    update_post_meta($post_id, 'jobsearch_field_affiliation_image', '');
                }
                
                if (isset($_POST['jobsearch_field_award_title'])) {
                    update_post_meta($post_id, 'jobsearch_field_award_title', $_POST['jobsearch_field_award_title']);
                } else {
                    update_post_meta($post_id, 'jobsearch_field_award_title', '');
                }
                if (isset($_POST['jobsearch_field_award_image'])) {
                    update_post_meta($post_id, 'jobsearch_field_award_image', $_POST['jobsearch_field_award_image']);
                } else {
                    update_post_meta($post_id, 'jobsearch_field_award_image', '');
                }
                
                //
                $get_team_size_count = 0;
                $get_team_size_title = get_post_meta($post_id, $jobsearch_field_team_title, true);
                if (!empty($get_team_size_title)) {
                    $get_team_size_count = count($get_team_size_title);
                }
                update_post_meta($post_id, 'jobsearch_field_employer_team_size', $get_team_size_count);
            }
        }
    }

    add_action('save_post', 'jobsearch_employer_team_fields_save_callback');
}
if (!function_exists('team_meta_fields_callback')) {

    function team_meta_fields_callback($post) {
        global $jobsearch_form_fields;
        wp_enqueue_script('jobsearch-plugin-custom-multi-meta-fields');
        $rand_num = rand(1000000, 99999999);
        ?>
        <div class="jobsearch-teams">
            <div class="jobsearch-elem-heading">
                <h2><?php echo esc_html__('Team Members', 'wp-jobsearch') ?></h2>
            </div> 
            <div class="jobsearch-bk-multi-fields">
                <div class="multi-list-add">
                    <a class="jobsearch-bk-btn open-add-box" href="javascript:void(0)"><?php esc_html_e('Add Team Member', 'wp-jobsearch') ?></a>
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
                                'id' => 'team_title',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div> 
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Designation', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'team_designation',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Experience', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'team_experience',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Profile Image', 'wp-jobsearch') ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'team_image_' . $rand_num,
                            );
                            $jobsearch_form_fields->image_upload_field($field_params);
                            ?> 
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Facebook URL', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'team_facebook',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Google+ URL', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'team_google',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Twitter URL', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'team_twitter',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('LinkedIn URL', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'team_linkedin',
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
                                'id' => 'team_description',
                            );
                            $jobsearch_form_fields->textarea_field($field_params);
                            ?>
                        </div>
                    </div> 
                    <div class="addto-list-btn"><a id="jobsearch-add-team-exfield" data-id="<?php echo absint($rand_num) ?>" class="jobsearch-bk-btn" href="javascript:void(0)"><?php esc_html_e('Add to List', 'wp-jobsearch') ?></a><span class="ajax-loader"></span></div>
                </div>
                <?php
                $exfield_list = get_post_meta($post->ID, 'jobsearch_field_team_title', true);
                $exfield_list_val = get_post_meta($post->ID, 'jobsearch_field_team_description', true);
                $team_designationfield_list = get_post_meta($post->ID, 'jobsearch_field_team_designation', true);
                $team_experiencefield_list = get_post_meta($post->ID, 'jobsearch_field_team_experience', true);
                $team_imagefield_list = get_post_meta($post->ID, 'jobsearch_field_team_image', true);
                $team_facebookfield_list = get_post_meta($post->ID, 'jobsearch_field_team_facebook', true);
                $team_googlefield_list = get_post_meta($post->ID, 'jobsearch_field_team_google', true);
                $team_twitterfield_list = get_post_meta($post->ID, 'jobsearch_field_team_twitter', true);
                $team_linkedinfield_list = get_post_meta($post->ID, 'jobsearch_field_team_linkedin', true);
                ?>
                <ul id="jobsearch-teamfields-con" class="jobsearch-bk-sortable">
                    <?php
                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {
                            $rand_num = rand(1000000, 99999999);

                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                            $team_designationfield_val = isset($team_designationfield_list[$exfield_counter]) ? $team_designationfield_list[$exfield_counter] : '';
                            $team_experiencefield_val = isset($team_experiencefield_list[$exfield_counter]) ? $team_experiencefield_list[$exfield_counter] : '';
                            $team_imagefield_val = isset($team_imagefield_list[$exfield_counter]) ? $team_imagefield_list[$exfield_counter] : '';
                            $team_facebookfield_val = isset($team_facebookfield_list[$exfield_counter]) ? $team_facebookfield_list[$exfield_counter] : '';
                            $team_googlefield_val = isset($team_googlefield_list[$exfield_counter]) ? $team_googlefield_list[$exfield_counter] : '';
                            $team_twitterfield_val = isset($team_twitterfield_list[$exfield_counter]) ? $team_twitterfield_list[$exfield_counter] : '';
                            $team_linkedinfield_val = isset($team_linkedinfield_list[$exfield_counter]) ? $team_linkedinfield_list[$exfield_counter] : '';
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
                                                'name' => 'team_title[]',
                                                'force_std' => $exfield,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div> 
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Designation', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'team_designation[]',
                                                'force_std' => $team_designationfield_val,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Experience', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'team_experience[]',
                                                'force_std' => $team_experiencefield_val,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Profile Image', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'id' => 'team_image_' . $rand_num . $exfield_counter,
                                                'name' => 'team_image[]',
                                                'force_std' => $team_imagefield_val,
                                            );
                                            $jobsearch_form_fields->image_upload_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Facebook URL', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'team_facebook[]',
                                                'force_std' => $team_facebookfield_val,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Google+ URL', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'team_google[]',
                                                'force_std' => $team_googlefield_val,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Twitter URL', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'team_twitter[]',
                                                'force_std' => $team_twitterfield_val,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('LinkedIn URL', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'name' => 'team_linkedin[]',
                                                'force_std' => $team_linkedinfield_val,
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
                                                'name' => 'team_description[]',
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
        
        //
        ob_start();
        $rand_num = rand(10000000, 99999999);
        ?>
        <div class="jobsearch-teams">
            <div class="jobsearch-elem-heading">
                <h2><?php echo esc_html__('Awards', 'wp-jobsearch') ?></h2>
            </div> 
            <div class="jobsearch-bk-multi-fields">
                <div class="multi-list-add">
                    <a class="jobsearch-bk-btn open-add-box" href="javascript:void(0)"><?php esc_html_e('Add Award', 'wp-jobsearch') ?></a>
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
                                'id' => 'award_title',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Image', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'award_image_' . $rand_num,
                            );
                            $jobsearch_form_fields->image_upload_field($field_params);
                            ?> 
                        </div>
                    </div>
                    <div class="addto-list-btn"><a id="jobsearch-add-empaward-exfield" data-id="<?php echo absint($rand_num) ?>" class="jobsearch-bk-btn" href="javascript:void(0)"><?php esc_html_e('Add to List', 'wp-jobsearch') ?></a><span class="ajax-loader"></span></div>
                </div>
                <?php
                $exfield_list = get_post_meta($post->ID, 'jobsearch_field_award_title', true);
                $award_imagefield_list = get_post_meta($post->ID, 'jobsearch_field_award_image', true);
                ?>
                <ul id="jobsearch-awardfields-con" class="jobsearch-bk-sortable">
                    <?php
                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {
                            $rand_num = rand(1000000, 99999999);

                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                            $award_imagefield_val = isset($award_imagefield_list[$exfield_counter]) ? $award_imagefield_list[$exfield_counter] : '';
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
                                            <label><?php esc_html_e('Image', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'id' => 'award_image_' . $rand_num . $exfield_counter,
                                                'name' => 'award_image[]',
                                                'force_std' => $award_imagefield_val,
                                            );
                                            $jobsearch_form_fields->image_upload_field($field_params);
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
        $awards_html = ob_get_clean();
        echo apply_filters('jobsearch_emp_meta_bx_awards_list', $awards_html);
        
        //
        ob_start();
        $rand_num = rand(10000000, 99999999);
        ?>
        <div class="jobsearch-teams">
            <div class="jobsearch-elem-heading">
                <h2><?php echo esc_html__('Affiliations', 'wp-jobsearch') ?></h2>
            </div> 
            <div class="jobsearch-bk-multi-fields">
                <div class="multi-list-add">
                    <a class="jobsearch-bk-btn open-add-box" href="javascript:void(0)"><?php esc_html_e('Add Affiliation', 'wp-jobsearch') ?></a>
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
                                'id' => 'affiliation_title',
                            );
                            $jobsearch_form_fields->input_field($field_params);
                            ?>
                        </div>
                    </div>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Image', 'wp-jobsearch') ?> *</label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'id' => 'affiliation_image_' . $rand_num,
                            );
                            $jobsearch_form_fields->image_upload_field($field_params);
                            ?> 
                        </div>
                    </div>
                    <div class="addto-list-btn"><a id="jobsearch-add-empaffiliation-exfield" data-id="<?php echo absint($rand_num) ?>" class="jobsearch-bk-btn" href="javascript:void(0)"><?php esc_html_e('Add to List', 'wp-jobsearch') ?></a><span class="ajax-loader"></span></div>
                </div>
                <?php
                $exfield_list = get_post_meta($post->ID, 'jobsearch_field_affiliation_title', true);
                $affiliation_imagefield_list = get_post_meta($post->ID, 'jobsearch_field_affiliation_image', true);
                ?>
                <ul id="jobsearch-affiliationfields-con" class="jobsearch-bk-sortable">
                    <?php
                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {
                            $rand_num = rand(1000000, 99999999);

                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                            $affiliation_imagefield_val = isset($affiliation_imagefield_list[$exfield_counter]) ? $affiliation_imagefield_list[$exfield_counter] : '';
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
                                                'name' => 'affiliation_title[]',
                                                'force_std' => $exfield,
                                            );
                                            $jobsearch_form_fields->input_field($field_params);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jobsearch-element-field">
                                        <div class="elem-label">
                                            <label><?php esc_html_e('Image', 'wp-jobsearch') ?></label>
                                        </div>
                                        <div class="elem-field">
                                            <?php
                                            $field_params = array(
                                                'id' => 'affiliation_image_' . $rand_num . $exfield_counter,
                                                'name' => 'affiliation_image[]',
                                                'force_std' => $affiliation_imagefield_val,
                                            );
                                            $jobsearch_form_fields->image_upload_field($field_params);
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
        $affil_html = ob_get_clean();
        echo apply_filters('jobsearch_emp_meta_bx_affiliations_list', $affil_html);
    }

    add_action('employer_multi_fields_meta', 'team_meta_fields_callback', 1, 10);
}

if (!function_exists('jobsearch_add_project_teamfield')) {
    
    function jobsearch_add_project_teamfield($post_id = '', $excerpt_length = '') {
        global $jobsearch_form_fields;
        $title = isset($_POST['team_title']) ? $_POST['team_title'] : '';
        $designation = isset($_POST['exteam_designation']) ? $_POST['exteam_designation'] : '';
        $experience = isset($_POST['exteam_experience']) ? $_POST['exteam_experience'] : '';
        $team_image = isset($_POST['exteam_image']) ? $_POST['exteam_image'] : '';
        $facebook = isset($_POST['exteam_facebook']) ? $_POST['exteam_facebook'] : '';
        $google = isset($_POST['exteam_google']) ? $_POST['exteam_google'] : '';
        $twitter = isset($_POST['exteam_twitter']) ? $_POST['exteam_twitter'] : '';
        $linkedin = isset($_POST['exteam_linkedin']) ? $_POST['exteam_linkedin'] : '';
        $team_description = isset($_POST['team_description']) ? $_POST['team_description'] : '';

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
                            'name' => 'team_title[]',
                            'force_std' => $title,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div> 
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Designation', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'team_designation[]',
                            'force_std' => $designation,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Experience', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'team_experience[]',
                            'force_std' => $experience,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Image', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'id' => 'team_image_' . $rand_num,
                            'name' => 'team_image[]',
                            'force_std' => $team_image,
                        );
                        $jobsearch_form_fields->image_upload_field($field_params);
                        ?>
                    </div>
                </div>

                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Facebook URL', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'team_facebook[]',
                            'force_std' => $facebook,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Google+ URL', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'team_google[]',
                            'force_std' => $google,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Twitter URL', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'team_twitter[]',
                            'force_std' => $twitter,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('LinkedIn URL', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'team_linkedin[]',
                            'force_std' => $linkedin,
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
                            'name' => 'team_description[]',
                            'force_std' => $team_description,
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

    add_action('wp_ajax_jobsearch_add_project_teamfield', 'jobsearch_add_project_teamfield');
}

if (!function_exists('jobsearch_add_projectemp_awardfield')) {
    
    function jobsearch_add_projectemp_awardfield($post_id = '', $excerpt_length = '') {
        global $jobsearch_form_fields;
        $title = isset($_POST['award_title']) ? $_POST['award_title'] : '';
        $award_image = isset($_POST['exaward_image']) ? $_POST['exaward_image'] : '';

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
                        <label><?php esc_html_e('Image', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'id' => 'award_image_' . $rand_num,
                            'name' => 'award_image[]',
                            'force_std' => $award_image,
                        );
                        $jobsearch_form_fields->image_upload_field($field_params);
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

    add_action('wp_ajax_jobsearch_add_projectemp_awardfield', 'jobsearch_add_projectemp_awardfield');
}

if (!function_exists('jobsearch_add_projectemp_affiliationfield')) {
    
    function jobsearch_add_projectemp_affiliationfield($post_id = '', $excerpt_length = '') {
        global $jobsearch_form_fields;
        $title = isset($_POST['affiliation_title']) ? $_POST['affiliation_title'] : '';
        $affiliation_image = isset($_POST['exaffiliation_image']) ? $_POST['exaffiliation_image'] : '';

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
                            'name' => 'affiliation_title[]',
                            'force_std' => $title,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Image', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'id' => 'affiliation_image_' . $rand_num,
                            'name' => 'affiliation_image[]',
                            'force_std' => $affiliation_image,
                        );
                        $jobsearch_form_fields->image_upload_field($field_params);
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

    add_action('wp_ajax_jobsearch_add_projectemp_affiliationfield', 'jobsearch_add_projectemp_affiliationfield');
}