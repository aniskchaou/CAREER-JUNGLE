<?php
/**
 * JobSearch  Facebook Likebox Class
 *
 * @package Facebook Likebox
 */
if (!class_exists('JobSearch_Facebook_Likeboxs')) {

    /**
      JobSearch  Facebook Likebox class used to implement the Custom facebook likeboxs widget.
     */
    class JobSearch_Facebook_Likeboxs extends WP_Widget {

        /**
         * Sets up a new jobsearch  facebook likeboxs widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'jobsearch_facebook_likeboxs', // Base ID.
                    __('Facebook Like box', 'wp-jobsearch'), // Name.
                    array('classname' => 'widget_follow_us', 'description' => __('Facebook Likebox widget for new posts.', 'wp-jobsearch'))
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
            $pageurl = isset($instance['pageurl']) ? esc_attr($instance['pageurl']) : '';
            $showfaces = isset($instance['showfaces']) ? esc_attr($instance['showfaces']) : '';
            $showstream = isset($instance['showstream']) ? esc_attr($instance['showstream']) : '';
            $showheader = isset($instance['showheader']) ? esc_attr($instance['showheader']) : '';
            $likebox_height = isset($instance['likebox_height']) ? esc_attr($instance['likebox_height']) : '';
            $likebox_width = isset($instance['likebox_width']) ? esc_attr($instance['likebox_width']) : '';
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
                    <label><?php esc_html_e('Page URL', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('pageurl'),
                        'force_std' => $pageurl,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Show Faces', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('showfaces'),
                        'force_std' => $showfaces,
                    );
                    $jobsearch_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Show Stream', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('showstream'),
                        'force_std' => $showstream,
                    );
                    $jobsearch_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Show Header', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('showheader'),
                        'force_std' => $showheader,
                    );
                    $jobsearch_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div> 
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Box Height', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('likebox_height'),
                        'force_std' => $likebox_height,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Box Width', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('likebox_width'),
                        'force_std' => $likebox_width,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>

            <?php
        }

        /**
         * Handles updating settings for the current jobsearch  facebook likeboxs widget instance.
         *
         * @param array $new_instance New settings for this instance as input by the user.
         * @param array $old_instance Old settings for this instance.
         * @return array Settings to save or bool false to cancel saving.
         */
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['pageurl'] = $new_instance['pageurl'];
            $instance['showfaces'] = $new_instance['showfaces'];
            $instance['showstream'] = $new_instance['showstream'];
            $instance['showheader'] = $new_instance['showheader'];
            $instance['likebox_height'] = $new_instance['likebox_height'];
            $instance['likebox_width'] = $new_instance['likebox_width'];
            return $instance;
        }

        /**
         * Outputs the content for the current jobsearch  facebook likeboxs widget instance.
         *
         * @param array $args Display arguments including 'before_title', 'after_title',
         * 'before_widget', and 'after_widget'.
         * @param array $instance Settings for the current Text widget instance.
         */
        function widget($args, $instance) {

            extract($args, EXTR_SKIP);

            $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
            $pageurl = empty($instance['pageurl']) ? ' ' : $instance['pageurl'];
            $showfaces = empty($instance['showfaces']) ? ' ' : $instance['showfaces'];
            $showstream = empty($instance['showstream']) ? ' ' : $instance['showstream'];
            $showheader = empty($instance['showheader']) ? ' ' : $instance['showheader'];
            $likebox_height = empty($instance['likebox_height']) ? ' ' : $instance['likebox_height'];
            $likebox_width = empty($instance['likebox_width']) ? ' ' : $instance['likebox_width'];

            if (isset($showfaces) AND $showfaces == 'on') {
                $showfaces = 'true';
            } else {
                $showfaces = 'false';
            }

            if (isset($showstream) AND $showstream == 'on') {
                $showstream = 'true';
            } else {
                $showstream = 'false';
            }

            echo ( $before_widget );
            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            }
            ?> 
            <div class="jobsearch-facebook">

                <div class="facebookOuter">

                    <div class="facebookInner">

                        <div class="fb-like-box" 

                             colorscheme="light" data-height="<?php echo $likebox_height; ?>"  data-width="<?php echo $likebox_width; ?>" 

                             data-href="<?php echo $pageurl; ?>" 

                             data-border-color="#fff" data-show-faces="<?php echo $showfaces; ?>"  data-show-border="false"

                             data-stream="<?php echo $showstream; ?>" data-header="false">

                        </div>          

                    </div>

                </div>

            </div>

            <script>(function (d, s, id) {

                    var js, fjs = d.getElementsByTagName(s)[0];

                    if (d.getElementById(id))
                        return;

                    js = d.createElement(s);
                    js.id = id;

                    js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";

                    fjs.parentNode.insertBefore(js, fjs);

                }(document, 'script', 'facebook-jssdk'));

            </script>
            <?php
            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {return register_widget("jobsearch_facebook_likeboxs");});
