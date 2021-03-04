<?php
/*
 * widget for about us in footer
 */
if (!class_exists('Careerfy_about_Infos')) {

    class Careerfy_about_Infos extends WP_Widget
    {
        /**
         * Sets up a new careerfy  flicker widget instance.
         */
        public function __construct()
        {
            parent::__construct(
                'careerfy_about_infos', // Base ID.
                __('About Info', 'careerfy-frame'), // Name.
                array('classname' => 'widget_footer_contact', 'description' => __('About Info widget for new posts.', 'careerfy-frame'))
            );
        }

        /**
         * Outputs the careerfy  flicker widget settings form.
         *
         * @param array $instance Current settings.
         */
        function form($instance)
        {

            global $careerfy_form_fields;

            $instance = wp_parse_args((array)$instance, array('title' => ''));
            $title = $instance['title'];
            $style = $instance['about_style'];
            $desc = isset($instance['desc']) ? esc_attr($instance['desc']) : '';
            $market_contact = isset($instance['market_contact']) ? esc_attr($instance['market_contact']) : '';
            $suport_num = isset($instance['suport_num']) ? esc_attr($instance['suport_num']) : '';
            $work_time = isset($instance['work_time']) ? esc_attr($instance['work_time']) : '';
            ?>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Style', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('about_style'),
                        'force_std' => $style,
                        'options' => array(
                            'style1' => esc_html__('Style 1', 'careerfy-frame'),
                            'style2' => esc_html__('Style 2', 'careerfy-frame'),
                        ),
                    );
                    $careerfy_form_fields->select_field($field_params);
                    ?>
                </div>
            </div>
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
                    <label><?php esc_html_e('Contact Email', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('market_contact'),
                        'force_std' => $market_contact,
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Customer Support', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('suport_num'),
                        'force_std' => $suport_num,
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Working Hours', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'cus_name' => $this->get_field_name('work_time'),
                        'force_std' => $work_time,
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
        function update($new_instance, $old_instance)
        {
            $instance = $old_instance;
            $instance['about_style'] = $new_instance['about_style'];
            $instance['title'] = $new_instance['title'];
            $instance['desc'] = $new_instance['desc'];
            $instance['market_contact'] = $new_instance['market_contact'];
            $instance['suport_num'] = $new_instance['suport_num'];
            $instance['work_time'] = $new_instance['work_time'];
            return $instance;
        }

        /**
         * Outputs the content for the current careerfy  flicker widget instance.
         *
         * @param array $args Display arguments including 'before_title', 'after_title',
         * 'before_widget', and 'after_widget'.
         * @param array $instance Settings for the current Text widget instance.
         */
        function widget($args, $instance)
        {
            extract($args, EXTR_SKIP);
            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $desc = empty($instance['desc']) ? '' : $instance['desc'];
            $style = empty($instance['about_style']) ? '' : $instance['about_style'];
            $market_contact = isset($instance['market_contact']) ? esc_attr($instance['market_contact']) : '';
            $suport_num = isset($instance['suport_num']) ? esc_attr($instance['suport_num']) : '';
            $work_time = isset($instance['work_time']) ? esc_attr($instance['work_time']) : '';

            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : '';

            $before_title = isset($args['before_title']) ? $args['before_title'] : '';
            $after_title = isset($args['after_title']) ? $args['after_title'] : '';
            echo($before_widget);

            if ($style == 'style1') {

                if ('' !== $title) {
                    echo ($before_title) . esc_html($title) . ($after_title);
                } ?>

                <p class="careerfy-description"><?php echo($desc) ?></p>
                <?php if ($market_contact != '') { ?>
                    <ul class="careerfy-about-address">
                    <li>
                        <i class="fa fa-envelope-o"></i><?php echo esc_html__('General/Marketing Contact:', 'careerfy-frame'); ?>
                        <a href="mailto:<?php echo($market_contact) ?>" class=""><?php echo($market_contact); ?></a>
                    </li>
                    <?php
                }
                ?>
                <li>
                    <i class="fa fa-phone"></i>
                    <?php echo esc_html__('Customer Support Hotline:', 'careerfy-frame'); ?>
                    <a href="tel:<?php echo($suport_num); ?>"> <?php echo($suport_num); ?></a>
                </li>
                <li>
                    <i class="fa fa-clock-o"></i>
                    <?php echo esc_html__('Office Hours:', 'careerfy-frame'); ?> <?php echo($work_time); ?>
                </li>
                </ul>
                <?php
            } else {
                if ('' !== $title) {
                    echo ($before_title) . esc_html($title) . ($after_title);
                }
                ?>
                <p><?php echo($desc) ?></p>
                <ul class="careerfy-about-address">
                    <li><i class="fa fa-phone"></i> <a
                                href="tel:<?php echo $suport_num; ?>"><?php echo($suport_num); ?></a></li>
                    <li><i class="fa fa-envelope"></i> <a
                                href="mailto:<?php echo($market_contact) ?>"><?php echo($market_contact) ?></a></li>
                    <li><i class="fa fa-clock-o"></i><?php echo $work_time; ?></li>
                </ul>
            <?php }
            echo($after_widget);
        }

    }

}
add_action('widgets_init', function () {
    return register_widget("careerfy_about_infos");
});