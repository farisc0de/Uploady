<?php

$handler = new Uploady\Handler\UploadHandler($db);

if (isset($_GET['file_id'])) {
    if (!$handler->fileExist($_GET['file_id'])) {
        die($lang['file_not_found']);
    }

    $file = $handler->getFile($_GET['file_id']);
    $file_data = json_decode($file->file_data);
    $handler->addDownload($_GET['file_id']);
}


$page = 'download_file';
$title = $lang['download_file_title'];
