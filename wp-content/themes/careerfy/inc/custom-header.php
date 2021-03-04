<?php
/**
 * Sample implementation of the Custom Header feature.
 *
 * @package Careerfy
 */

function careerfy_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'careerfy_custom_header_args', array(
		'default-image'          => '',
		'default-text-color'     => '000000',
		'width'                  => 1000,
		'height'                 => 250,
		'flex-height'            => true,
		'wp-head-callback'       => '',
	) ) );
        add_theme_support( 'custom-logo', array(
                'height'      => 39,
                'width'       => 177,
                'flex-height' => true,
                'flex-width'  => true,
                'header-text' => array( 'site-title', 'site-description' ),
        ) );
}
add_action( 'after_setup_theme', 'careerfy_custom_header_setup' );

add_action( 'customize_save_after', 'careerfy_customizer_save_after' );

function careerfy_customizer_save_after() {
    //var_dump($_POST);
}

class careerfy_mobile_menu_walker extends Walker_Nav_Menu {

    private $CurrentItem, $CategoryMenu, $menu_style;
    public $parent_menu_item_id = 0;
    public $child_items_count = 0;
    public $child_menu_item_id = 0;
    public $view = '';

    function __Construct($view = '') {
        $this->view = $view;
    }

    // Start function For Mega menu level
    function start_lvl(&$output, $depth = 0, $args = array(), $id = 0) {
        $indent = str_repeat("\t", $depth);

        $columns_class = $this->CurrentItem->columns;

        $careerfy_parent_id = $this->CurrentItem->menu_item_parent;
        
        $output .= "\n$indent<ul class=\"sidebar-submenu\">\n";
    }

    // Start function For Mega menu level end 

    function end_lvl(&$output, $depth = 0, $args = array()) {

        $careerfy_parent_id = $this->CurrentItem->menu_item_parent;

        $indent = str_repeat("\t", $depth);

        $output .= $indent . "</ul>\n";
        $item_id = $this->parent_menu_item_id . $this->child_menu_item_id;
    }

    // Start function For Mega menu items

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {

        global $wp_query;
        $this->CurrentItem = $item;

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
        }

        if (empty($args)) {
            $args = new stdClass();
        }

        $indent = ( $depth ) ? str_repeat("\t", $depth) : '';
        if ($depth == 0) {
            $class_names = $value = '';
            $mega_menu = '';
        } else if ($args->has_children) {
            $class_names = $value = '';
            $mega_menu = '';
        } else {
            $class_names = $value = '';
        }
        $classes = empty($item->classes) ? array() : (array) $item->classes;

        $class_names = join(" ", apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $class_names = ' class="' . esc_attr($class_names) . '"';
        
        $output .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';
        
        $attributes = isset($item->attr_title) && $item->attr_title != '' ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= isset($item->target) && $item->target != '' ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= isset($item->xfn) && $item->xfn != '' ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= isset($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        
        $item_output = isset($args->before) ? $args->before : '';

        $item_output .= '<a' . $attributes . '>';
        
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



        $item_output .= '</a>';
        
        if ($args->has_children) {
            $item_output .= '<span class="child-navitms-opner"><i class="fa fa-angle-down"></i></span>';
        }

        $item_output .= isset($args->after) ? $args->after : '';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args, $id);
        
        return $output;
    }

    // Start function For Mega menu display elements

    function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
        $id_field = $this->db_fields['id'];
        if (is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        }
        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

}