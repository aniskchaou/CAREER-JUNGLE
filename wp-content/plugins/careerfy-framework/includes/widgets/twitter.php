<?php
/**
 * Careerfy Twitter Class
 *
 * @package Twitter
 */
if (!class_exists('Careerfy_Twitter_Widget')) {

    /**
      Careerfy Twitter class used to implement the Custom  gallery widget.
     */
    class Careerfy_Twitter_Widget extends WP_Widget {

        /**
         * Sets up a new enroll lms widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'careerfy_twitter_widget', // Base ID.
                    __('Twitter Tweets', 'careerfy'), // Name.
                    array('classname' => 'careerfy_twitter_widget', 'description' => __('Twitter widget.', 'careerfy'))
            );
        }

        /**
         * Outputs the enroll lms widget settings form.
         *
         * @param array $instance Current settings.
         */
        function form($instance) {
            global $careerfy_form_fields;

            $instance = wp_parse_args((array) $instance, array('title' => ''));
            $title = $instance['title'];
            $username = isset($instance['username']) ? ($instance['username']) : '';
            $num_tweets = isset($instance['num_tweets']) ? $instance['num_tweets'] : '';
            ?>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Title', 'careerfy') ?></label>
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
                    <label><?php esc_html_e('Twitter Username', 'careerfy') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('username'),
                        'force_std' => $username,
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Number of Tweets', 'careerfy') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('num_tweets'),
                        'force_std' => $num_tweets,
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>

            <?php
        }

        /**
         * Handles updating settings for the current enroll lms  widget instance.
         *
         * @param array $new_instance New settings for this instance as input by the user.
         * @param array $old_instance Old settings for this instance.
         * @return array Settings to save or bool false to cancel saving.
         */
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['username'] = $new_instance['username'];
            $instance['num_tweets'] = $new_instance['num_tweets'];
            return $instance;
        }

        /**
         * Outputs the content for the current enroll lms  widget instance.
         *
         * @param array $args Display arguments including 'before_title', 'after_title',
         * 'before_widget', and 'after_widget'.
         * @param array $instance Settings for the current Text widget instance.
         */
        function widget($args, $instance) {

            global $careerfy_theme_options;
            extract($args, EXTR_SKIP);

            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $username = empty($instance['username']) ? '' : $instance['username'];
            $num_tweets = isset($instance['num_tweets']) ? $instance['num_tweets'] : '';

            $num_tweets = $num_tweets > 0 ? $num_tweets : 3;

            echo ( $before_widget );
            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            }

            if ($username != '' && $num_tweets > 0) {

                require_once careerfy_framework_get_path('includes/twitter-tweets/display-tweets.php');

                $cache_limit_time = isset($careerfy_theme_options['careerfy-twitter-cache-limit']) ? $careerfy_theme_options['careerfy-twitter-cache-limit'] : '';
                $tweet_num_from_twitter = isset($careerfy_theme_options['careerfy-twitter-num-tweets']) ? $careerfy_theme_options['careerfy-twitter-num-tweets'] : '';
                $twitter_datetime_formate = isset($careerfy_theme_options['careerfy-twitter-time-format']) ? $careerfy_theme_options['careerfy-twitter-time-format'] : '';

                if ('' === intval($cache_limit_time)) {
                    $cache_limit_time = 60;
                }
                if ('' === $twitter_datetime_formate) {
                    $twitter_datetime_formate = 'time_since';
                }
                if ('' === intval($tweet_num_from_twitter)) {
                    $tweet_num_from_twitter = 5;
                }
                careerfy_display_tweets($username, $twitter_datetime_formate, $tweet_num_from_twitter, $num_tweets, $cache_limit_time, 'widget');
            }
            
            echo '<small><a href="https://twitter.com/' . esc_html($username) . '" target="_blank"><i class="fa fa-twitter"></i> @' . $username . '</a></small>';

            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {return register_widget("careerfy_twitter_widget");});
