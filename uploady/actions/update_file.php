<?php

include_once '../session.php';

header("Content-type: application/json; charset=UTF-8");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

use Uploady\Handler\UploadHandler;

$utilty = new Farisc0de\PhpFileUploading\Utility();

$upload = new Farisc0de\PhpFileUploading\Upload($utilty);

$role = new Uploady\Role($db, $user);

$handler = new UploadHandler($db);

$upload->setSiteUrl(SITE_URL);

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if (isset($_GET['action']) && $_GET['action'] == "delete_settings") {
        $handler->updateFileSettings(
            $_POST['file_id'],
            $_SESSION['user_id'],
            json_encode(["delete_at" => [
                "days" => $_POST['days'],
                "downloads" => $_POST['downloads'],
            ]])
        );

        $utils->redirect($utils->siteUrl("/edit.php?user_id=" . $_SESSION['user_id'] . "&file_id=" . $_POST['file_id'] . "&success=1"));
    }

    if (isset($_GET['action']) && $_GET['action'] == "edit_image") {
        $upload->generateUserID();

        $upload->setUploadFolder([
            "folder_name" => $upload->getUserCloud(UPLOAD_FOLDER),
            "folder_path" => realpath($upload->getUserCloud("../" . UPLOAD_FOLDER)),
        ]);

        $upload->enableProtection();

        $upload->setSizeLimit($role->get($_SESSION['user_role'])->size_limit);

        $upload->setUpload(new Farisc0de\PhpFileUploading\File($_FILES['file'], $utilty));

        if (!$upload->checkIfNotEmpty()) {
            http_response_code(400);
            echo json_encode([
                "error" => $lang["general"]['file_is_empty'],
            ]);
            exit();
        }

        if ($upload->upload()) {
            http_response_code(200);
            echo json_encode([
                "success" => $lang["general"]['image_saved_success'],
            ]);
        }
    }
}
