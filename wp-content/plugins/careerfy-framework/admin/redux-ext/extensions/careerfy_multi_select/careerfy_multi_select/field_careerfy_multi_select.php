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
if (!class_exists('ReduxFramework_careerfy_multi_select')) {

    /**
     * Main ReduxFramework_custom_field class
     *
     * @since       1.0.0
     */
    class ReduxFramework_careerfy_multi_select extends ReduxFramework {

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

            // HTML output goes here

            $careerfy_framework_options = get_option('careerfy_framework_options');
            $field_value = isset($careerfy_framework_options['careerfy-footer-sidebars']) ? $careerfy_framework_options['careerfy-footer-sidebars'] : '';

            $field_random = rand(1000000, 9999999);

            $field_params = $this->field;
            $field_id = isset($field_params['id']) ? $field_params['id'] : '';
            $field_name = isset($field_params['name']) ? $field_params['name'] : '';
            $field_add_more = isset($field_params['add_more_text']) ? $field_params['add_more_text'] : '';
            $field_select_title = isset($field_params['select_title']) ? $field_params['select_title'] : '';
            $field_input_title = isset($field_params['input_title']) ? $field_params['input_title'] : '';
            $field_select_name = isset($field_params['select_name']) ? $field_params['select_name'] : '';
            $field_input_name = isset($field_params['input_name']) ? $field_params['input_name'] : '';
            $field_select_options = isset($field_params['select_options']) ? $field_params['select_options'] : '';

            $select_options_html = '';
            foreach ($field_select_options as $select_option_key => $select_option_val) {
                $select_options_html .= '<option value="' . $select_option_key . '">' . $select_option_val . '</option>';
            }

            $output = '
			<div id="multi-select-text-' . $field_random . '" class="careerfy-multi-select-text">
				<ul>
					<li class="multi-select-head">
						<div class="select-field">
							<h3>' . $field_select_title . '</h3>
						</div>
						<div class="select-field">
							<h3>' . $field_input_title . '</h3>
						</div>
					</li>';
            if (isset($field_value[$field_select_name]) && is_array($field_value[$field_select_name])) {
                $field_counter = 0;
                foreach ($field_value[$field_select_name] as $field_select_val) {
                    $field_input_val = isset($field_value[$field_input_name][$field_counter]) ? $field_value[$field_input_name][$field_counter] : '';
                    $output .= '
							<li>
								<div class="select-field">
									<select name="' . $field_name . '[' . $field_select_name . '][]">';
                    foreach ($field_select_options as $select_option_key => $select_option_val) {
                        $output .= '<option' . ($field_select_val == $select_option_key ? ' selected="selected"' : '') . ' value="' . $select_option_key . '">' . $select_option_val . '</option>' . "\n";
                    }
                    $output .= '
									</select>
								</div>
								<div class="select-field">
									<input type="text" name="' . $field_name . '[' . $field_input_name . '][]" value="' . $field_input_val . '" />
								</div>';
                    if ($field_counter > 0) {
                        $output .= '<div class="remove-field"><a href="javascript:void(0)">' . __('Remove', 'careerfy-frame') . '</a></div>';
                    }
                    $output .= '
							</li>';
                    $field_counter ++;
                }
            } else {
                $output .= '
						<li>
							<div class="select-field">
								<select name="' . $field_name . '[' . $field_select_name . '][]">
									' . $select_options_html . '
								</select>
							</div>
							<div class="select-field">
								<input type="text" name="' . $field_name . '[' . $field_input_name . '][]" />
							</div>
						</li>';
            }
            $output .= '
				</ul>
				<a href="javascript:void(0)" class="add-more" data-id="' . $field_random . '">' . $field_add_more . '</a>
				<script>
				jQuery(document).ready(function($){
					$(document).on(\'click\', \'.careerfy-multi-select-text > .add-more\', function() {
						var field_id = $(this).data(\'id\');
						var app_html = \'\
						<li>\
							<div class="select-field">\
								<select name="' . $field_name . '[' . $field_select_name . '][]">\
									' . $select_options_html . '\
								</select>\
							</div>\
							<div class="select-field">\
								<input type="text" name="' . $field_name . '[' . $field_input_name . '][]" />\
							</div>\
							<div class="remove-field"><a href="javascript:void(0)">' . __('Remove', 'careerfy-frame') . '</a></div>\
						</li>\';
						$(\'#multi-select-text-\' + field_id + \' ul\').append(app_html);
					});
					$(document).on(\'click\', \'.careerfy-multi-select-text .remove-field\', function() {
						$(this).parents(\'li\').remove();
					});
				});
				</script>
			</div>';

            echo force_balance_tags($output);
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

            $extension = ReduxFramework_extension_careerfy_multi_select::getInstance();

            wp_enqueue_script(
                    'careerfy-redux-multi-select-js', $this->extension_url . 'field_careerfy_multi_select.js', array('jquery'), time(), true
            );

            wp_enqueue_style(
                    'careerfy-redux-multi-select-css', $this->extension_url . 'field_careerfy_multi_select.css', time(), true
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
