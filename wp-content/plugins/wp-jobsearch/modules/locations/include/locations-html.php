<?php
/*
  Class : LocationHTML
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_LocationHTML {

// hook things up
    public function __construct() {
        add_action('jobsearch_location_search_field_html', array($this, 'jobsearch_location_search_field_html_callback'), 1);
    }

    public function jobsearch_location_search_field_html_callback() {
       
    }


}

// class Jobsearch_LocationHTML 
$Jobsearch_LocationHTML_obj = new Jobsearch_LocationHTML();
global $Jobsearch_LocationHTML_obj;
