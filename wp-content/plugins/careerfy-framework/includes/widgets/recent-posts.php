<?php
/**
 * Careerfy Recent Post Class
 *
 * @package Recent Post
 */
if (!class_exists('Careerfy_Recent_Posts')) {

    /**
      Careerfy  Recent Post class used to implement the Custom flicker gallery widget.
     */
    class Careerfy_Recent_Posts extends WP_Widget {

        /**
         * Sets up a new careerfy  flicker widget instance.
         */
        public function __construct() {
            parent::__construct(
                    'careerfy_recent_posts', // Base ID.
                    __('Careerfy Recent Posts', 'careerfy-frame'), // Name.
                    array('classname' => 'widget_careerfy_recposts', 'description' => __('Recent Post widget for new posts.', 'careerfy-frame'))
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
            $view = isset($instance['view']) ? esc_attr($instance['view']) : '';
            $category = isset($instance['category']) ? esc_attr($instance['category']) : '';
            $view_style = isset($instance['view_style']) ? esc_attr($instance['view_style']) : '';
            $no_of_posts = isset($instance['no_of_posts']) ? esc_attr($instance['no_of_posts']) : '';
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
                    <label><?php esc_html_e('Style', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('view_style'),
                        'options' => array(
                            'style1' => esc_html__('Style 1', 'careerfy-frame'),
                            'style2' => esc_html__('Style 2', 'careerfy-frame'),
                        ),
                        'force_std' => $view_style,
                    );
                    $careerfy_form_fields->select_field($field_params);
                    ?>
                </div>
            </div>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Category', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $categories = get_categories(array(
                        'orderby' => 'name',
                    ));

                    $cate_array = array('' => esc_html__("Select Category", "careerfy-frame"));
                    if (is_array($categories) && sizeof($categories) > 0) {
                        foreach ($categories as $categ) {
                            $cate_array[$categ->slug] = $categ->cat_name;
                        }
                    }
                    $field_params = array(
                        'cus_name' => $this->get_field_name('category'),
                        'options' => $cate_array,
                        'force_std' => $category,
                    );
                    $careerfy_form_fields->select_field($field_params);
                    ?>
                </div>
            </div>

            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Number of Posts', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('no_of_posts'),
                        'force_std' => $no_of_posts,
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
            $instance['view'] = $new_instance['view'];
            $instance['view_style'] = $new_instance['view_style'];
            $instance['category'] = $new_instance['category'];
            $instance['no_of_posts'] = $new_instance['no_of_posts'];
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
            $view_style = empty($instance['view_style']) ? '' : $instance['view_style'];
            $category = empty($instance['category']) ? '' : apply_filters('widget_title', $instance['category']);
            $no_of_posts = empty($instance['no_of_posts']) ? ' ' : apply_filters('widget_title', $instance['no_of_posts']);
            if ('' === $instance['no_of_posts']) {
                $instance['no_of_posts'] = '3';
            }
            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : '';

            $before_title = isset($args['before_title']) ? $args['before_title'] : '';
            $after_title = isset($args['after_title']) ? $args['after_title'] : '';

            echo ( $before_widget );
            if ('' !== $title) {
                echo ( $before_title ) . esc_html($title) . ( $after_title );
            }

            $args = array(
                'post_type' => 'post',
                'posts_per_page' => $no_of_posts,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
            );

            if ($category && $category != '' && $category != '0') {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'category',
                        'field' => 'slug',
                        'terms' => $category,
                    ),
                );
            }

            $blog_query = new WP_Query($args);

            if ($blog_query->have_posts()) {
                global $post;
                
                $wigdt_class = 'widget_recent_posts';
                if ($view_style == 'style2') {
                    $wigdt_class = 'widget_newsfeed';
                }
                ?>
                <div class="<?php echo ($wigdt_class) ?>">
                    <div class="recent-posts">
                        <ul>
                            <?php
                            while ($blog_query->have_posts()) : $blog_query->the_post();

                                $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
                                if ($view_style == 'style2') {
                                    $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-posts-msmal');
                                } else {
                                    $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                                }
                                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                                ?>
                                <li>
                                    <figure>
                                        <a title="<?php echo get_the_title(get_the_ID()) ?>" href="<?php echo esc_url(get_permalink(get_the_ID())) ?>">
                                            <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="<?php echo get_the_title(get_the_ID()) ?>">
                                        </a>
                                    </figure>
                                    <?php
                                    if ($view_style == 'style2') {
                                        $ptimes_date = date('d-m-Y', strtotime(get_the_date()));
                                        $ptimes_time = date('H:i:s', strtotime(get_the_time()));
                                        $ptimes = strtotime($ptimes_date . ' ' . $ptimes_time);
                                        ?>
                                        <div class="widget_newsfeed_text">
                                            <h3><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wp_trim_words(get_the_title(get_the_ID()), 5, '...') ?></a></h3>
                                            <time><i class="fa fa-clock-o"></i> <?php echo careerfy_time_elapsed_string($ptimes) ?></time>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="recent-post-text">
                                            <h5><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wp_trim_words(get_the_title(get_the_ID()), 5, '...') ?></a></h5>
                                            <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>" class="read-more-btn"><i class="careerfy-icon careerfy-right-arrow-long"></i> <?php esc_html_e('read article', 'careerfy-frame') ?></a>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </li> 

                                <?php
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </ul>
                    </div>
                </div>
                <?php
            }

            echo ( $after_widget );
        }

    }

}
add_action('widgets_init', function() {
    return register_widget("careerfy_recent_posts");
});
