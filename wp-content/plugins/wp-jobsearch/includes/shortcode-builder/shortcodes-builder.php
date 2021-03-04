<?php
if (!class_exists('jobsearch_builder_Shortcodes')) {

    class jobsearch_builder_Shortcodes {

        function __construct() {

            add_action('admin_init', array($this, 'jobsearch_builder_admin_init'));
        }

        /**
         * Enqueue Scripts and Styles
         *
         * @return	void
         */
        function jobsearch_builder_admin_init() {
            include plugin_dir_path(dirname(__FILE__)) . 'shortcode-builder/class-shortcode-insert.php';
            // css
            wp_enqueue_style('jobsearch-builder-shotcode-popup', jobsearch_plugin_get_url('includes/shortcode-builder/assets/css/admin.css'), false, '1.0', 'all');
            // js
            wp_enqueue_script('jquery-ui-sortable');
        }

    }

    new jobsearch_builder_Shortcodes();
}
