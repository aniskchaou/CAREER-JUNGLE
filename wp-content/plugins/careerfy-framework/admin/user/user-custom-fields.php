<?php
add_action('show_user_profile', 'careerfy_show_extra_profile_fields');
add_action('edit_user_profile', 'careerfy_show_extra_profile_fields');

function careerfy_show_extra_profile_fields($user) {

    $user_id = $user->ID;
    
    $user_facebook = get_user_meta($user_id, 'careerfy_user_facebook', true);
    $user_google = get_user_meta($user_id, 'careerfy_user_google', true);
    $user_linkedin = get_user_meta($user_id, 'careerfy_user_linkedin', true);
    $user_twitter = get_user_meta($user_id, 'careerfy_user_twitter', true);
    ?>

    <h3><?php esc_html_e('User Social Links', 'careerfy') ?></h3>

    <table class="form-table">

        <tr>
            <th><label><?php esc_html_e('Facebook', 'careerfy') ?></label></th>

            <td>
                <input type="text" name="user_facebook" class="regular-text" value="<?php echo esc_html($user_facebook) ?>">
            </td>
        </tr>

        <tr>
            <th><label><?php esc_html_e('Google Plus', 'careerfy') ?></label></th>

            <td>
                <input type="text" name="user_google" class="regular-text" value="<?php echo esc_html($user_google) ?>">
            </td>
        </tr>

        <tr>
            <th><label><?php esc_html_e('Linkedin', 'careerfy') ?></label></th>

            <td>
                <input type="text" name="user_linkedin" class="regular-text" value="<?php echo esc_html($user_linkedin) ?>">
            </td>
        </tr>

        <tr>
            <th><label><?php esc_html_e('Twitter', 'careerfy') ?></label></th>

            <td>
                <input type="text" name="user_twitter" class="regular-text" value="<?php echo esc_html($user_twitter) ?>">
            </td>
        </tr>

    </table>
    <?php
}

add_action('personal_options_update', 'careerfy_save_extra_profile_fields');
add_action('edit_user_profile_update', 'careerfy_save_extra_profile_fields');

function careerfy_save_extra_profile_fields($user_id) {

    if (!current_user_can('edit_user', $user_id))
        return false;

    if (isset($_POST['user_facebook'])) {
        update_user_meta($user_id, 'careerfy_user_facebook', sanitize_text_field($_POST['user_facebook']));
    }
    if (isset($_POST['user_google'])) {
        update_user_meta($user_id, 'careerfy_user_google', sanitize_text_field($_POST['user_google']));
    }
    if (isset($_POST['user_linkedin'])) {
        update_user_meta($user_id, 'careerfy_user_linkedin', sanitize_text_field($_POST['user_linkedin']));
    }
    if (isset($_POST['user_twitter'])) {
        update_user_meta($user_id, 'careerfy_user_twitter', sanitize_text_field($_POST['user_twitter']));
    }
}
