<?php
$upload = new \Uploady\Handler\UploadHandler($db);

$files = $upload->getFiles();

$files_info = [];

foreach ($files as $file) {
    array_push($files_info, json_decode($file->file_data, true));
}
