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
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

// Don't duplicate me!
if (!class_exists('ReduxFramework_jobsearch_ajax_users')) {

    /**
     * Main ReduxFramework_custom_field class
     *
     * @since       1.0.0
     */
    class ReduxFramework_jobsearch_ajax_users extends ReduxFramework {

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
            $field_title = isset($field_params['title']) ? $field_params['title'] : '';
            $field_user_role = isset($field_params['user_role']) ? $field_params['user_role'] : '';

            $option_name = "jobsearch_plugin_options[{$field_id}]";

            $field_value = isset($jobsearch_plugin_options[$field_id]) ? $jobsearch_plugin_options[$field_id] : '';

            ob_start();
            ?>
            <div id="ajax-users-load-sec-<?php echo ($field_random) ?>" data-role="<?php echo ($field_user_role) ?>" data-value="<?php echo ($field_value) ?>" class="jobsearch-ajax-users">
                <select id="<?php echo ($field_id) ?>" name="<?php echo ($option_name) ?>" class="redux-select-item" data-id="<?php echo ($field_random) ?>" style="width: 40%;">
                    <option value=""><?php esc_html_e('Select User', 'wp-jobsearch') ?></option>
                    <?php
                    if ($field_value != '') {
                        $sel_user_obj = get_user_by('login', $field_value);
                        $sel_user_name = isset($sel_user_obj->display_name) ? $sel_user_obj->display_name : '';
                        $sel_user_name = apply_filters('jobsearch_user_display_name', $sel_user_name, $sel_user_obj);
                        echo '<option value="' . $field_value . '" selected="selected">'.($sel_user_name).'</option>';
                    }
                    ?>
                </select>
                <span class="ajax-loader"></span>
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

            $extension = ReduxFramework_extension_jobsearch_ajax_users::getInstance();

            wp_enqueue_script(
                    'jobsearch_ajax_users_script', $this->extension_url . 'field_jobsearch_ajax_users.js', array('jquery'), time(), true
            );

            wp_enqueue_style(
                    'jobsearch_ajax_users_styles', $this->extension_url . 'field_jobsearch_ajax_users.css', time(), true
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
