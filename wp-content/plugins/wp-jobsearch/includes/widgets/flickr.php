<?php
/**
 * JobSearch  Flickr Class
 *
 * @package Flickr
 */
if (!class_exists('JobSearch_Flickr')) {

    /**
      JobSearch  Flickr class used to implement the Custom flicker gallery widget.
     */
    class JobSearch_Flickr extends WP_Widget {

        /**
         * Sets up a new jobsearch  flicker widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'jobsearch_flickr', // Base ID.
                    __('Flickr Gallery', 'wp-jobsearch'), // Name.
                    array('classname' => 'widget_flickr_gallery', 'description' => __('Flickr Gallery widget for Images', 'wp-jobsearch'))
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
            $username = isset($instance['username']) ? esc_attr($instance['username']) : '';
            $no_of_photos = isset($instance['no_of_photos']) ? esc_attr($instance['no_of_photos']) : '';
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
                    <label><?php esc_html_e('Username', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
            <?php
            $field_params = array(
                'cus_name' => $this->get_field_name('username'),
                'force_std' => $username,
            );
            $jobsearch_form_fields->input_field($field_params);
            ?>
                </div>
            </div>

            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Number of Photos', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
            <?php
            $field_params = array(
                'cus_name' => $this->get_field_name('no_of_photos'),
                'force_std' => $no_of_photos,
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
            $instance['username'] = $new_instance['username'];
            $instance['no_of_photos'] = $new_instance['no_of_photos'];
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

            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $username = empty($instance['username']) ? ' ' : apply_filters('widget_title', $instance['username']);
            $no_of_photos = empty($instance['no_of_photos']) ? ' ' : apply_filters('widget_title', $instance['no_of_photos']);
            if ('' === $instance['no_of_photos']) {
                $instance['no_of_photos'] = '3';
            }
            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : ''; 
            
            echo ( $before_widget );
            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            } 

            $get_flickr_array = array();
            $apikey = isset($jobsearch_plugin_options['jobsearch-flickr-api']) ? $jobsearch_plugin_options['jobsearch-flickr-api'] : '';
            if ('' !== $apikey) {
                wp_enqueue_style('jobsearch-fancybox-style');
                wp_enqueue_script('jobsearch-fancybox-script');
                // Getting transient.
                $cachetime = 86400;
                $transient = 'flickr_gallery_data';
                $check_transient = get_transient($transient);
                // Get Flickr Gallery saved data.
                $saved_data = get_option('flickr_gallery_data');
                $db_apikey = '';
                $db_user_name = '';
                $db_total_photos = '';
                if ('' !== $saved_data) {
                    $db_apikey = isset($saved_data['api_key']) ? $saved_data['api_key'] : '';
                    $db_user_name = isset($saved_data['user_name']) ? $saved_data['user_name'] : '';
                    $db_total_photos = isset($saved_data['total_photos']) ? $saved_data['total_photos'] : '';
                }
                if (false === $check_transient || ($apikey !== $db_apikey || $username !== $db_user_name || $no_of_photos !== $db_total_photos)) {
                    $user_id = 'https://api.flickr.com/services/rest/?method=flickr.people.findByUsername&api_key=' . $apikey . '&username=' . $username . '&format=json&nojsoncallback=1';
                    $user_info = wp_remote_get($user_id, array('decompress' => false));
                    
                    $user_info = isset($user_info['body']) ? $user_info['body'] : '';

                    $user_info = json_decode($user_info, true);
                    if (isset($user_info['stat']) && 'ok' === $user_info['stat']) {
                        $user_get_id = $user_info['user']['id'];
                        $get_flickr_array['api_key'] = $apikey;
                        $get_flickr_array['user_name'] = $username;
                        $get_flickr_array['user_id'] = $user_get_id;
                        $url = 'https://api.flickr.com/services/rest/?method=flickr.people.getPublicPhotos&api_key=' . $apikey . '&user_id=' . $user_get_id . '&per_page=' . $no_of_photos . '&format=json&nojsoncallback=1';
                        $content = wp_remote_get($url, array('decompress' => false));
                        $content = isset($content['body']) ? $content['body'] : '';
                        $content = json_decode($content, true);
                        if ('ok' === $content['stat']) {
                            $counter = 0;
                            echo '<ul>';
                            foreach ((array) $content['photos']['photo'] as $photo) {
                                $image_file = "https://farm{$photo['farm']}.staticflickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_s.jpg";
                                $img_headers = get_headers($image_file);
                                if (strpos($img_headers[0], '200') !== false) {
                                    $image_file = $image_file;
                                } else {
                                    $image_file = "https://farm{$photo['farm']}.staticflickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_q.jpg";
                                    $img_headers = get_headers($image_file);
                                    if (strpos($img_headers[0], '200') !== false) {
                                        $image_file = $image_file;
                                    } else {
                                        $image_file = jobsearch_plugin_get_url('images/no-image-grid2.jpg');
                                    }
                                }
                                echo '<li>'
                                . '<a href="https://www.flickr.com/photos/' . $photo['owner'] . '/' . $photo['id'] . '/"  target="_blank">'
                                        . '<img src="' . esc_url($image_file) . '" alt="">'
                                        . '</a>'
                                        . '</li>';
                                $counter ++;
                                $get_flickr_array['photo_src'][] = $image_file;
                                $get_flickr_array['photo_title'][] = $photo['title'];
                                $get_flickr_array['photo_owner'][] = $photo['owner'];
                                $get_flickr_array['photo_id'][] = $photo['id'];
                            }
                            echo '</ul>';
                            $get_flickr_array['total_photos'] = $counter;
                            // Setting Transient.
                            set_transient($transient, true, $cachetime);
                            update_option('flickr_gallery_data', $get_flickr_array);
                            if ($counter == 0) {
                                _e('No results found.', 'wp-jobsearch');
                            }
                        } else {
                            echo esc_html(__('Error: ', 'wp-jobsearch')) . $content['code'] . ' - ' . $content['message'];
                        }
                    } else {
                        echo esc_html(__('Error: ', 'wp-jobsearch')) . $user_info['code'] . ' - ' . $user_info['message'];
                    }
                } else {
                    if (get_option('flickr_gallery_data') <> '') {
                        $flick_data = get_option('flickr_gallery_data');
                        echo '<ul>';
                        if (isset($flick_data['photo_src'])) :
                            $i = 0;
                            foreach ($flick_data['photo_src'] as $ph) {
                                echo '<li>';
                                echo '
                                <figure> 
                                <a target="_blank" href="https://www.flickr.com/photos/' . $flick_data['photo_owner'][$i] . '/' . $flick_data['photo_id'][$i] . '/"><img alt="" src="' . esc_url($flick_data['photo_src'][$i]) . '"></a>
                                </figure>';
                                echo '</li>';
                                $i ++;
                            }
                        endif;
                        echo '</ul>';
                    } else {
                        _e('No results found.', 'wp-jobsearch');
                    }
                }
            } else {
                _e('API key error.', 'wp-jobsearch');
            }
            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {return register_widget("jobsearch_flickr");});
