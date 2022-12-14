<?php
$handler = new Uploady\Handler\UploadHandler($db);

if (isset($_GET['file_id']) && isset($_GET['user_id'])) {
    if ($handler->fileExist($_GET['file_id']) && $handler->userExist($_GET['user_id'])) {
        $file = json_decode($handler->getFile($_GET['file_id'])->file_data);
        if ($handler->deleteFile($_GET['file_id'], $_GET['user_id'])) {
            unlink(realpath("uploads/{$_GET['user_id']}/{$file->filename}"));
            $msg = $lang['file_deleted_success'];
        } else {
            $msg = $lang['file_deleted_failed'] . " ):";
        }
    } else {
        $msg = $lang["file_or_user_not_found"];
    }
} else {
    $msg = $lang['file_id_missing'];
}

$page = 'delete_file';
