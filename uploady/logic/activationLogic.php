<?php

$token = $utils->sanitize($_GET['token']);

if ($token) {
    if ($user->activate($token)) {
        $msg = $lang["general"]["account_activation_success"];
    } else {
        $msg = $lang["general"]["account_activation_failed"];
    }
}

$page = 'activation';
$title = $lang["general"]['activation_title'];
