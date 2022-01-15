<?php
include_once '../../session.php';

if ($auth->checkToken($_GET['token'], $_SESSION['csrf'])) {
    $user->deleteUser($_SESSION['username']);
    $utils->redirect($utils->siteUrl('/logout.php?redirect=delete'));
} else {
    $utils->redirect($utils->siteUrl('profile/account.php?msg=csrf'));
}
