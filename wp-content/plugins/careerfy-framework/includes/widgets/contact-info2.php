<?php
/**
 * Careerfy Contact Info Class
 *
 * @package Contact Info
 */
if (!class_exists('Careerfy_Contact_Infos2')) {

    /**
      Careerfy  Contact Info class used to implement the Custom flicker gallery widget.
     */
    class Careerfy_Contact_Infos2 extends WP_Widget {

        /**
         * Sets up a new careerfy  flicker widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'careerfy_contact_infos2', // Base ID.
                    __('Careerfy: Contact Info 2', 'careerfy-frame'), // Name.
                    array('classname' => 'widget_text_nine', 'description' => __('Contact Info widget for new posts.', 'careerfy-frame'))
            );
        }

        /**
         * Outputs the careerfy  flicker widget settings form.
         *
         * @param array $instance Current settings.
         */
        function form($instance) {
            global $careerfy_form_fields;

            $instance = wp_parse_args((array) $instance, array('title' => ''));
            $title = $instance['title'];
            $desc = isset($instance['desc']) ? esc_attr($instance['desc']) : '';
            $phone = isset($instance['phone']) ? esc_attr($instance['phone']) : '';
            $phone2 = isset($instance['phone2']) ? esc_attr($instance['phone2']) : '';
            $email = isset($instance['email']) ? esc_attr($instance['email']) : '';
            $email2 = isset($instance['email2']) ? esc_attr($instance['email2']) : '';
            ?>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Title', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('title'),
                        'force_std' => $title,
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>

            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Description', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('desc'),
                        'force_std' => $desc,
                    );
                    $careerfy_form_fields->textarea_field($field_params);
                    ?>
                </div>
            </div>

            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Phone Number', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('phone'),
                        'force_std' => $phone,
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>

            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Second Phone Number', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('phone2'),
                        'force_std' => $phone2,
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>

            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Email', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('email'),
                        'force_std' => $email,
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>

            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Second Email', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('email2'),
                        'force_std' => $email2,
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <?php
        }

        /**
         * Handles updating settings for the current careerfy  flicker widget instance.
         *
         * @param array $new_instance New settings for this instance as input by the user.
         * @param array $old_instance Old settings for this instance.
         * @return array Settings to save or bool false to cancel saving.
         */
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['desc'] = $new_instance['desc'];
            $instance['phone'] = $new_instance['phone'];
            $instance['phone2'] = $new_instance['phone2'];
            $instance['email'] = $new_instance['email'];
            $instance['email2'] = $new_instance['email2'];
            return $instance;
        }

        /**
         * Outputs the content for the current careerfy  flicker widget instance.
         *
         * @param array $args Display arguments including 'before_title', 'after_title',
         * 'before_widget', and 'after_widget'.
         * @param array $instance Settings for the current Text widget instance.
         */
        function widget($args, $instance) {

            extract($args, EXTR_SKIP);

            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $desc = empty($instance['desc']) ? '' : $instance['desc'];
            $phone = empty($instance['phone']) ? '' : $instance['phone'];
            $phone2 = empty($instance['phone2']) ? '' : $instance['phone2'];
            $email = empty($instance['email']) ? '' : $instance['email'];
            $email2 = empty($instance['email2']) ? '' : $instance['email2'];

            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : '';

            $before_title = isset($args['before_title']) ? $args['before_title'] : '';
            $after_title = isset($args['after_title']) ? $args['after_title'] : '';

            echo ( $before_widget );
            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            }

            if ($desc != '') {
                echo '<p>' . $desc . '</p>';
            }
            ?>
            <ul>
                <?php
                if ($phone != '') {
                    ?>
                    <li><i class="fa fa-phone"></i> <?php echo ($phone) ?></li>
                    <?php
                }
                if ($phone2 != '') {
                    ?>
                    <li><i class="fa fa-phone"></i> <?php echo ($phone2) ?></li>
                    <?php
                }
                if ($email != '') {
                    ?>
                    <li><i class="fa fa-envelope"></i> <a href="mailto:<?php echo ($email) ?>"><?php echo ($email) ?></a></li>
                    <?php
                }
                if ($email2 != '') {
                    ?>
                    <li><i class="fa fa-envelope"></i> <a href="mailto:<?php echo ($email2) ?>"><?php echo ($email2) ?></a></li>
                    <?php
                }
                ?>
            </ul>
            <?php
            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {
    return register_widget("careerfy_contact_infos2");
});
