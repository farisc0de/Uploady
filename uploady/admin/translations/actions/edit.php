<?php

include_once '../../session.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $translation = new Uploady\PageTranslation($db);
    $translation->updateTranslation($_POST);
    $utils->redirect(SITE_URL . '/admin/translations/view.php?message=translation_updated');
}
