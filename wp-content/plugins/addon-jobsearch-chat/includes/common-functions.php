<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
//
function jobsearch_chat_files_upload_dir($dir = '')
{
    $cus_dir = 'jobsearch-chat-share-files';
    $dir_path = array(
        'path' => $dir['basedir'] . '/' . $cus_dir,
        'url' => $dir['baseurl'] . '/' . $cus_dir,
        'subdir' => $cus_dir,
    );
    return $dir_path + $dir;
}