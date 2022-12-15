<?php

$role = new Uploady\Role($db, $user);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        unset($_POST['csrf']);
        if ($role->createRole(
            $utils->sanitize($_POST['role_name']),
            $utils->sanitize($_POST['size_limit'])
        )) {
            $msg = 'yes';
        } else {
            $msg = 'error';
        }
    } else {
        $msg = 'csrf';
    }
}
