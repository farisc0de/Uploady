<?php

use Uploady\Handler\UploadHandler;

$hander = new UploadHandler($db);

$user_id = $_GET['user_id'];
$file_id = $_GET['file_id'];

if ($_SESSION['user_id'] != $user_id) {
    die("You are not authorized to edit this file");
}

$file = json_decode($hander->getFile($file_id)->file_data, true);

$picture = $file['directlink'];

$filters = [
    "Brightness",
    "Contrast",
    "Saturation",
    "Vibrance",
    "Sharpen",
    "Blur",
    "Hue",
    "Sepia"
];

$title = $lang["general"]['edit_file_title'];
