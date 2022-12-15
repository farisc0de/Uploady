<?php

$user_data = $user->get($_GET['username']);

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}

$roleobj = new Uploady\Role($db, $user);
$roles = $roleobj->getAll();
