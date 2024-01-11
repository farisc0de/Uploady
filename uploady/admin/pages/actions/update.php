<?php

include_once '../../session.php';

$loclizer = new Uploady\Localization($db);

$utils = new Uploady\Utils($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['csrf']) && $_POST['csrf'] != $_SESSION['csrf']) {
        $slug = $_POST['slug'];
        $page = new Uploady\Page($db, $loclizer);
        $page->update($_POST['pageid'], $slug);

        $utils->redirect(SITE_URL . "/admin/pages/view.php?message=yes");
    } else {
        $utils->redirect($utils->siteUrl("/admin/pages/view.php?message=csrf"));
    }
}
