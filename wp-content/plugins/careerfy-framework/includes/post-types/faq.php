<?php

/**
 * @Manage Columns
 * @return
 *
 */
if (!class_exists('post_type_faq')) {

    class post_type_faq {

        // The Constructor
        public function __construct() {
            // 
            add_action('init', array($this, 'faqsearch_faq_register'), 1); // post type register
            add_action('init', array($this, 'careerfy_faq_category'), 3, 0);
        }

        public function faqsearch_faq_register() {
            $labels = array(
                'name' => _x('FAQs', 'post type general name', 'careerfy-frame'),
                'singular_name' => _x('FAQ', 'post type singular name', 'careerfy-frame'),
                'menu_name' => _x('FAQs', 'admin menu', 'careerfy-frame'),
                'name_admin_bar' => _x('FAQ', 'add new on admin bar', 'careerfy-frame'),
                'add_new' => _x('Add New', 'faq', 'careerfy-frame'),
                'add_new_item' => __('Add New FAQ', 'careerfy-frame'),
                'new_item' => __('New FAQ', 'careerfy-frame'),
                'edit_item' => __('Edit FAQ', 'careerfy-frame'),
                'view_item' => __('View FAQ', 'careerfy-frame'),
                'all_items' => __('All FAQs', 'careerfy-frame'),
                'search_items' => __('Search FAQs', 'careerfy-frame'),
                'parent_item_colon' => __('Parent FAQs:', 'careerfy-frame'),
                'not_found' => __('No faqs found.', 'careerfy-frame'),
                'not_found_in_trash' => __('No faq found in Trash.', 'careerfy-frame')
            );

            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'careerfy-frame'),
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => false,
                'rewrite' => array('slug' => 'faq'),
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor')
            );

            register_post_type('faq', $args);
        }
        
        public function careerfy_faq_category() {
            // Add new taxonomy, make it hierarchical (like sectors)
            $labels = array(
                'name' => _x('FAQ Categories', 'taxonomy general name', 'careerfy-frame'),
                'singular_name' => _x('FAQ Category', 'taxonomy singular name', 'careerfy-frame'),
                'search_items' => __('Search FAQ Categories', 'careerfy-frame'),
                'all_items' => __('All FAQ Categories', 'careerfy-frame'),
                'parent_item' => __('Parent FAQ Category', 'careerfy-frame'),
                'parent_item_colon' => __('Parent FAQ Category:', 'careerfy-frame'),
                'edit_item' => __('Edit FAQ Category', 'careerfy-frame'),
                'update_item' => __('Update FAQ Category', 'careerfy-frame'),
                'add_new_item' => __('Add New FAQ Category', 'careerfy-frame'),
                'new_item_name' => __('New FAQ Category Name', 'careerfy-frame'),
                'menu_name' => __('FAQ Category', 'careerfy-frame'),
            );

            $args = array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'faq-category'),
            );

            register_taxonomy('faq-category', array('faq'), $args);
        }

    }

    return new post_type_faq();
}
