<?php

declare(strict_types=1);

include_once __DIR__ . "/bootstrap.php";

session_start();

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

$api = new Uploady\API(
    $upload,
    $role,
    $localization,
    $utilty,
    $dataCollection,
    $browser,
    $handler
);

$auth_needed_routes = ["upload"];

if (in_array($route, $auth_needed_routes)) {
    if (!$auth->authenticateApiKey()) {
        exit();
    }
}

/** API Router */
switch ($route) {
    case 'upload':
        $api->processRequest($_SERVER['REQUEST_METHOD'], $id);
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "Route not found"]);
        break;
}
