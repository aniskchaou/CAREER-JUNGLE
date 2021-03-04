<?php
if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_init_emoji') {
    echo json_encode(array('emoji' => 'yes'));
    die();
}