<?php

$localization = new Uploady\Localization($db);
$page = new Uploady\Page($db, $localization);

$pages = $page->getAll();
$langs = $localization->getActiveLanguages();

$translation = new Uploady\PageTranslation($db);

$paget = $translation->getTranslationRecord($_GET['id']);
