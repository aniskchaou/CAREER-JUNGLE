<?php
if (!defined('ABSPATH')) {
    die;
}

global $jobsearch_gdapi_allocation;
if (!class_exists('jobsearch_import_data_handle')) {

    class jobsearch_import_data_handle
    {
        public $auto_load_files;
        private $countries;
        private $states;
        private $query;

        // hook things up
        public function __construct()
        {
            $this->auto_load_files = false;
            add_action('init', array($this, 'jobsearch_location_tables'), 1);
            add_action('init', array($this, 'load_locfiles_init'), 1);
            add_action('init', array($this, 'update_country_id_cities'), 1);
            //
            add_action('wp_ajax_update_country_id_cities', array($this, 'update_country_id_cities_callback'), 1);
            add_action('wp_ajax_nopriv_update_country_id_cities', array($this, 'update_country_id_cities_callback'), 1);
            //add_action('admin_init', array($this, 'jobsearch_import_datatables'), 10);
            add_action('wp_ajax_jobsearch_locations_import_countries', array($this, 'jobsearch_locations_import_countries_callback'));
            add_action('wp_ajax_nopriv_jobsearch_locations_import_countries', array($this, 'jobsearch_locations_import_countries_callback'));
            //
            add_action('wp_ajax_jobsearch_locations_import_states', array($this, 'jobsearch_locations_import_states_callback'));
            add_action('wp_ajax_nopriv_jobsearch_locations_import_states', array($this, 'jobsearch_locations_import_states_callback'));
            //
            add_action('wp_ajax_jobsearch_locations_import_cities', array($this, 'jobsearch_locations_import_cities_callback'));
            add_action('wp_ajax_nopriv_jobsearch_locations_import_cities', array($this, 'jobsearch_locations_import_cities_callback'));
        }

        public function load_locfiles_init()
        {
            if (defined('WP_JOBSEARCH_VERSION')) {
                $jobsearch_version = WP_JOBSEARCH_VERSION;
                if ($jobsearch_version <= '1.3.5') {
                    $this->auto_load_files = true;
                }
            }
        }

        public function update_country_id_cities()
        {
            global $wpdb;
            $flag = false;
            $current_url = isset($_GET['page']) ? $_GET['page'] : '';
            if ($current_url == 'jobsearch-location-sett-editor' || $current_url == 'jobsearch-location-sett') {
                $flag = true;
            }
            if ($flag == true) {
                $update_param = isset($_GET['update']) ? $_GET['update'] : '';
                if (!empty($update_param)) {
                    $this->states = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` ");
                    foreach ($this->states as $states_info) {
                        $this->query = "update `{$wpdb->base_prefix}jobsearch_cities` set cntry_id = '" . $states_info->cntry_id . "' where state_id = '" . $states_info->state_id . "' ";
                        $wpdb->query($this->query);
                    }
                    echo "data updated";
                }
            }
        }

        public function update_country_id_cities_callback()
        {
            global $wpdb;
            $cntry_id = isset($_POST['cntry_id']) && $_POST['cntry_id'] != 0 ? $_POST['cntry_id'] : '';
            $this->countries = $this->get_all_countries_detail($cntry_id);

            foreach ($this->countries as $cntry_info) {
                $this->countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` where cntry_id = '" . $cntry_info->cntry_id . "' ");
                foreach ($this->countries as $state_info) {
                    $this->query = "update `{$wpdb->base_prefix}jobsearch_cities` set cntry_id = '" . $state_info->cntry_id . "' where state_id = '" . $state_info->state_id . "' ";
                    $wpdb->query($this->query);
                }
            }

            $x = $cntry_id;
            $y = 220;

            $percent = $x / $y;
            $totl_percentage = round($percent * 100);

            echo json_encode(array('status' => 'data_updated', 'cntry_id' => $cntry_id, 'data_percent' => $totl_percentage));
            wp_die();
        }

        public function update_country_id_cities_callback_old()
        {
            global $wpdb;
            $this->states = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` ");
            foreach ($this->states as $states_info) {
                $this->query = "update `{$wpdb->base_prefix}jobsearch_cities` set cntry_id = '" . $states_info->cntry_id . "' where state_id = '" . $states_info->state_id . "' ";
                $wpdb->query($this->query);
            }
        }

        public function jobsearch_locations_import_countries_callback()
        {
            global $wpdb;
            $this->countries = read_location_file('countries.json');
            $this->countries = json_decode($this->countries);
            $this->query = "TRUNCATE TABLE `{$wpdb->base_prefix}jobsearch_countries` ";
            $wpdb->query($this->query);

            foreach ($this->countries as $cntries_info) {
                $country = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` WHERE (name = '" . $cntries_info->name . "' AND code = '" . $cntries_info->code . "')");
                if (count($country) == 0) {
                    $wpdb->insert($wpdb->base_prefix . 'jobsearch_countries', array(
                        'name' => $cntries_info->name,
                        'code' => $cntries_info->code,
                    ));
                }
            }
            echo json_encode(array('status' => 'countries_added'));
            wp_die();
        }

        public function jobsearch_locations_import_states_callback()
        {
            global $wpdb;
            $this->query = "TRUNCATE TABLE `{$wpdb->base_prefix}jobsearch_states` ";
            $wpdb->query($this->query);
            $this->countries = $this->get_all_countries_detail();
            foreach ($this->countries as $cntry_info) {
                $states_file = read_location_file('countries/' . $cntry_info->code . '/' . $cntry_info->code . '-states.json');
                $states_file = json_decode($states_file);
                foreach ($states_file->result as $states_info) {
                    $state_name = preg_replace("/[^\p{L}\p{N}.-]/u", " ", $states_info);
                    $wpdb->insert($wpdb->base_prefix . 'jobsearch_states', array(
                        'state_name' => $state_name,
                        'cntry_id' => $cntry_info->cntry_id,
                    ));
                }
            }
            echo json_encode(array('status' => 'states_added'));
            wp_die();
        }

        public function jobsearch_locations_import_cities_callback()
        {
            global $wpdb;
            $cntry_id = isset($_POST['cntry_id']) && $_POST['cntry_id'] != 0 ? $_POST['cntry_id'] : '';
            $this->countries = $this->get_all_countries_detail($cntry_id);

            foreach ($this->countries as $cntry_info) {
                $this->countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` where cntry_id = '" . $cntry_info->cntry_id . "' ");
                foreach ($this->countries as $state_info) {
                    $cities = read_cities_file('countries/' . $cntry_info->code . '/' . $state_info->state_name . '/' . $cntry_info->code . '-' . $state_info->state_name . '-cities.json');
                    $cities = json_decode($cities, true);
                    foreach ($cities['result'] as $city_name) {
                        $city = preg_replace("/[^a-zA-Z]/", " ", $city_name);
                        $wpdb->insert($wpdb->base_prefix . 'jobsearch_cities', array(
                            'city_name' => $city,
                            'state_id' => $state_info->state_id,
                            'cntry_id' => $cntry_info->cntry_id,
                        ));
                    }
                }
            }

            $x = $cntry_id;
            $y = 220;

            $percent = $x / $y;
            $totl_percentage = round($percent * 100);

            echo json_encode(array('status' => 'cities_added', 'cntry_id' => $cntry_id, 'data_percent' => $totl_percentage));
            wp_die();
        }

        public function jobsearch_locations_import_cities_callback_old()
        {
            global $wpdb;
            $this->countries = $this->get_all_countries_detail();
            $this->query = "TRUNCATE TABLE `{$wpdb->base_prefix}jobsearch_cities` ";
            $wpdb->query($this->query);
            foreach ($this->countries as $cntry_info) {
                $this->countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` where cntry_id = '" . $cntry_info->cntry_id . "' ");
                foreach ($this->countries as $state_info) {
                    $cities = read_cities_file('countries/' . $cntry_info->code . '/' . $state_info->state_name . '/' . $cntry_info->code . '-' . $state_info->state_name . '-cities.json');
                    $cities = json_decode($cities, true);
                    foreach ($cities['result'] as $city_name) {
                        $city = preg_replace("/[^a-zA-Z]/", " ", $city_name);
                        $wpdb->insert($wpdb->base_prefix . 'jobsearch_cities', array(
                            'city_name' => $city,
                            'state_id' => $state_info->state_id,
                            'cntry_id' => $cntry_info->cntry_id,
                        ));
                    }
                }
            }
            echo json_encode(array('status' => 'cities_added'));
            wp_die();
        }

        public function get_all_countries_detail($cntry_id = '')
        {
            global $wpdb;
            $this->query = "SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries`";
            if (!empty($cntry_id)) {
                $this->query .= " where cntry_id = '" . $cntry_id . "' ";
            }
            $this->countries = $wpdb->get_results($this->query);
            return $this->countries;
        }

        public function get_all_cities_detail()
        {
            global $wpdb;
            $this->query = "SELECT cntry_id FROM `{$wpdb->base_prefix}jobsearch_cities` where cntry_id != 0";
            $this->countries = $wpdb->get_results($this->query);
            return count($this->countries) > 0 ? 'data_is_updated' : '';
        }

        public static function jobsearch_countries_check()
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . 'jobsearch_countries';
            $countries = array();
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
                //$countries = $this->get_all_countries_detail();
            }
            return count($countries);
        }

        public function jobsearch_import_datatables()
        {
            global $wpdb, $jobsearch_download_locations;
            $jobsearch_download_locations = true;

            add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $wp_upload_dir = wp_upload_dir();
            $upload_file_path = $wp_upload_dir['path'];

            if (!file_exists($upload_file_path . "/countries")) {
                $upload_file_path = $wp_upload_dir['basedir'] . '/jobsearch-locations';
            }

            remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $jobsearch_download_locations = false;

            if (file_exists($upload_file_path . "/countries") && (self::jobsearch_countries_check() == 0)) {
                $output = '';
                $count = 0;
                $file_data = file($upload_file_path . '/countries_data.sql');
                foreach ($file_data as $row) {
                    $start_character = substr(trim($row), 0, 2);
                    if ($start_character != '--' || $start_character != '/*' || $start_character != '//' || $row != '') {
                        $output = $output . $row;
                        $end_character = substr(trim($row), -1, 1);
                        if ($end_character == ';') {
                            if (!$wpdb->query($output)) {
                                $count++;
                            }
                            $output = '';
                        }
                    }
                }
            }
        }

        public function jobsearch_location_tables()
        {
            global $wpdb;
            $flag = false;
            $current_url = isset($_GET['page']) ? $_GET['page'] : '';
            if ($current_url == 'jobsearch-location-sett-editor' || $current_url == 'jobsearch-location-sett') {
                $flag = true;
            }

            if ($flag == false) {
                return;
            }

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $charset_collate = $wpdb->get_charset_collate();
            $sql_1 = "CREATE TABLE `{$wpdb->base_prefix}jobsearch_countries` (
             `cntry_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` LONGTEXT  NOT NULL,
              `code` LONGTEXT  NOT NULL,
              PRIMARY KEY (`cntry_id`)
              ) $charset_collate;";
            dbDelta($sql_1);
            //
            $sql_2 = "CREATE TABLE `{$wpdb->base_prefix}jobsearch_states` (
                `state_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `state_name` LONGTEXT NOT NULL,
                `cntry_id` INT(11) NOT NULL,
                 PRIMARY KEY (`state_id`)
                ) $charset_collate;";
            dbDelta($sql_2);
            //
            $sql_3 = "CREATE TABLE `{$wpdb->base_prefix}jobsearch_cities` (
                `city_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `state_id` INT(11) NOT NULL,
                `cntry_id` INT(11) NOT NULL,
                `city_name` LONGTEXT NOT NULL,
                 PRIMARY KEY (`city_id`)
                ) $charset_collate;";
            dbDelta($sql_3);
        }
    }
}
global $jobsearch_import_data_handle;
$jobsearch_import_data_handle = new jobsearch_import_data_handle();