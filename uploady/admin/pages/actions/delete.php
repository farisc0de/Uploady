<?php

include_once '../../session.php';

$localization = new \Uploady\Localization($db);
$page = new \Uploady\Page($db, $localization);


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    foreach ($_POST['slug'] as $slug) {
        $page->delete($slug);
    }

    $utils->redirect(SITE_URL . "/admin/pages/view.php?message=yes");
}
