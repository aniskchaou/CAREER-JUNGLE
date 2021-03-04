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
 * @author      Dovy Paukstys (dovy)
 * @version     3.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_extension_jobsearch_multi_socialfileds' ) ) {


    /**
     * Main ReduxFramework custom_field extension class
     *
     * @since       3.1.6
     */
    class ReduxFramework_extension_jobsearch_multi_socialfileds extends ReduxFramework {

        // Protected vars
        protected $parent;
        public $extension_url;
        public $extension_dir;
        public static $theInstance;

        /**
        * Class Constructor. Defines the args for the extions class
        *
        * @since       1.0.0
        * @access      public
        * @param       array $sections Panel sections.
        * @param       array $args Class constructor arguments.
        * @param       array $extra_tabs Extra panel tabs.
        * @return      void
        */
        public function __construct( $parent ) {
            $this->parent = $parent;
            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
            }
            $this->field_name = 'jobsearch_multi_socialfileds';

            self::$theInstance = $this;

            add_filter( 'redux/'.$this->parent->args['opt_name'].'/field/class/'.$this->field_name, array( &$this, 'overload_field_path' ) ); // Adds the local field

            //
            add_action('wp_ajax_jobsearch_poptions_social_field_show_icon_cjax', array($this, 'to_show_icon'));
            //
            add_action('wp_ajax_jobsearch_poptions_addmore_social_field_cjax', array($this, 'add_more_social_field'));
        }
        
        public function to_show_icon() {
            $field_random = isset($_POST['random_id']) ? $_POST['random_id'] : '';
            $option_name = isset($_POST['field_name']) ? $_POST['field_name'] : '';
            $field_icon = isset($_POST['field_icon']) ? $_POST['field_icon'] : '';
            $field_icon_group = isset($_POST['icon_group']) ? $_POST['icon_group'] : '';
            
            ob_start();
            if (class_exists('Careerfy_Icons_Fields')) {
                echo Careerfy_Icons_Fields::careerfy_icons_fields_callback($field_icon, $field_random, $option_name . '[icon][]', $field_icon_group, $option_name . '[icon_group][]');
            } else {
                echo jobsearch_icon_picker($field_icon, $field_random, $option_name . '[icon][]', 'jobsearch-icon-pickerr');
                ?>
                <input type="hidden" name="<?php echo ($option_name) ?>[icon_group][]" value="<?php echo ($field_icon_group) ?>">
                <?php
            }
            $html = ob_get_clean();
            echo json_encode(array('html' => $html, 'success' => '1'));
            die;
        }

        public function add_more_social_field() {
            $option_name = $_POST['field_id'];
            $field_random = rand(10000000, 99999999);
            ob_start();
            ?>
            <div class="upldfield-item">
                <ul>
                    <li>
                        <label><?php esc_html_e('Social Field Title', 'wp-jobsearch') ?></label>
                        <input type="text" name="<?php echo ($option_name) ?>[title][]" class="regular-text">
                        <a href="javascript:void(0);" class="remv-upldfiled-item deletion redux-multi-text-remove"><?php esc_html_e('Remove', 'wp-jobsearch') ?></a>
                    </li>
                    <li>
                        <label><?php esc_html_e('Icon', 'wp-jobsearch') ?></label>
                        <div class="icon-appendr">
                            <?php
                            if (class_exists('Careerfy_Icons_Fields')) {
                                echo Careerfy_Icons_Fields::careerfy_icons_fields_callback('', $field_random, $option_name . '[icon][]', 'default', $option_name . '[icon_group][]');
                            } else {
                                echo jobsearch_icon_picker($field_icon, $field_random, $option_name . '[icon][]', 'jobsearch-icon-pickerr');
                                ?>
                                <input type="hidden" name="<?php echo ($option_name) ?>[icon_group][]" value="default">
                                <?php
                            }
                            ?>
                        </div>
                    </li>
                    <li>
                        <label><?php esc_html_e('Icon Color', 'wp-jobsearch') ?></label>
                        <input type="text" name="<?php echo ($option_name) ?>[icon_clr][]" class="jobsearch-bk-color">
                    </li>
                    <li>
                        <label><?php esc_html_e('Icon Background Color', 'wp-jobsearch') ?></label>
                        <input type="text" name="<?php echo ($option_name) ?>[icon_bgclr][]" class="jobsearch-bk-color">
                    </li>
                </ul>
                <script>
                $('.jobsearch-bk-color').wpColorPicker();
                </script>
            </div>
            <?php
            $html = ob_get_clean();
            echo json_encode(array('html' => $html, 'success' => '1'));
            die;
        }

        public static function getInstance() {
            return self::$theInstance;
        }

        // Forces the use of the embeded field path vs what the core typically would use    
        public function overload_field_path($field) {
            return dirname(__FILE__).'/'.$this->field_name.'/field_'.$this->field_name.'.php';
        }

    } // class
} // if
