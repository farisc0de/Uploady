<?php

use Uploady\Handler\UploadHandler;

$handler = new UploadHandler($db);

$user_id = $_GET['user_id'];
$file_id = $_GET['file_id'];

if ($_SESSION['user_id'] != $user_id) {
    die("You are not authorized to edit this file");
}

$file = $handler->getFile($file_id);

$file_data = json_decode($file->file_data, true);
$file_settings = json_decode($file->file_settings, true);

$picture = $file_data['directlink'];

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

$effects = [
    "vintage" => "Vintage",
    "lomo" => "Lomo",
    "clarity" => "Clarity",
    "sinCity" => "Sin City",
    "crossProcess" => "Cross Process",
    "sunrise" => "Sunrise",
    "orangePeel" => "Orange Peel",
    "love" => "Love",
    "grungy" => "Grungy",
    "jarques" => "Jarques",
    "pinhole" => "Pinhole",
    "oldBoot" => "Old Boot",
    "glowingSun" => "Glowing Sun",
    "hazyDays" => "Hazy Days",
    "nostalgia" => "Nostalgia",
    "herMajesty" => "Her Majesty",
    "hemingway" => "Hemingway",
    "concentrate" => "Concentrate"
];



$image_mime = [
    "image/jpeg",
    "image/png",
    "image/gif",
    "image/jpeg",
    "image/bmp",
    "image/tiff",
    "image/x-icon",
    "image/svg+xml"
];

$title = $lang["general"]['edit_file_title'];
