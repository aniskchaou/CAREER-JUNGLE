<?php
/**
 * JobSearch  Featured Cause Class
 *
 * @package Featured Cause
 */
if (!class_exists('JobSearch_Featured_Cause')) {

    /**
      JobSearch  Featured Cause class used to implement the Custom flicker gallery widget.
     */
    class JobSearch_Featured_Cause extends WP_Widget {

        /**
         * Sets up a new jobsearch  flicker widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'jobsearch_featured_cause', // Base ID.
                    __('Featured Causes', 'wp-jobsearch'), // Name.
                    array('classname' => 'widget_feature', 'description' => __('Featured Causes widget.', 'wp-jobsearch'))
            );
        }

        /**
         * Outputs the jobsearch  flicker widget settings form.
         *
         * @param array $instance Current settings.
         */
        function form($instance) {
            global $jobsearch_form_fields;

            $instance = wp_parse_args((array) $instance, array('title' => '', 'cause' => ''));
            $title = $instance['title'];
            $cause = $instance['cause'];
            $causes = jobsearch_all_causes(false, true);
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
                    <label><?php esc_html_e('Select Causes', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">

                    <?php
                    $field_params = array(
                        'force_std' => $cause,
                        'cus_name' => $this->get_field_name('cause') . '[]',
                        'options' => $causes,
                    );

                    $jobsearch_form_fields->multi_select_field($field_params);

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
            $instance['cause'] = $new_instance['cause'];
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
            $cause = empty($instance['cause']) ? '' : $instance['cause'];

            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : '';

            $before_title = isset($args['before_title']) ? $args['before_title'] : '';
            $after_title = isset($args['after_title']) ? $args['after_title'] : '';

            echo ( $before_widget );
            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            }
            $args = array(
                'post_type' => 'cause',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'fields' => 'ids',
                'order' => 'ASC',
                'orderby' => 'post_title',
            );
            if (!empty($cause)) {
                $args['post__in'] = $cause;
            }
            $cause_query = new WP_Query($args);
            if ($cause_query->have_posts()) {
                wp_enqueue_script('jobsearch-skill');
                while ($cause_query->have_posts()) : $cause_query->the_post();
                    global $post;
                    $post_id = $post;
                    $post_thumbnail_id = get_post_thumbnail_id($post_id);
                    $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id);
                    $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                    $cause_date = get_post_meta($post_id, 'jobsearch_field_cause_date', true);
                    $related_cause_goal_amount = get_post_meta($post_id, 'jobsearch_field_cause_goal_amount', true);
                    $related_cause_raised_amount = get_post_meta($post_id, 'jobsearch_field_cause_raised_amount', true);
                    $related_raised_percentage = 0;
                    if ($related_cause_goal_amount == '') {
                        $related_cause_goal_amount = 0;
                    }
                    if ($related_cause_raised_amount == '') {
                        $related_cause_raised_amount = 0;
                    }
                    if ($related_cause_raised_amount > 0 && $related_cause_goal_amount > 0) {
                        $related_raised_percentage = ( $related_cause_raised_amount / $related_cause_goal_amount ) * 100;
                    }
                    $causes_donors_count = jobsearch_donors_count_by_cause_id($post_id);
                    ?>
                    <div class="featured-cause">
                        <figure>
                            <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img src="<?php echo esc_url($post_thumbnail_src) ?>" alt=""></a>
                            <small>$<?php echo absint($related_cause_goal_amount); ?></small> 
                        </figure>
                        <div class="jobsearch-widget-feature">
                            <time><i class="jobsearch-icon-time"></i><?php echo date_i18n(get_option('date_format'), strtotime($cause_date)) ?></time>
                            <h6><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>" title="<?php echo esc_html(get_the_title(get_the_ID())) ?>"><?php echo wp_trim_words(get_the_title(get_the_ID()), 4, '...') ?></a></h6>
                            <div class="jobsearch-skillst jobsearch-feature-cause">
                                <div class="skillbar" data-percent="<?php echo absint($related_raised_percentage); ?>%">
                                    <div class="count"></div>
                                    <div class="count-bar"></div>
                                </div>
                                <small><?php esc_html_e('completed', 'wp-jobsearch') ?></small>
                            </div> 
                        </div>
                        <div class="clear"></div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?><script>  jQuery(document).ready(function () {
                        jQuery('.skillbar').each(function () {
                            //alert('a');
                            jQuery(this).appear(function () {
                                jQuery(this).find('.count-bar').animate({
                                    width: jQuery(this).attr('data-percent')
                                }, 3000);
                                var percent = jQuery(this).attr('data-percent');
                                jQuery(this).find('.count').html('<span>' + percent + '</span>');
                            });
                        });
                    });
                </script><?php
            }
            ?>

            <?php
            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {return register_widget("jobsearch_featured_cause");});
