<?php

include_once '../../session.php';

$localization = new \Uploady\Localization($db);
$page = new \Uploady\Page($db, $localization);


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!$auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        $utils->redirect(SITE_URL . "/admin/pages/view.php?msg=csrf");
    }

    foreach ($_POST['slug'] as $slug) {
        if (!$page->delete($slug)) {
            $utils->redirect(SITE_URL . "/admin/pages/view.php?msg=forbidden");
        }
    }

    $utils->redirect(SITE_URL . "/admin/pages/view.php?msg=page_deleted");
}
