<?php

$token = $utils->sanitize($_GET['token']);

if ($token) {
    if ($user->activateAccount($token)) {
        $msg = 'Account Activated';
    } else {
        $msg = 'Token does not exist';
    }
}

$page = 'activation';
