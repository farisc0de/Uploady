<?php
$upload = new \Farisc0de\PhpFileUploading\Upload();

$upload->generateUserID(true);

$user_id = $upload->getUserID();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        unset($_POST['csrf']);
        $_POST['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        if ($user->add($utils->esc($_POST))) {
            $msg = 'yes';
        } else {
            $msg = 'error';
        }
    } else {
        $msg = 'csrf';
    }
}
