<?php

include_once '../../session.php';

$utility = new \Farisc0de\PhpFileUploading\Utility();

$upload = new \Farisc0de\PhpFileUploading\Upload($utility);

$upload->generateUserID(true);

$user_id = $upload->getUserID();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!$auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        $utils->redirect(SITE_URL . "/admin/pages/view.php?message=csrf");
    }

    unset($_POST['csrf']);

    $_POST['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $_POST['user_id'] = $user_id;

    if ($user->add($utils->esc($_POST))) {
        $msg = 'user_created';
    } else {
        $msg = 'error';
    }
}

$utils->redirect($utils->siteUrl("/admin/users/view.php?&msg={$msg}"));
