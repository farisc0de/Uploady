<?php

use Uploady\Handler\Upload;
use Uploady\Handler\UploadHandler;

$upload = new Upload;
$handler = new UploadHandler($db);

$upload->setController("src/Uploady/Handler/");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $inputs = $upload->fixArray($_FILES['file']);

    if (count($inputs) > 10) {
        $utils->redirect('index.php?error=1');
    }

    $upload->setUserID(
        (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $upload->generateUserID())
    );

    $upload->createUserCloud(UPLOAD_FOLDER);

    $upload->setUploadFolder([
        "folder_name" => $upload->getUserCloud(UPLOAD_FOLDER),
        "folder_path" => realpath($upload->getUserCloud(UPLOAD_FOLDER)),
    ]);

    $upload->enableProtection();

    $upload->setSizeLimit(MAX_SIZE);

    foreach ($inputs as $file) {

        $upload->setFileID($upload->generateFileID());

        $upload->setUpload($file);

        if ($upload->checkIfEmpty()) {

            $upload->hashName();

            if ($upload->checkSize()) {
                if (
                    $upload->checkForbidden() &&
                    $upload->checkExtension() &&
                    $upload->checkMime()
                ) {
                    if ($upload->upload()) {
                        $handler->addFile(
                            $upload->getFileID(),
                            $upload->getUserID(),
                            $upload->getJSON()
                        );
                    }
                }
            }
        }
    }
}

$resp = $upload->getLogs();
$files = $upload->getFiles();

$page = 'upload_file';
