<?php

$tpl = new Uploady\Template("template/emails");

$page = "resetPasswordPage";

$token = $utils->sanitize($_GET['token']);

$updatePassword = new Uploady\ResetPassword($database, $user, $utils, $tpl);

if ($updatePassword->isExist($token) == true) {
    $data = $updatePassword->getUserAssignToToken($token);
    $answered = isset($_GET['answered']) ? $utils->sanitize($_GET['answered']) : "false";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $Password = $utils->sanitize($_POST['password']);
        $confirmPassword = $utils->sanitize($_POST['confirmPassword']);
        if ($Password == $confirmPassword) {
            if ($updatePassword->updatePassword($token, $data->username, $_POST['password'])) {
                $msg = "Password has been updated";
            } else {
                $err = "Please enter more then 8 characters";
            }
        } else {
            $err = "Password confirm is incorrect";
        }
    }
} else {
    $utils->redirect($utils->siteUrl("/expire.php"));
}

session_destroy();
