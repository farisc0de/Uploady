<?php
$upload = new \Uploady\Handler\UploadHandler($db);

$files = $upload->getFiles();

$files_info = [];

foreach ($files as $file) {
    array_push($files_info, json_decode($file->file_data, true));
}

$mailer = new Uploady\Mailer($db);

$user = new Uploady\User($db, $utils);

$count_user = $user->numUsers();

$count_files = $upload->countFiles();
