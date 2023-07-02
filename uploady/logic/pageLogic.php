<?php

$pages = new Uploady\Page($db, $localization);

if (!isset($_GET['s'])) {
    $utils->redirect($utils->siteUrl("/"));
}

if (!$pages->isExist($utils->sanitize($_GET['s']))) {
    $utils->redirect($utils->siteUrl("/"));
}

$page_content = $pages->get($utils->sanitize($_GET['s']), $_SESSION['language']);

if ($page_content == false) {
    $page_content = json_decode(json_encode([
        'title' => $lang["general"]['page_not_found'],
        'content' => $lang["general"]['page_not_found_content']
    ]));
}

$page = $_GET['s'];
$title = $page_content->title;
