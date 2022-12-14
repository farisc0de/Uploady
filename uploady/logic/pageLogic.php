<?php

$pages = new Uploady\Page($db);

if (!isset($_GET['s'])) {
    $utils->redirect($utils->siteUrl("/"));
}

if (!$pages->isExist($_GET['s'])) {
    $utils->redirect($utils->siteUrl("/"));
}

$page_content = $pages->get($_GET['s']);

$page = $_GET['s'];
