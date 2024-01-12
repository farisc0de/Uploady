<?php

include_once '../../session.php';

$loclizer = new Uploady\Localization($db);

$utils = new Uploady\Utils($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!$auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        $utils->redirect(SITE_URL . "/admin/pages/view.php?msg=csrf");
    }

    $slug = $_POST['slug'];
    $page = new Uploady\Page($db, $loclizer);
    $page->update($_POST['pageid'], $slug);

    $utils->redirect(SITE_URL . "/admin/pages/view.php?msg=page_updated");
}
