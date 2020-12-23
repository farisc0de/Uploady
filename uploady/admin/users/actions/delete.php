<?php
include_once '../../session.php';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        foreach ($_POST['userid'] as $id) {
            $user->deleteUser((int) $id);
        }

        $utils->redirect($utils->siteUrl('/admin/users/view.php?msg=ok'));
    } else {
        $utils->redirect($utils->siteUrl('/admin/users/view.php?msg=csrf'));
    }
}
