<?php

session_start();

include_once 'config/config.php';

$utils = new Uploady\Utils();

if (session_destroy()) {
    if (isset($_GET['redirect']) && $_GET['redirect'] == "delete") {
        $utils->redirect($utils->siteUrl('/login.php?msg=delete'));
    }

    $utils->redirect($utils->siteUrl('/index.php'));
}
