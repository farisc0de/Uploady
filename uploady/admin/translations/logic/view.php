<?php

use Uploady\Localization;
use Uploady\Page;
use Uploady\PageTranslation;

$PTobj = new PageTranslation($db);
$LocalObject = new Localization($db);
$pageObject = new Page($db, $LocalObject);

$page_translations = $PTobj->getTranslations();
$languages = $LocalObject->getLanguages();

if (isset($_GET['message'])) {
    $msg = $_GET['message'];
}
