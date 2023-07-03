<?php

$localization = new Uploady\Localization($db);
$page = new Uploady\Page($db, $localization);

$pages = $page->getAll();
$langs = $localization->getActiveLanguages();
