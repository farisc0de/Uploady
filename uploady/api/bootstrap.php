<?php

include_once dirname(__DIR__) . "/config/config.php";

header("Content-type: application/json; charset=UTF-8");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

set_error_handler("Uploady\Handler\ErrorHandler::handleError");
set_exception_handler("Uploady\Handler\ErrorHandler::handleException");

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$parts = explode("/", $path);

$route = $parts[2];

$id = $parts[3] ?? null;

$method = $_SERVER['REQUEST_METHOD'];
