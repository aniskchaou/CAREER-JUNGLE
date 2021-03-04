<?php
/**
 * Directory Plus ShortlistLoads Module
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Jobsearch_ShortlistLoad')) {

    class Jobsearch_ShortlistLoad {

        public $admin_notices;
 
        
    }

    global $jobsearch_shortlist_load;
    $jobsearch_shortlist_load = new Jobsearch_ShortlistLoad();
}