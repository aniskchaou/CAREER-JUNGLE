<?php
/**
 * JobSearch  Newsletter Class
 *
 * @package Newsletter
 */
if (!class_exists('JobSearch_Newsletter')) {

    /**
      JobSearch  Newsletter class used to implement the Custom flicker gallery widget.
     */
    class JobSearch_Newsletter extends WP_Widget {

        /**
         * Sets up a new jobsearch  flicker widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'jobsearch_newsletter', // Base ID.
                    __('Newsletter', 'wp-jobsearch'), // Name.
                    array('classname' => 'widget_newsletter', 'description' => __('Newsletter widget.', 'wp-jobsearch'))
            );
        }

        /**
         * Outputs the jobsearch  flicker widget settings form.
         *
         * @param array $instance Current settings.
         */
        function form($instance) {
            global $jobsearch_form_fields, $jobsearch_plugin_options;

            $instance = wp_parse_args((array) $instance, array('title' => '', 'desc' => ''));
            $title = $instance['title'];
            $desc = $instance['desc'];
            $mailchimp_api_key = '';
            $mailchimp_list = '';
            if (isset($jobsearch_plugin_options['jobsearch-mailchimp-api-key'])) {
                $mailchimp_api_key = $jobsearch_plugin_options['jobsearch-mailchimp-api-key'];
            }
            if (isset($jobsearch_plugin_options['jobsearch-mailchimp-list'])) {
                $mailchimp_list = $jobsearch_plugin_options['jobsearch-mailchimp-list'];
            }

            wp_enqueue_media();
            ?>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Title', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('title'),
                        'force_std' => $title,
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
                        'cus_name' => $this->get_field_name('desc'),
                        'force_std' => $desc,
                    );
                    $jobsearch_form_fields->textarea_field($field_params);
                    ?>
                </div>
            </div> 
            <?php
            if ('' == $mailchimp_list || '' == $mailchimp_api_key) {
                echo '<p class="error-api">' . esc_html__('Please set Mailchimp', 'wp-jobsearch').' '. '<a href="'.admin_url( 'admin.php?page=JobSearch &tab=9', '' ).'">' .esc_html__('API settings', 'wp-jobsearch') . '</a></p>';
            }
        }

        /**
         * Handles updating settings for the current jobsearch  flicker widget instance.
         *
         * @param array $new_instance New settings for this instance as input by the user.
         * @param array $old_instance Old settings for this instance.
         * @return array Settings to save or bool false to cancel saving.
         */
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['desc'] = $new_instance['desc'];
//            $instance['mailchimp_list'] = $new_instance['mailchimp_list'];
//            $instance['mailchimp_api_key'] = $new_instance['mailchimp_api_key'];
            return $instance;
        }

        /**
         * Outputs the content for the current jobsearch  flicker widget instance.
         *
         * @param array $args Display arguments including 'before_title', 'after_title',
         * 'before_widget', and 'after_widget'.
         * @param array $instance Settings for the current Text widget instance.
         */
        function widget($args, $instance) {
            global $jobsearch_plugin_options;
            extract($args, EXTR_SKIP);

            $counter = rand(3333, 322342);
            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $desc = isset($instance['desc']) ? $instance['desc'] : '';
            $mailchimp_api_key = '';
            $mailchimp_list = '';
            if (isset($jobsearch_plugin_options['jobsearch-mailchimp-api-key'])) {
                $mailchimp_api_key = $jobsearch_plugin_options['jobsearch-mailchimp-api-key'];
            }
            if (isset($jobsearch_plugin_options['jobsearch-mailchimp-list'])) {
                $mailchimp_list = $jobsearch_plugin_options['jobsearch-mailchimp-list'];
            }

            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : '';

            $before_title = isset($args['before_title']) ? $args['before_title'] : '';
            $after_title = isset($args['after_title']) ? $args['after_title'] : '';

            echo ( $before_widget );
            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            }
            if ('' !== $desc) {
                echo '<p>' . esc_html($desc) . '</p>';
            }
            if ('' != $mailchimp_list && '' != $mailchimp_api_key) {
                ?>
                <script>
                    function jobsearch_mailchimp_submit<?php echo intval($counter); ?>(counter, admin_url) {
                        'use strict';
                        var $ = jQuery;
                        $('#newsletter_error_div_' + counter).fadeOut();
                        $('#newsletter_success_div_' + counter).fadeOut();
                        $('#process_' + counter).show();
                        $('#process_' + counter).html('<i class="fa fa-refresh fa-spin"></i>');

                        $.ajax({
                            type: 'POST',
                            url: admin_url,
                            data: "cp_email=" + $('#mc_email' + counter).val() + "&cp_fname=" + $('#mc_fname' + counter).val() + '&action=jobsearch_mailchimp',
                            dataType: "json",
                            success: function (response) {
                                $('#mcform_' + counter).get(0).reset();
                                if (response.type === 'error') {
                                    $('#process_' + counter).hide();
                                    $('#newsletter_mess_error_' + counter).html(response.msg);
                                    $('#newsletter_error_div_' + counter).fadeIn();
                                } else {
                                    $('#process_' + counter).hide();
                                    $('#newsletter_mess_success_' + counter).html(response.msg);
                                    $('#newsletter_success_div_' + counter).fadeIn();
                                }
                                $('#newsletter_mess_' + counter).fadeIn(600);
                                $('#newsletter_mess_' + counter).html(response);
                                $('#process_' + counter).html('');
                            }
                        });
                    }
                    function hide_div(div_hide) {
                        jQuery('#' + div_hide).hide();
                    }
                </script>
                <form action="javascript:jobsearch_mailchimp_submit<?php echo intval($counter); ?>('<?php echo esc_js($counter); ?>','<?php echo admin_url('admin-ajax.php'); ?>')" id="mcform_<?php echo intval($counter); ?>" method="post">
                    <ul>
                        <li> 
                            <input name="mc_fname" id="mc_fname<?php echo intval($counter); ?>" value="" placeholder="<?php echo esc_html__('Your Name', 'wp-jobsearch'); ?>" tabindex="0" type="text"></li>
                        <li><input type="email" id="mc_email<?php echo intval($counter); ?>" tabindex="0" name="mc_email" placeholder="<?php echo esc_html__('Your Email', 'wp-jobsearch'); ?>">
                        </li>
                        <li><label><input id="btn_newsletter_<?php echo intval($counter); ?>" value="<?php esc_html_e('Subscribe', 'wp-jobsearch') ?>" type="submit"><span></span></label></li>
                    </ul>
                    <div id="process_<?php echo intval($counter); ?>" class="status status-message" style="display:none"></div>
                </form>
                <div id="newsletter_error_div_<?php echo intval($counter); ?>" style="display:none" class="alert alert-danger">
                    <button class="close" type="button" onclick="hide_div('newsletter_error_div_<?php echo intval($counter); ?>')" aria-hidden="true">×</button>
                    <p><i class="icon-warning"></i>
                        <span id="newsletter_mess_error_<?php echo intval($counter); ?>"></span></p>
                </div> 
                <div id="newsletter_success_div_<?php echo intval($counter); ?>" style="display:none" class="alert alert-success">
                    <button class="close" type="button" onclick="hide_div('newsletter_success_div_<?php echo intval($counter); ?>')" aria-hidden="true">×</button>
                    <p><i class="icon-checkmark"></i><span id="newsletter_mess_success_<?php echo intval($counter); ?>"></span></p>
                </div>
                <?php
            } else {
                echo '<p class="error-api">' . esc_html__('Please contact to administrator to set settings for Newsletter API', 'wp-jobsearch') . '</p>';
            }

            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {return register_widget("jobsearch_newsletter");});
