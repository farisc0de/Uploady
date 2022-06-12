<?php

use Uploady\Handler\UploadHandler;

$utilty = new Farisc0de\PhpFileUploading\Utility();

$upload = new Farisc0de\PhpFileUploading\Upload();

$handler = new UploadHandler($db);

$upload->setController("vendor/farisc0de/phpfileuploading/src/");

$upload->setSiteUrl(SITE_URL);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $inputs = $utilty->fixArray($_FILES['file']);

    if (count($inputs) > 10) {
        $utils->redirect('index.php?error=1');
    }

    $upload->generateUserID();

    $upload->createUserCloud(UPLOAD_FOLDER);

    $upload->setUploadFolder([
        "folder_name" => $upload->getUserCloud(UPLOAD_FOLDER),
        "folder_path" => realpath($upload->getUserCloud(UPLOAD_FOLDER)),
    ]);

    $upload->enableProtection();

    $upload->setSizeLimit(MAX_SIZE);

    foreach ($inputs as $file) {

        $upload->generateFileID();

        $upload->setUpload(new Farisc0de\PhpFileUploading\File($file));

        if ($upload->checkIfNotEmpty()) {

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
