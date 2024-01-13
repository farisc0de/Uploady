<?php
include_once '../../session.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!$auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        $utils->redirect($utils->siteUrl('/admin/users/view.php?msg=csrf'));
    }

    foreach ($_POST['userid'] as $id) {
        if ($data->id == $id) {
            $utils->redirect($utils->siteUrl('/admin/users/view.php?msg=forbidden'));
        }
        $user->delete((int) $id);
    }

    $utils->redirect($utils->siteUrl('/admin/users/view.php?msg=user_deleted'));
}
