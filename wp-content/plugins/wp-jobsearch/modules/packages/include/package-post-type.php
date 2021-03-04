<?php
/**
 * @Manage Columns
 * @return
 *
 */
if (!class_exists('post_type_package')) {

    class post_type_package {

        // The Constructor
        public function __construct() {
            
            add_filter('manage_package_posts_columns', array($this, 'columns_add'));
            add_action('manage_package_posts_custom_column', array($this, 'pckg_columns'), 10, 2);
            add_action('init', array($this, 'jobsearch_package_register'), 1); // post type register
        }

        public function jobsearch_package_register() {
            $labels = array(
                'name' => _x('Packages', 'post type general name', 'wp-jobsearch'),
                'singular_name' => _x('Package', 'post type singular name', 'wp-jobsearch'),
                'menu_name' => _x('Packages', 'admin menu', 'wp-jobsearch'),
                'name_admin_bar' => _x('Package', 'add new on admin bar', 'wp-jobsearch'),
                'add_new' => _x('Add New', 'package', 'wp-jobsearch'),
                'add_new_item' => __('Add New Package', 'wp-jobsearch'),
                'new_item' => __('New Package', 'wp-jobsearch'),
                'edit_item' => __('Edit Package', 'wp-jobsearch'),
                'view_item' => __('View Package', 'wp-jobsearch'),
                'all_items' => __('All Packages', 'wp-jobsearch'),
                'search_items' => __('Search Packages', 'wp-jobsearch'),
                'parent_item_colon' => __('Parent Packages:', 'wp-jobsearch'),
                'not_found' => __('No packages found.', 'wp-jobsearch'),
                'not_found_in_trash' => __('No packages found in Trash.', 'wp-jobsearch')
            );

            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'wp-jobsearch'),
                'public' => false,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'package'),
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => 31,
                'supports' => array('title')
            );

            register_post_type('package', $args);
        }
        
        public function columns_add($columns) {
            unset($columns['date']);
            $columns['pckg_type'] = esc_html('Package Type', 'wp-jobsearch');
            $columns['pkg_price'] = esc_html__('Price', 'wp-jobsearch');
            $columns['date'] = esc_html__('Date', 'wp-jobsearch');

            return $columns;
        }

        public function pckg_columns($column) {
            global $post;
            switch ($column) {
                case 'pckg_type' :
                    $pckg_type = get_post_meta($post->ID, 'jobsearch_field_package_type', true);
                    if ($pckg_type == 'feature_job') {
                        $type = esc_html__('Single Featured Job credit', 'wp-jobsearch');
                    } else if ($pckg_type == 'featured_jobs') {
                        $type = esc_html__('Jobs Package with featured credits', 'wp-jobsearch');
                    } else if ($pckg_type == 'emp_allin_one') {
                        $type = esc_html__('All in one', 'wp-jobsearch');
                    } else if ($pckg_type == 'cv') {
                        $type = esc_html__('Employer download CV\'s Package', 'wp-jobsearch');
                    } else if ($pckg_type == 'candidate') {
                        $type = esc_html__('Candidate Job Apply Package', 'wp-jobsearch');
                    } else if ($pckg_type == 'promote_profile') {
                        $type = esc_html__('Promote Profile', 'wp-jobsearch');
                    } else if ($pckg_type == 'urgent_pkg') {
                        $type = esc_html__('Urgent Package', 'wp-jobsearch');
                    } else if ($pckg_type == 'candidate_profile') {
                        $type = esc_html__('Candidate Profile Package', 'wp-jobsearch');
                    } else if ($pckg_type == 'employer_profile') {
                        $type = esc_html__('Employer Profile Package', 'wp-jobsearch');
                    }  else {
                        $type = esc_html__('Job Package', 'wp-jobsearch');
                    }
                    echo apply_filters('jobsearch_pkgs_admin_columns_title', $type, $post->ID);
                    break;
                case 'pkg_price' :
                    $chrg_type = get_post_meta($post->ID, 'jobsearch_field_charges_type', true);
                    $pckg_price = get_post_meta($post->ID, 'jobsearch_field_package_price', true);
                    if ($chrg_type == 'paid' && $pckg_price > 0) {
                        echo jobsearch_get_price_format($pckg_price);
                    } else {
                        echo '-';
                    }
                    break;
            }
        }
    }
    return new post_type_package();
}