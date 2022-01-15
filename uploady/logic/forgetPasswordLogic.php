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
            $msg = "Instructions has been send to your email";
        } else {
            $err = "Username does not exist!";
        }
    } else {
        $err = "Please enter a valid email";
    }
}

$page = "forgetPassword";
