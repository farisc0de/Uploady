<?php

$user_data = $user->getUserData($_GET['username']);

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}
