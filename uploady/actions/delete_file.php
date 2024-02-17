<?php

include_once '../session.php';

header("Content-type: application/json; charset=UTF-8");

$handler = new Uploady\Handler\UploadHandler($db);

if (isset($_POST['file_id']) && isset($_POST['user_id'])) {

    $fileID = $utils->sanitize($_POST['file_id']);
    $userID = $utils->sanitize($_POST['user_id']);

    if ($handler->fileExist($fileID) && $handler->userExist($userID)) {
        $file = json_decode($handler->getFile($fileID)->file_data);
        if ($handler->deleteFile($fileID, $userID)) {
            unlink(realpath("../" . UPLOAD_FOLDER . "/{$userID}/{$file->filename}"));
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
