<?php

$pages = new Uploady\Page($db, $localization);

if (!isset($_GET['s'])) {
    $utils->redirect($utils->siteUrl("/"));
}

if (!$pages->isExist($_GET['s'])) {
    $utils->redirect($utils->siteUrl("/"));
}

$page_content = $pages->get($_GET['s'], $_SESSION['language']);

if ($page_content == false) {
    $page_content = json_decode(json_encode([
        'title' => $lang['page_not_found'],
        'content' => $lang['page_not_found_content']
    ]));
}

$page = $_GET['s'];
$title = $page_content->title;
