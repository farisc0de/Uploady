<?php

$handler = new Uploady\Handler\UploadHandler($db);

if (isset($_GET['file_id']) && isset($_GET['user_id'])) {

    $fileID = $utils->sanitize($_POST['file_id']);
    $userID = $utils->sanitize($_POST['user_id']);

    if ($handler->fileExist($fileID) && $handler->userExist($userID) && $_SESSION['user_id'] == $userID) {
        $file = json_decode($handler->getFile($fileID)->file_data);
        if ($handler->deleteFile($fileID, $userID)) {
            unlink(realpath("uploads/{$userID}/{$file->filename}"));
            $msg = $lang["general"]['file_deleted_success'];
        } else {
            $msg = $lang["general"]['file_deleted_failed'] . " ):";
        }
    } else {
        $msg = $lang["general"]["file_or_user_not_found"];
    }
} else {
    $msg = $lang["general"]['file_id_missing'];
}

$title = $lang["general"]['delete_file_title'];
$page = 'delete_file';
