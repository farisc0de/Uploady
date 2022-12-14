<?php

$token = $utils->sanitize($_GET['token']);

if ($token) {
    if ($user->activate($token)) {
        $msg = $lang["account_activation_success"];
    } else {
        $msg = $lang["account_activation_failed"];
    }
}

$page = 'activation';
