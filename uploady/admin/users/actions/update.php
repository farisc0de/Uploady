<?php
require_once '../../session.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $msg_code = "";

    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf']) == false) {
        $msg_code = "csrf";
    } else {
        unset($_POST['csrf']);
        $id = (int) $_POST['id'];
        if ($_POST['password'] || $_POST['password'] != "") {
            $password = $utils->sanitize($_POST['password']);
            $_POST['password'] = password_hash($password, PASSWORD_BCRYPT);
        } else {
            unset($_POST['password']);
        }

        if ($user->updateUser($id, $utils->esc($_POST))) {
            $msg_code = "yes";
        } else {
            $msg_code = "error";
        }
    }

    $utils->redirect($utils->siteUrl(
        "/admin/users/edit.php?username={$_POST['username']}&msg={$msg_code}"
    ));
}
