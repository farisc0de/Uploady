<?php

include_once '../../session.php';

$PageTranslation = new Uploady\PageTranslation($db);


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if (!$auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        $utils->redirect(SITE_URL . "/admin/translations/view.php?msg=csrf");
    }

    foreach ($_POST['translations'] as $translation) {
        if (!$PageTranslation->deleteTranslation($translation)) {
            $utils->redirect(SITE_URL . "/admin/translations/view.php?msg=forbidden");
        }
    }

    $utils->redirect(SITE_URL . "/admin/translations/view.php?msg=translation_deleted");
}
