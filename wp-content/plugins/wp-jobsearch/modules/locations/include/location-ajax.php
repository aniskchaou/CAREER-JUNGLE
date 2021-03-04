<?php
if (!defined('ABSPATH')) {
    die;
}

global $jobsearch_gdapi_allocation;
if (!class_exists('jobsearch_location_ajax')) {

    class jobsearch_location_ajax
    {
        public $query;

        public function __construct()
        {
            add_action('wp_ajax_jobsearch_location_load_countries_data', array($this, 'jobsearch_location_load_countries_data_callback'));
            add_action('wp_ajax_nopriv_jobsearch_location_load_countries_data', array($this, 'jobsearch_location_load_countries_data_callback'));
            //
            add_action('wp_ajax_jobsearch_location_load_states_data', array($this, 'jobsearch_location_load_states_data_callback'));
            add_action('wp_ajax_nopriv_jobsearch_location_load_states_data', array($this, 'jobsearch_location_load_states_data_callback'));
            //
            add_action('wp_ajax_jobsearch_location_load_cities_data', array($this, 'jobsearch_location_load_cities_data_callback'));
            add_action('wp_ajax_nopriv_jobsearch_location_load_cities_data', array($this, 'jobsearch_location_load_cities_data_callback'));
            //
            add_action('wp_ajax_jobsearch_load_single_country_data', array($this, 'jobsearch_load_single_country_data_callback'));
            add_action('wp_ajax_nopriv_jobsearch_load_single_country_data', array($this, 'jobsearch_load_single_country_data_callback'));
            //
            add_action('wp_ajax_jobsearch_load_single_state_data', array($this, 'jobsearch_load_single_state_data_callback'));
            add_action('wp_ajax_nopriv_jobsearch_load_single_state_data', array($this, 'jobsearch_load_single_state_data_callback'));
            //
            add_action('wp_ajax_jobsearch_load_single_state_cities_data', array($this, 'jobsearch_load_single_state_cities_data_callback'));
            add_action('wp_ajax_nopriv_jobsearch_load_single_state_cities_data', array($this, 'jobsearch_load_single_state_cities_data_callback'));
            //
            add_action('wp_ajax_jobsearch_update_country', array($this, 'jobsearch_update_country_callback'));
            //
            add_action('wp_ajax_jobsearch_add_new_states', array($this, 'jobsearch_add_new_states_callback'));
            //
            add_action('wp_ajax_jobsearch_delete_country', array($this, 'jobsearch_delete_country_callback'));
            //
            add_action('wp_ajax_jobsearch_delete_state', array($this, 'jobsearch_delete_state_callback'));
            //
            add_action('wp_ajax_jobsearch_delete_city', array($this, 'jobsearch_delete_city_callback'));
            //
            add_action('wp_ajax_jobsearch_add_new_cities', array($this, 'jobsearch_add_new_cities_callback'));
            //
            add_action('wp_ajax_jobsearch_delete_states', array($this, 'jobsearch_delete_states_callback'));
            //
            add_action('wp_ajax_jobsearch_delete_cities', array($this, 'jobsearch_delete_cities_callback'));
            //
            add_action('wp_ajax_jobsearch_locations_reset_data', array($this, 'jobsearch_locations_reset_data_callback'));
            //
            add_action('wp_ajax_jobsearch_locations_get_country_states', array($this, 'jobsearch_locations_get_country_states_callback'));
            add_action('wp_ajax_nopriv_jobsearch_locations_get_country_states', array($this, 'jobsearch_locations_get_country_states_callback'));
            //
            add_action('wp_ajax_jobsearch_get_selected_states_cities', array($this, 'jobsearch_get_selected_states_cities_callback'));
            add_action('wp_ajax_nopriv_jobsearch_get_selected_states_cities', array($this, 'jobsearch_get_selected_states_cities_callback'));
            //
            add_action('wp_ajax_jobsearch_location_load_excel_file', array($this, 'jobsearch_location_load_excel_file_callback'));
            add_action('wp_ajax_nopriv_jobsearch_location_load_excel_file', array($this, 'jobsearch_location_load_excel_file_callback'));
        }

        public function getExistingLocationDetail($param_1, $param_2, $loc_type)
        {
            global $wpdb;
            switch ($loc_type) {
                case "country":
                    $this->query = "SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` WHERE name = '" . preg_replace("/[^a-zA-Z]/", " ", $param_1) . "' and code = '" . $param_2 . "'";
                    return $wpdb->get_results($this->query);
                    break;
                case "state":
                    $this->query = "SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` WHERE state_name = '" . preg_replace("/[^a-zA-Z]/", " ", $param_1) . "' and cntry_id = '" . $param_2 . "'";
                    return $wpdb->get_results($this->query);
                    break;
                case "city":
                    $this->query = "SELECT * FROM `{$wpdb->base_prefix}jobsearch_cities` WHERE city_name = '" . preg_replace("/[^a-zA-Z]/", " ", $param_1) . "' and state_id = '" . $param_2 . "'";
                    return $wpdb->get_results($this->query);
                    break;
            }
        }

        public function jobsearch_location_load_excel_file_callback()
        {
            global $wpdb, $jobsearch_download_locations;
            $jobsearch_download_locations = true;

            add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $wp_upload_dir = wp_upload_dir();
            $location = $wp_upload_dir['basedir'] . '/jobsearch-locations';
            remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $jobsearch_download_locations = false;

            $uploadedFileTemp = '';
            $uploadedFileName = '';
            $file_ext = '';
            if (isset($_FILES['excel_file'])) {
                $uploadedFileTemp = $_FILES['excel_file']['tmp_name'];
                $uploadedFileName = $_FILES['excel_file']['name'];
                $file_ext = explode('.', $uploadedFileName);
                $file_ext = isset($file_ext[1]) ? $file_ext[1] : '';
                move_uploaded_file($uploadedFileTemp, $location . $uploadedFileName);
            }

            if ($file_ext == 'xlsx') {
                if ($xlsx = SimpleXLSX::parse($location . $uploadedFileName)) {
                    $excel_file_data = $xlsx->rows();

                    foreach ($excel_file_data as $key => $info) {

                        $country_name = $info[2];
                        $country_code = $info[3];
                        $state_name = $info[1];
                        $city_name = $info[0];
                        if ($key == 0) {
                            continue;
                        }
                        $countries = $this->getExistingLocationDetail($country_name, $country_code, 'country');

                        if (count($countries) == 0 && !empty($country_name) && !empty($country_code)) {
                            $wpdb->insert($wpdb->base_prefix . 'jobsearch_countries', array(
                                'name' => $country_name,
                                'code' => $country_code,
                            ));
                        }

                        $country_id = $this->get_country_id_by_code($country_code);
                        $states = $this->getExistingLocationDetail($state_name, $country_id, 'state');
                        if (count($states) == 0 && !empty($state_name)) {
                            $wpdb->insert($wpdb->base_prefix . 'jobsearch_states', array(
                                'state_name' => $state_name,
                                'cntry_id' => $country_id,
                            ));
                        }

                        $state_id = $this->get_state_id_by_name($state_name, $country_id);
                        $cities = $this->getExistingLocationDetail($city_name, $state_id, 'city');
                        if (count($cities) == 0 && !empty($city_name) && !empty($state_id)) {
                            $wpdb->insert($wpdb->base_prefix . 'jobsearch_cities', array(
                                'city_name' => $city_name,
                                'state_id' => $state_id,
                                'cntry_id' => $country_id,
                            ));
                        }
                    }
                    unlink($location . $uploadedFileName);
                } else {
                    echo SimpleXLSX::parseError();
                }
            } else {
                $row = 1;
                $csv_data_array = [];
                if (($handle = fopen($location . $uploadedFileName, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $row++;
                        $csv_data_array[] = $data;
                    }
                    fclose($handle);

                    foreach (array_slice($csv_data_array, 1) as $key => $info) {

                        $country_name = $info[2];
                        $country_code = $info[3];
                        $state_name = $info[1];
                        $city_name = $info[0];

                        $countries = $this->getExistingLocationDetail($country_name, $country_code, 'country');
                        if (count($countries) == 0 && !empty($country_name) && !empty($country_code)) {
                            $wpdb->insert($wpdb->base_prefix . 'jobsearch_countries', array(
                                'name' => $country_name,
                                'code' => $country_code,
                            ));
                        }

                        $country_id = $this->get_country_id_by_code($country_code);

                        $states = $this->getExistingLocationDetail($state_name, $country_id, 'state');
                        if (count($states) == 0 && !empty($state_name)) {
                            $wpdb->insert($wpdb->base_prefix . 'jobsearch_states', array(
                                'state_name' => $state_name,
                                'cntry_id' => $country_id,
                            ));
                        }

                        $state_id = $this->get_state_id_by_name($state_name, $country_id);
                        $cities = $this->getExistingLocationDetail($city_name, $state_id, 'city');

                        if (count($cities) == 0 && !empty($city_name) && !empty($state_id)) {
                            $wpdb->insert($wpdb->base_prefix . 'jobsearch_cities', array(
                                'city_name' => $city_name,
                                'state_id' => $state_id,
                                'cntry_id' => $country_id,
                            ));
                        }
                    }
                    unlink($location . $uploadedFileName);
                }
            }
            echo json_encode(array('status' => 'data_downloaded'));
            wp_die();
        }

        public function jobsearch_get_selected_states_cities_callback()
        {
            global $wpdb;

            $preselected_states = isset($_POST['preselected_states']) ? $_POST['preselected_states'] : '';
            $all_cities = [];
            foreach ($preselected_states as $state_id) {
                $cities = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_cities` WHERE state_id = '" . $state_id . "' ");
                foreach ($cities as $city_info) {
                    $all_cities[] = $city_info->city_name;
                }
            }
            echo json_encode(array('all_cities' => $all_cities));
            wp_die();
        }

        public function jobsearch_locations_get_country_states_callback()
        {
            global $wpdb;
            $rand_id = rand(999, 1000);
            $country_id = $this->get_country_id_by_code($_POST['country_code']);
            $states = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` WHERE cntry_id = '" . $country_id . "' ");
            ob_start(); ?>
            <label for="contry-states-<?php echo($rand_id) ?>"><?php esc_html_e('Select States', 'wp-jobsearch') ?></label>
            <select id="contry-states-<?php echo($rand_id) ?>" multiple="multiple" name="states_filtrs_by_cntry[]">
                <?php foreach ($states as $states_info) { ?>
                    <option value="<?php echo($states_info->state_id) ?>"><?php echo($states_info->state_name) ?></option>
                <?php } ?>
            </select>
            <?php
            $html = ob_get_clean();
            echo json_encode(array('status' => $html));
            wp_die();
        }

        public function jobsearch_locations_reset_data_callback()
        {
            global $jobsearch_download_locations, $wpdb;
            $jobsearch_download_locations = true;
            add_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $wp_upload_dir = wp_upload_dir();
            //
            $upload_file_path = $wp_upload_dir['path'];
            if (!file_exists($upload_file_path . "/countries")) {
                $upload_file_path = $wp_upload_dir['basedir'] . '/jobsearch-locations';
            }
            //
            remove_filter('upload_dir', 'jobsearch_locations_upload_dir', 10, 1);
            $jobsearch_download_locations = false;
            //
            $this->query = "TRUNCATE TABLE `{$wpdb->base_prefix}jobsearch_countries` ";
            $wpdb->query($this->query);
            //
            $this->query = "TRUNCATE TABLE `{$wpdb->base_prefix}jobsearch_states` ";
            $wpdb->query($this->query);
            //
            $this->query = "TRUNCATE TABLE `{$wpdb->base_prefix}jobsearch_cities` ";
            $wpdb->query($this->query);
            //
            self::delete_directory($upload_file_path);
            echo json_encode(array('status' => 'folder_deleted'));
            wp_die();
        }

        public function jobsearch_delete_cities_callback()
        {
            global $wpdb;
            $totl_city_ids = isset($_POST['jobsearch_city_ids']) ? $_POST['jobsearch_city_ids'] : '';
            foreach ($totl_city_ids as $city_id) {
                //
                $this->query = "delete from `{$wpdb->base_prefix}jobsearch_cities` where city_id = '" . $city_id . "' ";
                $wpdb->query($this->query);
            }

            echo json_encode(array('status' => 'data_deleted'));
            wp_die();
        }

        public function jobsearch_delete_states_callback()
        {
            global $wpdb;
            $totl_state_ids = isset($_POST['jobsearch_state_ids']) ? $_POST['jobsearch_state_ids'] : '';
            foreach ($totl_state_ids as $state_id) {

                $this->query = "DELETE FROM `{$wpdb->base_prefix}jobsearch_states` WHERE state_id = '" . $state_id . "' ";
                $wpdb->query($this->query);
                //
                $this->query = "delete from `{$wpdb->base_prefix}jobsearch_cities` where state_id = '" . $state_id . "' ";
                $wpdb->query($this->query);
            }

            echo json_encode(array('status' => 'data_deleted'));
            wp_die();
        }

        public function jobsearch_add_new_cities_callback()
        {
            global $wpdb;
            $cities_list = $_POST['cities_list'];
            $state_id = $_POST['state_id'];
            $country_id = $_POST['country_id'];

            foreach ($cities_list as $cities_info) {
                $city_name = $cities_info['city_name'];
                if ($cities_info['city_id'] == '') {
                    $this->query = "SELECT * FROM `{$wpdb->base_prefix}jobsearch_cities` where city_name = '" . $city_name . "' ";
                    $cities = $wpdb->get_results($this->query);
                    if (count($cities) == 0) {
                        $wpdb->insert($wpdb->base_prefix . 'jobsearch_cities', array(
                            'city_name' => $city_name,
                            'state_id' => $state_id,
                            'cntry_id' => $country_id,
                        ));
                    }
                } else {
                    $this->query = "update `{$wpdb->base_prefix}jobsearch_cities` set city_name = '" . $city_name . "' where city_id = '" . $cities_info['city_id'] . "' ";
                    $wpdb->query($this->query);
                }
            }
            echo json_encode(array('status' => 'data_updated'));
            wp_die();
        }

        public function jobsearch_delete_country_callback()
        {
            global $wpdb;
            $this->query = "delete from `{$wpdb->base_prefix}jobsearch_countries` where cntry_id = '" . $_POST['cntry_id'] . "' ";
            $wpdb->query($this->query);
            //
            $this->query = "delete from `{$wpdb->base_prefix}jobsearch_states` where cntry_id = '" . $_POST['cntry_id'] . "' ";
            $wpdb->query($this->query);
            //
            $this->query = "delete from `{$wpdb->base_prefix}jobsearch_cities` where cntry_id = '" . $_POST['cntry_id'] . "' ";
            $wpdb->query($this->query);
        }

        public function jobsearch_delete_state_callback()
        {
            global $wpdb;
            $this->query = "delete from `{$wpdb->base_prefix}jobsearch_states` where state_id = '" . $_POST['state_id'] . "' ";
            $wpdb->query($this->query);
            //
            $this->query = "delete from `{$wpdb->base_prefix}jobsearch_cities` where state_id = '" . $_POST['state_id'] . "' ";
            $wpdb->query($this->query);
        }

        public function jobsearch_delete_city_callback()
        {
            global $wpdb;
            $this->query = "delete from `{$wpdb->base_prefix}jobsearch_cities` where city_id = '" . $_POST['city_id'] . "' ";
            $wpdb->query($this->query);
        }

        public function delete_dummy_state($country_id)
        {
            global $wpdb;
            $states = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` where cntry_id = '" . $country_id . "' ");
            if (count($states) > 0) {
                $state_text = esc_html__('Enter Any State', 'wp-jobsearch');
                if ($states[0]->state_name == $state_text) {
                    $this->query = "DELETE FROM `{$wpdb->base_prefix}jobsearch_states` WHERE state_name = '" . $state_text . "' and cntry_id = '" . $country_id . "' ";
                    $wpdb->query($this->query);
                }
            }
        }

        public function jobsearch_add_new_states_callback()
        {
            global $wpdb;
            $states_list = $_POST['states_list'];
            $country_id = $_POST['country_id'];
            //
            $this->delete_dummy_state($country_id);
            foreach ($states_list as $states_info) {
                if ($states_info['state_id'] == '') {
                    $state_name = $states_info['state_name'];
                    $states = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` where state_name = '" . $state_name . "' ");
                    if (count($states) == 0) {
                        $wpdb->insert($wpdb->base_prefix . 'jobsearch_states', array(
                            'state_name' => $states_info['state_name'],
                            'cntry_id' => $country_id,
                        ));

                        $state_id = $wpdb->insert_id;
                        $wpdb->insert($wpdb->base_prefix . 'jobsearch_cities', array(
                            'city_name' => __('Enter Any City', 'wp-jobsearch'),
                            'state_id' => $state_id,
                            'cntry_id' => $country_id,
                        ));
                    }
                } else {
                    $this->query = "update `{$wpdb->base_prefix}jobsearch_states` set state_name = '" . $states_info['state_name'] . "' where state_id = '" . $states_info['state_id'] . "' ";
                    $wpdb->query($this->query);
                }
            }
            echo json_encode(array('status' => 'data_updated'));
            wp_die();
        }

        public function jobsearch_update_Country_Callback()
        {
            global $wpdb;
            $countries_list = $_POST['countries_list'];
            foreach ($countries_list as $contries_info) {
                $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` where name = '" . $contries_info['name'] . "' || code = '" . $contries_info['code'] . "' ");
                if (count($countries) === 0) {
                    $wpdb->insert($wpdb->base_prefix . 'jobsearch_countries', array(
                        'name' => $contries_info['name'],
                        'code' => $contries_info['code'],
                    ));

                    $cntry_id = $wpdb->insert_id;
                    $wpdb->insert($wpdb->base_prefix . 'jobsearch_states', array(
                        'state_name' => __('Enter Any State', 'wp-jobsearch'),
                        'cntry_id' => $cntry_id,
                    ));
                } else {
                    $this->query = "update `{$wpdb->base_prefix}jobsearch_countries`  set name = '" . $contries_info['name'] . "' where code = '" . $contries_info['code'] . "' ";
                    $wpdb->query($this->query);
                }
            }
            echo json_encode(array('status' => 'data_updated'));
            wp_die();
        }

        public function jobsearch_load_single_state_cities_data_callback()
        {
            global $wpdb;
            $state_id = isset($_POST['state_id']) ? $_POST['state_id'] : '';
            $cities = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_cities` WHERE state_id = '" . $state_id . "' ORDER BY city_name");

            if (!is_wp_error($cities)) {
                echo json_encode(array('result' => $cities));
            }
            wp_die();
        }

        public function jobsearch_load_single_state_data_callback()
        {
            global $wpdb;
            $country_id = isset($_POST['country_id']) ? $_POST['country_id'] : '';
            $states = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` WHERE cntry_id = '" . $country_id . "' ORDER BY state_name");
            if (!is_wp_error($states)) {
                echo json_encode(array('result' => $states));
            }
            wp_die();
        }

        public function jobsearch_load_single_country_data_callback()
        {
            global $wpdb;
            $country_id = isset($_POST['country_id']) ? $_POST['country_id'] : '';
            $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` where cntry_id = '" . $country_id . "' ");
            if (!is_wp_error($countries)) {
                echo json_encode(array('result' => $countries));
            }
            wp_die();
        }

        public function jobsearch_location_load_countries_data_callback()
        {
            global $wpdb;
            $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries`");
            if (isset($_POST['get_country_code']) && $_POST['get_country_code'] == true) {
                $jobsearch_sloc_country = $_POST['jobsearch_sloc_country'];
                $countries = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` where name = '" . $jobsearch_sloc_country . "'");
                echo json_encode($countries[0]->code);
            } else {
                echo json_encode($countries);
            }
            wp_die();
        }

        public function jobsearch_location_load_states_data_callback()
        {
            global $wpdb;
            $contry_code = isset($_POST['country_code']) ? $_POST['country_code'] : '';
            if (is_numeric($contry_code)) {
                $country_id = $contry_code;
            } else {
                $country_id = $this->get_country_id_by_code($contry_code);
            }
            $states = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` WHERE cntry_id = '" . $country_id . "' ");
            if (!is_wp_error($states)) {
                echo json_encode(array('result' => $states));
            }
            wp_die();
        }

        public function jobsearch_location_load_cities_data_callback()
        {
            global $wpdb;
            $country_code = isset($_POST['country_detail']) && strlen($_POST['country_detail']) == 2 ? $this->get_country_id_by_code($_POST['country_detail']) : $this->get_country_id_by_name($_POST['country_detail']);
            $state_id = isset($_POST['state']) ? $this->get_state_id_by_name($_POST['state'], $country_code) : '';
            $this->query = "SELECT * FROM `{$wpdb->base_prefix}jobsearch_cities` WHERE state_id = '" . $state_id . "' ";
            if ($country_code != '') {
                $this->query .= ' and cntry_id = ' . $country_code;
            }
            $cities = $wpdb->get_results($this->query);
            if (!is_wp_error($cities)) {
                echo json_encode(array('result' => $cities));
            }
            wp_die();
        }

        public function get_state_id_by_name($state_name, $country_id = '')
        {
            global $wpdb;
            $this->query = "SELECT * FROM `{$wpdb->base_prefix}jobsearch_states` WHERE state_name = '" . $state_name . "'";
            if (!empty($country_id)) {
                $this->query .= " and cntry_id = $country_id ";
            }

            $states = $wpdb->get_results($this->query);
            return $states[0]->state_id;
        }

        public function get_country_id_by_code($contry_code)
        {
            global $wpdb;
            $country_detail = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` WHERE code = '" . $contry_code . "' ");
            return $country_detail[0]->cntry_id;
        }

        public function get_country_id_by_name($contry_name)
        {
            global $wpdb;
            $country_detail = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}jobsearch_countries` WHERE name = '" . $contry_name . "' ");
            return $country_detail[0]->cntry_id;
        }

        public static function delete_directory($dirname)
        {

            if (is_dir($dirname))
                $dir_handle = opendir($dirname);
            if (!$dir_handle)
                return false;
            while ($file = readdir($dir_handle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($dirname . "/" . $file))
                        unlink($dirname . "/" . $file);
                    else
                        self::delete_directory($dirname . '/' . $file);
                }
            }
            closedir($dir_handle);
            rmdir($dirname);
            return true;
        }
    }
}
global $jobsearch_location_ajax;
$jobsearch_location_ajax = new jobsearch_location_ajax();