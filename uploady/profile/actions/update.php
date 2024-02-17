<?php
require_once '../../session.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $msg_code = "";

    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf']) == false) {
        $msg_code = "csrf";
    } else {
        $user_array = [];
        $current_id = (int) $data->id;
        $id = (int) $_POST['id'];
        if ($id == $current_id) {
            $user_array['username'] = $utils->sanitize($_POST['username']);
            $user_array['email'] = $utils->sanitize($_POST['email']);

            if ($_POST['password'] || $_POST['password'] != "") {
                $password = $utils->sanitize($_POST['password']);
                $user_array['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            if ($user->update($id, $user_array)) {
                $_SESSION['username'] = $user_array['username'];
                $msg_code = "profile_updated";
            } else {
                $msg_code = "error";
            }
        } else {
            $msg_code = "attack";
        }
    }

    $utils->redirect($utils->siteUrl("/profile/account.php?msg=" . $msg_code));
}
