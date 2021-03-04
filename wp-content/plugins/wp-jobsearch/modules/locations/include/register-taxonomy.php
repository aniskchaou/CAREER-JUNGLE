<?php
/*
  Class : Location
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Taxonomy_Location {

    // hook things up
    public function __construct() {
        add_action('init', array($this, 'jobsearch_job_location'), 0);
        add_action('admin_menu', array($this, 'jobsearch_remove_cus_meta_boxes'));
        add_action('do_meta_boxes', array($this, 'jobsearch_remove_cus_meta_boxes')); 
        add_action('create_job-location', array($this, 'save_locations_fields_added_callback'));
        add_action('edited_job-location', array($this, 'save_locations_fields_updated_callback'));
        add_action('job-location_edit_form_fields', array($this, 'edit_locations_fields_callback'));
        add_action('job-location_add_form_fields', array($this, 'locations_fields_callback'));
    }

    public function jobsearch_job_location() {
        // Add new taxonomy, make it hierarchical (like locations)
        $labels = array(
            'name' => _x('Locations', 'taxonomy general name', 'wp-jobsearch'),
            'singular_name' => _x('Location', 'taxonomy singular name', 'wp-jobsearch'),
            'search_items' => __('Search Locations', 'wp-jobsearch'),
            'all_items' => __('All Locations', 'wp-jobsearch'),
            'parent_item' => __('Parent Location', 'wp-jobsearch'),
            'parent_item_colon' => __('Parent Location:', 'wp-jobsearch'),
            'edit_item' => __('Edit Location', 'wp-jobsearch'),
            'update_item' => __('Update Location', 'wp-jobsearch'),
            'add_new_item' => __('Add New Location', 'wp-jobsearch'),
            'new_item_name' => __('New Location Name', 'wp-jobsearch'),
            'menu_name' => __('Location', 'wp-jobsearch'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'job-location'),
        );

        register_taxonomy('job-location', array('job'), $args);
    }

    function jobsearch_remove_cus_meta_boxes() {
        remove_meta_box('job-locationdiv', 'job', 'side');
    }

    /**
     * how to save location in fields
     */
    public function save_locations_fields_added_callback($term_id) {
        if (isset($_POST['jobsearch_field_locations_image_meta']) && $_POST['jobsearch_field_locations_image_meta'] == '1') {
            if (isset($_POST['jobsearch_field_iso_code'])) {
                $iso_code = $_POST['jobsearch_field_iso_code'];
                add_term_meta($term_id, 'jobsearch_field_iso_code', $iso_code, true);
            }
            if (isset($_POST['jobsearch_field_location_img_field'])) {
                $location_img_field = $_POST['jobsearch_field_location_img_field'];
                add_term_meta($term_id, 'jobsearch_field_location_img_field', $location_img_field, true);
            }
        }
    }

    /**
     * how to save location in fields
     */
    public function save_locations_fields_updated_callback($term_id) {
        if (isset($_POST['jobsearch_field_locations_image_meta']) and $_POST['jobsearch_field_locations_image_meta'] == '1') {
            if (isset($_POST['jobsearch_field_iso_code'])) {
                $iso_code = $_POST['jobsearch_field_iso_code'];
                update_term_meta($term_id, 'jobsearch_field_iso_code', $iso_code);
            }

            if (isset($_POST['jobsearch_field_location_img_field'])) {
                $location_img_field = $_POST['jobsearch_field_location_img_field'];
                update_term_meta($term_id, 'jobsearch_field_location_img_field', $location_img_field);
            }
        }
    }

    /**
     * Add ISO Code field.
     *
     * @global type $jobsearch_form_fields
     * @param type $tag
     */
    public function edit_locations_fields_callback($tag) { //check for existing featured ID
        global $jobsearch_form_fields;
        $iso_code = "";
        wp_enqueue_media();
        $location_coordinates = "";
        $location_url = '';
        if (isset($tag->term_id)) {
            $term_id = $tag->term_id;

            $iso_code = get_term_meta($term_id, 'jobsearch_field_iso_code', true);
            $location_url = get_term_meta($term_id, 'jobsearch_field_location_img_field', true);
        } 
        $opt_array = array(
            'id' => 'locations_image_meta',
            'force_std' => "1",
            'name' => "locations_image_meta",
            'return' => false,
        );
        $jobsearch_form_fields->input_hidden_field($opt_array);
        ?>
        <tr>
            <th><label for="cat_f_img_url"> <?php echo esc_html__('ISO Code', 'wp-jobsearch'); ?></label></th>
            <td>
                <?php
                $opt_array = array(
                    'name' => "iso_code",
                    'force_std' => esc_attr($iso_code),
                    'return' => false,
                );
                $jobsearch_form_fields->input_field($opt_array);
                ?>
            </td>
        </tr>
        <tr>
            <th><label for="cat_f_img_url"><?php echo esc_html__('Location Image', 'wp-jobsearch'); ?></label></th>
            <td class="location-img-field">
                <?php 
                $field_params = array(
                    'id' => rand(100000, 999999),
                    'name' => 'location_img_field',
                    'force_std' => esc_url($location_url),
                );
                $jobsearch_form_fields->image_upload_field($field_params);
                ?>
            </td>
        </tr>


        <?php
    }

    /**
     * Add Category Fields.
     *
     * @global type $jobsearch_form_fields
     * @param type $tag
     */
    public function locations_fields_callback($tag) { //check for existing featured ID
        global $jobsearch_form_fields;
        wp_enqueue_media();
        if (isset($tag->term_id)) {
            $t_id = $tag->term_id;
        } else {
            $t_id = '';
        }
        $locations_image = '';
        $iso_code = '';
        ?>
        <div class="form-field">

            <label><?php echo esc_html__('ISO Code', 'wp-jobsearch'); ?></label>
            <ul class="form-elements" style="margin:0; padding:0;">
                <li class="to-field" style="width:100%;">
                    <?php
                    $opt_array = array(
                        'std' => "",
                        'name' => "iso_code",
                        'return' => false,
                    );
                    $jobsearch_form_fields->input_field($opt_array);
                    ?>
                </li>
            </ul>
            <br> <br>
        </div>
        <div class="form-field location-img-field">
            <label><?php echo esc_html__('Location image', 'wp-jobsearch'); ?></label>
            <ul class="form-elements" style="margin:0; padding:0;">
                <li class="to-field" style="width:100%;">
                    <?php 
                    $field_params = array(
                        'id' => rand(100000, 999999),
                        'name' => 'location_img_field',
                        'force_std' => '',
                    );
                    $jobsearch_form_fields->image_upload_field($field_params);
                    ?>
                </li>
            </ul> 
        </div> 
        <?php
        $opt_array = array(
            'id' => 'locations_image_meta',
            'force_std' => "1",
            'name' => "locations_image_meta",
            'return' => false,
        );
        $jobsearch_form_fields->input_hidden_field($opt_array);
    }

}

// class Jobsearch_Taxonomy_Location 
$Jobsearch_Taxonomy_Location_obj = new Jobsearch_Taxonomy_Location();
