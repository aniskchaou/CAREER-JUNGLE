<?php
add_action('show_user_profile', 'jobsearch_show_extra_profile_fields');
add_action('edit_user_profile', 'jobsearch_show_extra_profile_fields');

function jobsearch_show_extra_profile_fields($user) {

    $user_id = $user->ID;

    $user_obj = get_user_by('ID', $user_id);

    $user_roles = $user_obj->roles;

    if ((isset($_POST['role']) && $_POST['role'] == 'jobsearch_employer') || (in_array('jobsearch_employer', $user_roles))) {
        //
        $user_employer_id = get_user_meta($user_id, 'jobsearch_employer_id', true);
        ?>

        <h3><?php esc_html_e('Extra profile information', 'wp-jobsearch') ?></h3>

        <table class="form-table">

            <?php
            if ($user_employer_id != '' && jobsearch_get_user_employer_id($user_id) == $user_employer_id) {
                $emp_user_id = get_post_meta($user_employer_id, 'jobsearch_user_id', true);
                if ($emp_user_id != $user_id) {
                    update_post_meta($user_employer_id, 'jobsearch_user_id', $user_id);
                }
                ?>
                <tr>
                    <th><label for="user-attach-member"><?php esc_html_e('Attached Employer', 'wp-jobsearch') ?></label></th>

                    <td>
                        <?php echo '<strong>' . get_the_title($user_employer_id) . '</strong>' ?>
                    </td>
                </tr>
                <?php
            } else {
                ?>
                <tr>
                    <th><label for="user-attach-member"><?php esc_html_e('Attach an Employer', 'wp-jobsearch') ?></label></th>

                    <td>
                        <input id="user-attach-member" type="text" name="user_attach_employer"><br>
                        <p><?php esc_html_e('Attach an Employer with this user by entering #Employer ID here.', 'wp-jobsearch') ?></p>
                    </td>
                </tr>
                <?php
            }
            ?>

        </table>
        <?php
    }
    if ((isset($_POST['role']) && $_POST['role'] == 'jobsearch_candidate') || (in_array('jobsearch_candidate', $user_roles))) {
        //
        $user_candidate_id = get_user_meta($user_id, 'jobsearch_candidate_id', true);
        ?>

        <h3><?php esc_html_e('Extra profile information', 'wp-jobsearch') ?></h3>

        <table class="form-table">

            <?php
            if ($user_candidate_id != '' && jobsearch_get_user_candidate_id($user_id) == $user_candidate_id) {
                $cand_user_id = get_post_meta($user_candidate_id, 'jobsearch_user_id', true);
                if ($cand_user_id != $user_id) {
                    update_post_meta($user_candidate_id, 'jobsearch_user_id', $user_id);
                }
                ?>
                <tr>
                    <th><label for="user-attach-member"><?php esc_html_e('Attached Candidate', 'wp-jobsearch') ?></label></th>

                    <td>
                        <?php echo '<strong>' . get_the_title($user_candidate_id) . '</strong>' ?>
                    </td>
                </tr>
                <?php
            } else {
                ?>
                <tr>
                    <th><label for="user-attach-member"><?php esc_html_e('Attach a Candidate', 'wp-jobsearch') ?></label></th>

                    <td>
                        <input id="user-attach-member" type="text" name="user_attach_candidate"><br>
                        <p><?php esc_html_e('Attach a Candidate with this user by entering #Candidate ID here.', 'wp-jobsearch') ?></p>
                    </td>
                </tr>
                <?php
            }
            ?>

        </table>
        <?php
    }
    echo apply_filters('jobsearch_user_editpage_attch_fields_after', '', $user);
    ?>
    <div class="jobsearch-memb-fields">
        <h3><?php esc_html_e('Jobsearch Member Fields', 'wp-jobsearch') ?></h3>
        <?php
        $user_phone_numb = get_user_meta($user_id, 'jobsearch_field_user_phone', true);
        ?>
        <table class="form-table">

            <tr>
                <th><label><?php esc_html_e('Phone Number', 'wp-jobsearch') ?></label></th>

                <td>
                    <input type="text" class="regular-text" name="jobsearch_user_phone" value="<?php echo ($user_phone_numb) ?>">
                </td>
            </tr>

        </table>
    </div>
    <?php
}

add_action('personal_options_update', 'jobsearch_save_extra_profile_fields');
add_action('edit_user_profile_update', 'jobsearch_save_extra_profile_fields');

function jobsearch_save_extra_profile_fields($user_id) {

    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    if (isset($_POST['admin_bar_front']) && $_POST['admin_bar_front'] == '1') {
        delete_user_option($user_id, 'show_admin_bar_front');
    }
    
    if (isset($_POST['user_attach_candidate'])) {

        $candidate_id = sanitize_text_field($_POST['user_attach_candidate']);
        $candidate_id = filter_var($candidate_id, FILTER_SANITIZE_NUMBER_INT);
        if (!jobsearch_get_candidate_user_id($candidate_id)) {
            update_user_meta($user_id, 'jobsearch_candidate_id', $candidate_id);
            update_post_meta($candidate_id, 'jobsearch_user_id', $user_id);
        }
    }

    if (isset($_POST['user_attach_employer'])) {

        $employer_id = sanitize_text_field($_POST['user_attach_employer']);
        $employer_id = filter_var($employer_id, FILTER_SANITIZE_NUMBER_INT);
        if (!jobsearch_get_employer_user_id($employer_id)) {
            update_user_meta($user_id, 'jobsearch_employer_id', $employer_id);
            update_post_meta($employer_id, 'jobsearch_user_id', $user_id);
        }
    }
    
    do_action('jobsearch_user_adminedit_save_fields', $user_id);
}

add_action('set_user_role', function($user_id, $role, $old_roles) {
    global $pagenow;
    $jobsearch_roles = array('jobsearch_candidate', 'jobsearch_employer');
    if (is_array($old_roles) && (!in_array('jobsearch_candidate', $old_roles) && !in_array('jobsearch_employer', $old_roles)) && in_array($role, $jobsearch_roles) && $pagenow == 'users.php') {
        if (!isset($_POST['role'])) {
            $_POST['role'] = $role;
        }
        $user_emp_id = get_user_meta($user_id, 'jobsearch_employer_id', true);
        $user_cand_id = get_user_meta($user_id, 'jobsearch_candidate_id', true);
        if (!$user_emp_id && !$user_cand_id) {
            do_action('jobsearch_when_user_update_at_bkend', $user_id);
        }
    }
}, 15, 3);