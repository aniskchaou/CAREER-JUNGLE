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
if( !class_exists( 'ReduxFramework_extension_jobsearch_multi_textareas' ) ) {


    /**
     * Main ReduxFramework custom_field extension class
     *
     * @since       3.1.6
     */
    class ReduxFramework_extension_jobsearch_multi_textareas extends ReduxFramework {

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
            $this->field_name = 'jobsearch_multi_textareas';

            self::$theInstance = $this;

            add_filter( 'redux/'.$this->parent->args['opt_name'].'/field/class/'.$this->field_name, array( &$this, 'overload_field_path' ) ); // Adds the local field
            //
            add_action('wp_ajax_jobsearch_poptions_addmore_txtarea_field_cjax', array($this, 'add_more_txtarea_field'));
        }

        public function add_more_txtarea_field() {
            $option_name = $_POST['field_id'];
            $field_random = rand(10000000, 99999999);
            //ob_start();
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
                    wp_editor('', 'canedmsgs-desc-admore', $editor_settings);
                    ?>
                </div>
                <a href="javascript:void(0);" class="remv-upldfiled-item deletion redux-multi-text-remove"><?php esc_html_e('Remove', 'wp-jobsearch') ?></a>
            </div>
            <?php
            //$html = ob_get_clean();
            //echo json_encode(array('html' => $html, 'success' => '1'));
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
