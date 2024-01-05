<?php

include_once '../session.php';

header("Content-type: application/json; charset=UTF-8");

$handler = new Uploady\Handler\UploadHandler($db);

if (isset($_POST['file_id']) && isset($_POST['user_id'])) {
    if ($handler->fileExist($_POST['file_id']) && $handler->userExist($_POST['user_id'])) {
        $file = json_decode($handler->getFile($_POST['file_id'])->file_data);
        if ($handler->deleteFile($_POST['file_id'], $_POST['user_id'])) {
            unlink(realpath("../" . UPLOAD_FOLDER . "/{$_POST['user_id']}/{$file->filename}"));
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => $lang["general"]['file_deleted_success']
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => $lang["general"]['file_deleted_failed'] . " ):"
            ]);
        }
    } else {
        http_response_code(400);
        echo  json_encode([
            "status" => "error",
            "message" => $lang["general"]["file_or_user_not_found"]
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => $lang["general"]['file_id_missing']
    ]);
}
