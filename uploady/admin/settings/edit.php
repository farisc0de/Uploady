<?php
require_once  '../session.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $status = null;
    $settings = new Uploady\Settings($db);
    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf']) == false) {
        $status = "csrf";
    } else {
        $settings->updateSettings($utils->esc($_POST));

        $status = "yes";
    }

    $utils->redirect("view.php?msg=" . $utils->sanitize($status));
}
