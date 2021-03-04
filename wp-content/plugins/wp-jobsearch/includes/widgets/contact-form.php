<?php
/**
 * JobSearch  Contact Form Class
 *
 * @package Contact Form
 */
if (!class_exists('JobSearch_Contact_Form')) {

    /**
      JobSearch  Contact Form class used to implement the Custom flicker gallery widget.
     */
    class JobSearch_Contact_Form extends WP_Widget {

        /**
         * Sets up a new jobsearch  flicker widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'jobsearch_contact_form', // Base ID.
                    __('Contact Form', 'wp-jobsearch'), // Name.
                    array('classname' => 'widget_message_form', 'description' => __('Contact Form widget.', 'wp-jobsearch'))
            );
        }

        /**
         * Outputs the jobsearch  flicker widget settings form.
         *
         * @param array $instance Current settings.
         */
        function form($instance) {
            global $jobsearch_form_fields;

            $instance = wp_parse_args((array) $instance, array('title' => ''));
            $title = $instance['title'];
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
            <?php
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

            extract($args, EXTR_SKIP);

            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));

            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : '';

            $before_title = isset($args['before_title']) ? $args['before_title'] : '';
            $after_title = isset($args['after_title']) ? $args['after_title'] : '';

            echo ( $before_widget );
            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            }
            $rand_numb = rand(1000000, 9999999);
            ?>
            <form id="ct-form-<?php echo absint($rand_numb) ?>" data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>" method="post">
                <ul>
                    <li><input name="u_name" type="text" placeholder="<?php esc_html_e('Name', 'wp-jobsearch') ?>"> </li>
                    <li><input name="u_email" type="text" placeholder="<?php esc_html_e('E-mail', 'wp-jobsearch') ?>"> </li> 
                    <li ><textarea cols="30" rows="10" name="u_msg" placeholder="<?php esc_html_e('Message', 'wp-jobsearch') ?>"></textarea></li>                      
                    <li>
                    
                    <label class="jobsearch-submit">
                        <i class="jobsearch-icon-symbol"></i> <input class="jobsearch-ct-form jobsearch-bgcolor" data-id="<?php echo absint($rand_numb) ?>" type="submit" value="<?php esc_html_e('Send Message', 'wp-jobsearch') ?>"> 
                    </label><span class="jobsearch-bt-msg jobsearch-ct-msg"></span><input type="hidden" name="u_type" value="content" />
                    </li>
                </ul>
            </form>
            <?php
            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {return register_widget("jobsearch_contact_form");});
