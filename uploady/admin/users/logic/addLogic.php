<?php
$upload = new Uploady\Handler\Upload();

$user_id = $upload->generateUserID(true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        unset($_POST['csrf']);
        $_POST['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        if ($user->createUser($utils->esc($_POST))) {
            $msg = 'yes';
        } else {
            $msg = 'error';
        }
    } else {
        $msg = 'csrf';
    }
}
