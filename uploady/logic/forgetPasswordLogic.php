<?php

$tpl = new Uploady\Template("template/emails");

$utils = new Uploady\Utils();

$database = new Uploady\Database();

$user = new Uploady\User($database, $utils);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = isset($_POST['email']) ? $utils->sanitize($_POST['email']) : null;

    $rp = new Uploady\ResetPassword($database, $user, $utils, $tpl);

    if (isset($username)) {
        if ($rp->sendMessage($username)) {
            $msg = $lang["general"]["forget_password_success"];
        } else {
            $err = $lang["general"]["forget_password_failed"];
        }
    } else {
        $err = $lang["general"]["no_valid_email"];
    }
}

$page = "forgetPassword";
$title = $lang["general"]['forget_password_title'];
