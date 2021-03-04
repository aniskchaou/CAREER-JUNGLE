<?php
/**
 * Careerfy  Simple_Nletter Class
 *
 * @package Simple_Nletter
 */

if (!class_exists('Careerfy_Simple_Nletter')) {

    class Careerfy_Simple_Nletter extends WP_Widget {

        public function __construct() {
            parent::__construct(
                    'careerfy_simple_nletter', // Base ID.
                    __('Simple Newsletter', 'careerfy-frame'), // Name.
                    array('classname' => 'careerfy-footer-newslatter', 'description' => __('Simple Newsletter widget.', 'careerfy-frame'))
            );
        }

        function form($instance) {
            global $careerfy_form_fields, $careerfy_framework_options;

            $instance = wp_parse_args((array) $instance, array('title' => '', 'nws_style' => ''));
            $title = $instance['title'];
            $nws_style = isset($instance['nws_style']) ? $instance['nws_style'] : '';
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
                        'cus_name' => $this->get_field_name('nws_style'),
                        'force_std' => $nws_style,
                        'options' => array(
                            'style1' => esc_html__('Style 1', 'careerfy-frame'),
                            'style2' => esc_html__('Style 2', 'careerfy-frame'),
                        ),
                    );
                    $careerfy_form_fields->select_field($field_params);
                    ?>
                </div>
            </div> 

            <?php
        }

        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['nws_style'] = $new_instance['nws_style'];
            return $instance;
        }

        function widget($args, $instance) {
            global $careerfy_framework_options;
            extract($args, EXTR_SKIP);

            $counter = rand(3333, 322342);
            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));

            $nws_style = isset($instance['nws_style']) ? $instance['nws_style'] : '';

            $before_widget = isset($args['before_widget']) ? $args['before_widget'] : '';
            $after_widget = isset($args['after_widget']) ? $args['after_widget'] : '';

            $before_title = isset($args['before_title']) ? $args['before_title'] : '';
            $after_title = isset($args['after_title']) ? $args['after_title'] : '';

            $mailchimp_api_key = '';
            $mailchimp_list = '';
            if (isset($careerfy_framework_options['careerfy-mailchimp-api-key'])) {
                $mailchimp_api_key = $careerfy_framework_options['careerfy-mailchimp-api-key'];
            }

            echo ( $before_widget );
            ?>
            <script>
                function careerfy_mailchimp_submit<?php echo intval($counter); ?>(counter, admin_url) {
                    'use strict';
                    var $ = jQuery;
                    $('#newsletter_error_div_' + counter).fadeOut();
                    $('#newsletter_success_div_' + counter).fadeOut();
                    $('#process_' + counter).show();
                    $('#process_' + counter).html('<i class="fa fa-refresh fa-spin"></i>');

                    $.ajax({
                        type: 'POST',
                        url: admin_url,
                        data: "cp_email=" + $('#mc_email' + counter).val() + "&cp_fname=" + $('#mc_fname' + counter).val() + "&cp_lname=" + $('#mc_lname' + counter).val() + '&' + $('#mcform_' + counter).serialize() + '&action=careerfy_mailchimp',
                        dataType: "json",
                        success: function (response) {
                            $('#mcform_' + counter).get(0).reset();
                            if (response.type === 'error') {
                                $('#process_' + counter).hide();
                                $('#newsletter_mess_error_' + counter).html(response.msg);
                                $('#newsletter_error_div_' + counter).fadeIn();
                            } else {
                                $('#process_' + counter).hide();
                                $('#newsletter_mess_success_' + counter).html(response.msg);
                                $('#newsletter_success_div_' + counter).fadeIn();
                            }
                            $('#newsletter_mess_' + counter).fadeIn(600);
                            $('#newsletter_mess_' + counter).html(response);
                            $('#process_' + counter).html('');
                        }
                    });
                }
                function hide_div(div_hide) {
                    jQuery('#' + div_hide).hide();
                }
            </script>
            <?php
            if ($nws_style == 'style2') {
                ?>
                <div class="careerfy-simplenewslettr-2">
                    <?php
                }

                if ('' !== $title) {
                    if ($nws_style == 'style2') {
                        echo ($before_title) . esc_html($title) . ($after_title);
                    } else {
                        echo '<label>' . esc_html($title) . '</label>';
                    }
                }
                ?>
                <form action="javascript:careerfy_mailchimp_submit<?php echo intval($counter); ?>('<?php echo esc_js($counter); ?>','<?php echo admin_url('admin-ajax.php'); ?>')" id="mcform_<?php echo intval($counter); ?>" method="post">
                    <?php
                    if ($nws_style == 'style2') {
                        ?>
                        <div style="display: none;"><input name="mc_fname" id="mc_fname<?php echo intval($counter); ?>" value="" placeholder="<?php echo esc_html__('First Name', 'careerfy-frame'); ?>" type="text"></div>
                        <div style="display: none;"><input name="mc_lname" id="mc_lname<?php echo intval($counter); ?>" value="" placeholder="<?php echo esc_html__('Last Name', 'careerfy-frame'); ?>" type="text"></div>
                        <input type="email" id="mc_email<?php echo intval($counter); ?>" tabindex="0" name="mc_email" placeholder="<?php echo esc_html__('Email Address', 'careerfy-frame'); ?>">
                        <input id="btn_newsletter_<?php echo intval($counter); ?>" value="" type="submit"> <i class="careerfy-icon careerfy-right-arrow-long"></i>
                        <div id="process_<?php echo intval($counter); ?>" class="status status-message" style="display:none"></div>
                        <div id="newsletter_error_div_<?php echo intval($counter); ?>" style="display:none" class="alert alert-danger">
                            <button class="close" type="button" onclick="hide_div('newsletter_error_div_<?php echo intval($counter); ?>')" aria-hidden="true">×</button>
                            <p>
                                <i class="icon-warning"></i>
                                <span id="newsletter_mess_error_<?php echo intval($counter); ?>"></span>
                            </p>
                        </div>
                        <div id="newsletter_success_div_<?php echo intval($counter); ?>" style="display:none" class="alert alert-success">
                            <button class="close" type="button" onclick="hide_div('newsletter_success_div_<?php echo intval($counter); ?>')" aria-hidden="true">×</button>
                            <p><i class="icon-checkmark"></i><span id="newsletter_mess_success_<?php echo intval($counter); ?>"></span></p>
                        </div>
                        <?php
                    } else {
                        ?>
                        <ul>
                            <li style="display: none;"><input name="mc_fname" id="mc_fname<?php echo intval($counter); ?>" value="" placeholder="<?php echo esc_html__('First Name', 'careerfy-frame'); ?>" type="text"></li>
                            <li style="display: none;"><input name="mc_lname" id="mc_lname<?php echo intval($counter); ?>" value="" placeholder="<?php echo esc_html__('Last Name', 'careerfy-frame'); ?>" type="text"></li>
                            <li>
                                <i class="careerfy-icon careerfy-envelope-line"></i> <input type="email" id="mc_email<?php echo intval($counter); ?>" tabindex="0" name="mc_email" placeholder="<?php echo esc_html__('Sign up for newsletter professionally designed templates.', 'careerfy-frame'); ?>">
                                <div id="process_<?php echo intval($counter); ?>" class="status status-message" style="display:none"></div>
                                <div id="newsletter_error_div_<?php echo intval($counter); ?>" style="display:none" class="alert alert-danger">
                                    <button class="close" type="button" onclick="hide_div('newsletter_error_div_<?php echo intval($counter); ?>')" aria-hidden="true">×</button>
                                    <p>
                                        <i class="icon-warning"></i>
                                        <span id="newsletter_mess_error_<?php echo intval($counter); ?>"></span>
                                    </p>
                                </div> 
                                <div id="newsletter_success_div_<?php echo intval($counter); ?>" style="display:none" class="alert alert-success">
                                    <button class="close" type="button" onclick="hide_div('newsletter_success_div_<?php echo intval($counter); ?>')" aria-hidden="true">×</button>
                                    <p><i class="icon-checkmark"></i><span id="newsletter_mess_success_<?php echo intval($counter); ?>"></span></p>
                                </div>
                            </li>
                            <li><i class="careerfy-icon careerfy-right-arrow-long"></i> <input id="btn_newsletter_<?php echo intval($counter); ?>" value="<?php esc_html_e('Subscribe', 'careerfy-frame') ?>" type="submit"></li>
                        </ul>
                        <?php
                    }
                    ?>
                </form>
                <?php
                if ($nws_style == 'style2') {
                    echo ($before_title) . esc_html__('Stay in touch', 'careerfy-frame') . ($after_title);
                    echo careerfy_social_icons('footer-three-social', 'view-2');
                }
                if ($nws_style == 'style2') {
                    ?>
                </div>
                <?php
            }
            echo ( $after_widget );
        }

    }
}
add_action('widgets_init', function() {return register_widget("careerfy_simple_nletter");});