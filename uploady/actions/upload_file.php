<?php

include_once '../session.php';

header("Content-type: application/json; charset=UTF-8");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

use Uploady\Handler\UploadHandler;
use Wolfcast\BrowserDetection;

$utilty = new Farisc0de\PhpFileUploading\Utility();

$upload = new Farisc0de\PhpFileUploading\Upload();

$dataCollection = new Uploady\DataCollection();

$browser = new BrowserDetection();

$role = new Uploady\Role($db, $user);

$handler = new UploadHandler($db);

$upload->setController("../vendor/farisc0de/phpfileuploading/src/");

$upload->setSiteUrl(SITE_URL);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $upload->generateUserID();

    $upload->createUserCloud("../" . UPLOAD_FOLDER);

    $upload->setUploadFolder([
        "folder_name" => $upload->getUserCloud("../" . UPLOAD_FOLDER),
        "folder_path" => realpath($upload->getUserCloud("../" . UPLOAD_FOLDER)),
    ]);

    $upload->enableProtection();

    $upload->setSizeLimit($role->get($_SESSION['user_role'])->size_limit);

    $upload->generateFileID();

    $upload->setUpload(new Farisc0de\PhpFileUploading\File($_FILES['file']));

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
                        $upload->getJSON(),
                        json_encode(
                            [
                                "ip_address" => $dataCollection->collectIP(),
                                "country" => $dataCollection->idendifyCountry(),
                                "browser" => $dataCollection->getBrowser($browser),
                                "os" => $dataCollection->getOS()
                            ]
                        )
                    );
                }
            }
        }
    }
}

$resp = $upload->getLogs();
$files = $upload->getFiles();

if ($resp[0]['message'] == 0) {
    http_response_code(200);
    echo json_encode($files[0]);
} else {
    http_response_code(400);
    echo json_encode([
        "error" => $upload->getMessage($resp[0]['message']),
    ]);
}
