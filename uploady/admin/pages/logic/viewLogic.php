<?php

$localization = new Uploady\Localization($db);
$page = new Uploady\Page($db, $localization);

$pages = $page->getAll();

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
