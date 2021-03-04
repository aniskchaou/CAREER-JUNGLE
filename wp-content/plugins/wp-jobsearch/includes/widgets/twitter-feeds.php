<?php
/**
 * JobSearch  Twitter Feed Class
 *
 * @package Twitter Feed
 */
if (!class_exists('JobSearch_Twitter_Feeds')) {

    /**
      JobSearch  Twitter Feed class used to implement the Custom twitter feeds widget.
     */
    class JobSearch_Twitter_Feeds extends WP_Widget {

        /**
         * Sets up a new jobsearch  twitter feeds widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'jobsearch_twitter_feeds', // Base ID.
                    __('Twitter Feeds', 'wp-jobsearch'), // Name.
                    array('classname' => 'widget_twitter_feed', 'description' => __('Twitter Feed widget for new posts.', 'wp-jobsearch'))
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
            $username = isset($instance['username']) ? esc_attr($instance['username']) : '';
            $no_of_tweets = isset($instance['no_of_tweets']) ? esc_attr($instance['no_of_tweets']) : '';
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
                    <label><?php esc_html_e('Twitter Username', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('username'),
                        'options' => $cate_array,
                        'force_std' => $username,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>

            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Number of Tweets', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('no_of_tweets'),
                        'force_std' => $no_of_tweets,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <?php
        }

        /**
         * Handles updating settings for the current jobsearch  twitter feeds widget instance.
         *
         * @param array $new_instance New settings for this instance as input by the user.
         * @param array $old_instance Old settings for this instance.
         * @return array Settings to save or bool false to cancel saving.
         */
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['username'] = $new_instance['username'];
            $instance['no_of_tweets'] = $new_instance['no_of_tweets'];
            return $instance;
        }

        /**
         * Outputs the content for the current jobsearch  twitter feeds widget instance.
         *
         * @param array $args Display arguments including 'before_title', 'after_title',
         * 'before_widget', and 'after_widget'.
         * @param array $instance Settings for the current Text widget instance.
         */
        function widget($args, $instance) {

            extract($args, EXTR_SKIP);

            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $username = empty($instance['username']) ? '' : apply_filters('widget_title', $instance['username']);
            $no_of_tweets = empty($instance['no_of_tweets']) ? ' ' : apply_filters('widget_title', $instance['no_of_tweets']);
            if ('' === $instance['no_of_tweets']) {
                $instance['no_of_tweets'] = '3';
            }
            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : '';

            $before_title = isset($args['before_title']) ? $args['before_title'] : '';
            $after_title = isset($args['after_title']) ? $args['after_title'] : '';

            echo ( $before_widget );
            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            }

            $jobsearch_twitter_user = $username;
            $jobsearch_twitter_num = $no_of_tweets;

            if ($jobsearch_twitter_user != '' && $jobsearch_twitter_num > 0) {
                require_once jobsearch_plugin_get_path('includes/twitter-tweets/display-tweets.php');

                $cache_limit_time = isset($jobsearch_plugin_options['jobsearch-twitter-cache-limit']) ? $jobsearch_plugin_options['jobsearch-twitter-cache-limit'] : '';
                $tweet_num_from_twitter = isset($jobsearch_plugin_options['jobsearch-twitter-num-tweets']) ? $jobsearch_plugin_options['jobsearch-twitter-num-tweets'] : '';
                $twitter_datetime_formate = isset($jobsearch_plugin_options['jobsearch-twitter-time-format']) ? $jobsearch_plugin_options['jobsearch-twitter-time-format'] : '';

                if ('' === intval($cache_limit_time)) {
                    $cache_limit_time = 60;
                }
                if ('' === $twitter_datetime_formate) {
                    $twitter_datetime_formate = 'time_since';
                }
                if ('' === intval($tweet_num_from_twitter)) {
                    $tweet_num_from_twitter = 5;
                }

                jobsearch_display_tweets($jobsearch_twitter_user, $twitter_datetime_formate, $tweet_num_from_twitter, $jobsearch_twitter_num, $cache_limit_time, 'in_widget');
            }

            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {return register_widget("jobsearch_twitter_feeds");});
