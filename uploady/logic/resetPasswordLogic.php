<?php

$tpl = new Uploady\Template("template/emails");

$page = "resetPasswordPage";

$token = $utils->sanitize($_GET['token']);

$updatePassword = new Uploady\ResetPassword($db, $user, $utils, $tpl);

if ($updatePassword->isExist($token) == true) {
    $data = $updatePassword->getUserAssignedToToken($token);
    $answered = isset($_GET['answered']) ? $utils->sanitize($_GET['answered']) : "false";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $Password = $utils->sanitize($_POST['password']);
        $confirmPassword = $utils->sanitize($_POST['confirmPassword']);
        if ($Password == $confirmPassword) {
            if ($updatePassword->updatePassword($token, $data->username, $_POST['password'])) {
                $msg = $lang["general"]["password_reset_success"];
            } else {
                $err = $lang["general"]['password_reset_failed'];
            }
        } else {
            $err = $lang["general"]['password_not_match'];
        }
    }
} else {
    $utils->redirect($utils->siteUrl("/expire.php"));
}

session_destroy();

$title = $lang["general"]['reset_password_title'];
