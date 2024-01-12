<?php

include_once '../../session.php';

$loclizer = new Uploady\Localization($db);

$lang = $_GET['lang'];
$staus = $_GET['status'];

if ($staus == "active") {
    $loclizer->activateLanguage($lang);
    $utils->redirect(SITE_URL . "/admin/languages/view.php?msg=language_enabled");
} else {
    $loclizer->deactivateLanguage($lang);
    $utils->redirect(SITE_URL . "/admin/languages/view.php?msg=language_disabled");
}
