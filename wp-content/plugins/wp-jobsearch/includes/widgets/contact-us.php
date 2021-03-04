<?php
/**
 * JobSearch  Contact Us Class
 *
 * @package Contact Us
 */
if (!class_exists('JobSearch_Contact_Us')) {

    /**
      JobSearch  Contact Us class used to implement the Custom  gallery widget.
     */
    class JobSearch_Contact_Us extends WP_Widget {

        /**
         * Sets up a new jobsearch  widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'jobsearch_contact_us', // Base ID.
                    __('Contact Us', 'wp-jobsearch'), // Name.
                    array('classname' => ' widget_aboutinfo', 'description' => __('Contact Us widget.', 'wp-jobsearch'))
            );
        }

        /**
         * Outputs the jobsearch  widget settings form.
         *
         * @param array $instance Current settings.
         */
        function form($instance) {
            global $jobsearch_form_fields;

            $instance = wp_parse_args((array) $instance, array('title' => ''));
            $title = $instance['title'];
            $desc = isset($instance['desc']) ? $instance['desc'] : '';
            $footer_logo_img = isset($instance['footer_logo_img']) ? esc_url($instance['footer_logo_img']) : '';
            $footer_logo_url = isset($instance['footer_logo_url']) ? esc_url($instance['footer_logo_url']) : '';
            $address = isset($instance['address']) ? $instance['address'] : '';
            $phone = isset($instance['phone']) ? $instance['phone'] : '';
            $mobile = isset($instance['mobile']) ? $instance['mobile'] : '';
            $email = isset($instance['email']) ? $instance['email'] : '';
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
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Image', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => rand(100000, 999999),
                        'cus_name' => $this->get_field_name('footer_logo_img'),
                        'force_std' => $footer_logo_img,
                    );
                    $jobsearch_form_fields->image_upload_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Url', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('footer_logo_url'),
                        'force_std' => $footer_logo_url,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Address', 'wp-jobsearch') ?>:</label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('address'),
                        'force_std' => $address,
                    );
                    $jobsearch_form_fields->textarea_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Mobile', 'wp-jobsearch') ?>:</label>

                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('mobile'),
                        'force_std' => $mobile,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Phone', 'wp-jobsearch') ?>:</label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('phone'),
                        'force_std' => $phone,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>

            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Email', 'wp-jobsearch') ?>:</label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('email'),
                        'force_std' => $email,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>

            <?php
        }

        /**
         * Handles updating settings for the current jobsearch   widget instance.
         *
         * @param array $new_instance New settings for this instance as input by the user.
         * @param array $old_instance Old settings for this instance.
         * @return array Settings to save or bool false to cancel saving.
         */
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['desc'] = $new_instance['desc'];
            $instance['footer_logo_img'] = $new_instance['footer_logo_img'];
            $instance['footer_logo_url'] = $new_instance['footer_logo_url'];
            $instance['address'] = $new_instance['address'];
            $instance['phone'] = $new_instance['phone'];
            $instance['mobile'] = $new_instance['mobile'];
            $instance['email'] = $new_instance['email'];
            $instance['social_icons'] = $new_instance['social_icons'];
            return $instance;
        }

        /**
         * Outputs the content for the current jobsearch   widget instance.
         *
         * @param array $args Display arguments including 'before_title', 'after_title',
         * 'before_widget', and 'after_widget'.
         * @param array $instance Settings for the current Text widget instance.
         */
        function widget($args, $instance) {

            extract($args, EXTR_SKIP);

            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $desc = isset($instance['desc']) ? $instance['desc'] : '';
            $footer_logo_img = empty($instance['footer_logo_img']) ? ' ' : $instance['footer_logo_img'];
            $footer_logo_url = empty($instance['footer_logo_url']) ? ' ' : $instance['footer_logo_url'];
            $address = isset($instance['address']) ? $instance['address'] : '';
            $phone = isset($instance['phone']) ? $instance['phone'] : '';
            $mobile = isset($instance['mobile']) ? $instance['mobile'] : '';
            $email = isset($instance['email']) ? $instance['email'] : '';
            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : '';
            $before_title = isset($args['before_title']) ? $args['before_title'] : '';
            $after_title = isset($args['after_title']) ? $args['after_title'] : '';

            echo ( $before_widget );
            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            }
            if ('' != $footer_logo_url && '' != $footer_logo_img) {
                echo '<a  class="jobsearch-footer-logo" href="' . $footer_logo_url . '"><img src="' . $footer_logo_img . '" alt=""></a>';
            }
            if ('' !== $desc) {
                echo '<p>' . esc_html($desc) . '</p>';
            }
            ?>
            <ul>
                <?php if ($address != '') { ?>
                    <li><i class="jobsearch-color jobsearch-icon-buildings"></i> <span><?php echo esc_html($address) ?></span></li>
                    <?php
                }
                if ($mobile != '') {
                    ?>
                    <li><i class="jobsearch-color jobsearch-icon-technology7"></i> <span>(<?php echo esc_html($mobile) ?></span></li>
                    <?php
                }
                if ($phone != '') {
                    ?>
                    <li><i class="jobsearch-color jobsearch-icon-technology6"></i> <span>(<?php echo esc_html($phone) ?></span></li>
                    <?php
                }
                if ($email != '') {
                    ?>
                    <li><i class="jobsearch-color jobsearch-icon-multimedia"></i> <a href="mailto:<?php echo esc_html($email) ?>"><?php echo esc_html($email) ?></a></li>
                    <?php } ?>
            </ul> 										
            <?php
            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {return register_widget("jobsearch_contact_us");});
