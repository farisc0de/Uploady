<?php

declare(strict_types=1);

include_once __DIR__ . "/bootstrap.php";

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

use Uploady\Handler\UploadHandler;

$db = new Uploady\Database();

$utils = new Uploady\Utils();

$user = new Uploady\User($db, $utils);

$utilty = new Farisc0de\PhpFileUploading\Utility();

$upload = new Farisc0de\PhpFileUploading\Upload($utilty);

$dataCollection = new Uploady\DataCollection();

$browser = new Wolfcast\BrowserDetection();

$role = new Uploady\Role($db, $user);

$auth = new Uploady\Auth($db, $utils, $user);

$handler = new UploadHandler($db);

$localization = new Uploady\Localization($db);

$lang = $localization->loadLangauge("en");

$auth_needed_routes = ["upload"];

if (in_array($route, $auth_needed_routes)) {
    if (!$auth->authenticateApiKey()) {
        exit();
    }
}

/** API Router */
switch ($route) {
    case 'upload':
        $upload->setSiteUrl(SITE_URL);

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $upload->generateUserID();

            $upload->createUserCloud("../" . UPLOAD_FOLDER);

            $upload->setUploadFolder([
                "folder_name" => $upload->getUserCloud(UPLOAD_FOLDER),
                "folder_path" => realpath($upload->getUserCloud("../" . UPLOAD_FOLDER)),
            ]);

            $upload->enableProtection();

            $upload->setSizeLimit($role->get($_SESSION['user_role'])->size_limit);

            $upload->generateFileID();

            $upload->setUpload(new Farisc0de\PhpFileUploading\File($_FILES['file'], $utilty));

            if (!$upload->checkIfNotEmpty()) {
                http_response_code(400);
                echo json_encode([
                    "error" => $lang["general"]['file_is_empty'],
                ]);
                exit();
            }

            $upload->hashName();

            if (!$upload->checkSize()) {
                http_response_code(400);
                echo json_encode([
                    "error" => $lang["general"]['file_is_too_large'],
                ]);
                exit();
            }

            if (
                !$upload->checkForbidden()
            ) {
                http_response_code(400);
                echo json_encode([
                    "error" => $lang["general"]['file_name_is_forbidden'],
                ]);
                exit();
            }

            if (
                !$upload->checkExtension()
            ) {
                http_response_code(400);
                echo json_encode([
                    "error" => $lang["general"]['file_type_is_not_allowed'],
                ]);
                exit();
            }

            if (
                !$upload->checkMime()
            ) {
                http_response_code(400);
                echo json_encode([
                    "error" => $lang["general"]['file_mime_type_is_not_allowed'],
                ]);
                exit();
            }

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
                    ),
                    json_encode(
                        [
                            "delete_at" => [
                                "downloads" => 0,
                                "days" => 0,
                            ],
                        ]
                    )
                );
            }

            $files = $upload->getFiles();

            http_response_code(200);
            echo json_encode($files[0]);
        }

        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "Page not found"]);
        break;
}
