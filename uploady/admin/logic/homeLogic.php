<?php

$upload = new \Uploady\Handler\UploadHandler($db);

$mailer = new Uploady\Mailer($db);

$user = new Uploady\User($db, $utils);

$page = new Uploady\Page($db, $utils);

$count_user = $user->countAll();

$count_files = $upload->countFiles();

$count_downloads = $upload->getDownloadsTotal();

$count_pages = $page->countAll();

$files_info = [];

$latest = $upload->getLatestFiles();

foreach ($latest as $file) {
    array_push($files_info, json_decode($file->file_data, true));
}

$users = $user->getAll();
