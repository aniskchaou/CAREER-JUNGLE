<?php
/**
 * JobSearch  Image Ads Class
 *
 * @package Image Ads
 */
if (!class_exists('JobSearch_Image_Ads')) {

    /**
      JobSearch  Image Ads class used to implement the Custom flicker gallery widget.
     */
    class JobSearch_Image_Ads extends WP_Widget {

        /**
         * Sets up a new jobsearch  flicker widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'jobsearch_image_ads', // Base ID.
                    __('Image Ads', 'wp-jobsearch'), // Name.
                    array('classname' => 'widget_add', 'description' => __('Image Ads widget.', 'wp-jobsearch'))
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
            $ad_img = isset($instance['ad_img']) ? esc_url($instance['ad_img']) : '';
            $ad_url = isset($instance['ad_url']) ? esc_url($instance['ad_url']) : '';

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
                    <label><?php esc_html_e('Image', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => rand(100000, 999999),
                        'cus_name' => $this->get_field_name('ad_img'),
                        'force_std' => $ad_img,
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
                        'cus_name' => $this->get_field_name('ad_url'),
                        'force_std' => $ad_url,
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
            $instance['ad_url'] = $new_instance['ad_url'];
            $instance['ad_img'] = $new_instance['ad_img'];
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
            $ad_url = empty($instance['ad_url']) ? ' ' : $instance['ad_url'];
            $ad_img = empty($instance['ad_img']) ? ' ' : $instance['ad_img'];

            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : '';

            $before_title = isset($args['before_title']) ? $args['before_title'] : '';
            $after_title = isset($args['after_title']) ? $args['after_title'] : '';

            echo ( $before_widget );
            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            }

            if ('' != $ad_url && '' != $ad_img) {
                echo '
				<figure>
					<a href="' . $ad_url . '"><img src="' . $ad_img . '" alt=""></a>
				</figure>';
            }

            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {return register_widget("jobsearch_image_ads");});
