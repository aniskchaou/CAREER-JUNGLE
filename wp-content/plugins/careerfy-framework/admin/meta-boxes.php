<?php
/**
 * Define Meta boxes for plugin
 * and theme.
 *
 */

/**
 * Page view meta box.
 */
function careerfy_page_view_meta_boxes() {
    add_meta_box('careerfy-page-view', esc_html__('Page View', 'careerfy-frame'), 'careerfy_page_meta_view', 'page', 'side');
}

/**
 * Page Sub Header meta box.
 */
function careerfy_page_subheader_meta_boxes() {
    add_meta_box('careerfy-page-subheader', esc_html__('Subheader', 'careerfy-frame'), 'careerfy_subheader_meta', 'page', 'normal');
    add_meta_box('careerfy-page-subheader', esc_html__('Subheader', 'careerfy-frame'), 'careerfy_subheader_meta', 'post', 'normal');
}

/**
 * Page Header meta box.
 */
function careerfy_page_header_meta_boxes() {
    add_meta_box('careerfy-page-header', esc_html__('Header Settings', 'careerfy-frame'), 'careerfy_header_settings_meta', 'page', 'normal');
}

/**
 * Page title meta box.
 */
function careerfy_page_title_meta_boxes() {
    add_meta_box('careerfy-page-title', esc_html__('Page Title', 'careerfy-frame'), 'careerfy_page_meta_title_switch', 'page', 'side');
}

//
///**
// * Page Layout meta box.
// */
function careerfy_page_layout_meta_boxes() {
    add_meta_box('careerfy-page-layout', esc_html__('Page Layout', 'careerfy-frame'), 'careerfy_post_meta_layout', 'page', 'side');
}

//
///**
// * Post Layout meta box.
// */
function careerfy_post_layout_meta_boxes() {
    add_meta_box('careerfy-post-layout', esc_html__('Post Layout', 'careerfy-frame'), 'careerfy_post_meta_layout', 'post', 'side');
}

//
///**
// * Post settings meta box.
// */
function careerfy_post_settings_meta_boxes() {
    add_meta_box('careerfy-post-settings', esc_html__('Post Settings', 'careerfy-frame'), 'careerfy_post_meta_settings', 'post', 'normal');
}

/**
 * Page header meta box callback.
 */
function careerfy_page_header_settings() {
    global $careerfy_form_fields;
    ?>
    <div class="careerfy-page-view">
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Breadcrumb', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'header_breadcrumb',
                );
                $careerfy_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Page header meta box callback.
 */
function careerfy_header_settings_meta() {
    global $post, $careerfy_form_fields;
    ?>
    <div class="careerfy-page-title">
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Page Header Position', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'page_header_position',
                    'options' => array(
                        'default' => esc_html__('Default', 'careerfy-frame'),
                        'absolute' => esc_html__('Absolute', 'careerfy-frame'),
                    ),
                );
                $careerfy_form_fields->select_field($field_params);
                ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Page sub-header meta box callback.
 */
function careerfy_subheader_meta() {
    global $post, $careerfy_form_fields;
    $rand_num = rand(1000000, 99999999);

    $page_subheader = get_post_meta($post->ID, 'careerfy_field_page_subheader', true);
    
    $linkedin_data =  get_post_meta(748,'linkedin_data',true);
    print_r($linkedin_data);
    
    ?>
    <div class="careerfy-page-title">
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Sub Header', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'page_subheader',
                    'ext_attr' => 'onchange="careerfy_subheader_change_action(this.value, \'' . $rand_num . '\')"',
                    'options' => array(
                        'default' => esc_html__('Default', 'careerfy-frame'),
                        'custom' => esc_html__('Custom', 'careerfy-frame'),
                    ),
                );
                $careerfy_form_fields->select_field($field_params);
                ?>
            </div>
        </div>
        <div id="careerfy-element-sbh-<?php echo absint($rand_num) ?>" style="display: <?php echo ($page_subheader == 'custom' ? 'block' : 'none') ?>;">
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Show Sub Header', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'page_subheader_switch',
                        'options' => array(
                            'on' => esc_html__('Yes', 'careerfy-frame'),
                            'no' => esc_html__('No', 'careerfy-frame'),
                        ),
                    );
                    $careerfy_form_fields->select_field($field_params);
                    ?>
                </div>
            </div> 
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Sub Header Height', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'page_subheader_height',
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Padding Top', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'page_subheader_pading_top',
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Padding Bottom', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'page_subheader_pading_bottom',
                    );
                    $careerfy_form_fields->input_field($field_params);
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
                        'name' => 'page_subheader_title',
                    );
                    $careerfy_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Subtitle', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'page_subheader_subtitle',
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Breadcrumb', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'page_subheader_breadcrumb',
                    );
                    $careerfy_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Background Image', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'page_subheader_bg_image_' . $rand_num,
                        'name' => 'page_subheader_bg_image',
                    );
                    $careerfy_form_fields->image_upload_field($field_params);
                    ?>
                </div>
            </div>
            <div class="careerfy-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Background Color', 'careerfy-frame') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'page_subheader_bg_color',
                        'classes' => 'color-picker',
                        'ext_attr' => 'data-alpha="true"',
                    );
                    $careerfy_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Page view meta box callback.
 */
function careerfy_page_meta_view() {
    global $careerfy_form_fields;
    ?>
    <div class="careerfy-page-view">
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Select View', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'page_view',
                    'options' => array(
                        'box' => esc_html__('Box View', 'careerfy-frame'),
                        'wide' => esc_html__('Wide View', 'careerfy-frame'),
                    ),
                );
                $careerfy_form_fields->select_field($field_params);
                ?>
            </div>
        </div>
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Page Spacing', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'page_spacing',
                    'options' => array(
                        'yes' => esc_html__('Yes', 'careerfy-frame'),
                        'no' => esc_html__('No', 'careerfy-frame'),
                    ),
                );
                $careerfy_form_fields->select_field($field_params);
                ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Page title meta box callback.
 */
function careerfy_page_meta_title_switch() {
    global $careerfy_form_fields;
    ?>
    <div class="careerfy-page-title">
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Page Title', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'page_title_switch',
                    'options' => array(
                        'yes' => esc_html__('Yes', 'careerfy-frame'),
                        'no' => esc_html__('No', 'careerfy-frame'),
                    ),
                );
                $careerfy_form_fields->select_field($field_params);
                ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Post Layout meta box callback.
 */
function careerfy_post_meta_layout() {
    global $careerfy_form_fields, $careerfy_framework_options;

    $sidebars_array = array('' => esc_html__('Select Sidebar', 'careerfy-frame'));
    $careerfy_sidebars = isset($careerfy_framework_options['careerfy-themes-sidebars']) ? $careerfy_framework_options['careerfy-themes-sidebars'] : '';
    if (is_array($careerfy_sidebars) && sizeof($careerfy_sidebars) > 0) {
        foreach ($careerfy_sidebars as $sidebar) {
            $sadbar_id = sanitize_title($sidebar);
            $sidebars_array[$sadbar_id] = $sidebar;
        }
    }
    ?>
    <div class="careerfy-post-layout">
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Select Layout', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'post_layout',
                    'options' => array(
                        'full' => esc_html__('Full', 'careerfy-frame'),
                        'right' => esc_html__('Right Sidebar', 'careerfy-frame'),
                        'left' => esc_html__('Left Sidebar', 'careerfy-frame'),
                    ),
                );
                $careerfy_form_fields->select_field($field_params);
                ?>
            </div>
        </div>
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Select Sidebar', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'post_sidebar',
                    'options' => $sidebars_array,
                );
                $careerfy_form_fields->select_field($field_params);
                ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Post settings meta box callback.
 */
function careerfy_post_meta_settings() {
    global $careerfy_form_fields;
    ?>
    <div class="careerfy-post-settings">
        <div class="careerfy-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Related Posts', 'careerfy-frame') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'std' => 'on',
                    'name' => 'related_posts',
                );
                $careerfy_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>
        <?php echo apply_filters('careerfy_post_meta_settins_after', '') ?>
    </div>
    <?php
}
