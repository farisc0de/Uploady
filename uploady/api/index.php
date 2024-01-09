<?php

declare(strict_types=1);

include_once __DIR__ . "/bootstrap.php";

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

use Uploady\Auth;
use Uploady\Database;
use Uploady\User;
use Uploady\Utils;

$database = new Database();
$utils = new Utils();

$user = new User($database, $utils);

$auth = new Auth($database, $utils);

$auth_needed_routes = ["tasks"];

if (in_array($route, $auth_needed_routes)) {
    $auth->authenticateApiKey($_ENV['AUTHENTICATE_BY']);

    $user_id = $_SESSION['user_id'];
}

/** API Router */
switch ($route) {
    case 'upload':
        $task_gatway = new TaskGateway($database);

        $task_controller = new TaskController($task_gatway, $user_id);

        $task_controller->processRequest($method, $id);

        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "Page not found"]);
        break;
}
