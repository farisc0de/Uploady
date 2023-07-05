<?php

include_once '../../session.php';

$PageTranslation = new Uploady\PageTranslation($db);


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    foreach ($_POST['translations'] as $translation) {
        if (!$PageTranslation->deleteTranslation($translation)) {
            $utils->redirect(SITE_URL . "/admin/translations/view.php?message=forbidden");
        }
    }

    $utils->redirect(SITE_URL . "/admin/translations/view.php?message=translation_deleted");
}
