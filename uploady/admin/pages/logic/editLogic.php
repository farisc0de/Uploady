<?php

$localization = new \Uploady\Localization($db);
$page = new \Uploady\Page($db, $localization);

$slug = $page->getSlug($_GET['pageid'])->slug;
