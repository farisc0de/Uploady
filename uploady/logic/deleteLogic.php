<?php
$handler = new Uploady\Handler\UploadHandler($db);

if (isset($_GET['file_id']) && isset($_GET['user_id'])) {
    if ($handler->fileExist($_GET['file_id']) && $handler->userExist($_GET['user_id'])) {
        $file = json_decode($handler->getFile($_GET['file_id'])->file_data);
        if ($handler->deleteFile($_GET['file_id'], $_GET['user_id'])) {
            unlink(realpath("uploads" . "/" . $file->filename));
            $msg = "File has been deleted (:";
        } else {
            $msg = "The delete process failed";
        }
    } else {
        $msg = "File does not exist or User ID is incorrect ):";
    }
} else {
    $msg = "User ID or File ID is missing";
}

$page = 'delete_file';
