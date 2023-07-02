<?php

$handler = new Uploady\Handler\UploadHandler($db);

if (isset($_GET['file_id'])) {
    if (!$handler->fileExist($_GET['file_id'])) {
        die($lang["general"]['file_not_found']);
    }

    $file = $handler->getFile($_GET['file_id']);
    $file_data = json_decode($file->file_data);
    $file_settings = json_decode($file->file_settings, true);


    if ($file_settings['delete_at']["downloads"] != 0) {
        if ($file_settings['delete_at']["downloads"] <= $file->downloads) {
            $handler->deleteFileAsAdmin($_GET['file_id']);
            unlink(realpath("uploads/{$_GET['user_id']}/{$file->filename}"));
            $utils->redirect(SITE_URL);
        }
    }

    if ($file_settings['delete_at']["days"] != 0) {
        if (
            $file_settings['delete_at']["days"] <=
            round((time() - strtotime($file->uploaded_at)) / (60 * 60 * 24))
        ) {
            $handler->deleteFileAsAdmin($_GET['file_id']);
            unlink(realpath("uploads/{$_GET['user_id']}/{$file->filename}"));
            $utils->redirect(SITE_URL);
        }
    }

    $handler->addDownload($_GET['file_id']);
}

$page = 'download_file';
$title = $lang["general"]['download_file_title'];
