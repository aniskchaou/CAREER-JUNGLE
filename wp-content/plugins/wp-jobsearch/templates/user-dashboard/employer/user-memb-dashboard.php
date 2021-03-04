<?php
global $jobsearch_plugin_options, $diff_form_errs;
$get_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$user_firstname = $user_obj->first_name;
$user_lastname = $user_obj->last_name;

$user_login = $user_obj->user_login;
$user_bio = $user_obj->description;
$user_website = $user_obj->user_url;
$user_email = $user_obj->user_email;

?>
<div class="jobsearch-typo-wrap">
    <form id="employer-profilesetings-form" class="jobsearch-employer-dasboard" method="post" action="<?php echo add_query_arg(array('tab' => 'dashboard-settings'), $page_url) ?>" enctype="multipart/form-data">
        <div class="jobsearch-employer-box-section">
            
            <div class="jobsearch-profile-title"><h2><?php esc_html_e('Basic Information', 'wp-jobsearch') ?></h2></div>
            <?php
            if (isset($_POST['user_settings_form']) && $_POST['user_settings_form'] == '1') {
                if (empty($diff_form_errs)) {
                    ?>
                    <div class="jobsearch-alert jobsearch-success-alert">
                        <p><?php echo wp_kses(__('<strong>Success!</strong> All changes updated.', 'wp-jobsearch'), array('strong' => array())) ?></p>
                    </div>
                    <?php
                } else if (isset($diff_form_errs['user_not_allow_mod']) && $diff_form_errs['user_not_allow_mod'] == true) {
                    ?>
                    <div class="jobsearch-alert jobsearch-error-alert">
                        <p><?php echo wp_kses(__('<strong>Error!</strong> You are not allowed to modify settings.', 'wp-jobsearch'), array('strong' => array())) ?></p>
                    </div>
                    <?php
                }
            }
            ?>
            <ul class="jobsearch-row jobsearch-employer-profile-form">
                
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('First Name', 'wp-jobsearch') ?></label>
                    <input type="text" value="<?php echo ($user_firstname) ?>" name="u_firstname">
                </li>
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('Last Name', 'wp-jobsearch') ?></label>
                    <input type="text" value="<?php echo ($user_lastname) ?>" name="u_lastname">
                </li>
                
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('Username', 'wp-jobsearch') ?></label>
                    <input type="text" value="<?php echo ($user_login) ?>" readonly="readonly">
                </li>
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('Email', 'wp-jobsearch') ?></label>
                    <input value="<?php echo ($user_email) ?>" type="text" readonly="readonly">
                </li>
                
                <li class="jobsearch-column-12">
                    <label><?php esc_html_e('Description', 'wp-jobsearch') ?></label>
                    <?php
                    $settings = array(
                        'media_buttons' => false,
                        'quicktags' => array('buttons' => 'strong,em,del,ul,ol,li,close'),
                        'tinymce' => array(
                            'toolbar1' => 'bold,bullist,numlist,italic,underline,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                            'toolbar2' => '',
                            'toolbar3' => '',
                        ),
                    );

                    wp_editor($user_bio, 'user_bio', $settings);
                    ?>
                </li>
            </ul>
        </div>
        <input type="hidden" name="user_settings_form" value="1">
        <?php jobsearch_terms_and_con_link_txt(); ?>
        <input type="submit" class="jobsearch-employer-profile-submit" value="<?php esc_html_e('Save Settings', 'wp-jobsearch') ?>">
        
    </form>
</div>
