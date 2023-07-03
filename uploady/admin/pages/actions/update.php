<?php

include_once '../../session.php';

$loclizer = new Uploady\Localization($db);

$utils = new Uploady\Utils($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $slug = $_POST['slug'];
    $page = new Uploady\Page($db, $loclizer);
    $page->update($_POST['pageid'], $slug);

    $utils->redirect(SITE_URL . "/admin/pages/view.php?message=yes");
}
