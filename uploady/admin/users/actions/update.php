<?php
require_once '../../session.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $msg_code = "";

    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        unset($_POST['csrf']);
        $id = (int) $_POST['id'];

        if (!$_POST['password'] || $_POST['password'] == "") {
            unset($_POST['password']);
        } else {
            $password = $utils->sanitize($_POST['password']);
            $_POST['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $msg_code = $user->update($id, $utils->esc($_POST)) ? "yes" : "error";
    }

    $utils->redirect($utils->siteUrl(
        "/admin/users/edit.php?username={$_POST['username']}&msg={$msg_code}"
    ));
}
