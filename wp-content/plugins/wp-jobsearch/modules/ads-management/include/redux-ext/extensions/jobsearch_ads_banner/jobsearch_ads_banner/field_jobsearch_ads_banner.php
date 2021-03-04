<?php
/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @author      Dovy Paukstys
 * @version     3.1.5
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

// Don't duplicate me!
if (!class_exists('ReduxFramework_jobsearch_ads_banner')) {

    /**
     * Main ReduxFramework_custom_field class
     *
     * @since       1.0.0
     */
    class ReduxFramework_jobsearch_ads_banner extends ReduxFramework {

        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct($field = array(), $value = '', $parent) {


            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;

            if (empty($this->extension_dir)) {
                $this->extension_dir = trailingslashit(str_replace('\\', '/', dirname(__FILE__)));
                $this->extension_url = site_url(str_replace(trailingslashit(str_replace('\\', '/', ABSPATH)), '', $this->extension_dir));
            }

            // Set default args for this field to avoid bad indexes. Change this to anything you use.
            $defaults = array(
                'options' => array(),
                'stylesheet' => '',
                'output' => true,
                'enqueue' => true,
                'enqueue_frontend' => true
            );
            $this->field = wp_parse_args($this->field, $defaults);
        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {

            global $JobsearchReduxFramework;
            // HTML output goes here
            $jobsearch_plugin_options = get_option('jobsearch_plugin_options');

            $field_random = rand(1000000, 9999999);

            $field_params = $this->field;
            $field_id = isset($field_params['id']) ? $field_params['id'] : '';
            $field_def_groups = isset($field_params['default_groups']) ? $field_params['default_groups'] : '';
            if (!isset($jobsearch_plugin_options['ad_banner_groups']) && !empty($field_def_groups)) {
                $def_ad_groups = $ad_group_codes = $ad_group_titles = $ad_group_sorts = $ad_group_visible_adss = array();
                foreach ($field_def_groups as $group_key => $group_field) {
                    $group_title = isset($group_field['title']) ? $group_field['title'] : '';
                    $group_sort = isset($group_field['sort']) ? $group_field['sort'] : '';
                    $group_visible_ads = isset($group_field['visible_ads']) ? $group_field['visible_ads'] : '';
                    $ad_group_codes[] = $group_key;
                    $ad_group_titles[] = $group_title;
                    $ad_group_sorts[] = $group_sort;
                    $ad_group_visible_adss[] = $group_visible_ads;
                }

                $def_ad_groups['group_code'] = $ad_group_codes;
                $def_ad_groups['group_title'] = $ad_group_titles;
                $def_ad_groups['group_sort'] = $ad_group_sorts;
                $def_ad_groups['group_visible_ads'] = $ad_group_visible_adss;
                //$JobsearchReduxFramework->ReduxFramework->set('ad_banner_groups', $def_ad_groups);
            }
            $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';

            $field_value = isset($jobsearch_plugin_options[$field_id]) ? $jobsearch_plugin_options[$field_id] : '';

            ob_start();
            ?>
            <div id="ads-management-sec-<?php echo ($field_random) ?>" class="ads_management_con">
                <div class="banner-groups-sec">
                    <div class="banner-groups-heading"><h2><?php esc_html_e('Ad Groups', 'wp-jobsearch') ?></h2></div>
                    <div class="pumflit-banner-fields-sec">
                        <div class="pumflit-banner-field-con ads-input-field">
                            <div class="field-label"><?php esc_html_e('Group Title', 'wp-jobsearch') ?></div>
                            <div class="field-value">
                                <input type="text" id="group-title">
                            </div>
                        </div>
                        <div class="pumflit-banner-field-con ads-select-field">
                            <div class="field-label"><?php esc_html_e('Ads Sorting', 'wp-jobsearch') ?></div>
                            <div class="field-value">
                                <select id="group-sort">
                                    <option value="random"><?php esc_html_e('Random Ads', 'wp-jobsearch') ?></option>
                                    <option value="ordered"><?php esc_html_e('Ordered Ads', 'wp-jobsearch') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="pumflit-banner-field-con ads-select-field">
                            <div class="field-label"><?php esc_html_e('Visible Ads', 'wp-jobsearch') ?></div>
                            <div class="field-value">
                                <select id="group-vis-ads">
                                    <?php
                                    for ($vi = 1; $vi <= 10; $vi++) {
                                        ?>
                                        <option value="<?php echo ($vi) ?>"><?php echo ($vi) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="pumflit-banner-field-con ads-submit-field">
                            <div class="submit-btn">
                                <a href="javascript:void(0);" class="add-pumflit-group-to-list"><?php esc_html_e('Add Group', 'wp-jobsearch') ?></a>
                                <span class="loader-box"></span>
                            </div>
                        </div>
                        <div class="message-box"></div>
                    </div>
                    <div class="pumflit-groups-list-sec">
                        <div class="group-item group-head">
                            <ul>
                                <li><?php esc_html_e('Group Title', 'wp-jobsearch') ?></li>
                                <li><?php esc_html_e('Ads Sorting', 'wp-jobsearch') ?></li>
                                <li><?php esc_html_e('Visible Ads', 'wp-jobsearch') ?></li>
                                <li class="group-code"><?php esc_html_e('Group Code', 'wp-jobsearch') ?></li>
                                <li>&nbsp;</li>
                            </ul>
                        </div>
                        <?php
                        if (isset($groups_value['group_title']) && !empty($groups_value['group_title'])) {
                            $b_group_counter = 0;
                            $group_sorts = isset($groups_value['group_sort']) ? $groups_value['group_sort'] : '';
                            $group_visible_adss = isset($groups_value['group_visible_ads']) ? $groups_value['group_visible_ads'] : '';
                            $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                            foreach ($groups_value['group_title'] as $group_title) {
                                $group_sort = isset($group_sorts[$b_group_counter]) ? $group_sorts[$b_group_counter] : '';
                                $group_visible_ads = isset($group_visible_adss[$b_group_counter]) ? $group_visible_adss[$b_group_counter] : '';
                                $group_code = isset($group_codes[$b_group_counter]) ? $group_codes[$b_group_counter] : '';

                                $option_name = "jobsearch_plugin_options[ad_banner_groups]";
                                ?>
                                <div class="group-item">
                                    <ul>
                                        <li><?php echo ($group_title) ?></li>
                                        <li><?php echo ($group_sort) ?></li>
                                        <li><?php echo ($group_visible_ads) ?></li>
                                        <li class="group-code"><?php echo ('[jobsearch_ads_group code="' . $group_code . '"]') ?></li>
                                        <li>
                                            <div class="action-btns">
                                                <a href="javascript:void(0);" class="update-group"><i class="dashicons dashicons-edit"></i></a>
                                                <a href="javascript:void(0);" class="remove-group"><i class="dashicons dashicons-trash"></i></a>
                                            </div>

                                        </li>
                                    </ul>
                                    <div class="action-update-con">
                                        <div class="pumflit-banner-field-con ads-input-field">
                                            <div class="field-label"><?php esc_html_e('Group Title', 'wp-jobsearch') ?></div>
                                            <div class="field-value">
                                                <input type="text" name="<?php echo ($option_name) . '[group_title][]' ?>" value="<?php echo ($group_title) ?>">
                                                <input type="hidden" name="<?php echo ($option_name) . '[group_code][]' ?>" value="<?php echo ($group_code) ?>">
                                            </div>
                                        </div>
                                        <div class="pumflit-banner-field-con ads-select-field">
                                            <div class="field-label"><?php esc_html_e('Ads Sorting', 'wp-jobsearch') ?></div>
                                            <div class="field-value">
                                                <select name="<?php echo ($option_name) . '[group_sort][]' ?>">
                                                    <option value="random"<?php echo ($group_sort == 'random' ? ' selected="selected"' : '') ?>><?php esc_html_e('Random Ads', 'wp-jobsearch') ?></option>
                                                    <option value="ordered"<?php echo ($group_sort == 'ordered' ? ' selected="selected"' : '') ?>><?php esc_html_e('Ordered Ads', 'wp-jobsearch') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="pumflit-banner-field-con ads-select-field">
                                            <div class="field-label"><?php esc_html_e('Visible Ads', 'wp-jobsearch') ?></div>
                                            <div class="field-value">
                                                <select name="<?php echo ($option_name) . '[group_visible_ads][]' ?>">
                                                    <?php
                                                    for ($vi = 1; $vi <= 10; $vi++) {
                                                        ?>
                                                        <option value="<?php echo ($vi) ?>"<?php echo ($group_visible_ads == $vi ? ' selected="selected"' : '') ?>><?php echo ($vi) ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="pumflit-banner-field-con ads-submit-field">
                                            <div class="submit-btn">
                                                <a href="javascript:void(0);" class="update-the-list"><?php esc_html_e('Update Group', 'wp-jobsearch') ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $b_group_counter ++;
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="pumflit-banner-seprator"></div>
                <div class="banner-all-ads-sec">
                    <div class="banner-ads-heading"><h2><?php esc_html_e('Ad Banners', 'wp-jobsearch') ?></h2></div>
                    <div class="pumflit-banner-fields-sec">
                        <div class="pumflit-banner-field-con ads-input-field">
                            <div class="field-label"><?php esc_html_e('Banner Title', 'wp-jobsearch') ?></div>
                            <div class="field-value">
                                <input type="text" id="pumflit-title">
                            </div>
                        </div>
                        <?php
                        $groups_select_opts = array('' => esc_html__('Select Group', 'wp-jobsearch'));
                        if (isset($groups_value['group_title']) && !empty($groups_value['group_title'])) {
                            $b_group_counter = 0;
                            $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                            foreach ($groups_value['group_title'] as $group_title) {
                                $group_code = isset($group_codes[$b_group_counter]) ? $group_codes[$b_group_counter] : '';
                                $groups_select_opts[$group_code] = $group_title;
                                $b_group_counter++;
                            }
                        }
                        ?>
                        <div class="pumflit-banner-field-con ads-select-field">
                            <div class="field-label"><?php esc_html_e('Banner Group', 'wp-jobsearch') ?></div>
                            <div class="field-value">
                                <select id="pumflit-style">
                                    <?php
                                    foreach ($groups_select_opts as $group_opt_key => $group_opt_title) {
                                        ?>
                                        <option value="<?php echo ($group_opt_key) ?>"><?php echo ($group_opt_title) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="pumflit-banner-field-con ads-select-field">
                            <div class="field-label"><?php esc_html_e('Banner Type', 'wp-jobsearch') ?></div>
                            <div class="field-value">
                                <select id="pumflit-type">
                                    <option value="image"><?php esc_html_e('Image Ad', 'wp-jobsearch') ?></option>
                                    <option value="adsense"><?php esc_html_e('Adsense Ad', 'wp-jobsearch') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="pumflit-banner-img-field" class="pumflit-banner-field-con ads-input-field">
                            <div class="field-label"><?php esc_html_e('Banner Image', 'wp-jobsearch') ?></div>
                            <div class="field-value">
                                <div id="pumflit-image-box" class="jobsearch-browse-med-image" style="display: none;">
                                    <a class="jobsearch-rem-media-b" data-id="pumflit-image"><i class="dashicons dashicons-no-alt"></i></a>
                                    <img id="pumflit-image-img" src="" alt="">
                                </div>
                                <input type="hidden" id="pumflit-image">
                                <input type="button" class="jobsearch-upload-media jobsearch-bk-btn" name="pumflit-image" value="<?php esc_html_e('Browse', 'wp-jobsearch') ?>"> 
                            </div>
                        </div>
                        <div id="pumflit-banner-img-url-field" class="pumflit-banner-field-con ads-input-field">
                            <div class="field-label"><?php esc_html_e('Image URL', 'wp-jobsearch') ?></div>
                            <div class="field-value">
                                <input type="text" id="pumflit-img-url">
                            </div>
                        </div>
                        <div id="pumflit-banner-url-target-field" class="pumflit-banner-field-con ads-input-field">
                            <div class="field-label"><?php esc_html_e('URL Target', 'wp-jobsearch') ?></div>
                            <div class="field-value">
                                <select id="pumflit-target">
                                    <option value="blank"><?php esc_html_e('_blank', 'wp-jobsearch') ?></option>
                                    <option value="self"><?php esc_html_e('_self', 'wp-jobsearch') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="pumflit-banner-adsense-field" class="pumflit-banner-field-con ads-textarea-field" style="display:none;">
                            <div class="field-label"><?php esc_html_e('Adsense Code', 'wp-jobsearch') ?></div>
                            <div class="field-value">
                                <textarea id="pumflit-adsense-code"></textarea>
                            </div>
                        </div>
                        <div class="pumflit-banner-field-con ads-submit-field">
                            <div class="submit-btn">
                                <a href="javascript:void(0);" class="add-banner-to-list" data-id="<?php echo ($field_id) ?>"><?php esc_html_e('Add Banner', 'wp-jobsearch') ?></a>
                                <span class="loader-box"></span>
                            </div>
                        </div>
                        <div class="message-box"></div>
                    </div>
                    <div class="pumflit-banners-list-sec">
                        <div class="pumflit-item pumflit-head">
                            <ul>
                                <li><?php esc_html_e('Title', 'wp-jobsearch') ?></li>
                                <li><?php esc_html_e('Type', 'wp-jobsearch') ?></li>
                                <li><?php esc_html_e('Group', 'wp-jobsearch') ?></li>
                                <li><?php esc_html_e('Total Clicks', 'wp-jobsearch') ?></li>
                                <li class="pumflit-code"><?php esc_html_e('Code', 'wp-jobsearch') ?></li>
                                <li>&nbsp;</li>
                            </ul>
                        </div>
                        <?php
                        if (isset($field_value['banner_title']) && !empty($field_value['banner_title'])) {
                            $b_field_counter = 0;
                            $banner_types = isset($field_value['banner_type']) ? $field_value['banner_type'] : '';
                            $banner_styles = isset($field_value['banner_group']) ? $field_value['banner_group'] : '';
                            $banner_imgs = isset($field_value['banner_image']) ? $field_value['banner_image'] : '';
                            $banner_img_urls = isset($field_value['banner_img_url']) ? $field_value['banner_img_url'] : '';
                            $banner_url_targets = isset($field_value['banner_url_target']) ? $field_value['banner_url_target'] : '';
                            $banner_adsense_codes = isset($field_value['banner_adsense_code']) ? $field_value['banner_adsense_code'] : '';
                            $banner_codes = isset($field_value['banner_code']) ? $field_value['banner_code'] : '';
                            foreach ($field_value['banner_title'] as $banner_title) {
                                $banner_type = isset($banner_types[$b_field_counter]) ? $banner_types[$b_field_counter] : '';
                                $banner_style = isset($banner_styles[$b_field_counter]) ? $banner_styles[$b_field_counter] : '';
                                $banner_img = isset($banner_imgs[$b_field_counter]) ? $banner_imgs[$b_field_counter] : '';
                                $banner_img_url = isset($banner_img_urls[$b_field_counter]) ? $banner_img_urls[$b_field_counter] : '';
                                $banner_url_target = isset($banner_url_targets[$b_field_counter]) ? $banner_url_targets[$b_field_counter] : '';
                                $banner_adsense_code = isset($banner_adsense_codes[$b_field_counter]) ? $banner_adsense_codes[$b_field_counter] : '';
                                $banner_code = isset($banner_codes[$b_field_counter]) ? $banner_codes[$b_field_counter] : '';

                                if ($banner_type == 'adsense') {
                                    $banner_counts = '-';
                                    $banner_type_txt = __('Adsense Code', 'wp-jobsearch');
                                } else {
                                    $banner_counts = absint(get_option('banner_click_' . $banner_code));
                                    $banner_type_txt = __('Image Ad', 'wp-jobsearch');
                                }
                                $option_name = "jobsearch_plugin_options[{$field_id}]";
                                ?>
                                <div class="pumflit-item">
                                    <ul>
                                        <li><?php echo ($banner_title) ?></li>
                                        <li><?php echo ($banner_type_txt) ?></li>
                                        <li><?php echo apply_filters('jobsearch_get_ads_group_title', $banner_style) ?></li>
                                        <li><?php echo ($banner_counts) ?></li>
                                        <li class="pumflit-code"><?php echo ('[jobsearch_ad code="' . $banner_code . '"]') ?></li>
                                        <li>
                                            <div class="action-btns">
                                                <a href="javascript:void(0);" class="update-ad"><i class="dashicons dashicons-edit"></i></a>
                                                <a href="javascript:void(0);" class="remove-ad"><i class="dashicons dashicons-trash"></i></a>
                                            </div>
                                        </li>
                                    </ul>

                                    <div class="action-update-con">
                                        <div class="pumflit-banner-field-con ads-input-field">
                                            <div class="field-label"><?php esc_html_e('Banner Title', 'wp-jobsearch') ?></div>
                                            <div class="field-value">
                                                <input type="text" name="<?php echo ($option_name) . '[banner_title][]' ?>" value="<?php echo ($banner_title) ?>">
                                                <input type="hidden" name="<?php echo ($option_name) . '[banner_code][]' ?>" value="<?php echo ($banner_code) ?>">
                                            </div>
                                        </div>
                                        <?php
                                        $groups_select_opts = array('' => esc_html__('Select Group', 'wp-jobsearch'));
                                        if (isset($groups_value['group_title']) && !empty($groups_value['group_title'])) {
                                            $b_group_counter = 0;
                                            $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                                            foreach ($groups_value['group_title'] as $group_title) {
                                                $group_code = isset($group_codes[$b_group_counter]) ? $group_codes[$b_group_counter] : '';
                                                $groups_select_opts[$group_code] = $group_title;
                                                $b_group_counter++;
                                            }
                                        }
                                        ?>
                                        <div class="pumflit-banner-field-con ads-select-field">
                                            <div class="field-label"><?php esc_html_e('Banner Group', 'wp-jobsearch') ?></div>
                                            <div class="field-value">
                                                <select name="<?php echo ($option_name) . '[banner_group][]' ?>">
                                                    <?php
                                                    foreach ($groups_select_opts as $group_opt_key => $group_opt_title) {
                                                        ?>
                                                        <option value="<?php echo ($group_opt_key) ?>"<?php echo ($banner_style == $group_opt_key ? ' selected="selected"' : '') ?>><?php echo ($group_opt_title) ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="pumflit-banner-field-con ads-select-field">
                                            <div class="field-label"><?php esc_html_e('Banner Type', 'wp-jobsearch') ?></div>
                                            <div class="field-value">
                                                <select class="jobsearch-pumflit-type-select" name="<?php echo ($option_name) . '[banner_type][]' ?>" data-id="<?php echo ($banner_code) ?>">
                                                    <option value="image"<?php echo ($banner_type == 'image' ? ' selected="selected"' : '') ?>><?php esc_html_e('Image Ad', 'wp-jobsearch') ?></option>
                                                    <option value="adsense"<?php echo ($banner_type == 'adsense' ? ' selected="selected"' : '') ?>><?php esc_html_e('Adsense Ad', 'wp-jobsearch') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="pumflit-banner-img-field-<?php echo ($banner_code) ?>" class="pumflit-banner-field-con ads-input-field" style="display: <?php echo ($banner_type == 'image' ? 'block' : 'none') ?>;">
                                            <div class="field-label"><?php esc_html_e('Banner Image', 'wp-jobsearch') ?></div>
                                            <div class="field-value">
                                                <div id="pumflit-image-<?php echo ($banner_code) ?>-box" class="jobsearch-browse-med-image" style="display: <?php echo ($banner_img != '' ? 'block' : 'none') ?>;">
                                                    <a class="jobsearch-rem-media-b" data-id="pumflit-image-<?php echo ($banner_code) ?>"><i class="dashicons dashicons-no-alt"></i></a>
                                                    <img id="pumflit-image-<?php echo ($banner_code) ?>-img" src="<?php echo ($banner_img) ?>" alt="">
                                                </div>
                                                <input type="hidden" id="pumflit-image-<?php echo ($banner_code) ?>" name="<?php echo ($option_name) . '[banner_image][]' ?>" value="<?php echo ($banner_img) ?>">
                                                <input type="button" class="jobsearch-uplopumflit-media jobsearch-bk-btn" name="pumflit-image-<?php echo ($banner_code) ?>" value="<?php esc_html_e('Browse', 'wp-jobsearch') ?>"> 
                                            </div>
                                        </div>
                                        <div id="pumflit-banner-img-url-field-<?php echo ($banner_code) ?>" class="pumflit-banner-field-con ads-input-field" style="display: <?php echo ($banner_type == 'image' ? 'block' : 'none') ?>;">
                                            <div class="field-label"><?php esc_html_e('Image URL', 'wp-jobsearch') ?></div>
                                            <div class="field-value">
                                                <input type="text" name="<?php echo ($option_name) . '[banner_img_url][]' ?>" value="<?php echo ($banner_img_url) ?>">
                                            </div>
                                        </div>
                                        <div id="pumflit-banner-url-target-field-<?php echo ($banner_code) ?>" class="pumflit-banner-field-con ads-input-field" style="display: <?php echo ($banner_type == 'image' ? 'block' : 'none') ?>;">
                                            <div class="field-label"><?php esc_html_e('URL Target', 'wp-jobsearch') ?></div>
                                            <div class="field-value">
                                                <select name="<?php echo ($option_name) . '[banner_url_target][]' ?>">
                                                    <option value="blank"<?php echo ($banner_url_target == 'blank' ? ' selected="selected"' : '') ?>><?php esc_html_e('_blank', 'wp-jobsearch') ?></option>
                                                    <option value="self"<?php echo ($banner_url_target == 'self' ? ' selected="selected"' : '') ?>><?php esc_html_e('_self', 'wp-jobsearch') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="pumflit-banner-adsense-field-<?php echo ($banner_code) ?>" class="pumflit-banner-field-con ads-textarea-field" style="display: <?php echo ($banner_type == 'adsense' ? 'block' : 'none') ?>;">
                                            <div class="field-label"><?php esc_html_e('Adsense Code', 'wp-jobsearch') ?></div>
                                            <div class="field-value">
                                                <textarea name="<?php echo ($option_name) . '[banner_adsense_code][]' ?>"><?php echo ($banner_adsense_code) ?></textarea>
                                            </div>
                                        </div>
                                        <div class="pumflit-banner-field-con ads-submit-field">
                                            <div class="submit-btn">
                                                <a href="javascript:void(0);" class="update-the-banner"><?php esc_html_e('Update Banner', 'wp-jobsearch') ?></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <?php
                                $b_field_counter++;
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
            $output = ob_get_clean();

            echo ($output);
        }

        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {

            $extension = ReduxFramework_extension_jobsearch_ads_banner::getInstance();

            wp_enqueue_script(
                    'jobsearch_ads_banner_script', $this->extension_url . 'field_jobsearch_ads_banner.js', array('jquery'), time(), true
            );

            wp_enqueue_style(
                    'jobsearch_ads_banner_styles', $this->extension_url . 'field_jobsearch_ads_banner.css', time(), true
            );
        }

        /**
         * Output Function.
         *
         * Used to enqueue to the front-end
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function output() {

            if ($this->field['enqueue_frontend']) {
                
            }
        }

    }

}
