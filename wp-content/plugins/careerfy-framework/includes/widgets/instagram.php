<?php
/**
 * Careerfy  Instagram Class
 *
 * @package Instagram
 */
if (!class_exists('Careerfy_Instagram')) {

    /**
      Careerfy  Instagram class used to implement the Custom flicker gallery widget.
     */
    class Careerfy_Instagram extends WP_Widget {

        /**
         * Sets up a new careerfy  flicker widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'careerfy_instagram', // Base ID.
                    __('Instagram', 'careerfy-frame'), // Name.
                    array('classname' => 'widget_instagram', 'description' => __('Instagram widget.', 'careerfy-frame'))
            );
        }

        /**
         * Outputs the careerfy  flicker widget settings form.
         *
         * @param array $instance Current settings.
         */
        function form($instance) {
            global $careerfy_form_fields, $careerfy_framework_options;

            $instance = wp_parse_args((array) $instance, array('title' => '', 'num_of_images' => ''));
            $title = $instance['title'];
            $num_of_images = $instance['num_of_images'];
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
                    <label><?php esc_html_e('Number of Images', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('num_of_images'),
                        'force_std' => $num_of_images,
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
            $instance['num_of_images'] = $new_instance['num_of_images'];
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
            global $careerfy_framework_options;
            extract($args, EXTR_SKIP);

            $counter = rand(3333, 322342);
            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $num_of_images = isset($instance['num_of_images']) ? $instance['num_of_images'] : '';

            $num_of_images = absint($num_of_images) > 0 ? absint($num_of_images) : 6;

            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : '';

            $before_title = isset($args['before_title']) ? $args['before_title'] : '';
            $after_title = isset($args['after_title']) ? $args['after_title'] : '';

            $access_token = isset($careerfy_framework_options['instagram-access-token']) ? $careerfy_framework_options['instagram-access-token'] : '';
            $instagram_user_id = isset($careerfy_framework_options['instagram-user-id']) ? $careerfy_framework_options['instagram-user-id'] : '';

            echo ( $before_widget );

            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            }

            $insta_error = true;
            $html = '';
            if ($access_token != '' && $instagram_user_id != '') {
                $cachetime = 1800; //half hour
                $transient = 'careerfy_instagram_gallery_cache';
                $check_transient = get_transient($transient);
                $saved_data = get_option('instagram_gallery_data');

                $db_access_token = isset($saved_data['access_token']) ? $saved_data['access_token'] : '';
                $db_insta_user_id = isset($saved_data['user_id']) ? $saved_data['user_id'] : '';
                $db_num_of_images = isset($saved_data['num_of_images']) ? $saved_data['num_of_images'] : '';

                if (false === $check_transient || ($access_token !== $db_access_token || $instagram_user_id !== $db_insta_user_id || $num_of_images !== $db_num_of_images)) {

                    $media_url = 'https://api.instagram.com/v1/users/' . $instagram_user_id . '/media/recent/?count=' . $num_of_images . '&access_token=' . $access_token;

                    $media_info = wp_remote_get($media_url, array('decompress' => false));
                    $media_info = isset($media_info['body']) ? $media_info['body'] : '';

                    $media_info = json_decode($media_info, true);
                    if (isset($media_info['meta']['code']) && $media_info['meta']['code'] == '200' && isset($media_info['data']) && !empty($media_info['data'])) {
                        $insta_error = false;
                        $insta_user = isset($media_info['data'][0]['user']['username']) ? $media_info['data'][0]['user']['username'] : '';

                        $insta_saving_data = array();
                        $insta_saving_data['access_token'] = $access_token;
                        $insta_saving_data['user_id'] = $instagram_user_id;
                        $insta_saving_data['username'] = $insta_user;
                        $insta_saving_data['num_of_images'] = $num_of_images;
                        $insta_saving_data['images'] = array();

                        $media_counter = 0;
                        $html .= '<ul>';
                        foreach ($media_info['data'] as $instagram_value) {
                            $html .= '<li><a href="https://www.instagram.com/' . $insta_user . '/" target="_blank"><img src="' . esc_url($instagram_value['images']['thumbnail']['url']) . '"></a></li>';
                            $insta_saving_data['images'][] = esc_url($instagram_value['images']['thumbnail']['url']);
                            $media_counter++;
                        }
                        $html .= '</ul>';

                        set_transient($transient, true, $cachetime);
                        update_option('instagram_gallery_data', $insta_saving_data);
                    }
                } else {
                    $insta_cache_data = get_option('instagram_gallery_data');
                    if (!empty($insta_cache_data) && isset($insta_cache_data['images']) && !empty($insta_cache_data['images'])) {
                        $insta_error = false;
                        $insta_user = isset($insta_cache_data['username']) ? $insta_cache_data['username'] : '';
                        
                        $html .= '<ul>';
                        foreach ($insta_cache_data['images'] as $img_url) {
                            $html .= '<li><a href="https://www.instagram.com/' . $insta_user . '/" target="_blank"><img src="' . esc_url($img_url) . '"></a></li>';
                        }
                        $html .= '</ul>';
                    }
                }
            }
            if ($insta_error === true) {
                $html .= '<p>' . esc_html__('There is some error.', 'careerfy-frame') . '</p>';
            }

            echo ($html);

            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {return register_widget("careerfy_instagram");});
