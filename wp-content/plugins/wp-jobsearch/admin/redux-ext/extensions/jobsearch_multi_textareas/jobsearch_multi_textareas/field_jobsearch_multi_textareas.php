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
if (!class_exists('ReduxFramework_jobsearch_multi_textareas')) {

    /**
     * Main ReduxFramework_custom_field class
     *
     * @since       1.0.0
     */
    class ReduxFramework_jobsearch_multi_textareas extends ReduxFramework {

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

            $option_name = "jobsearch_plugin_options[{$field_id}]";

            $field_value = isset($jobsearch_plugin_options[$field_id]) ? $jobsearch_plugin_options[$field_id] : '';

            ob_start();
            ?>
            <div class="jobsearch-textareamultifields-redux">
                <h3><?php echo ($field_title) ?></h3>
                <div class="jobsearch-textareamultifields-holdr redux-container-multi_text">
                    <?php
                    if (isset($field_value['title']) && is_array($field_value['title'])) {
                        $field_counter = 0;
                        foreach ($field_value['title'] as $field_title_val) {
                            $field_random = rand(10000000, 99999999);
                            $field_desc = isset($field_value['desc'][$field_counter]) ? $field_value['desc'][$field_counter] : '';
                            ?>
                            <div class="textareafield-item">
                                <div class="title-field">
                                    <label><?php esc_html_e('Title', 'wp-jobsearch') ?></label>
                                    <input type="text" name="<?php echo ($option_name) ?>[title][]" class="regular-text" value="<?php echo ($field_title_val) ?>">
                                </div>
                                <div class="title-field">
                                    <label><?php esc_html_e('Description', 'wp-jobsearch') ?></label>
                                    <?php
                                    $editor_settings = array(
                                        'media_buttons' => false,
                                        'textarea_name' => $option_name . '[desc][]',
                                        'textarea_rows' => 5,
                                        'quicktags' => array('buttons' => 'strong,em,del,ul,ol,li,close'),
                                        'tinymce' => array(
                                            'toolbar1' => 'wdm_mce_button,bold,bullist,numlist,italic,underline,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                                            'toolbar2' => '',
                                            'toolbar3' => '',
                                        ),
                                    );
                                    wp_editor($field_desc, 'desc-editor-' . $field_random, $editor_settings);
                                    ?>
                                </div>
                                <a href="javascript:void(0);" class="remv-upldfiled-item deletion redux-multi-text-remove"><?php esc_html_e('Remove', 'wp-jobsearch') ?></a>
                            </div>
                            <?php
                            $field_counter++;
                        }
                    } else {
                        $field_random = rand(10000000, 99999999);
                        ?>
                        <div class="textareafield-item">
                            <div class="title-field">
                                <label><?php esc_html_e('Title', 'wp-jobsearch') ?></label>
                                <input type="text" name="<?php echo ($option_name) ?>[title][]" class="regular-text">
                            </div>
                            <div class="title-field">
                                <label><?php esc_html_e('Description', 'wp-jobsearch') ?></label>
                                <?php
                                $editor_settings = array(
                                    'media_buttons' => false,
                                    'textarea_name' => $option_name . '[desc][]',
                                    'textarea_rows' => 5,
                                    'quicktags' => array('buttons' => 'strong,em,del,ul,ol,li,close'),
                                    'tinymce' => array(
                                        'toolbar1' => 'wdm_mce_button,bold,bullist,numlist,italic,underline,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                                        'toolbar2' => '',
                                        'toolbar3' => '',
                                    ),
                                );
                                wp_editor('', 'desc-editor-' . $field_random, $editor_settings);
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="add-more-msocilinks-con">
                    <a id="multxtarefields-addmore" href="javascript:void(0);" data-id="<?php echo ($option_name) ?>" class="multifields-addmore-btn button button-primary"><?php esc_html_e('Add More', 'wp-jobsearch') ?></a>
                    <span class="msocilinks-loder"></span>
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

            $extension = ReduxFramework_extension_jobsearch_multi_textareas::getInstance();

            wp_enqueue_script(
                    'jobsearch_multi_textareas_script', $this->extension_url . 'field_jobsearch_multi_textareas.js', array('jquery'), time(), true
            );
            $jobsearch_plugin_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => admin_url('admin-ajax.php'),
                'error_msg' => esc_html__('There is some problem.', 'wp-jobsearch'),
            );

            wp_localize_script('jobsearch_multi_textareas_script', 'jobsearch_multi_textareas_vars', $jobsearch_plugin_arr);

            wp_enqueue_style(
                    'jobsearch_multi_textareas_styles', $this->extension_url . 'field_jobsearch_multi_textareas.css', time(), true
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
