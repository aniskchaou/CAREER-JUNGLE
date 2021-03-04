<?php

// header function for styling
add_action('wp_head', function() {
    $theme_locations = get_nav_menu_locations();
    $addin_css_ids = array();
    $style_css = '';
    foreach ($theme_locations as $theme_location) {
        $menu_obj = get_term( $theme_location, 'nav_menu' );
        if (isset($menu_obj->term_id)) {
            $menu_id = $menu_obj->term_id;
            $menu_items = wp_get_nav_menu_items($menu_id);
            if (is_array($menu_items) && sizeof($menu_items) > 0) {
                foreach ($menu_items as $menu_item) {
                    $menu_item_id = $menu_item->ID;
                    if ($menu_item->menu_item_parent == '0' && !in_array($menu_item_id, $addin_css_ids)) {
                        $listin_count_colr = get_post_meta($menu_item_id, '_menu_item_counliscolr', true);
                        if ($listin_count_colr != '') {
                            $style_css .= '.navbar-nav > li#menu-item-' . $menu_item_id . ' > a[data-title]:after{background-color: ' . $listin_count_colr . ' !important;}';
                        }
                    }
                    $addin_css_ids[] = $menu_item_id;
                }
            }
        }
    }
    //
    if ($style_css != '') {
        echo '<style>' . $style_css . '</style>';
    }
}, 20);

/**
 * Custom Walker
 * @access      public
 * @since       1.0 
 * @return      void
 */
class careerfy_mega_menu_walker extends Walker_Nav_Menu {

    private $CurrentItem, $CategoryMenu, $menu_style;
    public $parent_menu_item_id = 0;
    public $child_items_count = 0;
    public $child_menu_item_id = 0;
    public $view = '';

    function __Construct($view = '') {
        $this->view = $view;
    }

    // Start function for Mega menu
    function careerfy_menu_start() {
        $sub_class = $last = '';
        $count_menu_posts = 0;
        $mega_menu_output = '';
    }

    // Start function For Mega menu level
    function start_lvl(&$output, $depth = 0, $args = array(), $id = 0) {
        $indent = str_repeat("\t", $depth);

        $output .= $this->careerfy_menu_start();
        $columns_class = $this->CurrentItem->columns;
        $careerfy_parent_id = $this->CurrentItem->menu_item_parent;

        $parent_nav_mega = get_post_meta($careerfy_parent_id, '_menu_item_megamenu', true);
        $parent_nav_mega_view = get_post_meta($careerfy_parent_id, '_menu_item_view', true);

        if ($this->CurrentItem->megamenu == 'on' && $depth == 0) {
            $output .= "\n$indent<ul class=\"careerfy-megamenu row\">\n";
        } else if ($parent_nav_mega == 'on' && $depth == 1) {
            $output .= "\n$indent<ul class=\"careerfy-megalist\">\n";
        } else {
            $output .= "\n$indent<ul class=\"sub-menu\">\n";
        }
    }

    // Start function For Mega menu level end
    function end_lvl(&$output, $depth = 0, $args = array()) {

        $careerfy_parent_id = $this->CurrentItem->menu_item_parent;
        $parent_nav_mega = get_post_meta($this->parent_menu_item_id, '_menu_item_megamenu', true);
        $parent_nav_mega_view = get_post_meta($this->parent_menu_item_id, '_menu_item_view', true);

        $indent = str_repeat("\t", $depth);

        if ($parent_nav_mega == 'on' && $depth == 0) {
            if ($parent_nav_mega_view == 'image-text') {
                $_menu_item_image_title = get_post_meta($this->parent_menu_item_id, '_menu_item_image_title', true);
                $_menu_item_image_paragragh = get_post_meta($this->parent_menu_item_id, '_menu_item_image_paragragh', true);
                $_menu_item_image_title_2 = get_post_meta($this->parent_menu_item_id, '_menu_item_image_title_2', true);
                $_menu_item_image_img = get_post_meta($this->parent_menu_item_id, '_menu_item_image_img', true);

                if ($_menu_item_image_paragragh != '') {
                    $output .= '
					<li class="col-md-5">
						<h4>' . $_menu_item_image_title . '</h4>
						<div class="careerfy-mega-text">
							<p>' . $_menu_item_image_paragragh . '</p>
						</div>
					</li>';
                }
                if ($_menu_item_image_img != '') {
                    $output .= '
					<li class="col-md-5">
						<h4>' . $_menu_item_image_title_2 . '</h4>
						<a class="careerfy-thumbnail">
							<img src="' . $_menu_item_image_img . '" alt="">
						</a>
					</li>';
                }
            }
            if ($parent_nav_mega_view == 'video') {
                $_menu_item_video = get_post_meta($this->parent_menu_item_id, '_menu_item_video', true);
                if ($_menu_item_video != '') {
                    $output .= '
					<li class="col-md-6">
						<a class="careerfy-thumbnail">
							' . wp_oembed_get($_menu_item_video, array('height' => 300)) . '
						</a>
					</li>';
                }
            }
        }
        $output .= $indent . "</ul>\n";
        $item_id = $this->parent_menu_item_id . $this->child_menu_item_id;
    }

    // Start function For Mega menu items

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {

        global $wp_query;
        $this->CurrentItem = $item;

        $parent_nav_mega = 'off';
        $parent_item_mega_view = '';

        // show more/less more variables 
        $li_style = '';
        if ($depth == 2) {
            $this->child_items_count ++;
        } else {
            $this->child_items_count = 0;
        }

        if ($depth == 0) {
            $this->parent_menu_item_id = $item->ID;
        }
        if ($depth == 1) {
            $this->child_menu_item_id ++;
        } else if ($depth == 0) {
            $this->child_menu_item_id = 0;
        }
        //// end show more/less more

        if ($depth == 1) {
            $parent_menu_id = $item->menu_item_parent;
            $parent_nav_mega = get_post_meta($parent_menu_id, '_menu_item_megamenu', true);
            $parent_item_mega_view = get_post_meta($parent_menu_id, '_menu_item_view', true);
        }
        if (empty($args)) {
            $args = new stdClass();
        }
        //
        $listin_counts = '';
        if ($depth == 0) {
            $show_listin_counts = false;
            $listin_count_for = get_post_meta($item->ID, '_menu_item_counlisfor', true);
            if ($listin_count_for == 'jobs') {
                $show_listin_counts = true;
                $jlistin_args = array(
                    'posts_per_page' => '1',
                    'post_type' => 'job',
                    'post_status' => 'publish',
                    'fields' => 'ids', // only load ids
                    'meta_query' => array(
                        array(
                            'key' => 'jobsearch_field_job_publish_date',
                            'value' => current_time('timestamp'),
                            'compare' => '<=',
                        ),
                        array(
                            'key' => 'jobsearch_field_job_expiry_date',
                            'value' => current_time('timestamp'),
                            'compare' => '>=',
                        ),
                        array(
                            'key' => 'jobsearch_field_job_status',
                            'value' => 'approved',
                            'compare' => '=',
                        ),
                    ),
                );
                $jlistin_query = new WP_Query($jlistin_args);
                $j_totl_listins = $jlistin_query->found_posts;
            } else if ($listin_count_for == 'employers') {
                $show_listin_counts = true;
                $jlistin_args = array(
                    'posts_per_page' => '1',
                    'post_type' => 'employer',
                    'post_status' => 'publish',
                    'fields' => 'ids', // only load ids
                    'meta_query' => array(
                        array(
                            'key' => 'jobsearch_field_employer_approved',
                            'value' => 'on',
                            'compare' => '=',
                        ),
                    ),
                );
                $jlistin_query = new WP_Query($jlistin_args);
                $j_totl_listins = $jlistin_query->found_posts;
            } else if ($listin_count_for == 'candidates') {
                $show_listin_counts = true;
                $jlistin_args = array(
                    'posts_per_page' => '1',
                    'post_type' => 'candidate',
                    'post_status' => 'publish',
                    'fields' => 'ids', // only load ids
                    'meta_query' => array(
                        array(
                            'key' => 'jobsearch_field_candidate_approved',
                            'value' => 'on',
                            'compare' => '=',
                        ),
                    ),
                );
                $jlistin_query = new WP_Query($jlistin_args);
                $j_totl_listins = $jlistin_query->found_posts;
            }
            
            if ($show_listin_counts) {
                $listin_counts = ' data-title="' . absint($j_totl_listins) . '"';
            }
        }
        //

        $indent = ( $depth ) ? str_repeat("\t", $depth) : '';
        if ($depth == 0) {
            $class_names = $value = '';
            $mega_menu = '';
        } else if ($args->has_children) {
            $class_names = $value = '';
            $mega_menu = '';
        } else {
            $class_names = $value = $mega_menu = '';
        }
        $classes = empty($item->classes) ? array() : (array) $item->classes;

        $class_names = join(" $mega_menu ", apply_filters('nav_menu_css_class', array_filter($classes), $item));
        if ($this->CurrentItem->megamenu == 'on' && $args->has_children && $depth == 0) {
            $class_names = ' class="' . esc_attr($class_names) . ' careerfy-megamenu-li"';
        } else if ($parent_nav_mega == 'on') {
            if ($depth == 1) {
                $class_names = ' class="col-md-2"';
            } else {
                $class_names = ' class="col-md-2"';
            }
        } else {
            $class_names = ' class="' . esc_attr($class_names) . '"';
        }
        
        $output .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';
        
        $attributes = isset($item->attr_title) && $item->attr_title != '' ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= isset($item->target) && $item->target != '' ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= isset($item->xfn) && $item->xfn != '' ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= isset($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        if ($listin_counts != '') {
            $attributes .= $listin_counts;
        }
        $item_output = isset($args->before) ? $args->before : '';

        if ($parent_nav_mega == 'on' && $depth == 1) {
            $item_output .= '<a class="megamenu-title" ' . $attributes . '>';
        } else {
            $item_output .= '<a' . $attributes . '>';
        }
        $careerfy_link_before = isset($args->link_before) ? $args->link_before : '';
        $item_output .= $careerfy_link_before . apply_filters('the_title', $item->title, $item->ID);
        
        if ($this->CurrentItem->subtitle != '') {
            $item_output .= '<span>' . $this->CurrentItem->subtitle . '</span>';
        }
        $careerfy_link_after = isset($args->link_before) ? $args->link_before : '';
        $item_output .= $careerfy_link_after;
        
        if (isset($item->description) && $item->description != '') {
            $item_output .= '<small class="menu-itm-description">' . esc_html($item->description) . '</small>';
        }
        if ($parent_nav_mega == 'on' && $depth == 1) {
            $item_output .= '</a>';
        } else {
            $item_output .= '</a>';
        }

        $item_output .= isset($args->after) ? $args->after : '';
        if (!empty($mega_menu) && empty($args->has_children) && $this->CurrentItem->megamenu == 'on') {
            $item_output .= $this->careerfy_menu_start();
        }
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args, $id);
        return $output;
    }

    //Start function For Mega menu display elements
    function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
        $id_field = $this->db_fields['id'];
        if (is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        }
        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

}
