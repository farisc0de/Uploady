<?php
$upload = new \Uploady\Handler\UploadHandler($db);

$files = $upload->getFilesById($_SESSION['user_id']);

$files_info = [];

foreach ($files as $file) {
    array_push($files_info, json_decode($file->file_data, true));
}

$page = 'myFiles';
$title = $lang["general"]['my_files_title'];
