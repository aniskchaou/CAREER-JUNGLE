<?php

/**
 * File Type: Icons Uploader
 */
if (!class_exists('Careerfy_Icons_Uploader')) {

    class Careerfy_Icons_Uploader {

        var $GROUP_NAME;
        var $GROUP_PATH;

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('wp_ajax_careerfy_reading_zip', array($this, 'careerfy_reading_zip_callback'));
            add_action('wp_ajax_careerfy_remove_group', array($this, 'careerfy_remove_group_callback'));
            add_action('wp_ajax_careerfy_group_status', array($this, 'careerfy_group_status_callback'));
        }
        
        public function icons_upload_files_path($dir = '') {

            $cus_dir = 'careerfy-icons-manager';
            $dir_path = array(
                'path' => $dir['basedir'] . '/' . $cus_dir,
                'url' => $dir['baseurl'] . '/' . $cus_dir,
                'subdir' => $cus_dir,
            );
            return $dir_path + $dir;
        }

        /*
         * Reading Zip File
         */

        public function careerfy_reading_zip_callback() {
            
            add_filter('upload_dir', array($this, 'icons_upload_files_path'));
            $file_id = $_POST['attachment_id'];
            $file_path = get_attached_file($file_id);
            $group_name = basename($file_path);
            if (!$this->is_file_zip($file_path)) {
                echo json_encode(array('type' => 'error', 'msg' => __('Only Zip Files are Supported', 'careerfy-frame')));
                wp_die();
            }
            $group_name = explode('.zip', $group_name);
            $group_name = $group_name[0];
            $this->GROUP_NAME = $group_name;
            $upload_dir = wp_upload_dir();
            $destination_path = $upload_dir['path'] . '/' . $group_name;
            $this->GROUP_PATH = $destination_path;
            wp_mkdir_p($destination_path);
            $destination_path = $destination_path . '/';
            $unzipfile = unzip_file($file_path, $destination_path);

            $json_exists = $this->check_file_exists($this->GROUP_PATH . '*/selection.json');
            $css_exists = $this->check_file_exists($this->GROUP_PATH . '*/style.css');
            $svg_exists = $this->check_file_exists($this->GROUP_PATH . '*/*.svg');

            if (empty($json_exists) || empty($css_exists) || empty($svg_exists)) {
                echo json_encode(array('type' => 'error', 'msg' => __('Zip must contain the selection.json, SVG, and style.css files.', 'careerfy-frame')));
                wp_die();
            }
            $this->register_icon();
            
            remove_filter('upload_dir', array($this, 'icons_upload_files_path'));
            
            echo json_encode(array('type' => 'success', 'msg' => __('Icons are uploaded Successfully', 'careerfy-frame')));
            wp_die();
        }

        /*
         * Registering Icon
         */

        public function register_icon() {
            add_filter('upload_dir', array($this, 'icons_upload_files_path'));
            
            $upload_dir = wp_upload_dir();
            $new_group[$this->GROUP_NAME] = array(
                'path' => $this->GROUP_PATH,
                'url' => $upload_dir['url'] . '/' . $this->GROUP_NAME,
                'status' => 'on',
            );
            $icons_groups = get_option('careerfy_icons_groups');
            if (!empty($icons_groups)) {
                $new_group = array_merge($icons_groups, $new_group);
            }
            
            remove_filter('upload_dir', array($this, 'icons_upload_files_path'));
            
            if (isset($icons_groups[$this->GROUP_NAME]) && !empty($icons_groups[$this->GROUP_NAME])) {
                echo json_encode(array('type' => 'error', 'msg' => __('Group name already exists', 'careerfy-frame')));
                wp_die();
            }
            update_option('careerfy_icons_groups', $new_group);
        }

        /*
         * Check if file exists recursively
         */

        public function check_file_exists($pattern, $flags = 0) {
            $files = glob($pattern, $flags);
            foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
                $files = array_merge($files, $this->check_file_exists($dir . '/' . basename($pattern), $flags));
            }
            return $files;
        }

        /*
         * Removing Group
         */

        public function careerfy_remove_group_callback() {
            $group_name = $_POST['group_name'];
            $icons_groups = get_option('careerfy_icons_groups');
            $icons_obj = array();
            if (isset($icons_groups[$group_name])) {
                $icons_obj = $icons_groups[$group_name];
                unset($icons_groups[$group_name]);
            }
            if (isset($icons_obj['path'])) {
                $this->deleteDirectory($icons_obj['path']);
            }
            update_option('careerfy_icons_groups', $icons_groups);
            echo json_encode(array('type' => 'success', 'msg' => __('Group Successfully Removed', 'careerfy-frame')));
            wp_die();
        }

        /*
         * Deleting Directory
         */

        public function deleteDirectory($dirPath) {
            if (is_dir($dirPath)) {
                $objects = scandir($dirPath);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
                            $this->deleteDirectory($dirPath . DIRECTORY_SEPARATOR . $object);
                        } else {
                            unlink($dirPath . DIRECTORY_SEPARATOR . $object);
                        }
                    }
                }
                reset($objects);
                rmdir($dirPath);
            }
        }

        /*
         * Changing Status for Group
         */

        public function careerfy_group_status_callback() {
            $group_name = $_POST['group_name'];
            $status_value = $_POST['status_value'];
            $icons_groups = get_option('careerfy_icons_groups');
            if (isset($icons_groups[$group_name])) {
                $icons_groups[$group_name]['status'] = $status_value;
            }
            update_option('careerfy_icons_groups', $icons_groups);
            echo json_encode(array('type' => 'success', 'msg' => __('Group Status Changed Successfully', 'careerfy-frame')));
            wp_die();
        }

        /*
         * Check if file is Zip
         */

        public function is_file_zip($filename) {
            //check is valid file or not  
            if (is_file($filename) == false) {
                return false;
            }
            //check file extension match with .zip or not  
            if (pathinfo($filename, PATHINFO_EXTENSION) != 'zip') {
                return false;
            }
            $fileHeader = "\x50\x4b\x03\x04";
            $data = file_get_contents($filename);
            if (strpos($data, $fileHeader) === false) {
                return false;
            }
            return true;
        }

    }

    global $careerfy_icons_uploader;
    $careerfy_icons_uploader = new Careerfy_Icons_Uploader();
}