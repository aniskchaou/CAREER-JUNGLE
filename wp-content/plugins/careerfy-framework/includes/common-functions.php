<?php
/**
 * common functions files
 * html fields
 * @return functions
 */


if (!function_exists('careerfy_pagination')) {

    /*
     * Pagination.
     * @return markup
     */

    function careerfy_pagination($careerfy_query = '', $return = false)
    {

        global $wp_query;

        $careerfy_big = 999999999; // need an unlikely integer

        $careerfy_cus_query = $wp_query;

        if (!empty($careerfy_query)) {
            $careerfy_cus_query = $careerfy_query;
        }

        $careerfy_pagination = paginate_links(array(
            'base' => str_replace($careerfy_big, '%#%', esc_url(get_pagenum_link($careerfy_big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $careerfy_cus_query->max_num_pages,
            'prev_text' => '<i class="careerfy-icon careerfy-arrow-right-bold"></i>',
            'next_text' => '<i class="careerfy-icon careerfy-arrow-right-bold"></i>',
            'type' => 'array'
        ));


        if (is_array($careerfy_pagination) && sizeof($careerfy_pagination) > 0) {
            $careerfy_html = '<div class="careerfy-pagination-blog">';
            $careerfy_html .= '<ul>';
            foreach ($careerfy_pagination as $careerfy_link) {
                $prev_item = $next_item = false;
                if (strpos($careerfy_link, 'current') !== false) {
                    $careerfy_html .= '<li class="active"><a>' . preg_replace("/[^0-9]/", "", $careerfy_link) . '</a></li>';
                } else {
                    $dom = new DOMDocument;
                    @$dom->loadHTML($careerfy_link, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                    @$this_anchor = $dom->getElementsByTagName('a');
                    if ($this_anchor->length > 0) {

                        $a_page_num = $this_anchor->item(0)->nodeValue;
                        if ($this_anchor instanceof DOMNodeList) {
                            foreach ($this_anchor as $this_anch) {
                                if ($this_anch->hasAttribute('class') && false !== strpos($this_anch->getAttribute('class'), 'prev')) {
                                    $a_page_class = str_replace('prev', 'a-prev', $this_anch->getAttribute('class'));
                                    $this_anch->setAttribute('class', $a_page_class);
                                    $prev_item = true;
                                }
                                if ($this_anch->hasAttribute('class') && false !== strpos($this_anch->getAttribute('class'), 'next')) {
                                    $a_page_class = str_replace('next', 'a-next', $this_anch->getAttribute('class'));
                                    $this_anch->setAttribute('class', $a_page_class);
                                    $next_item = true;
                                }
                            }
                        }
                        $careerfy_link = $dom->saveHtml($dom);
                    }
                    if ($prev_item == true) {
                        $careerfy_html .= '<li class="prev">' . $careerfy_link . '</li>';
                    } else if ($next_item == true) {
                        $careerfy_html .= '<li class="next">' . $careerfy_link . '</li>';
                    } else {
                        $careerfy_html .= '<li>' . $careerfy_link . '</li>';
                    }
                }
            }
            $careerfy_html .= '</ul>';

            $careerfy_html .= '</div>';

            if ($return === false) {
                echo($careerfy_html);
            } else {
                return $careerfy_html;
            }
        }
    }

}


function careerfy_frame_input_post_vals_validate($post_data)
{
    if (!empty($post_data)) {
        foreach ($post_data as $post_input_key => $post_input_val) {
            if (is_array($post_input_val)) {
                $post_data[$post_input_key] = $post_input_val;
            } else if (strpos($post_input_val, 'alert(') > 0) {
                $post_data[$post_input_key] = '';
            } else if (strpos($post_input_val, 'alert)') > 0) {
                $post_data[$post_input_key] = '';
            } else if (strpos($post_input_val, 'focus=') > 0) {
                $post_data[$post_input_key] = '';
            } else if (strpos($post_input_val, 'onerror=') > 0) {
                $post_data[$post_input_key] = '';
            } else if (strpos($post_input_val, 'window.location=') > 0) {
                $post_data[$post_input_key] = '';
            } else {
                $post_data[$post_input_key] = $post_input_val;
            }
        }
    }
    return $post_data;
}

if (!function_exists('careerfy_get_user_ip_addr')) {

    function careerfy_get_user_ip_addr()
    {

        $ip = 'unknown';
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

}

if (!function_exists('careerfy_theme_custom_js')) {

    /*
     * Custom theme js code.
     * @return js
     */

    function careerfy_theme_custom_js()
    {
        global $careerfy_framework_options;

        $custom_js = isset($careerfy_framework_options['javascript_editor']) ? $careerfy_framework_options['javascript_editor'] : '';

        $js_code = '';
        if ($custom_js != '') {
            $js_code = '
            <script>
                ' . $custom_js . '
            </script>' . "\n";
        }
        echo($js_code);
    }

    add_action('wp_footer', 'careerfy_theme_custom_js', 99);
}

if (!function_exists('careerfy_excerpt')) {

    /*
     * Custom excerpt.
     * @return content
     */

    function careerfy_excerpt($length = '', $read_more = false, $cont = false, $id = '')
    {

        $excerpt = get_the_content();
        if ('' != $id) {
            $excerpt = get_the_content($id);
        }
        if (true === $cont) {

            if ('' == $id) {
                $excerpt = get_the_content();
            } else {
                $excerpt = get_post_field('post_content', $id);
            }
        }

        if ($length > 0) {
            $excerpt = wp_trim_words($excerpt, $length, '...');
        }

        if ($read_more) {
            $excerpt .= '<a class="careerfy-readmore-btn careerfy-color" href="' . esc_url(get_permalink(get_the_ID())) . '">' . esc_html__('Read More', 'careerfy-frame') . ' <i class="fa fa-angle-right"></i></a>';
        }

        return $excerpt;
    }

}

if (!function_exists('careerfy_frame_icon_picker')) {

    /*
     * Icon Picker.
     * @return markup
     */

    function careerfy_frame_icon_picker($value = '', $id = '', $name = '', $class = 'careerfy-icon-pickerr')
    {
        $html = "
        <script>
        jQuery(document).ready(function ($) {
            var this_icons;
            var rand_num = " . $id . ";
            var e9_element = $('#e9_element_' + rand_num).fontIconPicker({
                theme: 'fip-bootstrap'
            });
            icons_load_call.always(function () {
                this_icons = loaded_icons;
                // Get the class prefix
                var classPrefix = this_icons.preferences.fontPref.prefix,
                                icomoon_json_icons = [],
                                icomoon_json_search = [];
                $.each(this_icons.icons, function (i, v) {
                    icomoon_json_icons.push(classPrefix + v.properties.name);
                    if (v.icon && v.icon.tags && v.icon.tags.length) {
                        icomoon_json_search.push(v.properties.name + ' ' + v.icon.tags.join(' '));
                    } else {
                        icomoon_json_search.push(v.properties.name);
                    }
                });
                // Set new fonts on fontIconPicker
                e9_element.setIcons(icomoon_json_icons, icomoon_json_search);
                // Show success message and disable
                $('#e9_buttons_' + rand_num + ' button').removeClass('btn-primary').addClass('btn-success').text('Successfully loaded icons').prop('disabled', true);
            })
            .fail(function () {
                // Show error message and enable
                $('#e9_buttons_' + rand_num + ' button').removeClass('btn-primary').addClass('btn-danger').text('Error: Try Again?').prop('disabled', false);
            });
        });
        </script>";

        $html .= '
        <input type="text" id="e9_element_' . $id . '" class="' . $class . '" name="' . $name . '" value="' . $value . '">
        <span id="e9_buttons_' . $id . '" style="display:none">\
            <button autocomplete="off" type="button" class="btn btn-primary">Load from IcoMoon selection.json</button>
        </span>';

        return $html;
    }

}

if (!function_exists('careerfy_contact_form_submit')) {

    /**
     * User contact form submit
     * @generate mail
     */
    function careerfy_contact_form_submit()
    {
        global $careerfy_framework_options;

        $uname = isset($_POST['u_name']) ? $_POST['u_name'] : '';
        $uemail = isset($_POST['u_email']) ? $_POST['u_email'] : '';
        $usubject = isset($_POST['u_subject']) ? $_POST['u_subject'] : '';
        $uphone = isset($_POST['u_phone']) ? $_POST['u_phone'] : '';
        $umsg = isset($_POST['u_msg']) ? $_POST['u_msg'] : '';
        $utype = isset($_POST['u_type']) ? $_POST['u_type'] : '';

        if ($utype == 'content') {
            $cnt_email = get_bloginfo('admin_email');
        } else {
            $cnt_email = $utype;
        }

        $error = 0;
        $msg = '';

        if ($umsg != '' && $error == 0) {
            $umsg = esc_html($umsg);
        } else {
            $error = 1;
            $msg = esc_html__('Please type your message.', 'careerfy-frame');
        }

        if ($uemail != '' && $error == 0 && filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
            $uemail = esc_html($uemail);
        } else {
            $error = 1;
            $msg = esc_html__('Please Enter a valid email.', 'careerfy-frame');
        }
        if ($uname != '' && $error == 0) {
            $uname = esc_html($uname);
        } else {
            $error = 1;
            $msg = esc_html__('Please Enter your Name.', 'careerfy-frame');
        }

        if ($msg == '' && $error == 0) {

            $subject = sprintf('%s - Contact Form Message - (%s)', get_bloginfo('name'), $usubject);

            add_filter('wp_mail_from', function () {
                $p_mail_from = get_bloginfo('admin_email');
                return $p_mail_from;
            });
            add_filter('wp_mail_from_name', function () {
                $p_mail_from = get_bloginfo('name');
                return $p_mail_from;
            });
            add_filter('wp_mail_content_type', function () {
                return 'text/html';
            });

            $headers = array('Reply-To: ' . $uname . ' <' . $uemail . '>');

            $email_message = sprintf(esc_html__('Name : %s', 'careerfy-frame'), $uname) . "<br>";
            $email_message .= sprintf(esc_html__('Email : %s', 'careerfy-frame'), $uemail) . "<br>";
            $email_message .= sprintf(esc_html__('Subject : %s', 'careerfy-frame'), $usubject) . "<br>";
            $email_message .= sprintf(esc_html__('Phone Number: %s', 'careerfy-frame'), $uphone) . "<br>";
            $email_message .= sprintf(esc_html__('Message : %s', 'careerfy-frame'), $umsg) . "<br>";
            if (wp_mail($cnt_email, '=?utf-8?B?' . base64_encode($subject) . '?=', $email_message, $headers)) {
                $msg = esc_html__('Mail sent successfully', 'careerfy-frame');
            } else {
                $msg = esc_html__('Error! There is some problem.', 'careerfy-frame');
            }
        }

        echo json_encode(array('msg' => $msg));
        wp_die();
    }

    add_action('wp_ajax_careerfy_contact_form_submit', 'careerfy_contact_form_submit');
    add_action('wp_ajax_nopriv_careerfy_contact_form_submit', 'careerfy_contact_form_submit');
}

if (!function_exists('careerfy_admin_gallery')) {

    function careerfy_admin_gallery($id = 'careerfy_gallery', $name = '')
    {
        global $post;

        wp_enqueue_media();

        $careerfy_field_random_id = rand(10000000, 99999999);
        ?>
        <div id="gallery_container_<?php echo esc_attr($careerfy_field_random_id); ?>"
             data-ecid="careerfy_field_<?php echo esc_attr($id) ?>">
            <?php
            $careerfy_inline_script = '
		<script>
                jQuery(document).ready(function () {
                    jQuery("#gallery_sortable_' . esc_attr($careerfy_field_random_id) . '").sortable({
                        out: function (event, ui) {
                            careerfy_field_gallery_sorting_list(\'careerfy_field_' . sanitize_html_class($id) . '\', \'' . esc_attr($careerfy_field_random_id) . '\');
                        }
                    });

                    careerfy_field_num_of_items(\'' . esc_attr($id) . '\', \'' . absint($careerfy_field_random_id) . '\');

                    jQuery(\'#gallery_container_' . esc_attr($careerfy_field_random_id) . '\').on(\'click\', \'a.delete\', function () {
                        var listItems = jQuery(\'#gallery_sortable_' . esc_attr($careerfy_field_random_id) . '\').children();
                        var count = listItems.length;
                        careerfy_field_num_of_items(\'' . esc_attr($id) . '\', \'' . absint($careerfy_field_random_id) . '\', count);
                        jQuery(this).closest(\'li.image\').remove();
                        careerfy_field_gallery_sorting_list(\'careerfy_field_' . sanitize_html_class($id) . '\', \'' . esc_attr($careerfy_field_random_id) . '\');
                    });
                });
		</script>';
            echo force_balance_tags($careerfy_inline_script);
            ?>
            <ul class="careerfy-gallery-images"
                id="gallery_sortable_<?php echo esc_attr($careerfy_field_random_id); ?>">
                <?php
                $gallery = get_post_meta($post->ID, 'careerfy_field_' . $id, true);
                $gallery_titles = get_post_meta($post->ID, 'careerfy_field_' . $id . '_title', true);
                $gallery_style = get_post_meta($post->ID, 'careerfy_field_' . $id . '_style', true);
                $gallery_description = get_post_meta($post->ID, 'careerfy_field_' . $id . '_description', true);
                $gallery_link = get_post_meta($post->ID, 'careerfy_field_' . $id . '_link', true);
                $careerfy_field_gal_counter = 0;
                if (is_array($gallery) && sizeof($gallery) > 0) {
                    foreach ($gallery as $attach_id) {

                        if ($attach_id != '') {

                            $post_thumbnail_image = wp_get_attachment_image_src($attach_id, 'thumbnail');
                            $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';

                            $careerfy_field_gal_id = rand(156546, 956546);

                            $careerfy_field_gallery_title = isset($gallery_titles[$careerfy_field_gal_counter]) ? $gallery_titles[$careerfy_field_gal_counter] : '';
                            $careerfy_field_gallery_style = isset($gallery_style[$careerfy_field_gal_counter]) ? $gallery_style[$careerfy_field_gal_counter] : '';
                            $careerfy_field_gallery_description = isset($gallery_description[$careerfy_field_gal_counter]) ? $gallery_description[$careerfy_field_gal_counter] : '';
                            $careerfy_field_gallery_link = isset($gallery_link[$careerfy_field_gal_counter]) ? $gallery_link[$careerfy_field_gal_counter] : '';

                            $grid_selected = '';
                            $medium_selected = '';
                            $large_selected = '';
                            if ($careerfy_field_gallery_style == 'medium') {
                                $medium_selected = 'selected="selected"';
                            } elseif ($careerfy_field_gallery_style == 'large') {
                                $large_selected = 'selected="selected"';
                            } else {
                                $grid_selected = 'selected="selected"';
                            }

                            $careerfy_field_attach_img = '<div class="gal-thumb"><img src="' . $post_thumbnail_src . '" width="150" alt="" /></div>';
                            echo '
                            <li class="image" data-attachment_id="' . esc_attr($careerfy_field_gal_id) . '">
                                    ' . $careerfy_field_attach_img . '
                                    <input type="hidden" value="' . $attach_id . '" name="careerfy_field_' . $id . '[]" />
                                    <div class="gal-actions">
                                        <span style="display:none;"><a href="javascript:void(0);" class="update-gal" data-id="' . absint($careerfy_field_gal_id) . '"><i class="fa fa-pencil"></i></a></span>
                                        <span><a href="javascript:void(0);" class="delete" data-tip="' . __('Delete', 'careerfy-frame') . '"><i class="fa fa-times"></i></a></span>
                                    </div>
                                    <div id="edit_gal_form' . absint($careerfy_field_gal_id) . '" style="display: none;" class="gallery-form-elem">
                                        <div class="gallery-form-inner">
                                            <div class="careerfy-heading-area">
                                                    <h3>' . __('Edit', 'careerfy-frame') . '</h3>
                                                    <a href="javascript:void(0);" class="close-gal" data-id="' . absint($careerfy_field_gal_id) . '"> <i class="fa fa-times"></i></a>
                                            </div>
                                            ' . $careerfy_field_attach_img . '
                                            <div class="careerfy-element-field">
                                                    <div class="elem-label">
                                                            <label>' . __('Title', 'careerfy-frame') . '</label>
                                                    </div>
                                                    <div class="elem-field">
                                                            <input type="text" name="careerfy_field_' . $id . '_title[]" value="' . esc_html($careerfy_field_gallery_title) . '" />
                                                    </div>
                                            </div>

                                            <div class="careerfy-element-field">
                                                    <div class="elem-label">
                                                            <label>' . __('Description', 'careerfy-frame') . '</label>
                                                    </div>
                                                    <div class="elem-field">
                                                            <textarea type="text" name="careerfy_field_' . $id . '_description[]" >' . force_balance_tags($careerfy_field_gallery_description) . '</textarea>
                                                    </div>
                                            </div>

                                            <div class="careerfy-element-field">
                                                    <div class="elem-label">
                                                            <label>' . __('URL', 'careerfy-frame') . '</label>
                                                    </div>
                                                    <div class="elem-field">
                                                            <input type="text" name="careerfy_field_' . $id . '_link[]" value="' . esc_html($careerfy_field_gallery_link) . '" />
                                                    </div>
                                            </div>

                                            <div class="careerfy-element-field">
                                                    <div class="elem-label">
                                                            <label>' . __('Style', 'careerfy-frame') . '</label>
                                                    </div>
                                                    <div class="elem-field">
                                                            <select name="careerfy_field_' . $id . '_style[]" value="' . esc_html($careerfy_field_gallery_style) . '">
                                                            <option value="grid" ' . esc_html($grid_selected) . '>Grid</option>
                                                            <option value="medium" ' . esc_html($medium_selected) . '>Medium</option>
                                                            <option value="large" ' . esc_html($large_selected) . '>Large</option>
                                                            </select>
                                                    </div>
                                            </div>
                                            <input type="button" class="close-gal" data-id="' . absint($careerfy_field_gal_id) . '" value="' . __('Update', 'careerfy-frame') . '" />
                                    </div>
                                </div>
                            </li>';
                        }
                        $careerfy_field_gal_counter++;
                    }
                }
                ?>
            </ul>
        </div>
        <div id="careerfy_field_<?php echo esc_attr($id) ?>_temp"></div>
        <input type="hidden" value="" name="careerfy_field_<?php echo esc_attr($id) ?>_num"/>
        <div class="careerfy-add-gal-btn">
            <label class="browse-icon add_gallery hide-if-no-js"
                   data-id="<?php echo 'careerfy_field_' . sanitize_html_class($id); ?>"
                   data-rand_id="<?php echo esc_attr($careerfy_field_random_id); ?>">
                <input type="button" class="left" data-choose="<?php echo esc_attr($name); ?>"
                       data-update="<?php echo esc_attr($name); ?>"
                       data-delete="<?php _e('Delete', 'careerfy-frame'); ?>" value="<?php echo esc_attr($name); ?>">
            </label>
        </div>
        <?php
    }

}

if (!function_exists('careerfy_social_share')) {

    /*
     * Social Icons.
     * @return
     */

    function careerfy_social_share()
    {
        global $careerfy_framework_options;

        wp_enqueue_script('careerfy-addthis');

        $social_facebook = isset($careerfy_framework_options['careerfy-social-sharing-facebook']) ? $careerfy_framework_options['careerfy-social-sharing-facebook'] : '';
        $social_twitter = isset($careerfy_framework_options['careerfy-social-sharing-twitter']) ? $careerfy_framework_options['careerfy-social-sharing-twitter'] : '';
        $social_google = isset($careerfy_framework_options['careerfy-social-sharing-google']) ? $careerfy_framework_options['careerfy-social-sharing-google'] : '';
        $social_pinterest = isset($careerfy_framework_options['careerfy-social-sharing-pinterest']) ? $careerfy_framework_options['careerfy-social-sharing-pinterest'] : '';
        $social_tumblr = isset($careerfy_framework_options['careerfy-social-sharing-tumblr']) ? $careerfy_framework_options['careerfy-social-sharing-tumblr'] : '';
        $social_dribbble = isset($careerfy_framework_options['careerfy-social-sharing-dribbble']) ? $careerfy_framework_options['careerfy-social-sharing-dribbble'] : '';
        $social_stumbleupon = isset($careerfy_framework_options['careerfy-social-sharing-stumbleupon']) ? $careerfy_framework_options['careerfy-social-sharing-stumbleupon'] : '';

        $social_youtube = isset($careerfy_framework_options['careerfy-social-sharing-youtube']) ? $careerfy_framework_options['careerfy-social-sharing-youtube'] : '';
        $social_sharemore = isset($careerfy_framework_options['careerfy-social-sharing-more']) ? $careerfy_framework_options['careerfy-social-sharing-more'] : '';

        if ($social_facebook == 'on' || $social_twitter == 'on' || $social_google == 'on' || $social_tumblr == 'on' || $social_dribbble == 'on' || $social_stumbleupon == 'on' || $social_youtube == 'on') {
            ?>

            <ul class="careerfy-blog-social-network">
                <li><span><?php esc_html_e('Share this post', 'careerfy-frame') ?></span></li>
                <?php
                if ($social_facebook == 'on') {
                    ?>
                    <li>
                        <a class="addthis_button_facebook">
                            <i class="fa fa-facebook"></i>
                        </a>
                    </li>
                    <?php
                }
                if ($social_twitter == 'on') {
                    ?>
                    <li>
                        <a class="addthis_button_twitter">
                            <i class="fa fa-twitter"></i>
                        </a>
                    </li>
                    <?php
                }
                if ($social_google == 'on') {
                    ?>
                    <li>
                        <a class="addthis_button_google">
                            <i class="fa fa-google-plus"></i>
                        </a>
                    </li>
                    <?php
                }
                if ($social_tumblr == 'on') {
                    ?>
                    <li>
                        <a class="addthis_button_tumblr">
                            <i class="fa fa-tumblr"></i>
                        </a>
                    </li>
                    <?php
                }
                if ($social_dribbble == 'on') {
                    ?>
                    <li>
                        <a class="addthis_button_dribbble">
                            <i class="fa fa-dribbble"></i>
                        </a>
                    </li>
                    <?php
                }

                if ($social_stumbleupon == 'on') {
                    ?>
                    <li>
                        <a class="addthis_button_stumbleupon">
                            <i class="fa fa-stumbleupon"></i>
                        </a>
                    </li>
                    <?php
                }
                if ($social_youtube == 'on') {
                    ?>
                    <li>
                        <a class="addthis_button_youtube">
                            <i class="fa fa-youtube"></i>
                        </a>
                    </li>
                    <?php
                }
                if ($social_sharemore == 'on') {
                    ?>
                    <li>
                        <a class="addthis_button_compact">
                            <i class="fa fa-plus"></i>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }
    }

}

if (!function_exists('careerfy_get_image_id')) {

    function careerfy_get_image_id($image_url)
    {
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url));
        $attachment = isset($attachment[0]) ? $attachment[0] : '';
        return $attachment;
    }

}

if (!function_exists('careerfy_framework_get_times_array')) {

    function careerfy_framework_get_times_array($interval = '+30 minutes', $same_value = false)
    {

        $output = array();

        $current = strtotime('00:00');
        $end = strtotime('23:59');

        while ($current <= $end) {
            $time = date('H:i', $current);
            if ($same_value == false) {
                $output[$time] = date('h.i A', $current);
            } else {
                $output[$time] = date('H:i', $current);
            }
            $current = strtotime($interval, $current);
        }

        return $output;
    }

}

if (!function_exists('careerfy_all_users')) {

    /**
     * all users.
     * @return markup
     */
    function careerfy_all_users($first_element = false, $dropdown = false, $role = '')
    {
        $args = array(
            'order' => 'ASC',
            'orderby' => 'display_name',
        );
        if ($role != '') {
            $args['role'] = $role;
        }
        $user_query = new WP_User_Query($args);
        // Get the results
        $all_users = $user_query->get_results();
        $users_arr = array();
        if ($first_element == true) {
            $users_arr[''] = esc_html__('Please select user', 'careerfy-frame');
        }
        if (!empty($all_users)) {
            foreach ($all_users as $alb) {
                $author_info = get_userdata($alb->ID);
                $this_users = $author_info->display_name;
                if ($dropdown == false) {
                    $users_arr[$this_users] = $alb->ID;
                } else {
                    $users_arr[$alb->ID] = $this_users;
                }
            }
        }
        return $users_arr;
    }

}

if (!function_exists('careerfy_get_times_array')) {

    function careerfy_get_times_array($interval = '+30 minutes', $same_value = false)
    {

        $output = array();

        $current = strtotime('00:00');
        $end = strtotime('23:59');

        while ($current <= $end) {
            $time = date('H:i', $current);
            if ($same_value == false) {
                $output[$time] = date('h.i A', $current);
            } else {
                $output[$time] = date('H:i', $current);
            }
            $current = strtotime($interval, $current);
        }

        return $output;
    }

}

if (!function_exists('careerfy_get_user_field')) {

    function careerfy_get_user_field($selected_user, $role = '')
    {
        global $careerfy_form_fields;
        $user_first_element = esc_html__('Please select user', 'careerfy-frame');
        $users = array(
            '' => $user_first_element,
        );
        if ($selected_user) {
            $author_info = get_userdata($selected_user);
            $this_users = $author_info->display_name;
            $users[$selected_user] = $this_users;
        }

        $rand_num = rand(1234, 6867867);
        $field_params = array(
            'classes' => 'user_field',
            'id' => 'user_field_' . $rand_num,
            'name' => 'users',
            'options' => $users,
            'force_std' => $selected_user,
            'ext_attr' => ' data-randid="' . $rand_num . '" data-forcestd="' . $selected_user . '" data-loaded="false" data-role="' . $role . '"',
        );
        $careerfy_form_fields->select_field($field_params);
        ?><span class="careerfy-field-loader user_loader_<?php echo absint($rand_num); ?>"></span><?php
    }

}

if (!function_exists('careerfy_load_all_users_data')) {

    function careerfy_load_all_users_data()
    {
        $force_std = $_POST['force_std'];
        $role = $_POST['role'];
        $all_users = careerfy_all_users(true, true, $role);
        $html .= "";
        if (isset($all_users) && !empty($all_users)) {
            foreach ($all_users as $user_var => $user_val) {
                $selected = $user_var == $force_std ? ' selected="selected"' : '';
                $html .= "<option{$selected} value=\"{$user_var}\">{$user_val}</option>" . "\n";
            }
        }
        echo json_encode(array('html' => $html));

        wp_die();
    }

    add_action('wp_ajax_careerfy_load_all_users_data', 'careerfy_load_all_users_data');
    add_action('wp_ajax_nopriv_careerfy_load_all_users_data', 'careerfy_load_all_users_data');
}

if (!function_exists('careerfy_load_all_custom_post_data')) {

    function careerfy_load_all_custom_post_data()
    {
        $force_std = $_POST['force_std'];
        $posttype = $_POST['posttype'];
        $args = array(
            'posts_per_page' => "-1",
            'post_type' => $posttype,
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_query' => array(),
        );
        $custom_query = new WP_Query($args);
        $all_records = $custom_query->posts;

        $html .= "";
        if (isset($all_records) && !empty($all_records)) {
            foreach ($all_records as $user_var) {
                $selected = $user_var == $force_std ? ' selected="selected"' : '';
                $post_title = get_the_title($user_var);
                $html .= "<option{$selected} value=\"{$user_var}\">{$post_title}</option>" . "\n";
            }
        }
        echo json_encode(array('html' => $html));

        wp_die();
    }

    add_action('wp_ajax_careerfy_load_all_custom_post_data', 'careerfy_load_all_custom_post_data');
    add_action('wp_ajax_nopriv_careerfy_load_all_custom_post_data', 'careerfy_load_all_custom_post_data');
}

if (!function_exists('careerfy_count_custom_post_with_filter')) {

    function careerfy_count_custom_post_with_filter($posttype, $arg = '')
    {

        $args = array(
            'posts_per_page' => "1",
            'post_type' => $posttype,
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_query' => $arg,
        );

        $custom_query = new WP_Query($args);
        $all_post_count = $custom_query->found_posts;
        return $all_post_count;
    }

}

if (!function_exists('careerfy_get_custom_post_field')) {

    function careerfy_get_custom_post_field($selected_id, $custom_post_slug, $field_label, $field_name)
    {
        global $careerfy_form_fields;
        $custom_post_first_element = esc_html__('Please select ', 'careerfy-frame');
        $custom_posts = array(
            '' => $custom_post_first_element . $field_label,
        );
        if ($selected_id) {
            $this_custom_posts = get_the_title($selected_id);
            $custom_posts[$selected_id] = $this_custom_posts;
        }

        $rand_num = rand(1234, 6867867);
        $field_params = array(
            'classes' => 'custom_post_field',
            'id' => 'custom_post_field_' . $rand_num,
            'name' => $field_name,
            'options' => $custom_posts,
            'force_std' => $selected_id,
            'ext_attr' => ' data-randid="' . $rand_num . '" data-forcestd="' . $selected_id . '" data-loaded="false" data-posttype="' . $custom_post_slug . '"',
        );
        $careerfy_form_fields->select_field($field_params);
        ?>
        <span class="careerfy-field-loader custom_post_loader_<?php echo absint($rand_num); ?>"></span>
        <?php
    }

}

if (!function_exists('careerfy_frame_template_path')) {

    function careerfy_frame_template_path()
    {
        return apply_filters('careerfy_framework_template_path', 'careerfy-framework/');
    }

}

if (!function_exists('careerfy_frame_get_template_part')) {

    function careerfy_frame_get_template_part($slug = '', $name = '', $ext_template = '')
    {
        $template = '';
        if ($ext_template != '') {
            $ext_template = trailingslashit($ext_template);
        }
        if ($name) {
            $template = locate_template(array("{$slug}-{$name}.php", careerfy_frame_template_path() . "templates/{$ext_template}/{$slug}-{$name}.php"));
        }

        if (!$template && $name && file_exists(careerfy_framework_get_path() . "templates/{$ext_template}/{$slug}-{$name}.php")) {
            $template = careerfy_framework_get_path() . "templates/{$ext_template}{$slug}-{$name}.php";
        }
        if (!$template) {
            $template = locate_template(array("{$slug}.php", careerfy_frame_template_path() . "{$ext_template}/{$slug}.php"));
        }
        if ($template) {
            load_template($template, false);
        }
    }

}
if (!function_exists('careerfy_get_cached_obj')) {

    function careerfy_get_cached_obj($cache_variable, $args, $time = 12, $cache = true, $type = 'wp_query', $taxanomy_name = '')
    {
        $loop_obj = '';
        if ($cache == true) {
            $time_string = $time * HOUR_IN_SECONDS;
            if ($cache_variable != '') {
                if (false === ($loop_obj = wp_cache_get($cache_variable))) {
                    if ($type == 'wp_query') {
                        $loop_obj = new WP_Query($args);
                    } else if ($type == 'get_term') {
                        $loop_obj = array();
                        $terms = get_terms($taxanomy_name, $args);
                        if (sizeof($terms) > 0) {
                            foreach ($terms as $term_data) {
                                $loop_obj[] = $term_data->name;
                            }
                        }
                    }
                    wp_cache_set($cache_variable, $loop_obj, $time_string);
                }
            }
        } else {
            if ($type == 'wp_query') {
                $loop_obj = new WP_Query($args);
            } else if ($type == 'get_term') {
                $loop_obj = array();
                $terms = get_terms($taxanomy_name, $args);
                if (sizeof($terms) > 0) {
                    foreach ($terms as $term_data) {
                        $loop_obj[] = $term_data->name;
                    }
                }
            }
        }


        return $loop_obj;
    }

}

if (!function_exists('careerfy_server_protocol')) {

    function careerfy_server_protocol()
    {

        if (is_ssl()) {
            return 'https://';
        }

        return 'http://';
    }

}
if (!function_exists('careerfy_time_elapsed_string')) {


    function careerfy_time_elapsed_string($ptime)
    {
        if ($ptime != '') {
            return human_time_diff($ptime, current_time('timestamp', 1)) . " " . esc_html__('ago', 'careerfy-frame');
        } else {
            return '';
        }
    }

}

if (!function_exists('careerfy_wpml_lang_page_id')) {

    function careerfy_wpml_lang_page_id($id = '', $post_type = '')
    {
        if (function_exists('icl_object_id') && $id != '' && is_numeric($id) && $post_type != '') {
            return icl_object_id($id, $post_type, true);
        } else {
            return $id;
        }
    }

}

if (!function_exists('careerfy_breadcrumbs')) {

    /**
     * Breadcrumbs markup section.
     * @return markup
     */
    function careerfy_breadcrumbs($candidate_id = '')
    {
        global $wp_query, $post, $careerfy_framework_options;
        $header_style = isset($careerfy_framework_options['header-style']) ? $careerfy_framework_options['header-style'] : '';
        $bread_crumb_class = isset($view) && !empty($view) ? $view : '';

        $text['home'] = '' . __('Home', 'careerfy-frame'); // text for the 'Home' link
        $text['category'] = '%s'; // text for a category page
        $text['search'] = '%s'; // text for a search results page
        $text['tag'] = '%s'; // text for a tag page
        $text['author'] = '%s'; // text for an author page
        $text['404'] = __('Error 404', 'careerfy-frame'); // text for the 404 page

        $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
        $showOnHome = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
        $delimiter = ''; // delimiter between crumbs
        $before = '<li class="active">'; // tag before the current crumb
        $after = '</li>'; // tag after the current crumb

        $current_page = __('Current Page', 'careerfy-frame');
        $homeLink = esc_url(home_url('/')) . '/';
        $linkBefore = '<li>';
        $linkAfter = '</li>';
        $linkAttr = '';
        $link = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;
        $linkhome = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;
        $bread_crumb_args = array(
            'candidate_id' => $candidate_id,
            'bread_crumb_class' => 'careerfy-breadcrumb',

        );
        ?>
        <div class="<?php echo apply_filters('careerfy_breadcrum_main_con_class', $bread_crumb_args) ?>">
            <?php
            echo '<ul>' . sprintf($linkhome, $homeLink, $text['home']) . $delimiter;
            if (is_category()) {
                $thisCat = get_category(get_query_var('cat'), false);
                if ($thisCat->parent != 0) {
                    $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
                    $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
                    $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
                    echo($cats);
                }
                echo ($before) . sprintf($text['category'], single_cat_title('', false)) . ($after);
            } elseif (is_search()) {

                echo ($before) . sprintf($text['search'], get_search_query()) . $after;
            } elseif (is_day()) {

                echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
                echo sprintf($link, get_month_link(get_the_time('Y'), get_the_time('m')), get_the_time('F')) . $delimiter;
                echo ($before) . get_the_time('d') . $after;
            } elseif (is_month()) {

                echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
                echo ($before) . get_the_time('F') . $after;
            } elseif (is_year()) {

                echo ($before) . get_the_time('Y') . $after;
            } elseif (is_single() && !is_attachment()) {

                if (function_exists("is_shop") && get_post_type() == 'product') {
                    $careerfy_shop_page_id = wc_get_page_id('shop');
                    $current_page = get_the_title(get_the_id());
                    $careerfy_shop_page = "<li><a href='" . esc_url(get_permalink($careerfy_shop_page_id)) . "'>" . get_the_title($careerfy_shop_page_id) . "</a></li>";
                    echo($careerfy_shop_page);
                    if ($showCurrent == 1)
                        echo ($before) . $current_page . $after;
                } else if (get_post_type() != 'post') {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    $current_page = get_the_title(get_the_id());
                    printf($link, $homeLink . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);
                    if ($showCurrent == 1)
                        echo ($delimiter) . $before . $current_page . $after;
                } else {

                    $cat = get_the_category();
                    $cat = $cat[0];
                    $cats = get_category_parents($cat, TRUE, $delimiter);
                    if ($showCurrent == 0)
                        $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
                    $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
                    $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
                    echo($cats);
                    if ($showCurrent == 1)
                        echo ($before) . $current_page . $after;
                }
            } elseif (!is_single() && !is_page() && get_post_type() <> '' && get_post_type() != 'post' && !is_404()) {

                $post_type = get_post_type_object(get_post_type());
                echo ($before) . $post_type->labels->singular_name . $after;
            } elseif (isset($wp_query->query_vars['taxonomy']) && !empty($wp_query->query_vars['taxonomy'])) {
                $taxonomy = $taxonomy_category = '';
                $taxonomy = $wp_query->query_vars['taxonomy'];
                echo ($before) . $wp_query->query_vars[$taxonomy] . $after;
            } elseif (is_page() && !$post->post_parent) {

                if ($showCurrent == 1)
                    echo ($before) . get_the_title() . $after;

            } elseif (is_page() && $post->post_parent) {

                $parent_id = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_page($parent_id);
                    $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
                    $parent_id = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                for ($i = 0; $i < count($breadcrumbs); $i++) {
                    echo($breadcrumbs[$i]);
                    if ($i != count($breadcrumbs) - 1)
                        echo($delimiter);
                }

                if ($showCurrent == 1)
                    echo($delimiter . $before . get_the_title() . $after);
            } elseif (is_tag()) {
                echo ($before) . sprintf($text['tag'], single_tag_title('', false)) . $after;
            } elseif (is_author()) {

                global $author;
                $userdata = get_userdata($author);
                echo ($before) . sprintf($text['author'], $userdata->display_name) . $after;
            } elseif (is_404()) {

                echo ($before) . $text['404'] . $after;
            }
            echo '</ul>';
            ?>
        </div>
        <?php
    }
}

add_filter('careerfy_breadcrum_main_con_class', 'careerfy_breadcrumb_main_wrapper_class');
function careerfy_breadcrumb_main_wrapper_class($arg = '')
{
    $candidate_id = isset($arg['candidate_id']) ? $arg['candidate_id'] : '';
    $bread_crumb_class = isset($arg['bread_crumb_class']) ? $arg['bread_crumb_class'] : '';

    global $jobsearch_plugin_options;
    $cand_view = isset($jobsearch_plugin_options['jobsearch_cand_detail_views']) && !empty($jobsearch_plugin_options['jobsearch_cand_detail_views']) ? $jobsearch_plugin_options['jobsearch_cand_detail_views'] : '';
    $cand_view = apply_filters('careerfy_cand_detail_page_style_display', $cand_view, $candidate_id);

    $show_subheader = false;
    $show_subheader = apply_filters('careerfy_subheader_display_switch', $show_subheader);
    if ($show_subheader == true && $cand_view == 'view5') {
        $bread_crumb_class = 'careerfy-breadcrumb-style5';
    }
    return $bread_crumb_class;
}


if (!function_exists('careerfy_post_page_title')) {

    function careerfy_post_page_title()
    {

        if (function_exists('is_shop') && is_shop()) {
            $careerfy_page_id = wc_get_page_id('shop');
            echo get_the_title($careerfy_page_id);
        } else if (!is_page() && !is_search() && !is_404() && !is_single()) {
            the_archive_title();
        } else if (is_search()) {
            printf(__('Search for : %s', 'careerfy-frame'), '<span>' . get_search_query() . '</span>');
        } else if (is_404()) {
            echo '404';
        } else if (is_page() || is_singular()) {
            echo apply_filters('careerfy_subheader_post_page_title', get_the_title(), get_the_ID());
        }
    }

}


if (!function_exists('careerfy_subheader_display_switch_callback')) {

    function careerfy_subheader_display_switch_callback($show_subheader = false)
    {
        global $jobsearch_plugin_options;

        $cand_view = isset($jobsearch_plugin_options['jobsearch_cand_detail_views']) && !empty($jobsearch_plugin_options['jobsearch_cand_detail_views']) ? $jobsearch_plugin_options['jobsearch_cand_detail_views'] : '';
        $job_view = isset($jobsearch_plugin_options['jobsearch_job_detail_views']) && !empty($jobsearch_plugin_options['jobsearch_job_detail_views']) ? $jobsearch_plugin_options['jobsearch_job_detail_views'] : '';
        $emp_view = isset($jobsearch_plugin_options['jobsearch_emp_detail_views']) && !empty($jobsearch_plugin_options['jobsearch_emp_detail_views']) ? $jobsearch_plugin_options['jobsearch_emp_detail_views'] : '';

        // posts
        $cand_post_style = get_post_meta(get_the_ID(), 'careerfy_field_candidate_post_detail_style', true);
        $emp_post_style = get_post_meta(get_the_ID(), 'careerfy_field_employer_post_detail_style', true);

        // employer subheader switch
        $emp_switch = true;
        if ((isset($emp_post_style) && $emp_post_style == 'view1')) {
            $emp_switch = false;
        } elseif ((!isset($emp_post_style) || $emp_post_style == '')) {
            if ((isset($emp_view) && $emp_view == 'view1')) {
                $emp_switch = false;
            } else {
                $emp_switch = true;
            }
        } elseif (isset($emp_post_style) && $emp_post_style != 'view1') {
            $emp_switch = true;
        }
        // candidate subheader switch
        $cand_switch = true;
        if ((isset($cand_post_style) && ($cand_post_style == 'view1'))) {
            $cand_switch = false;
        } elseif ((!isset($cand_post_style) || $cand_post_style == '')) {
            if ((isset($cand_view) && $cand_view == 'view1')) {
                $cand_switch = false;
            } else {
                $cand_switch = true;
            }
        } elseif (isset($cand_post_style) && $cand_post_style != 'view1') {
            $cand_switch = true;
        }


        if (is_singular('job')) {
            $show_subheader = true;
        } elseif (is_singular('candidate') && $cand_switch) {
            $show_subheader = true;
        } elseif (is_singular('employer') && $emp_switch) {
            $show_subheader = true;
        }

        //echo '( '.$show_subheader.' )';

        return $show_subheader;
    }

    add_filter('careerfy_subheader_display_switch', 'careerfy_subheader_display_switch_callback');
}


if (!function_exists('careerfy_breadcrumbs_markup')) {

    /**
     * Breadcrumbs markup section.
     * @return markup
     */
    function careerfy_breadcrumbs_markup()
    {
        global $post, $careerfy_framework_options, $subheader_title;
        $show_subheader = false;
        $show_subheader = apply_filters('careerfy_subheader_display_switch', $show_subheader);
        if ($show_subheader) {
            return;
        }

        $post_id = 0;
        if (!(class_exists('Careerfy_MMC') && true == Careerfy_MMC::is_construction_mode_enabled(false))) {

            if (!is_home() && !is_front_page()) {

                $custom_subheader = false;
                if (is_page() || is_single()) {
                    $post_id = $post->ID;
                    $page_subheader = get_post_meta($post_id, 'careerfy_field_page_subheader', true);
                    if ($page_subheader == 'custom') {
                        $custom_subheader = true;
                    }
                } else if (function_exists('is_shop') && is_shop()) {
                    $post_id = wc_get_page_id('shop');
                    $page_subheader = get_post_meta($post_id, 'careerfy_field_page_subheader', true);
                    if ($page_subheader == 'custom') {
                        $custom_subheader = true;
                    }
                }


                if ($custom_subheader === true) {
                    $subheader_switch = get_post_meta($post_id, 'careerfy_field_page_subheader_switch', true);
                    $subheader_height = get_post_meta($post_id, 'careerfy_field_page_subheader_height', true);
                    $subheader_title = get_post_meta($post_id, 'careerfy_field_page_subheader_title', true);
                    $subheader_subtitle = get_post_meta($post_id, 'careerfy_field_page_subheader_subtitle', true);
                    $subheader_pading_top = get_post_meta($post_id, 'careerfy_field_page_subheader_pading_top', true);
                    $subheader_pading_bottom = get_post_meta($post_id, 'careerfy_field_page_subheader_pading_bottom', true);
                    $subheader_breadcrumb = get_post_meta($post_id, 'careerfy_field_page_subheader_breadcrumb', true);
                    $subheader_bg_img = get_post_meta($post_id, 'careerfy_field_page_subheader_bg_image', true);
                    $subheader_bg_color = get_post_meta($post_id, 'careerfy_field_page_subheader_bg_color', true);
                } else {
                    $subheader_switch = isset($careerfy_framework_options['careerfy-subheader']) ? $careerfy_framework_options['careerfy-subheader'] : '';
                    $subheader_title = isset($careerfy_framework_options['careerfy-subheader-title']) ? $careerfy_framework_options['careerfy-subheader-title'] : '';
                    $subheader_subtitle = isset($careerfy_framework_options['careerfy-subheader-subtitle']) ? $careerfy_framework_options['careerfy-subheader-subtitle'] : '';
                    $subheader_height = isset($careerfy_framework_options['careerfy-subheader-height']) && $careerfy_framework_options['careerfy-subheader-height'] > 0 ? $careerfy_framework_options['careerfy-subheader-height'] : '';
                    $subheader_pading_top = isset($careerfy_framework_options['careerfy-subheader-pading-top']) && $careerfy_framework_options['careerfy-subheader-pading-top'] > 0 ? $careerfy_framework_options['careerfy-subheader-pading-top'] : '';
                    $subheader_pading_bottom = isset($careerfy_framework_options['careerfy-subheader-pading-bottom']) && $careerfy_framework_options['careerfy-subheader-pading-bottom'] > 0 ? $careerfy_framework_options['careerfy-subheader-pading-bottom'] : '';
                    $subheader_breadcrumb = isset($careerfy_framework_options['careerfy-subheader-breadcrumb']) ? $careerfy_framework_options['careerfy-subheader-breadcrumb'] : '';
                    $subheader_bg_img = isset($careerfy_framework_options['careerfy-subheader-bg-img']["url"]) ? $careerfy_framework_options['careerfy-subheader-bg-img']["url"] : '';
                    $subheader_bg_color = isset($careerfy_framework_options['careerfy-subheader-bg-color']) ? $careerfy_framework_options['careerfy-subheader-bg-color'] : '';
                    if (isset($subheader_bg_color['rgba'])) {
                        $subheader_bg_color = $subheader_bg_color['rgba'];
                    }
                }

                $subheader_bg_img = apply_filters('careerfy_subheader_postpage_bg_img', $subheader_bg_img, $post_id);

                $subheader_style = '';
                $careerfy_color_transparent = '';
                if ($subheader_height != '') {
                    $subheader_style .= 'height: ' . $subheader_height . 'px !important;';
                }
                if ($subheader_pading_top != '') {
                    $subheader_style .= 'padding-top: ' . $subheader_pading_top . 'px !important;';
                }
                if ($subheader_pading_bottom != '') {
                    $subheader_style .= 'padding-bottom: ' . $subheader_pading_bottom . 'px !important;';
                }
                if ($subheader_bg_img != '') {
                    $subheader_style .= 'background: url(\'' . $subheader_bg_img . '\') no-repeat center/cover;';
                }
                if ($subheader_bg_color != '') {
                    $careerfy_color_transparent .= 'background-color: ' . $subheader_bg_color . ' !important;';
                }
                if ($subheader_style != '') {
                    $subheader_style = ' style="' . $subheader_style . '"';
                }
                if ($careerfy_color_transparent != '') {
                    $careerfy_color_transparent = ' style="' . $careerfy_color_transparent . '"';
                }
                if ($subheader_switch == 'on') {
                    ob_start();
                    ?>
                    <div class="<?php echo apply_filters('careerfy_subheader_main_con_class', 'careerfy-subheader careerfy-subheader-with-bg') ?>" <?php echo($subheader_style) ?>>
                        <?php if ($careerfy_color_transparent != '') { ?>
                            <span class="<?php echo apply_filters('careerfy_subheader_transp_con_class', 'careerfy-banner-transparent') ?>" <?php echo $careerfy_color_transparent ?>></span>
                        <?php } ?>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="<?php echo apply_filters('careerfy_subheader_title_con_class', 'careerfy-page-title') ?>">
                                        <?php
                                        if ($subheader_title == 'on') {
                                            $subheader_title = true;

                                            ob_start();
                                            ?>
                                            <h1><?php careerfy_post_page_title(); ?></h1>
                                            <?php
                                            $main_sb_title = ob_get_clean();
                                            echo apply_filters('careerfy_subheader_page_main_title', $main_sb_title);
                                            if ($subheader_subtitle != '') {
                                                ?>
                                                <p><?php echo($subheader_subtitle) ?></p>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                    $subhder_html = ob_get_clean();
                    $subhdr_args = array(
                        'subheader_style' => $subheader_style,
                        'transparent_color' => $careerfy_color_transparent,
                        'subheader_title' => $subheader_title,
                        'subheader_subtitle' => $subheader_subtitle,
                        'subheader_breadcrumb' => $subheader_breadcrumb,
                        'subheader_height' => $subheader_height,
                        'subheader_pading_top' => $subheader_pading_top,
                        'subheader_pading_bottom' => $subheader_pading_bottom,
                        'subheader_bg_img' => $subheader_bg_img,
                        'subheader_bg_color' => $subheader_bg_color,
                    );
                    echo apply_filters('careerfy_after_header_subheader_html', $subhder_html, $subhdr_args);
                    if ($subheader_breadcrumb == 'on') {
                        ob_start();
                        careerfy_breadcrumbs();
                        $breadcrumbs_html = ob_get_clean();

                        echo apply_filters('careerfy_after_header_breadcrumbs_html', $breadcrumbs_html);
                    }
                }
            }
        } // end maintenance mode check
    }

    add_action('careerfy_header_breadcrumbs', 'careerfy_breadcrumbs_markup', 10);
}

add_action('careerfy_after_header_subheader_html', 'jobsearch_detail_subheader_remhtml', 10, 2);

function jobsearch_detail_subheader_remhtml($subhder_html, $subhdr_args = array())
{
    if (is_singular('employer') || is_singular('candidate')) {
        $subhder_html = '';
    }

    return $subhder_html;
}

add_action('careerfy_after_header_breadcrumbs_html', 'jobsearch_detail_breadcrumbs_remhtml', 10, 1);

function jobsearch_detail_breadcrumbs_remhtml($breadcrumbs_html)
{
    if (is_singular('employer') || is_singular('candidate')) {
        $breadcrumbs_html = '';
    }

    return $breadcrumbs_html;
}

if (!function_exists('careerfy_visibility_query_args')) {

    function careerfy_visibility_query_args($element_filter_arr = array())
    {

        return $element_filter_arr;
    }

}

if (!function_exists('careerfy_remove_qrystr_extra_var')) {

    function careerfy_remove_qrystr_extra_var($qStr, $key, $withqury_start = 'yes')
    {
        $qr_str = preg_replace('/[?&]' . $key . '=[^&]+$|([?&])' . $key . '=[^&]+&/', '$1', $qStr);
        if (!(strpos($qr_str, '?') !== false)) {
            $qr_str = "?" . $qr_str;
        }
        $qr_str = str_replace("?&", "?", $qr_str);
        $qr_str = careerfy_remove_dupplicate_var_val($qr_str);
        if ($withqury_start == 'no') {
            $qr_str = str_replace("?", "", $qr_str);
        }
        return $qr_str;
        die();
    }

}

if (!function_exists('careerfy_remove_dupplicate_var_val')) {

    function careerfy_remove_dupplicate_var_val($qry_str)
    {
        $old_string = $qry_str;
        $qStr = str_replace("?", "", $qry_str);
        $query = explode('&', $qStr);
        $params = array();
        if (isset($query) && !empty($query)) {
            foreach ($query as $param) {
                if (!empty($param)) {
                    $param_array = explode('=', $param);
                    $name = isset($param_array[0]) ? $param_array[0] : '';
                    $value = isset($param_array[1]) ? $param_array[1] : '';
                    $new_str = $name . "=" . $value;
                    // count matches
                    $count_str = substr_count($old_string, $new_str);
                    $count_str = $count_str - 1;
                    if ($count_str > 0) {
                        $old_string = careerfy_str_replace_limit($new_str, "", $old_string, $count_str);
                    }
                    $old_string = str_replace("&&", "&", $old_string);
                }
            }
        }
        $old_string = str_replace("?&", "?", $old_string);
        return $old_string;
    }

}

if (!function_exists('careerfy_str_replace_limit')) {

    function careerfy_str_replace_limit($search, $replace, $string, $limit = 1)
    {
        if (is_bool($pos = (strpos($string, $search))))
            return $string;
        $search_len = strlen($search);
        for ($i = 0; $i < $limit; $i++) {
            $string = substr_replace($string, $replace, $pos, $search_len);
            if (is_bool($pos = (strpos($string, $search))))
                break;
        }
        return $string;
    }

}
if (!function_exists('getMultipleParameters')) {

    function getMultipleParameters($query_string = '')
    {
        if ($query_string == '')
            $query_string = $_SERVER['QUERY_STRING'];
        $params = explode('&', $query_string);
        foreach ($params as $param) {

            $k = $param;
            $v = '';
            if (strpos($param, '=')) {
                list($name, $value) = explode('=', $param);
                $k = rawurldecode($name);
                $v = rawurldecode($value);
            }
            if (isset($query[$k])) {
                if (is_array($query[$k])) {
                    $query[$k][] = $v;
                } else {
                    $query[$k][] = array($query[$k], $v);
                }
            } else {
                $query[$k][] = $v;
            }
        }

        return $query;
    }

}


if (!function_exists('careerfy_get_taxanomy_type_item_count')) {

    function careerfy_get_taxanomy_type_item_count($left_filter_count_switch, $field_meta_key, $tax_type, $args_filters)
    {

        if ($left_filter_count_switch == 'yes') {
            if (isset($args_filters['tax_query'])) {
                $finded_index = careerfy_find_in_multiarray($tax_type, $args_filters['tax_query'], 'taxonomy');

                $finded_index = isset($finded_index[0]) ? $finded_index[0] : '-1';
                if ($finded_index >= 0) {
                    $args_filters['tax_query'] = array_splice($args_filters['tax_query'], $finded_index, (count($args_filters['tax_query']) - 1));
                }
            }
            $args_filters['tax_query'][] = array(
                'taxonomy' => $tax_type,
                'field' => 'slug',
                'terms' => $field_meta_key
            );
            $job_qry = new WP_Query($args_filters);
            return $job_qry->found_posts;
            wp_reset_postdata();
        }
    }

}

if (!function_exists('careerfy_get_item_count')) {

    function careerfy_get_item_count($left_filter_count_switch, $args, $count_arr, $job_short_counter, $field_meta_key, $open_house = '')
    {
        if ($left_filter_count_switch == 'yes') {
            global $careerfy_shortcode_jobs_frontend;


            // get all arguments from getting flters
            $left_filter_arr = array();
            $left_filter_arr = $careerfy_shortcode_jobs_frontend->get_filter_arg($job_short_counter, $field_meta_key);
            if (!empty($count_arr)) {
                // check if count array has multiple condition
                foreach ($count_arr as $count_arr_single) {
                    $left_filter_arr[] = $count_arr_single;
                }
            }

            $post_ids = '';
            if (!empty($left_filter_arr)) {
                // apply all filters and get ids
                $post_ids = $careerfy_shortcode_jobs_frontend->get_job_id_by_filter($left_filter_arr);
            }

            if (isset($_REQUEST['location']) && $_REQUEST['location'] != '' && !isset($_REQUEST['loc_polygon_path'])) {
                $radius = isset($_REQUEST['radius']) ? $_REQUEST['radius'] : '';
                $post_ids = $careerfy_shortcode_jobs_frontend->job_location_filter($_REQUEST['location'], $post_ids);
                if (empty($post_ids)) {
                    $post_ids = array(0);
                }
            }

            $all_post_ids = $post_ids;
            if (!empty($all_post_ids)) {
                $args['post__in'] = $all_post_ids;
            }

            $restaurant_loop_obj = careerfy_get_cached_obj('job_result_cached_loop_count_obj', $args, 12, false, 'wp_query');
            $restaurant_totnum = $restaurant_loop_obj->found_posts;
            return $restaurant_totnum;
        }
    }

}

if (!function_exists('careerfy_get_cached_obj')) {

    function careerfy_get_cached_obj($cache_variable, $args, $time = 12, $cache = true, $type = 'wp_query', $taxanomy_name = '')
    {
        $job_loop_obj = '';
        if ($cache == true) {
            $time_string = $time * HOUR_IN_SECONDS;
            if ($cache_variable != '') {
                if (false === ($job_loop_obj = wp_cache_get($cache_variable))) {
                    if ($type == 'wp_query') {
                        $job_loop_obj = new WP_Query($args);
                    } else if ($type == 'get_term') {
                        $job_loop_obj = array();
                        $terms = get_terms($taxanomy_name, $args);
                        if (sizeof($terms) > 0) {
                            foreach ($terms as $term_data) {
                                $job_loop_obj[] = $term_data->name;
                            }
                        }
                    }
                    wp_cache_set($cache_variable, $job_loop_obj, $time_string);
                }
            }
        } else {
            if ($type == 'wp_query') {
                $job_loop_obj = new WP_Query($args);
            } else if ($type == 'get_term') {
                $job_loop_obj = array();
                $terms = get_terms($taxanomy_name, $args);
                if (sizeof($terms) > 0) {
                    foreach ($terms as $term_data) {
                        $job_loop_obj[] = $term_data->name;
                    }
                }
            }
        }


        return $job_loop_obj;
    }

}

if (!function_exists('careerfy_find_in_multiarray')) {

    function careerfy_find_in_multiarray($elem, $array, $field)
    {

        $top = sizeof($array);
        $k = 0;
        $new_array = array();
        for ($i = 0; $i <= $top; $i++) {
            if (isset($array[$i])) {
                $new_array[$k] = $array[$i];
                $k++;
            }
        }
        $array = $new_array;
        $top = sizeof($array) - 1;
        $bottom = 0;

        $finded_index = array();
        if (is_array($array)) {
            while ($bottom <= $top) {
                if (isset($array[$bottom][$field]) && $array[$bottom][$field] == $elem)
                    $finded_index[] = $bottom;
                else
                    if (isset($array[$bottom][$field]) && is_array($array[$bottom][$field]))
                        if (careerfy_find_in_multiarray($elem, ($array[$bottom][$field])))
                            $finded_index[] = $bottom;
                $bottom++;
            }
        }
        return $finded_index;
    }

}

if (!function_exists('careerfy_filter_querystring_variables')) {

    function careerfy_filter_querystring_variables($qrystr)
    {

        $qrystr;
        return $qrystr;
    }

}

if (!function_exists('careerfy_get_user_id')) {

    function careerfy_get_user_id()
    {

        global $current_user;
        wp_get_current_user();
        return $current_user->ID;
    }

}

if (!function_exists('careerfy_get_user_jobapply_meta')) {

    function careerfy_get_user_jobapply_meta($user = "")
    {
        if (!empty($user)) {
            $userdata = get_user_by('login', $user);
            $user_id = $userdata->ID;
            return get_user_meta($user_id, 'careerfy-jobs-applied', true);
        } else {
            return get_user_meta(careerfy_get_user_id(), 'careerfy-jobs-applied', true);
        }
    }

}

if (!function_exists('careerfy_update_user_jobapply_meta')) {

    function careerfy_update_user_jobapply_meta($arr)
    {
        return update_user_meta(careerfy_get_user_id(), 'careerfy-jobs-applied', $arr);
    }

}

if (!function_exists('careerfy_create_user_meta_list')) {

    function careerfy_create_user_meta_list($post_id, $list_name, $user_id)
    {
        $current_timestamp = strtotime(current_time('d-m-Y H:i:s'));
        $existing_list_data = array();
        $existing_list_data = get_user_meta($user_id, $list_name, true);
        if (!is_array($existing_list_data)) {
            $existing_list_data = array();
        }

        if (is_array($existing_list_data)) {
            // search duplicat and remove it then arrange new ordering
            $finded = careerfy_find_in_multiarray($post_id, $existing_list_data, 'post_id');
            $existing_list_data = remove_index_from_array($existing_list_data, $finded);
            // adding one more entry
            $existing_list_data[] = array('post_id' => $post_id, 'date_time' => $current_timestamp);
            update_user_meta($user_id, $list_name, $existing_list_data);
        }
    }

}

if (!function_exists('remove_index_from_array')) {

    function remove_index_from_array($array, $index_array)
    {
        $top = sizeof($index_array) - 1;
        $bottom = 0;
        if (is_array($index_array)) {
            while ($bottom <= $top) {
                unset($array[$index_array[$bottom]]);
                $bottom++;
            }
        }
        if (!empty($array))
            return array_values($array);
        else
            return $array;
    }

}

if (!function_exists('careerfy_find_index_user_meta_list')) {

    function careerfy_find_index_user_meta_list($post_id, $list_name, $need_find, $user_id)
    {
        $existing_list_data = get_user_meta($user_id, $list_name, true);
        if (empty($existing_list_data)) {
            $existing_list_data = array();
        }
        $finded = array();
        if (is_array($existing_list_data) && !empty($existing_list_data)) {
            $finded = find_in_multiarray($post_id, $existing_list_data, $need_find);
        }
        return $finded;
    }

}

if (!function_exists('find_in_multiarray')) {

    function find_in_multiarray($elem, $array, $field)
    {
        $top = sizeof($array);
        $k = 0;
        $new_array = array();
        for ($i = 0; $i <= $top; $i++) {
            if (isset($array[$i])) {
                $new_array[$k] = $array[$i];
                $k++;
            }
        }
        $array = $new_array;
        $top = sizeof($array) - 1;
        $bottom = 0;
        $finded_index = array();
        if (is_array($array)) {
            while ($bottom <= $top) {
                if ($array[$bottom][$field] == $elem)
                    $finded_index[] = $bottom;
                else
                    if (is_array($array[$bottom][$field]))
                        if (find_in_multiarray($elem, ($array[$bottom][$field])))
                            $finded_index[] = $bottom;
                $bottom++;
            }
        }
        return $finded_index;
    }

}

if (!function_exists('careerfy_frame_get_attachment_id_from_url')) {

    function careerfy_frame_get_attachment_id_from_url($attachment_url = '')
    {

        global $wpdb;
        $attachment_id = false;

        // If there is no url, return.
        if ('' == $attachment_url)
            return;

        // Get the upload directory paths
        $upload_dir_paths = wp_upload_dir();

        // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
        if (false !== strpos($attachment_url, $upload_dir_paths['baseurl'])) {

            // If this is the URL of an auto-generated thumbnail, get the URL of the original image
            $attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);

            // Remove the upload path base directory from the attachment URL
            $attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);

            // Finally, run a custom database query to get the attachment ID from the modified attachment URL
            $attachment_id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url));
        }

        return $attachment_id;
    }

}

add_filter('jobsearch_sector_term_add_cusmeta_fields_before', 'careerfy_sector_term_add_cusmeta_fields_before', 10, 1);

function careerfy_sector_term_add_cusmeta_fields_before($html)
{
    global $careerfy_form_fields, $careerfy_icons_fields;
    $rand_id = rand(10000000, 99999999);
    ob_start();
    ?>
    <div class="form-field">
        <label for="cat_cus_icon"> <?php esc_html_e("Choose Icon", "careerfy-frame"); ?></label>
        <?php //echo careerfy_frame_icon_picker('', $rand_id, 'cat_icon')
        ?>
        <?php echo $careerfy_icons_fields->careerfy_icons_fields_callback('', $rand_id, 'cat_icon') ?>
    </div>
    <div class="form-field">
        <label for="cat_cus_color"> <?php esc_html_e("Choose Color", "careerfy-frame"); ?></label>
        <?php
        $field_params = array(
            'force_std' => '',
            'cus_name' => 'cat_color',
            'classes' => 'careerfy-bk-color',
        );
        $careerfy_form_fields->input_field($field_params);
        ?>
    </div>
    <div class="form-field">
        <label for="cat-cus-image-<?php echo($rand_id) ?>"> <?php esc_html_e("Upload Image", "careerfy-frame"); ?></label>
        <?php
        $field_params = array(
            'id' => 'cat-cus-image-' . $rand_id,
            'force_std' => '',
            'cus_name' => 'cat_image',
        );
        $careerfy_form_fields->image_upload_field($field_params);
        ?>
    </div>
    <?php
    $html = ob_get_clean();
    return $html;
}

add_filter('jobsearch_sector_term_edit_cusmeta_fields_before', 'careerfy_sector_term_edit_cusmeta_fields_before', 10, 2);

function careerfy_sector_term_edit_cusmeta_fields_before($html, $term_id)
{
    global $careerfy_form_fields, $careerfy_icons_fields;
    $rand_id = rand(10000000, 99999999);

    $term_fields = get_term_meta($term_id, 'careerfy_frame_cat_fields', true);
    $term_icon = isset($term_fields['icon']) ? $term_fields['icon'] : '';
    $term_icon_lib = isset($term_fields['icon_lib']) && $term_fields['icon_lib'] != '' ? $term_fields['icon_lib'] : 'default';
    $term_color = isset($term_fields['color']) ? $term_fields['color'] : '';
    $term_image = isset($term_fields['image']) ? $term_fields['image'] : '';

    ob_start();
    ?>
    <tr class="form-field">
        <th><label for="cat_cus_icon"> <?php esc_html_e("Choose Icon", "careerfy-frame"); ?></label></th>
        <td>
            <?php //echo careerfy_frame_icon_picker($term_icon, $rand_id, 'cat_icon')
            ?>
            <?php echo $careerfy_icons_fields->careerfy_icons_fields_callback($term_icon, $rand_id, 'cat_icon', $term_icon_lib) ?>
        </td>
    </tr>
    <tr class="form-field">
        <th><label for="cat_cus_color"> <?php esc_html_e("Choose Color", "careerfy-frame"); ?></label></th>
        <td>
            <?php
            $field_params = array(
                'force_std' => $term_color,
                'cus_name' => 'cat_color',
                'classes' => 'careerfy-bk-color',
            );
            $careerfy_form_fields->input_field($field_params);
            ?>
        </td>
    </tr>
    <tr class="form-field">
        <th>
            <label for="cat-cus-image-<?php echo($rand_id) ?>"> <?php esc_html_e("Upload Image", "careerfy-frame"); ?></label>
        </th>
        <td>
            <?php
            $field_params = array(
                'id' => 'cat-cus-image-' . $rand_id,
                'force_std' => $term_image,
                'cus_name' => 'cat_image',
            );
            $careerfy_form_fields->image_upload_field($field_params);
            ?>
        </td>
    </tr>
    <?php
    $html = ob_get_clean();
    return $html;
}

add_filter('jobsearch_sector_term_save_cusmeta_fields', 'careerfy_sector_term_save_cusmeta_fields', 10, 2);

function careerfy_sector_term_save_cusmeta_fields($fields, $term_id)
{
    $term_icon = isset($_POST['cat_icon']) ? $_POST['cat_icon'] : '';
    $term_icon_lib = isset($_POST['cat_icon_group']) ? $_POST['cat_icon_group'] : '';
    $term_color = isset($_POST['cat_color']) ? $_POST['cat_color'] : '';
    $term_image = isset($_POST['cat_image']) ? $_POST['cat_image'] : '';

    $fields['icon'] = $term_icon;
    $fields['icon_lib'] = $term_icon_lib;
    $fields['color'] = $term_color;
    $fields['image'] = $term_image;

    return $fields;
}

if (!function_exists('careerfy_post_views_count')) {

    add_action('careerfy_before_single_post_header', 'careerfy_post_views_count', 10, 1);

    function careerfy_post_views_count($postID)
    {
        $careerfy_post_views_count = get_post_meta($postID, "careerfy_post_views_count", true);
        if ($careerfy_post_views_count == '') {
            $careerfy_post_views_count = 0;
        }
        if (!isset($_COOKIE["careerfy_post_views_count" . $postID])) {
            setcookie("careerfy_post_views_count" . $postID, time() + 86400);
            $careerfy_post_views_count = $careerfy_post_views_count + 1;
            update_post_meta($postID, 'careerfy_post_views_count', $careerfy_post_views_count);
        }
    }

}

if (!function_exists('careerfy_post_likes_count')) {

    function careerfy_post_likes_count()
    {
        $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
        $careerfy_like_counter = get_post_meta($post_id, "careerfy_post_likes", true);
        $careerfy_like_counter = $careerfy_like_counter > 0 ? $careerfy_like_counter : 0;
        if (!isset($_COOKIE["careerfy_post_likes" . $post_id])) {
            setcookie("careerfy_post_likes" . $post_id, 'true', time() + 186400, '/');
            update_post_meta($post_id, 'careerfy_post_likes', absint($careerfy_like_counter) + 1);
        }
        $careerfy_like_counter = get_post_meta($post_id, "careerfy_post_likes", true);
        $careerfy_like_counter = $careerfy_like_counter > 0 ? $careerfy_like_counter : 0;
        echo json_encode(array('counter' => $careerfy_like_counter));
        wp_die();
    }

    add_action('wp_ajax_careerfy_post_likes_count', 'careerfy_post_likes_count');
    add_action('wp_ajax_nopriv_careerfy_post_likes_count', 'careerfy_post_likes_count');
}

if (!function_exists('careerfy_post_dislikes_count')) {

    function careerfy_post_dislikes_count()
    {
        $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
        $careerfy_like_counter = get_post_meta($post_id, "careerfy_post_dislikes", true);
        $careerfy_like_counter = $careerfy_like_counter > 0 ? $careerfy_like_counter : 0;
        if (!isset($_COOKIE["careerfy_post_dislikes" . $post_id])) {
            setcookie("careerfy_post_dislikes" . $post_id, 'true', time() + 186400, '/');
            update_post_meta($post_id, 'careerfy_post_dislikes', absint($careerfy_like_counter) + 1);
        }
        $careerfy_like_counter = get_post_meta($post_id, "careerfy_post_dislikes", true);
        $careerfy_like_counter = $careerfy_like_counter > 0 ? $careerfy_like_counter : 0;
        echo json_encode(array('counter' => $careerfy_like_counter));
        wp_die();
    }

    add_action('wp_ajax_careerfy_post_dislikes_count', 'careerfy_post_dislikes_count');
    add_action('wp_ajax_nopriv_careerfy_post_dislikes_count', 'careerfy_post_dislikes_count');
}

add_action('careerfy_post_like_btns', 'careerfy_frame_post_like_btns', 10, 1);
function careerfy_frame_post_like_btns($post_id)
{ ?>
    <a href="javascript:void(0);" class="careerfy-blog-style14-like careerfy-blog-post-like-btn"
       data-id="<?php echo absint($post_id) ?>"><i
                class="fa fa-heart-o"></i></a>
<?php }

add_action('careerfy_post_acts_btns', 'careerfy_frame_post_acts_btns', 10, 1);

function careerfy_frame_post_acts_btns($post_id)
{
    $post_views_count = get_post_meta($post_id, 'careerfy_post_views_count', true);
    $careerfy_dislike_counter = get_post_meta($post_id, "careerfy_post_dislikes", true);
    $careerfy_like_counter = get_post_meta($post_id, "careerfy_post_likes", true);
    ?>
    <ul class="post-acts">
        <li><i class="fa fa-eye"></i> <?php echo absint($post_views_count); ?></li>
        <?php
        if (isset($_COOKIE["careerfy_post_dislikes" . $post_id])) {
            ?>
            <li><a><i class="fa fa-thumbs-up"></i> <span><?php echo absint($careerfy_dislike_counter) ?></span></a></li>
            <?php
        } else { ?>
            <li><a href="javascript:void(0);" class="careerfy-post-dislike-btn"
                   data-id="<?php echo absint($post_id) ?>"><i class="fa fa-thumbs-o-up"></i>
                    <span><?php echo absint($careerfy_dislike_counter) ?></span></a></li>
            <?php
        }
        if (isset($_COOKIE["careerfy_post_likes" . $post_id])) {
            ?>
            <li><a><i class="fa fa-heart"></i> <span><?php echo absint($careerfy_like_counter) ?></span></a></li>
            <?php
        } else {
            ?>
            <li><a href="javascript:void(0);" class="careerfy-post-like-btn" data-id="<?php echo absint($post_id) ?>"><i
                            class="fa fa-heart-o"></i> <span><?php echo absint($careerfy_like_counter) ?></span></a>
            </li>
            <?php
        }
        ?>
    </ul>
    <?php
}

if (!function_exists('careerfy__get_post_id')) {

    function careerfy__get_post_id($id_slug, $type = 'post')
    {
        if ($id_slug != '') {
            $post_obj = get_page_by_path($id_slug, 'OBJECT', $type);
            if (is_object($post_obj) && isset($post_obj->ID)) {
                return $post_obj->ID;
            }
        } else if ($id_slug > 0) {
            return $id_slug;
        }
        return 0;
    }
}

add_action('careerfy_post_author_social_links', 'careerfy_frame_post_author_social_links', 10, 1);

function careerfy_frame_post_author_social_links($post_id)
{
    $user_id = get_the_author_meta('ID');
    $user_facebook = get_user_meta($user_id, 'careerfy_user_facebook', true);
    $user_google = get_user_meta($user_id, 'careerfy_user_google', true);
    $user_linkedin = get_user_meta($user_id, 'careerfy_user_linkedin', true);
    $user_twitter = get_user_meta($user_id, 'careerfy_user_twitter', true);
    if ($user_facebook != '' || $user_google != '' || $user_twitter != '' || $user_linkedin != '') { ?>
        <div class="author-social-links">
            <ul>
                <?php
                if ($user_facebook != '') {
                    ?>
                    <li><a href="<?php echo esc_url($user_facebook) ?>" target="_blank"><i
                                    class="fa fa-facebook"></i></a></li>
                    <?php
                }
                if ($user_twitter != '') {
                    ?>
                    <li><a href="<?php echo esc_url($user_twitter) ?>" target="_blank"><i class="fa fa-twitter"></i></a>
                    </li>
                    <?php
                }
                if ($user_google != '') {
                    ?>
                    <li><a href="<?php echo esc_url($user_google) ?>" target="_blank"><i class="fa fa-google"></i></a>
                    </li>
                    <?php
                }
                if ($user_linkedin != '') {
                    ?>
                    <li><a href="<?php echo esc_url($user_linkedin) ?>" target="_blank"><i
                                    class="fa fa-linkedin"></i></a></li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <?php
    }
}

if (!function_exists('careerfy_social_icons_footer_eighteen')) {

    /*
     * Social Icons for header style 20.
     * @return
     */


    function careerfy_social_icons_footer_eighteen()
    {
        global $careerfy_framework_options;
        $social_twitter = isset($careerfy_framework_options['careerfy-social-networking-twitter']) ? $careerfy_framework_options['careerfy-social-networking-twitter'] : '';
        $social_facebook = isset($careerfy_framework_options['careerfy-social-networking-facebook']) ? $careerfy_framework_options['careerfy-social-networking-facebook'] : '';
        $social_googleplus = isset($careerfy_framework_options['careerfy-social-networking-google']) ? $careerfy_framework_options['careerfy-social-networking-google'] : '';
        $social_youtube = isset($careerfy_framework_options['careerfy-social-networking-youtube']) ? $careerfy_framework_options['careerfy-social-networking-youtube'] : '';
        $social_vimeo = isset($careerfy_framework_options['careerfy-social-networking-vimeo']) ? $careerfy_framework_options['careerfy-social-networking-vimeo'] : '';
        $social_linkedin = isset($careerfy_framework_options['careerfy-social-networking-linkedin']) ? $careerfy_framework_options['careerfy-social-networking-linkedin'] : '';
        $social_pinterest = isset($careerfy_framework_options['careerfy-social-networking-pinterest']) ? $careerfy_framework_options['careerfy-social-networking-pinterest'] : '';
        $social_instagram = isset($careerfy_framework_options['careerfy-social-networking-instagram']) ? $careerfy_framework_options['careerfy-social-networking-instagram'] : '';
        ?>
        <li>
            <?php if ($social_facebook != '') { ?>
                <a href="<?php echo esc_url($social_facebook) ?>" target="_blank" class="social-icon-footer-twenty"><i
                            class="fa fa-facebook"></i></a>
                <?php
            }
            if ($social_twitter != '') { ?>
                <a href="<?php echo esc_url($social_twitter) ?>" target="_blank" class="social-icon-footer-twenty"><i
                            class="fa fa-twitter"></i></a>
                <?php
            }
            if ($social_googleplus != '') { ?>

                <a href="<?php echo esc_url($social_googleplus) ?>" target="_blank" class="social-icon-footer-twenty"><i
                            class="fa fa-google-plus"></i></a>
                <?php

            }
            if ($social_youtube != '') { ?>
                <a href="<?php echo esc_url($social_youtube) ?>" target="_blank" class="social-icon-footer-twenty"><i
                            class="fa fa-youtube"></i></a>
                <?php
            }
            if ($social_vimeo != '') {
                ?>
                <a href="<?php echo esc_url($social_vimeo) ?>" target="_blank" class="social-icon-footer-twenty"><i
                            class="fa fa-vimeo"></i></a>
                <?php

            }
            if ($social_linkedin != '') { ?>
                <a href="<?php echo esc_url($social_linkedin) ?>" target="_blank" class="social-icon-footer-twenty"><i
                            class="fa fa-linkedin"></i></a>
                <?php
            }

            if ($social_pinterest != '') { ?>
                <a href="<?php echo esc_url($social_pinterest) ?>" target="_blank" class="social-icon-footer-twenty"><i
                            class="fa fa-pinterest"></i></a>
            <?php }
            if ($social_instagram != '') { ?>
                <a href="<?php echo esc_url($social_instagram) ?>" target="_blank" class="social-icon-footer-twenty"><i
                            class="fa fa-instagram"></i></a>
            <?php } ?>
        </li>
        <?php
    }
}

if (!function_exists('careerfy_social_icons')) {

    /*
     * Social Icons.
     * @return
     */

    add_action('careerfy_social_icons', 'careerfy_social_icons', 10, 2);

    function careerfy_social_icons($social_class = '', $social_view = '')
    {
        global $careerfy_framework_options;
        $social_twitter = isset($careerfy_framework_options['careerfy-social-networking-twitter']) ? $careerfy_framework_options['careerfy-social-networking-twitter'] : '';
        $social_facebook = isset($careerfy_framework_options['careerfy-social-networking-facebook']) ? $careerfy_framework_options['careerfy-social-networking-facebook'] : '';
        $social_googleplus = isset($careerfy_framework_options['careerfy-social-networking-google']) ? $careerfy_framework_options['careerfy-social-networking-google'] : '';
        $social_youtube = isset($careerfy_framework_options['careerfy-social-networking-youtube']) ? $careerfy_framework_options['careerfy-social-networking-youtube'] : '';
        $social_vimeo = isset($careerfy_framework_options['careerfy-social-networking-vimeo']) ? $careerfy_framework_options['careerfy-social-networking-vimeo'] : '';
        $social_linkedin = isset($careerfy_framework_options['careerfy-social-networking-linkedin']) ? $careerfy_framework_options['careerfy-social-networking-linkedin'] : '';
        $social_pinterest = isset($careerfy_framework_options['careerfy-social-networking-pinterest']) ? $careerfy_framework_options['careerfy-social-networking-pinterest'] : '';
        $social_instagram = isset($careerfy_framework_options['careerfy-social-networking-instagram']) ? $careerfy_framework_options['careerfy-social-networking-instagram'] : '';

        $ul_class = 'careerfy-social-network';
        if ($social_class != '') {
            $ul_class = $social_class;
        } ?>
        <ul class="<?php echo($ul_class) ?>">
            <?php
            if ($social_facebook != '') {
                if ($social_view == 'view-2') {
                    ?>
                    <li><a href="<?php echo esc_url($social_facebook) ?>" target="_blank" class="fa fa-facebook"></a>
                    </li>
                    <?php
                } else if ($social_view == 'view-3') {
                    ?>
                    <li><a href="<?php echo esc_url($social_facebook) ?>" target="_blank"><i class="fa fa-facebook"></i></a>
                    </li>
                    <?php
                } else if ($social_view == 'view-4' || $social_view == 'view-5' || $social_view == 'view-6') {
                    ?>
                    <li><a href="<?php echo esc_url($social_facebook) ?>" target="_blank"
                           class="careerfy-icon careerfy-facebook"></a></li>
                <?php } else { ?>
                    <li><a href="<?php echo esc_url($social_facebook) ?>" target="_blank" class="fa fa-facebook"></a>
                    </li>
                    <?php
                }
            }
            if ($social_twitter != '') {
                if ($social_view == 'view-2') {
                    ?>
                    <li><a href="<?php echo esc_url($social_twitter) ?>" target="_blank" class="fa fa-twitter"></a></li>
                    <?php
                } else if ($social_view == 'view-3') {
                    ?>
                    <li><a href="<?php echo esc_url($social_twitter) ?>" target="_blank"><i
                                    class="fa fa-twitter"></i></a></li>
                    <?php
                } else if ($social_view == 'view-4' || $social_view == 'view-5' || $social_view == 'view-6') {
                    ?>
                    <li><a href="<?php echo esc_url($social_twitter) ?>" target="_blank"
                           class="careerfy-icon careerfy-twitter"></a></li>
                    <?php
                } else {
                    ?>
                    <li><a href="<?php echo esc_url($social_twitter) ?>" target="_blank" class="fa fa-twitter"></a></li>
                    <?php
                }
            }
            if ($social_googleplus != '') {
                if ($social_view == 'view-2') {
                    ?>
                    <li><a href="<?php echo esc_url($social_googleplus) ?>" target="_blank"
                           class="fa fa-google-plus"></a></li>
                    <?php
                } else if ($social_view == 'view-3') {
                    ?>
                    <li><a href="<?php echo esc_url($social_googleplus) ?>" target="_blank"><i
                                    class="fa fa-google-plus"></i></a></li>
                    <?php
                } else if ($social_view == 'view-4' || $social_view == 'view-5' || $social_view == 'view-6') {
                    ?>
                    <li><a href="<?php echo esc_url($social_googleplus) ?>" target="_blank"
                           class="careerfy-icon careerfy-google-plus"></a></li>
                    <?php
                } else {
                    ?>
                    <li><a href="<?php echo esc_url($social_googleplus) ?>" target="_blank"
                           class="fa fa-google-plus"></a></li>
                    <?php
                }
            }
            if ($social_youtube != '') {
                if ($social_view == 'view-2') {
                    ?>
                    <li><a href="<?php echo esc_url($social_youtube) ?>" target="_blank" class="fa fa-youtube"></a></li>
                    <?php
                } else if ($social_view == 'view-3') {
                    ?>
                    <li><a href="<?php echo esc_url($social_youtube) ?>" target="_blank"><i
                                    class="fa fa-youtube"></i></a></li>
                    <?php
                } else {
                    ?>
                    <li><a href="<?php echo esc_url($social_youtube) ?>" target="_blank" class="fa fa-youtube"></a></li>
                    <?php
                }
            }
            if ($social_vimeo != '') {
                if ($social_view == 'view-2') {
                    ?>
                    <li><a href="<?php echo esc_url($social_vimeo) ?>" target="_blank" class="fa fa-vimeo"></a></li>
                    <?php
                } else if ($social_view == 'view-3') {
                    ?>
                    <li><a href="<?php echo esc_url($social_vimeo) ?>" target="_blank"><i class="fa fa-vimeo"></i></a>
                    </li>
                    <?php
                } else if ($social_view != 'view-4') {
                    ?>
                    <li><a href="<?php echo esc_url($social_vimeo) ?>" target="_blank" class="fa fa-vimeo"></a></li>
                    <?php
                }
            }
            if ($social_linkedin != '') {
                if ($social_view == 'view-2') {
                    ?>
                    <li><a href="<?php echo esc_url($social_linkedin) ?>" target="_blank" class="fa fa-linkedin"></a>
                    </li>
                    <?php
                } else if ($social_view == 'view-3') {
                    ?>
                    <li><a href="<?php echo esc_url($social_linkedin) ?>" target="_blank"><i
                                    class="fa fa-linkedin"></i></a>
                    </li>
                    <?php
                } else if ($social_view == 'view-4' || $social_view == 'view-5' || $social_view == 'view-6') {
                    ?>
                    <li><a href="<?php echo esc_url($social_linkedin) ?>" target="_blank"
                           class="careerfy-icon careerfy-linkedin"></a></li>
                    <?php
                } else { ?>
                    <li><a class="fa fa-linkedin" href="<?php echo esc_url($social_linkedin) ?>" target="_blank"></a>
                    </li>
                    <?php
                }
            }
            if ($social_pinterest != '') {
                if ($social_view == 'view-2') { ?>
                    <li><a href="<?php echo esc_url($social_pinterest) ?>" target="_blank" class="fa fa-pinterest"></a>
                    </li>
                    <?php
                } else if ($social_view == 'view-3') { ?>
                    <li><a href="<?php echo esc_url($social_pinterest) ?>" target="_blank"><i
                                    class="fa fa-pinterest-p"></i></a></li>
                    <?php
                } else if ($social_view == 'view-4' || $social_view == 'view-5' || $social_view == 'view-6') { ?>
                    <li><a href="<?php echo esc_url($social_pinterest) ?>" target="_blank"
                           class="careerfy-icon careerfy-pinterest"></a></li>
                    <?php
                } else { ?>
                    <li><a href="<?php echo esc_url($social_pinterest) ?>" target="_blank" class="fa fa-pinterest"></a>
                    </li>
                    <?php
                }
            }
            if ($social_instagram != '') {
                if ($social_view == 'view-2') { ?>
                    <li><a href="<?php echo esc_url($social_instagram) ?>" target="_blank" class="fa fa-instagram"></a>
                    </li>
                    <?php
                } else if ($social_view == 'view-3') { ?>
                    <li><a href="<?php echo esc_url($social_instagram) ?>" target="_blank"><i
                                    class="fa fa-instagram"></i></a></li>
                    <?php
                } else { ?>
                    <li><a href="<?php echo esc_url($social_instagram) ?>" target="_blank" class="fa fa-instagram"></a>
                    </li>
                    <?php
                }
            } ?>
        </ul>
        <?php
    }
}

if (!function_exists('get_image_id_by_path')) {

    add_action('get_image_id_by_path', 'get_image_id_by_path', 10, 2);

    function get_image_id_by_path($image_url)
    {
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url));
        return $attachment[0];
    }
}

if (!function_exists('trim_salary_type')) {
    function trim_salary_type_text($text, $length = 1)
    {
        if (strpos($text, 'Weekly') != "") {
            $trimed_text = $length == 2 ? str_replace('Weekly', 'We', $text) : str_replace('Weekly', 'W', $text);
        } else if (strpos($text, 'Monthly') != "") {
            $trimed_text = $length == 2 ? str_replace('Monthly', 'Mo', $text) : str_replace('Monthly', 'M', $text);
        } else if (strpos($text, 'Hourly') != "") {

            $trimed_text = $length == 2 ? str_replace('Hourly', 'Hr', $text) : str_replace('Hourly', 'H', $text);
        } else {

            return false;
        }
        return $trimed_text;
    }
}
if (!function_exists('limit_text')) {

    function limit_text($text, $limit)
    {
        if (str_word_count(strip_tags($text), 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }
}

add_filter('rwmb_meta_boxes', function ($meta_boxes) {
    $meta_boxes[] = [
        'title' => 'Page Settings',
        'post_types' => 'page',
        'fields' => [
            [
                'id' => 'heading',
                'type' => 'text',
                'name' => 'Heading'
            ],
            [
                'id' => 'subheading',
                'type' => 'text',
                'name' => 'Sub Heading'
            ], [
                'id' => 'background',
                'type' => 'single_image',
                'name' => 'Background Image'
            ]
        ],
    ];
    return $meta_boxes;
});
function JobsearchAddHTTP($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

