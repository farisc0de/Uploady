<?php

use Uploady\Localization;
use Uploady\Page;
use Uploady\PageTranslation;

$PTobj = new PageTranslation($db);
$LocalObject = new Localization($db);
$pageObject = new Page($db, $LocalObject);

$page_translations = $obj->getTranslations();

print_r($page_translations);
