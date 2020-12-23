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

    $upload->setUploadFolder([
        "folder_name" => "uploads",
        "folder_path" => realpath("uploads"),
    ]);

    $upload->useHashAsName(true);

    $upload->enableProtection();

    $upload->setSizeLimit('100 MB');

    $upload->setUserID(
        (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $upload->generateUserID())
    );

    foreach ($inputs as $file) {
        $upload->setFileID($upload->generateFileID());

        $upload->setUpload($file);

        if ($upload->checkIfEmpty()) {
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
