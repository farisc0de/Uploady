<?php

include_once '../../session.php';

$utility = new \Farisc0de\PhpFileUploading\Utility();

$upload = new \Farisc0de\PhpFileUploading\Upload($utility);

$upload->generateUserID(true);

$user_id = $upload->getUserID();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!$auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        $utils->redirect($utils->siteUrl("/admin/users/view.php?msg=csrf"));
    }

    unset($_POST['csrf']);

    $password = $utils->sanitize($_POST['password']);
    $_POST['password'] = password_hash($password, PASSWORD_BCRYPT);
    $_POST['user_id'] = $user_id;

    if ($user->add($utils->esc($_POST))) {
        $msg = 'user_created';
    } else {
        $msg = 'error';
    }
}

$utils->redirect($utils->siteUrl("/admin/users/view.php?&msg={$msg}"));
