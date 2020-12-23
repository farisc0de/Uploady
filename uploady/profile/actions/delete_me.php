<?php
include_once '../../session.php';

$user->deleteUser($_SESSION['username']);

$utils->redirect($utils->siteUrl('/logout.php'));
