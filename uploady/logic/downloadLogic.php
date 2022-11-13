<?php

$handler = new Uploady\Handler\UploadHandler($db);

if (isset($_GET['file_id'])) {
    if ($handler->fileExist($_GET['file_id'])) {
        $file = $handler->getFile($_GET['file_id']);
        $file_data = json_decode($file->file_data);
        $handler->addDownload($_GET['file_id']);
    } else {
        die("File does not exist ):");
    }
}


$page = 'download_file';
