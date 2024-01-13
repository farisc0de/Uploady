<?php
include_once '../../session.php';

if ($auth->checkToken($_GET['token'], $_SESSION['csrf'])) {
    if ($user->get($_SESSION['username'])->role == 3) {
        $utils->redirect($utils->siteUrl('/profile/account.php?msg=error'));
        return;
    }
    $user->delete($_SESSION['username']);
    $utils->redirect($utils->siteUrl('/logout.php?redirect=user_deleted'));
} else {
    $utils->redirect($utils->siteUrl('/profile/account.php?msg=csrf'));
}
