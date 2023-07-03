<?php

$localization = new Uploady\Localization($db);
$page = new Uploady\Page($db, $localization);

$pages = $page->getAll();

$msg = isset($_GET['message']) ? $_GET['message'] : null;
